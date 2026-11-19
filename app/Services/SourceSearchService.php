<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class SourceSearchService
{
    private const MAX_RESULTS = 5;

    public function __construct(
        private OpenAIService $openAIService,
        private ReferenceService $referenceService,
        private PromptService $promptService,
    ) {}

    public function official(string $query): array
    {
        $domains = config('services.sources.official_domains', []);
        $max = self::MAX_RESULTS;

        $prompt = <<<PROMPT
Busca en fuentes oficiales del Estado peruano documentos o páginas con información sobre: "{$query}".

Devuelve hasta {$max} resultados, los más pertinentes y recientes. Para cada uno indica:
- institucion: nombre completo de la institución autora (por ejemplo, "Instituto Nacional de Estadística e Informática").
- anio: año de publicación; vacío si no aparece.
- titulo: título exacto del documento o de la página.
- tipo: "report" si es un informe, boletín o publicación en PDF; "web_page" si es una página web; "dataset" si es un conjunto de datos.
- url: el enlace exacto que encontraste en la búsqueda. No construyas ni adivines enlaces.
- extracto: una o dos frases con el dato que aporta, tal como aparece en la fuente.

No incluyas resultados que no encontraste en la búsqueda.

Responde únicamente con un JSON válido:
{"resultados":[{"institucion":"","anio":"","titulo":"","tipo":"","url":"","extracto":""}]}
PROMPT;

        $response = $this->openAIService->webSearch($prompt, $domains);
        $items = $this->decodeJson($response['text'])['resultados'] ?? [];

        $results = [];

        foreach (array_slice((array) $items, 0, self::MAX_RESULTS) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $url = $this->cleanUrl((string) ($item['url'] ?? ''));

            if (!$url || !$this->isAllowedDomain($url, $domains)) {
                continue;
            }

            $institution = trim((string) ($item['institucion'] ?? ''));
            $type = in_array($item['tipo'] ?? '', ['report', 'web_page'], true) ? $item['tipo'] : 'web_page';

            $results[] = [
                'authors' => [$institution],
                'year' => trim((string) ($item['anio'] ?? '')),
                'title' => trim((string) ($item['titulo'] ?? '')),
                'source_type' => $type,
                'container_title' => $type === 'web_page' ? $institution : null,
                'publisher' => null,
                'url' => $url,
                'excerpt' => trim((string) ($item['extracto'] ?? '')),
            ];
        }

        return $this->withApaText($this->onlyReachable($results));
    }

    public function academic(string $query): array
    {
        $keywords = $this->academicKeywords($query);
        $works = $this->openAlex($keywords['es'], 'es', self::MAX_RESULTS);

        if (count($works) < self::MAX_RESULTS && $keywords['en'] !== '') {
            $titles = array_map(fn ($work) => mb_strtolower((string) ($work['title'] ?? '')), $works);

            foreach ($this->openAlex($keywords['en'], null, self::MAX_RESULTS) as $work) {
                if (count($works) >= self::MAX_RESULTS) {
                    break;
                }
                if (!in_array(mb_strtolower((string) ($work['title'] ?? '')), $titles, true)) {
                    $works[] = $work;
                }
            }
        }

        $results = [];

        foreach ($works as $work) {
            $authors = array_values(array_filter(array_map(
                fn ($authorship) => $this->apaAuthor((string) ($authorship['author']['display_name'] ?? '')),
                array_slice($work['authorships'] ?? [], 0, 20)
            )));

            $title = trim((string) ($work['title'] ?? ''));

            if (empty($authors) || $title === '') {
                continue;
            }

            $doi = $work['doi'] ? preg_replace('#^https?://doi\.org/#i', '', $work['doi']) : null;
            $location = $work['primary_location'] ?? [];
            $sourceName = $location['source']['display_name'] ?? null;
            $type = match ($work['type'] ?? '') {
                'article', 'review' => 'journal_article',
                'dissertation' => 'thesis',
                'book', 'book-chapter' => 'book',
                'report' => 'report',
                default => 'web_page',
            };
            $biblio = $work['biblio'] ?? [];
            $pages = trim(($biblio['first_page'] ?? '') . (!empty($biblio['last_page']) ? '-' . $biblio['last_page'] : ''), '-');

            $results[] = [
                'authors' => $authors,
                'year' => (string) ($work['publication_year'] ?? ''),
                'title' => $title,
                'source_type' => $type,
                'container_title' => in_array($type, ['journal_article', 'web_page'], true) ? $sourceName : null,
                'volume' => $biblio['volume'] ?? null,
                'issue' => $biblio['issue'] ?? null,
                'pages' => $pages ?: null,
                // Tesis y libros: el repositorio o la editorial
                'publisher' => in_array($type, ['thesis', 'book', 'report'], true) ? $sourceName : null,
                'url' => $doi ? null : ($location['landing_page_url'] ?? null),
                'doi' => $doi,
                'excerpt' => $this->abstractExcerpt($work['abstract_inverted_index'] ?? null),
            ];
        }

        return $this->withApaText($results);
    }

    private function academicKeywords(string $query): array
    {
        try {
            $result = $this->openAIService->json($this->promptService->promptSearchKeywords($query)) ?? [];
        } catch (\Throwable $e) {
            \Log::warning('No se pudieron generar las palabras clave de búsqueda: ' . $e->getMessage());
            $result = [];
        }

        // Comas, barras y dos puntos separan filtros en OpenAlex: se quitan
        $clean = fn ($text) => trim(preg_replace('/\s+/u', ' ', str_replace([',', '|', ':'], ' ', (string) $text)));

        return [
            'es' => $clean($result['es'] ?? '') ?: $clean($query),
            'en' => $clean($result['en'] ?? ''),
        ];
    }

    // Búsqueda en OpenAlex: título y resumen, solo artículos, libros, capítulos y tesis
    private function openAlex(string $keywords, ?string $language, int $limit): array
    {
        $filters = [
            "title_and_abstract.search:{$keywords}",
            'type:article|book|book-chapter|dissertation|review',
        ];

        if ($language) {
            $filters[] = "language:{$language}";
        }

        $response = Http::timeout(20)
            ->retry(2, 500)
            ->get('https://api.openalex.org/works', [
                'filter' => implode(',', $filters),
                'per-page' => $limit,
                'select' => 'title,publication_year,doi,authorships,primary_location,biblio,type,abstract_inverted_index',
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAlex: ' . $response->status());
        }

        return $response->json('results') ?? [];
    }

    // Normaliza y agrega el texto APA 7
    private function withApaText(array $results): array
    {
        $formatted = [];

        foreach ($results as $result) {
            $reference = $this->referenceService->normalize($result);

            if (!$reference) {
                continue;
            }

            $reference['excerpt'] = $result['excerpt'] ?? '';
            $reference['apa_text'] = $this->referenceService->formatPlain($reference);
            $formatted[] = $reference;
        }

        return $formatted;
    }

    // Consulta los enlaces en paralelo y se queda con los que responden
    private function onlyReachable(array $results): array
    {
        if (empty($results)) {
            return [];
        }

        $responses = Http::pool(fn (Pool $pool) => array_map(
            fn ($result) => $pool->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; TesisIA)', 'Range' => 'bytes=0-2048'])
                ->timeout(8)
                ->get($result['url']),
            $results
        ));

        return array_values(array_filter($results, function ($result, $index) use ($responses) {
            $response = $responses[$index] ?? null;

            return $response instanceof \Illuminate\Http\Client\Response
                && ($response->successful() || in_array($response->status(), [403, 405], true));
        }, ARRAY_FILTER_USE_BOTH));
    }

    private function isAllowedDomain(string $url, array $domains): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach ($domains as $domain) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    private function cleanUrl(string $url): ?string
    {
        $url = trim($url);

        if (!preg_match('#^https?://#i', $url)) {
            return null;
        }

        $parts = parse_url($url);
        $query = [];
        parse_str($parts['query'] ?? '', $query);
        $query = array_filter($query, fn ($key) => !str_starts_with((string) $key, 'utm_'), ARRAY_FILTER_USE_KEY);

        return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '')
            . ($parts['path'] ?? '')
            . ($query ? '?' . http_build_query($query) : '');
    }

    private function apaAuthor(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name));

        // Instituciones registradas como autor ("Escuela de Negocios"): van tal cual
        $institution = '/\b(escuela|universidad|university|instituto|institute|facultad|ministerio|centro|center|departamento|department|organizaci[oó]n|asociaci[oó]n|banco|consejo|comisi[oó]n|direcci[oó]n|negocios|editorial|grupo)\b/iu';

        if (count($words) < 2 || count($words) > 5 || preg_match($institution, $name)) {
            return trim($name);
        }

        // Dos apellidos si el nombre tiene 4 palabras o más (costumbre hispana)
        $surnameCount = count($words) >= 4 ? 2 : 1;
        $surnames = array_slice($words, -$surnameCount);
        $given = array_slice($words, 0, -$surnameCount);
        $initials = implode(' ', array_map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)) . '.', $given));

        return implode(' ', $surnames) . ', ' . $initials;
    }

    // OpenAlex guarda el resumen como índice invertido: se reconstruye y se recorta
    private function abstractExcerpt(?array $index): string
    {
        if (empty($index)) {
            return '';
        }

        $words = [];
        foreach ($index as $word => $positions) {
            foreach ($positions as $position) {
                $words[$position] = $word;
            }
        }
        ksort($words);

        $text = implode(' ', $words);

        return mb_strlen($text) > 300 ? mb_substr($text, 0, 300) . '…' : $text;
    }

    private function decodeJson(string $text): array
    {
        $decoded = json_decode(trim($text), true);

        if (is_array($decoded)) {
            return $decoded;
        }

        // Por si la respuesta trae texto alrededor del JSON
        if (preg_match('/\{.*\}/s', $text, $match)) {
            return json_decode($match[0], true) ?: [];
        }

        return [];
    }
}
