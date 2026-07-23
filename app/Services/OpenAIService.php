<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private string $url = 'https://api.openai.com/v1/responses';

    public function chat(array|string $input): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post($this->url, [
                'model' => 'gpt-4.1-mini',
                'input' => $input,
                'text' => [
                    'format' => [
                        'type' => 'json_object'
                    ]
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception(
                $response->body()
            );
        }

        return $response->json()['output'][0]['content'][0]['text']
            ?? throw new \Exception('Sin respuesta');
    }

    public function json(string $prompt): array
    {
        $response = Http::withToken(config('services.openai.key'))
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

        $content = $response->json()['output'][0]['content'][0]['text']
            ?? '{}';

        return json_decode($content, true);
    }

    // public function imageJson(string $prompt, array $imageUrls): array
    // {
    //     $images = [];

    //     foreach ($imageUrls as $url) {
    //         $images[] = [
    //             'type' => 'input_image',
    //             'image_url' => $url,
    //         ];
    //     }

    //     $response = Http::withToken(config('services.openai.key'))
    //         ->post($this->url, [
    //             'model' => 'gpt-4.1-mini',

    //             'input' => [
    //                 [
    //                     'role' => 'user',
    //                     'content' => array_merge(
    //                         [
    //                             [
    //                                 'type' => 'input_text',
    //                                 'text' => $prompt,
    //                             ]
    //                         ],
    //                         $images
    //                     ),
    //                 ],
    //             ],

    //             'text' => [
    //                 'format' => [
    //                     'type' => 'json_object'
    //                 ]
    //             ]
    //         ]);

    //     if ($response->failed()) {
    //         throw new \Exception($response->body());
    //     }

    //     $content = $response->json()['output'][0]['content'][0]['text']
    //         ?? '{}';

    //     return json_decode($content, true);
    // }

    // public function imageJsonGPT4(string $prompt, string $imageUrl): array
    // {
    //     $response = Http::withToken(config('services.openai.key'))
    //         ->post('https://api.openai.com/v1/responses', [
    //             'model' => 'gpt-4o',

    //             'input' => [
    //                 [
    //                     'role' => 'user',
    //                     'content' => [
    //                         [
    //                             'type' => 'input_text',
    //                             'text' => $prompt
    //                         ],
    //                         [
    //                             'type' => 'input_image',
    //                             'image_url' => $imageUrl
    //                         ]
    //                     ]
    //                 ]
    //             ],

    //             'text' => [
    //                 'format' => [
    //                     'type' => 'json_object'
    //                 ]
    //             ]
    //         ]);

    //     if ($response->failed()) {
    //         throw new \Exception($response->body());
    //     }

    //     $content = $response->json()['output'][0]['content'][0]['text']
    //         ?? '{}';

    //     return json_decode($content, true);
    // }

    // public function generateImages(string $prompt): array
    // {
    //     $response = Http::withToken(config('services.openai.key'))
    //         ->timeout(120)
    //         ->post('https://api.openai.com/v1/images/generations', [
    //             'model' => 'gpt-image-1',
    //             'prompt' => $prompt,
    //             'size' => '1024x1024',
    //             'quality' => 'low',
    //             // 'response_format' => 'url',
    //             'n' => 1
    //         ]);

    //     if ($response->failed()) {
    //         throw new \Exception($response->body());
    //     }

    //     $raw = $response->json();

    //     $images = collect($raw['data'] ?? [])
    //         ->map(function ($item) {
    //             return $item['url']
    //                 ?? (isset($item['b64_json']) ? 'data:image/png;base64,' . $item['b64_json'] : null);
    //         })
    //         ->filter()
    //         ->values()
    //         ->toArray();

    //     if (empty($images)) {
    //         throw new \Exception('No images returned from OpenAI');
    //     }

    //     return $images;
    // }

    // public function imageInputJsonOutput(string $prompt, string $image): array
    // {
    //     $response = Http::withToken(config('services.openai.key'))
    //         ->post($this->url, [
    //             'model' => 'gpt-4.1-mini',

    //             'input' => [
    //                 [
    //                     'role' => 'user',
    //                     'content' => [
    //                         [
    //                             'type' => 'input_text',
    //                             'text' => $prompt,
    //                         ],
    //                         [
    //                             'type' => 'input_image',
    //                             'image_url' => $image,
    //                         ]
    //                     ],
    //                 ],
    //             ],

    //             'text' => [
    //                 'format' => [
    //                     'type' => 'json_object'
    //                 ]
    //             ]
    //         ]);

    //     if ($response->failed()) {
    //         throw new \Exception($response->body());
    //     }

    //     $content = $response->json()['output'][0]['content'][0]['text'] ?? '{}';

    //     return json_decode($content, true);
    // }

    public function imageInputStringOutput(string $prompt, string $image): string
    {
        $response = Http::withToken(config('services.openai.key'))
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

        $content = $response->json()['output'][0]['content'][0]['text'] ?? '{}';

        return $content;
    }

    public function inputCadenaOutputString(string $content): string // Metodo que limpia lo obtenido en el archivo excel
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