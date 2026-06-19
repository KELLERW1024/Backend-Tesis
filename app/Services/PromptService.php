<?php 

namespace App\Services;

use App\Constants\Prompts;

class PromptService
{

    public function buildValidationPrompt( array $data ): string
    {

        //     \Log::info('BUILD VALIDATION INPUT DATA', [
        //     'pregunta' => $data['pregunta'] ?? null,
        //     'detail' => $data['detail'] ?? null,
        //     'evidence' => $data['evidence'] ?? null,
        //     'respuesta' => $data['respuesta'] ?? null,
        //     'file_content_length' => strlen($data['file_content'] ?? ''),
        //     'file_content_sample' => substr($data['file_content'] ?? '', 0, ),
        //     'image_content_sample' => substr($data['image_content'] ?? '', 0, ),
        // ]);
            $sections = [];

            $sections[] = "Evalúa si la respuesta del usuario responde correctamente la pregunta tomando en cuenta el detalle y la validacion. Y menciona que está faltando, 
            pero si la respuesta no responde la pregunta, debes verificar estrictamente que sea una paticion válida que ayude a resolver la pregunta
            y ya no debes realizar la validacion, lo que  debes hacer es tomar la respuesta como una directiva válida y devolver el json valido con is_valid igual a true.";

            $sections[] = "PREGUNTA:\n{$data['question']}";
            $sections[] = "DETALLE:\n{$data['detail']}";
            $sections[] = "VALIDACION:\n{$data['validation']}";
            // $sections[] = "APA:\n{$data['apa']}";

            $response = "RESPUESTA DEL USUARIO:\n{$data['response']}";

            if (!empty($data['image_content'])) {
                $response .= "\n\n{$data['image_content']}";
            }

            if (!empty($data['file_content'])) {
                $response .= "\n\n{$data['file_content']}";
            }

            $sections[] = $response;

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
    public function buildMessages( array $data, array $history , string   $documentContent,string   $imageContent, bool $isApa): array {

        $promptIni = str_replace(
            '[ESPECIALIDAD:]',
            $data['plan'],
            Prompts::PROMPT_INICIAL
        );

        $response = $data['response'];

        if (!empty($imageContent)) {  $response .= "\n\nIMAGEN:\n" . $imageContent;  }

        if (!empty($documentContent)) {  $response .= "\n\nDOCUMENTO:\n" . $documentContent;  }
        
        // if( !$isApa ){
            $promptEsp = strtr(
                Prompts::PROMPT_ESPECIFICO,
                [
                    '[Capítulo]' => $data['title'],
                    '[Descripcion Capítulo]' => $data['description'],
                    '[Pregunta]' => $data['question'],
                    '[Validacion]' => $data['validation'],
                    '[Respuesta]' => $data['response'],
                    '[Apa]' => $data['apa'],
                ]
            );
        // }else{
            // $promptEsp = strtr(
            //     Prompts::PROMPT_ESPECIFICO_APA,
            //     [
            //         '[Capítulo]' => $data['title'],
            //         '[Descripcion Capítulo]' => $data['description'],
            //         '[Pregunta]' => $data['question'],
            //         '[Validacion]' => $data['validation'],
            //         '[Respuesta]' => $data['response'],
            //     ]
            // );
        // }
        

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

    public function buildMessageTable(string $resp): string
    {
        return <<<PROMPT
        A partir de la siguiente respuesta:

        {$resp}

        Convierte la información en una tabla.

        Responde únicamente con JSON válido.

        Formato esperado:

        {
            "title": "string",
            "columns": ["columna1", "columna2"],
            "rows": [
            ["valor1", "valor2"],
            ["valor1", "valor2"]
            ]
        }

        Reglas:
        - No agregues explicaciones.
        - No uses markdown.
        - No uses bloques de código.
        - La respuesta debe ser únicamente JSON válido.
        - Detecta automáticamente las columnas y filas.
        - Si la información no puede representarse como una tabla o no contiene datos tabulares, responde únicamente:

        null
        PROMPT;
    }
}