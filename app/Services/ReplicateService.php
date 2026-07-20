<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReplicateService
{

    protected string $url = 'https://api.replicate.com/v1/models/black-forest-labs/flux-schnell/predictions';
    public function generateImage(string $prompt)
    {
        $response = Http::timeout(180)
                        ->connectTimeout(30)
                        ->withHeaders([
            'Authorization' => 'Bearer '.config('services.replicate.token'),
            'Content-Type' => 'application/json',
            'Prefer' => 'wait'
        ])->post($this->url, [

            'input' => [

                'prompt' => $prompt,

                'go_fast' => true,

                'num_outputs' => 1,

                'aspect_ratio' => '1:1',

                'output_format' => 'png',

                'output_quality' => 80

            ]

        ]);

        return $response->json();
    }
}