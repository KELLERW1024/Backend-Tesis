<?php 

namespace App\Services;

use App\Constants\Prompts;

class PromptService
{

    public function buildValidationPrompt(array $data): string
    {

            \Log::info('BUILD VALIDATION INPUT DATA', [
            'pregunta' => $data['pregunta'] ?? null,
            'detail' => $data['detail'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'respuesta' => $data['respuesta'] ?? null,
            'file_content_length' => strlen($data['file_content'] ?? ''),
            'file_content_sample' => substr($data['file_content'] ?? '', 0, 300),
        ]);
        $sections = [];

        $sections[] = "Evalúa si la respuesta del usuario responde correctamente la pregunta tomando en cuenta el detalle y la evidencia. Y meciona que está faltando";

        $sections[] = "PREGUNTA:\n{$data['pregunta']}";
        $sections[] = "DETALLE:\n{$data['detail']}";
        $sections[] = "EVIDENCIA:\n{$data['evidence']}";

        if (!empty($data['file_content'])) {
             $sections[] = "RESPUESTA DEL USUARIO:\n{$data['respuesta']}\n\n{$data['file_content']}";
        }

        else if (!empty($data['image_analysis'])) {
            $sections[] = "RESPUESTA DEL USUARIO:\n{$data['respuesta']}\n\n{$data['image_analysis']}\n";
        }else {
             $sections[] = "RESPUESTA DEL USUARIO:\n{$data['respuesta']}";
        }

        
        // $sections[] = "RESPUESTA DEL USUARIO:\n{$data['respuesta']}";

        $sections[] = "
                REGLAS:
                - Evalúa solo la respuesta textual
                - No inventes información externa
                - Sé estricto con coherencia con el material dado
                - Devuelve true en is_valid en el json a partir de un score de 75 y el feedback tiene que ser corto y preciso.

                Devuelve únicamente JSON válido:
                {
                \"is_valid\": true,
                \"score\": 85,
                \"feedback\": \"...\"
                }
                ";

        return implode("\n\n", array_filter($sections));
    }
    public function buildMessages( array $data, array $history ): array {

        $promptIni = str_replace(
            '[ESPECIALIDAD:]',
            $data['plan'],
            Prompts::PROMPT_INICIAL
        );

        $promptEsp = strtr(
            Prompts::PROMPT_ESPECIFICO,
            [
                '[Capítulo]' => $data['title'],
                '[Descripcion Capítulo]' => $data['description'],
                '[Pregunta]' => $data['question'],
                '[Respuesta]' => $data['response'],
            ]
        );

        $messages = collect($history)
            ->take(-10)
            ->map(fn($msg) => [
                'role' => $msg['role'],
                'content' => $msg['content']
            ])
            ->values()
            ->toArray();

        array_unshift($messages, [
            'role' => 'system',
            'content' => $promptIni
        ]);

        $messages[] = [
            'role' => 'user',
            'content' => $promptEsp
        ];

        return $messages;
    }
}