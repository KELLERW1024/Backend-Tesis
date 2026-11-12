<?php 
namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private string $url = 'https://api.openai.com/v1/responses';

    public function __construct(
        protected TokenUsageService $tokenUsageService,
    ) {}

    // Cliente para llamar a OpenAI. Si la conexión falla (corte breve de internet o de DNS),
    // reintenta 3 veces esperando 1, 2 y 4 segundos. Cada intento espera hasta 90 segundos.
    private function client(): PendingRequest
    {
        return Http::withToken(config('services.openai.key'))
            ->timeout(90)
            ->retry([1000, 2000, 4000], 0, fn ($exception) => $exception instanceof ConnectionException);
    }

    public function chat(array|string $input, array $options = []): string
    {
        $response = $this->client()
            ->post($this->url, [
                'model' => 'gpt-4.1-mini',
                'input' => $input,
                'text' => [
                    'format' => [
                        'type' => 'json_object'
                    ]
                ],
                ...$options,
            ]);

        if ($response->failed()) {
            throw new \Exception(
                $response->body()
            );
        }

        // Tokens consumidos por el usuario (user_token_quota)
        $this->tokenUsageService->record($response->json('usage'));

        return $response->json()['output'][0]['content'][0]['text']
            ?? throw new \Exception('Sin respuesta');
    }

    public function json(string $prompt): array
    {
        $response = $this->client()
            ->post($this->url, [
                'model' => 'gpt-4.1-mini',
                'input' => $prompt,
                'text' => [
                    'format' => [
                        'type' => 'json_object'
                    ]
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception($response->body());
        }

        // Tokens consumidos por el usuario (user_token_quota)
        $this->tokenUsageService->record($response->json('usage'));

        $content = $response->json()['output'][0]['content'][0]['text']
            ?? '{}';

        return json_decode($content, true);
    }

    public function imageInputStringOutput(string $prompt, string $image): string
    {
        $response = $this->client()
            ->post($this->url, [
                'model' => 'gpt-4.1-mini',

                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => $image,
                            ]
                        ],
                    ],
                ],

                'text' => [
                    'format' => [
                        'type' => 'text'
                    ]
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception($response->body());
        }

        // Tokens consumidos por el usuario (user_token_quota)
        $this->tokenUsageService->record($response->json('usage'));

        $content = $response->json()['output'][0]['content'][0]['text'] ?? '{}';

        return $content;
    }

    // Metodo que limpia lo obtenido en el archivo excel
    public function inputCadenaOutputString(string $content): string
    {
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return trim($content);
        }

        $texts = [];

        $extract = function ($item) use (&$extract, &$texts) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $extract($value);
                }
            } elseif (is_string($item) && trim($item) !== '') {
                $texts[] = trim($item);
            }
        };

        $extract($data);

        return implode("\n\n", $texts);
    }
}