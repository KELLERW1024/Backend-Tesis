<?php 

namespace App\Services;

use App\Constants\Prompts;

class PromptService
{

    public function buildValidationPrompt(array $data): string
    {
        $sections = [];

        $sections[] = "
            Eres un asesor académico especializado en elaboración y sustentación de tesis universitarias.

            Tu función es evaluar la respuesta proporcionada por el tesista y determinar si puede formar parte del documento de tesis.

            La evaluación debe considerar:
            - La pregunta actual.
            - El objetivo del capítulo o sección.
            - Los criterios de validación definidos.
            - La información obtenida previamente en otras preguntas.

            Debes analizar si la respuesta:
            - Responde directamente a la pregunta planteada.
            - Mantiene coherencia con el objetivo académico del capítulo.
            - Es consistente con las respuestas anteriores.
            - Mantiene un nivel académico adecuado para una tesis de sustentación.
            - Presenta ideas claras, estructuradas y comprensibles.
            - Tiene suficiente profundidad para formar parte del documento final.
            - Evita respuestas generales, ambiguas o sin sustento.

            Si la respuesta es incompleta, indica exactamente qué elemento académico falta.
            Si la respuesta contradice información previa, indica la inconsistencia.
            Si la respuesta no responde la pregunta, explica brevemente el motivo.
            ";


        $sections[] = "PREGUNTA DE TESIS: {$data['question']}";
        $sections[] = "OBJETIVO DEL CAPÍTULO / SECCIÓN: {$data['objective']}";
        $sections[] = "CRITERIOS DE VALIDACIÓN: {$data['validation']}";

        if (!empty($data['history'])) {
            $sections[] = "
            CONTEXTO E HISTORIAL DE RESPUESTAS ANTERIORES:

            Utiliza esta información únicamente para verificar coherencia y continuidad de la tesis.
            No copies contenido del historial ni lo consideres válido automáticamente.

            {$data['history']}
            ";
        }

        $responseParts = [];

        // Respuesta escrita del tesista
        if (!empty($data['response'])) {
            $responseParts[] = "RESPUESTA TEXTUAL DEL TESISTA:\n{$data['response']}";
        }

        // Imagen convertida a base64
        if (!empty($data['image_content'])) {
            $responseParts[] = "IMAGEN ADJUNTA:\n{$data['image_content']}";
        }

        // Contenido extraído de documento
        if (!empty($data['file_content'])) {
            $responseParts[] = "DOCUMENTACIÓN ADJUNTA:\n{$data['file_content']}";
        }

        $response = "INFORMACIÓN PROPORCIONADA POR EL TESISTA:\n\n" .
            implode("\n\n", $responseParts);

        $sections[] = $response;


        $sections[] = "
            CRITERIOS DE EVALUACIÓN:

            Evalúa con la siguiente escala:

            90-100:
            Respuesta completa, académica, coherente y lista para integrarse en la tesis.

            75-89:
            Respuesta válida, pero requiere mejoras menores de profundidad, precisión o redacción.

            50-74:
            Respuesta parcialmente válida, pero necesita complementar información importante.

            0-49:
            Respuesta inválida, no responde la pregunta o no tiene contenido suficiente.


            REGLAS IMPORTANTES:

            - Evalúa únicamente la información proporcionada.
            - No inventes datos, referencias, autores, estadísticas ni fuentes.
            - No agregues información externa.
            - No modifiques el significado de la respuesta del tesista.
            - Usa el historial solamente como contexto de coherencia.
            - Si falta información, indica exactamente qué debe agregar.
            - El feedback debe ser corto, específico y orientado a mejorar la tesis.
            - is_valid debe ser true únicamente cuando score sea igual o mayor a 75.


            Devuelve únicamente JSON válido:

            {
                \"is_valid\": true,
                \"score\": 85,
                \"feedback\": \"La respuesta es válida, pero debe profundizar en...\"
            }
            ";


        return implode("\n\n", array_filter($sections));
    }
    public function buildMessagesBitacoraResponseCurrent( array $data, array $history , string   $documentContent,string   $imageContent, bool $isApa): array {

        $promptIni = str_replace(
            '[ESPECIALIDAD:]',
            $data['plan'],
            Prompts::PROMPT_INICIAL
        );

        $studentInput = [];

        if (!empty($data['response'])) {
            $studentInput[] = "RESPUESTA TEXTUAL DEL TESISTA:\n" . $data['response'];
        }

        if (!empty($imageContent)) {
            $studentInput[] = "EVIDENCIA VISUAL:\n" . $imageContent;
        }

        if (!empty($documentContent)) {
            $studentInput[] = "DOCUMENTACIÓN:\n" . $documentContent;
        }

        $response = implode("\n\n", $studentInput);
        
        // if( !$isApa ){
            $promptEsp = strtr(
                Prompts::PROMPT_ESPECIFICO,
                [
                    '[Capítulo]' => $data['title'],
                    '[Descripcion Capítulo]' => $data['description'],
                    '[Pregunta]' => $data['question'],
                    '[Objetivo]' => $data['objective'] ?? '',
                    '[Validacion]' => $data['validation'],
                    '[Respuesta]' => $response,
                    '[Apa]' => $data['apa'],
                ]
            );

        $messages = collect($history)
            ->take(-15)
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