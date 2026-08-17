<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReplicateService
{

    protected string $url = 'https://api.replicate.com/v1/models/black-forest-labs/flux-schnell/predictions';
    public function generateImage(string $prompt)
    {
        $response = Http::timeout(30)
            ->connectTimeout(10)
            ->withToken(config('services.replicate.token'))
            ->post($this->url, [
                'input' => [
                    'prompt' => $prompt,
                    'go_fast' => true,
                    'num_outputs' => 1,
                    'aspect_ratio' => '1:1',
                    'output_format' => 'png',
                    'output_quality' => 80,
                ]
            ]);

        return $response->json();
    }


    public function getPrediction(string $id)
    {
        $url = 'https://api.replicate.com/v1/predictions/' . $id;
        $response = Http::timeout(30)
            ->connectTimeout(10)
            ->withToken(config('services.replicate.token'))
            ->get($url);

        \Log::info('Respuesta Replicate prediction', [
            'id' => $id,
            'status_http' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->json();
    }

}