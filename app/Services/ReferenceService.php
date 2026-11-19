<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReferenceService
{
    private const SOURCE_TYPES = ['book', 'journal_article', 'web_page', 'thesis', 'report', 'conference_paper'];
    public function normalize(array $ref): ?array
    {
        $authors = array_values(array_filter(
            array_map(fn ($author) => trim((string) $author), (array) ($ref['authors'] ?? [])),
            fn ($author) => $author !== ''
        ));

        $title = trim((string) ($ref['title'] ?? ''));

        if (empty($authors) || $title === '') {
            return null;
        }

        $year = trim((string) ($ref['year'] ?? ''));
        $sourceType = in_array($ref['source_type'] ?? null, self::SOURCE_TYPES, true) ? $ref['source_type'] : 'web_page';

        return [
            'authors' => $authors,
            'year' => $year !== '' ? $year : 's.f.',
            'title' => $title,
            'source_type' => $sourceType,
            'container_title' => $this->optional($ref['container_title'] ?? null),
            'volume' => $this->optional($ref['volume'] ?? null),
            'issue' => $this->optional($ref['issue'] ?? null),
            'pages' => $this->optional($ref['pages'] ?? null),
            'publisher' => $this->optional($ref['publisher'] ?? null),
            'url' => $this->optional($ref['url'] ?? null),
            'doi' => $this->optional($ref['doi'] ?? null),
            'in_text_citation' => $this->optional($ref['in_text_citation'] ?? null),
        ];
    }

    public function withApaText(?array $references): array
    {
        $result = [];

        foreach ((array) $references as $ref) {
            if (!is_array($ref) || !($normalized = $this->normalize($ref))) {
                continue;
            }

            $normalized['apa_text'] = $this->formatPlain($normalized);
            $result[] = $normalized;
        }

        return $result;
    }

    public function mergeWithSources(array $references, array $sources): array
    {
        if (empty($sources)) {
            return $references;
        }

        $key = fn (?string $value) => preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower((string) $value));

        foreach ($references as $index => $reference) {
            foreach ($sources as $source) {
                $sameLink = ($source['doi'] && $key($source['doi']) === $key($reference['doi'] ?? null))
                    || ($source['url'] && $key($source['url']) === $key($reference['url'] ?? null));
                $sameTitle = $key($source['title']) !== '' && $key($source['title']) === $key($reference['title'] ?? null);

                if ($sameLink || $sameTitle) {
                    $merged = [...$source, 'in_text_citation' => $reference['in_text_citation'] ?? $source['in_text_citation']];
                    unset($merged['excerpt']);
                    $merged['apa_text'] = $this->formatPlain($merged);
                    $references[$index] = $merged;
                    break;
                }
            }
        }

        return $references;
    }

    public function saveForAnswer(int $conversationId, int $answerId, array $references, ?string $answerText): void
    {
        DB::table('answer_reference_rel')->where('answer_id', $answerId)->delete();

        $order = 1;

        foreach ($references as $ref) {
            if (!is_array($ref) || !($ref = $this->normalize($ref))) {
                continue;
            }

            if (!$this->isCited($ref, (string) $answerText)) {
                continue;
            }

            $hash = $this->hash($ref);

            $referenceId = DB::table('references_library')
                ->where('conversation_id', $conversationId)
                ->where('reference_hash', $hash)
                ->value('id');

            if (!$referenceId) {
                $referenceId = DB::table('references_library')->insertGetId([
                    'conversation_id' => $conversationId,
                    'authors' => json_encode($ref['authors'], JSON_UNESCAPED_UNICODE),
                    'publication_year' => $ref['year'],
                    'title' => $ref['title'],
                    'container_title' => $ref['container_title'],
                    'volume' => $ref['volume'],
                    'issue' => $ref['issue'],
                    'pages' => $ref['pages'],
                    'publisher' => $ref['publisher'],
                    'url' => $ref['url'],
                    'doi' => $ref['doi'],
                    'source_type' => $ref['source_type'],
                    'in_text_citation' => $ref['in_text_citation'],
                    'reference_hash' => $hash,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('answer_reference_rel')->insertOrIgnore([
                'answer_id' => $answerId,
                'reference_id' => $referenceId,
                'citation_order' => $order++,
                'created_at' => now(),
            ]);
        }

        $this->deleteOrphans($conversationId);
    }

    // Referencias que ya no cita ninguna respuesta
    public function deleteOrphans(int $conversationId): void
    {
        DB::table('references_library')
            ->where('conversation_id', $conversationId)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('answer_reference_rel')
                    ->whereColumn('answer_reference_rel.reference_id', 'references_library.id');
            })
            ->delete();
    }

    public function forConversation(int $conversationId): Collection
    {
        return DB::table('references_library')
            ->where('references_library.conversation_id', $conversationId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('answer_reference_rel')
                    ->join('user_answers', 'user_answers.id', '=', 'answer_reference_rel.answer_id')
                    ->whereColumn('answer_reference_rel.reference_id', 'references_library.id');
            })
            ->get()
            ->map(fn ($row) => $this->fromRow($row))
            ->sortBy(fn ($ref) => $this->sortKey($this->formatPlain($ref)), SORT_NATURAL)
            ->values();
    }

    // Modificar respuesta: se quitan las referencias que ya no se citan en el texto modificado
    public function pruneUncited(int $conversationId, int $answerId, string $answerText): void
    {
        $rows = DB::table('answer_reference_rel')
            ->join('references_library', 'references_library.id', '=', 'answer_reference_rel.reference_id')
            ->where('answer_reference_rel.answer_id', $answerId)
            ->get(['references_library.*', 'answer_reference_rel.reference_id']);

        $uncited = $rows
            ->filter(fn ($row) => !$this->isCited($this->fromRow($row), $answerText))
            ->pluck('reference_id')
            ->all();

        if ($uncited) {
            DB::table('answer_reference_rel')
                ->where('answer_id', $answerId)
                ->whereIn('reference_id', $uncited)
                ->delete();

            $this->deleteOrphans($conversationId);
        }
    }

    private function fromRow(object $row): array
    {
        return [
            'authors' => json_decode($row->authors, true) ?: [],
            'year' => $row->publication_year ?: 's.f.',
            'title' => $row->title,
            'source_type' => $row->source_type,
            'container_title' => $row->container_title,
            'volume' => $row->volume,
            'issue' => $row->issue,
            'pages' => $row->pages,
            'publisher' => $row->publisher,
            'url' => $row->url,
            'doi' => $row->doi,
        ];
    }

    public function formatRuns(array $ref): array
    {
        $runs = [];
        $add = function (string $text, bool $italic = false, ?string $url = null) use (&$runs) {
            if ($text !== '') {
                $runs[] = ['text' => $text, 'italic' => $italic, 'url' => $url];
            }
        };

        $authors = $this->formatAuthors($ref['authors'] ?? []);
        $year = $ref['year'] ?: 's.f.';
        $title = $this->withoutFinalDot($ref['title']);
        $type = $ref['source_type'] ?? 'web_page';
        $container = $ref['container_title'] ?? null;
        $publisher = $ref['publisher'] ?? null;

        // Autor. (Año).
        $add($this->withFinalDot($authors) . ' (' . $year . '). ');

        if ($type === 'journal_article') {
            // Título del artículo. Revista, volumen(número), páginas.
            $add($title . '. ');

            if ($container) {
                $add($container, true);

                if (!empty($ref['volume'])) {
                    $add(', ');
                    $add($ref['volume'], true);
                }

                if (!empty($ref['issue'])) {
                    $add('(' . $ref['issue'] . ')');
                }

                if (!empty($ref['pages'])) {
                    $add(', ' . $ref['pages']);
                }

                $add('. ');
            }
        } else {
            $add($title, true);
            $add($type === 'thesis' ? ' [Tesis]. ' : '. ');

            $source = $type === 'web_page' ? ($container ?: $publisher) : $publisher;

            if ($source && !$this->sameAsAuthor($source, $ref['authors'] ?? [])) {
                $add($this->withFinalDot($source) . ' ');
            }
        }

        // DOI (preferido) o URL, sin punto final
        // Solo es enlace si es una dirección web (http o https); otra cosa queda como texto
        $link = $this->link($ref['doi'] ?? null, $ref['url'] ?? null);
        $add($link, false, preg_match('#^https?://\S+$#i', $link) ? $link : null);

        // Quita el espacio final que pudo quedar
        if (!empty($runs)) {
            $last = count($runs) - 1;
            $runs[$last]['text'] = rtrim($runs[$last]['text']);
        }

        return $runs;
    }

    public function formatPlain(array $ref): string
    {
        return implode('', array_column($this->formatRuns($ref), 'text'));
    }

    public function isCited(array $ref, string $text): bool
    {
        $text = mb_strtolower($text);

        // "s.f." también puede aparecer como "s. f." o "sin fecha"
        if (!preg_match('/' . $this->yearPattern($ref['year'] ?? '') . '/iu', $text)) {
            return false;
        }

        // Apellido o institución del primer autor, y sus variantes ("Ministerio de Salud - MINSA" → "MINSA")
        $keys = $this->authorKeys($ref['authors'][0] ?? '');

        // Forma usada en la cita: "(INEI, 2022)" → "INEI"; "Pérez y Gómez (2021)" → "Pérez"
        if (!empty($ref['in_text_citation'])) {
            $citation = trim($ref['in_text_citation'], " ()[]");
            $keys[] = trim(preg_split('/,|\(| y | & | et al/u', $citation)[0] ?? '');

            // Abreviatura entre corchetes: "Instituto ... [INEI] (2022)"
            if (preg_match('/\[([^\]]+)\]/u', $ref['in_text_citation'], $match)) {
                $keys[] = $match[1];
            }
        }

        foreach (array_filter($keys) as $key) {
            if (mb_strlen($key) >= 2 && str_contains($text, mb_strtolower($key))) {
                return true;
            }
        }

        return false;
    }

    // Cita de una fuente en el texto (autor o sigla con el año, parentética o narrativa), o null
    public function citationIn(array $source, string $text): ?string
    {
        $year = $this->yearPattern($source['year'] ?? '');

        foreach ($this->authorKeys($source['authors'][0] ?? '') as $key) {
            $key = preg_quote($key, '/');

            if (preg_match("/\\([^()]*?{$key}[^()]*?(?:{$year})[^()]*?\\)/iu", $text, $match)
                || preg_match("/{$key}[^()]{0,60}?\\(\\s*(?:{$year})\\s*\\)/iu", $text, $match)) {
                return $match[0];
            }
        }

        return null;
    }

    // Agrega las fuentes del tesista citadas en el texto que la IA omitió en "references"
    public function addCitedSources(array $references, array $sources, string $text): array
    {
        foreach ($sources as $source) {
            if ($this->findSource($references, $source) !== null) {
                continue;
            }

            $citation = $this->citationIn($source, $text);

            if ($citation !== null) {
                $reference = [...$source, 'in_text_citation' => $citation];
                unset($reference['excerpt']);
                $reference['apa_text'] = $this->formatPlain($reference);
                $references[] = $reference;
            }
        }

        return $references;
    }

    // Fuentes del tesista que no se usaron, con su motivo
    public function unusedSources(array $references, array $sources, array $aiUnused): array
    {
        $key = fn (?string $value) => preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower((string) $value));
        $unused = [];

        foreach ($sources as $source) {
            if ($this->findSource($references, $source) !== null) {
                continue;
            }

            $reason = '';
            foreach ($aiUnused as $item) {
                $title = $key($item['title'] ?? '');
                if ($title !== '' && (str_contains($key($source['title']), $title) || str_contains($title, $key($source['title'])))) {
                    $reason = trim((string) ($item['reason'] ?? ''));
                    break;
                }
            }

            $unused[] = [
                'apa_text' => $this->formatPlain($source),
                'reason' => $reason !== '' ? $reason : 'No se usó porque no respalda lo que escribiste en tu respuesta.',
            ];
        }

        return $unused;
    }

    // Índice de la referencia que corresponde a la fuente (mismo enlace, DOI o título), o null
    private function findSource(array $references, array $source): ?int
    {
        $key = fn (?string $value) => preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtolower((string) $value));

        foreach ($references as $index => $reference) {
            $sameLink = (!empty($source['doi']) && $key($source['doi']) === $key($reference['doi'] ?? null))
                || (!empty($source['url']) && $key($source['url']) === $key($reference['url'] ?? null));
            $sameTitle = $key($source['title'] ?? '') !== '' && $key($source['title']) === $key($reference['title'] ?? null);

            if ($sameLink || $sameTitle) {
                return $index;
            }
        }

        return null;
    }

    // Expresión del año: "2014", o "s.f." en sus formas habituales
    private function yearPattern(string $year): string
    {
        $year = trim($year);

        if ($year === '' || preg_match('/^s\.?\s?f\.?$/iu', $year)) {
            return 's\.\s?f\.?|sin fecha|n\.\s?d\.';
        }

        return preg_quote($year, '/');
    }

    // Formas del primer autor en una cita: apellido, nombre de la institución o su sigla
    private function authorKeys(string $author): array
    {
        $author = trim($author);
        $keys = [trim(explode(',', $author)[0])];

        foreach (preg_split('/\s+[-–—]\s+|\(|\)|\[|\]/u', $author) as $part) {
            $keys[] = trim($part);
        }

        // Siglas en mayúsculas (MINSA, INEI, BCRP)
        if (preg_match_all('/\b\p{Lu}{3,}\b/u', $author, $acronyms)) {
            array_push($keys, ...$acronyms[0]);
        }

        return array_values(array_unique(array_filter($keys, fn ($key) => mb_strlen($key) >= 2)));
    }

    private function formatAuthors(array $authors): string
    {
        $authors = array_values(array_filter(array_map('trim', $authors)));
        $count = count($authors);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $authors[0];
        }

        if ($count === 2) {
            return $authors[0] . ' y ' . $authors[1];
        }

        if ($count > 20) {
            return implode(', ', array_slice($authors, 0, 19)) . ', . . . ' . $authors[$count - 1];
        }

        return implode(', ', array_slice($authors, 0, -1)) . ' y ' . $authors[$count - 1];
    }

    private function link(?string $doi, ?string $url): string
    {
        if ($doi) {
            $doi = preg_replace('#^(https?://(dx\.)?doi\.org/|doi:\s*)#i', '', trim($doi));
            return 'https://doi.org/' . $doi;
        }

        return $url ? trim($url) : '';
    }

    private function sameAsAuthor(string $source, array $authors): bool
    {
        $normalize = fn ($value) => mb_strtolower(trim($value, " ."));

        return in_array($normalize($source), array_map($normalize, $authors), true);
    }

    private function withFinalDot(string $text): string
    {
        $text = trim($text);

        return preg_match('/[.?!]$/u', $text) ? $text : $text . '.';
    }

    private function withoutFinalDot(string $text): string
    {
        return rtrim(trim($text), '.');
    }

    private function optional($value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : null;
    }

    private function hash(array $ref): string
    {
        $normalize = fn ($value) => preg_replace('/\s+/u', ' ', mb_strtolower(trim($value)));

        return hash('sha256', implode('|', [
            $normalize($ref['title']),
            $normalize($ref['year']),
            $normalize(implode(',', $ref['authors'])),
        ]));
    }

    // Orden alfabético sin distinguir tildes ni mayúsculas
    private function sortKey(string $text): string
    {
        $text = mb_strtolower($text);

        return strtr($text, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n~']);
    }
}
