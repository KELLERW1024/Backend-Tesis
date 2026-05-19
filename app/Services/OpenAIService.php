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
                'input' => $input
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

    public function imageJson(string $prompt, array $imageUrls): array
    {
        $images = [];

        foreach ($imageUrls as $url) {
            $images[] = [
                'type' => 'input_image',
                'image_url' => $url,
            ];
        }

        $response = Http::withToken(config('services.openai.key'))
            ->post($this->url, [
                'model' => 'gpt-4.1-mini',

                'input' => [
                    [
                        'role' => 'user',
                        'content' => array_merge(
                            [
                                [
                                    'type' => 'input_text',
                                    'text' => $prompt,
                                ]
                            ],
                            $images
                        ),
                    ],
                ],

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

    public function imageJsonGPT4(string $prompt, string $imageUrl): array
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4o',

                'input' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'input_text',
                                'text' => $prompt
                            ],
                            [
                                'type' => 'input_image',
                                'image_url' => $imageUrl
                            ]
                        ]
                    ]
                ],

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

    public function generateImage(string $prompt): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/images/generations', [
                'model' => 'gpt-image-1',
                'prompt' => $prompt,
                'size' => '1024x1024',
                'response_format' => 'url'
            ]);

        if ($response->failed()) {
            throw new \Exception($response->body());
        }

        $url = $response->json()['data'][0]['url'] ?? null;

        if (!$url) {
            throw new \Exception('No image URL returned from OpenAI');
        }

        return $url;
    }

    public function imageInputJsonOutput(string $prompt, string $image): array
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
                        'type' => 'json_object'
                    ]
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception($response->body());
        }

        $content = $response->json()['output'][0]['content'][0]['text'] ?? '{}';

        return json_decode($content, true);
    }
}