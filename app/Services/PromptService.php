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

            Tu función es evaluar la respuesta proporcionada por el tesista y si está incompleta, completar con la información previa que tenemos de respuestas anteriores
             y hacer que forme parte del documento de tesis.

            La evaluación debe considerar:
            - La pregunta actual.
            - El objetivo del capítulo o sección.
            - Los criterios de validación definidos.
            - La información obtenida previamente en otras preguntas.

            Debes analizar si la respuesta:
            - Mantiene coherencia con el objetivo académico del capítulo.
            - Es consistente con las respuestas anteriores.
            - Presenta ideas claras, estructuradas y comprensibles.

            Si la respuesta es incompleta, completalo siguiendo la data de las respuestas anteriores.
            Si la respuesta contradice información previa, indica la inconsistencia.
            Si la respuesta no responde la pregunta, explica brevemente el motivo.
            ";


        $sections[] = "PREGUNTA DE TESIS: {$data['question']}";
        $sections[] = "OBJETIVO DEL CAPÍTULO / SECCIÓN: {$data['objective']}";
        $sections[] = "CRITERIOS DE VALIDACIÓN: {$data['validation']}";

        if (!empty($data['history'])) {
            $history = json_encode(  $data['history'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

            $sections[] = "
            CONTEXTO E HISTORIAL DE RESPUESTAS ANTERIORES:

            Utiliza esta información únicamente para verificar coherencia y continuidad de la tesis.
            No copies contenido del historial ni lo consideres válido automáticamente.

            {$history}
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
            Respuesta válida, por lo que debes completar la información faltante tomando la data hitorica.

            50-74:
            Respuesta parcialmente válida, por lo que debes completar la información faltante.

            0-49:
            Respuesta inválida, no responde la pregunta o no tiene contenido suficiente.


            REGLAS IMPORTANTES:

            - Evalúa únicamente la información proporcionada.
            - No inventes datos, referencias, autores, estadísticas ni fuentes.
            - No agregues información externa.
            - Usa el historial solamente como contexto de coherencia.
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

        $parentNode = $data['parent_node'] ?? [];
        $promptIni = str_replace(
            '[ESPECIALIDAD:]',
            $parentNode['titulo'] ?? '',
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
                    '[Capítulo]' => $parentNode['titulo'] ?? '',
                    '[Descripcion Capítulo]' => $parentNode['titulo'] ?? '',
                    '[Pregunta]' => $data['question'],
                    '[Objetivo]' => $data['objective'] ?? '',
                    '[Validacion]' => $data['validation'],
                    '[Respuesta]' => $response,
                    // '[Apa]' => $data['apa'],
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

    public function diagnosticRubroPlan( String $questionsAnswers ){
        $prompt = <<<PROMPT
Eres un experto en clasificación de modelos de negocio.

Debes analizar las preguntas y respuestas proporcionadas por una persona
y determinar cuál es el RUBRO PRINCIPAL de su plan de negocio.

Solo puedes seleccionar uno de estos rubros:

- produccion
- comercio
- servicio
- intermediacion
- mixto

Definiciones:

PRODUCCION:
El negocio transforma, fabrica, cultiva, procesa o elabora productos.
Ejemplos: fabricación de muebles, panadería, agricultura, elaboración de alimentos,
fabricación de ropa.

COMERCIO:
El negocio compra productos terminados para venderlos nuevamente.
No fabrica ni transforma significativamente el producto.
Ejemplos: tienda de ropa, minimarket, venta de electrodomésticos.

SERVICIO:
El negocio principalmente ofrece conocimientos, trabajo, atención o una actividad
a sus clientes, sin que la venta de productos sea la actividad principal.
Ejemplos: consultoría, peluquería, reparación, transporte, diseño gráfico.

INTERMEDIACION:
El negocio conecta compradores con vendedores o proveedores y obtiene ingresos
por comisión, margen o intermediación, sin que la actividad principal sea fabricar
el producto o prestar directamente el servicio.
Ejemplos: marketplace, corredor, agente comercial, plataforma que conecta clientes
con proveedores.

MIXTO:
El negocio combina de manera significativa dos o más de los rubros anteriores
y no existe uno claramente predominante.
Ejemplos: fabrica productos y además presta servicios relacionados; vende productos
y ofrece servicios como actividad importante.

REGLAS IMPORTANTES:

1. Analiza TODAS las preguntas y respuestas.
2. No te bases únicamente en una respuesta.
3. Identifica cuál es la actividad económica principal del negocio.
4. Si fabrica o transforma productos, normalmente corresponde a "produccion".
5. Si compra productos terminados y los revende, normalmente corresponde a "comercio".
6. Si vende principalmente trabajo, conocimiento o atención, corresponde a "servicio".
7. Si principalmente conecta compradores y vendedores, corresponde a "intermediacion".
8. Usa "mixto" solamente cuando exista una combinación significativa de actividades
   y no sea posible identificar una actividad principal claramente dominante.
9. No inventes información que no aparezca en las respuestas.
10. La respuesta debe contener únicamente información basada en el diagnóstico.

PREGUNTAS Y RESPUESTAS DEL DIAGNÓSTICO:

{$questionsAnswers}

Devuelve el resultado en formato JSON con esta estructura:

{
    "rubro": "produccion|comercio|servicio|intermediacion|mixto",
    
}
PROMPT;

    return $prompt;

    }

    public function promptFiltroNode( string $rubro,  array $nodesForAI ){
        $nodesJson = json_encode(
    $nodesForAI,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

$prompt = <<<PROMPT
Eres un experto en estructuración de planes de negocio y proyectos de tesis.

Tu tarea es seleccionar, de una lista de capítulos, títulos y subtítulos
existentes, únicamente los contenidos que son necesarios para construir
un plan de negocio cuyo rubro principal es:

RUBRO: {$rubro}

IMPORTANTE:

1. NO debes crear nuevos capítulos.
2. NO debes modificar los títulos.
3. NO debes cambiar los IDs.
4. NO debes inventar contenidos.
5. Solo puedes seleccionar elementos que aparezcan en la lista proporcionada.
6. Debes mantener la estructura jerárquica existente.
7. Debes seleccionar los capítulos y sus respectivos títulos/subtítulos
   que sean pertinentes para el rubro.
8. Los contenidos generales y fundamentales del plan de negocio deben
   conservarse cuando sean aplicables.
9. Debes excluir únicamente los contenidos que claramente no correspondan
   al rubro.
10. Si un capítulo es necesario, debes incluir también los subtítulos
    necesarios para desarrollar ese capítulo.
11. La respuesta debe contener únicamente los IDs seleccionados.
12. Mantén el orden original de los IDs.

LISTA DE PLAN NODES:

{$nodesJson}

Devuelve exclusivamente este JSON:

{
    "rubro": "{$rubro}",
    "nodes": [
        {
            "id": 1
        },
        {
            "id": 2
        }
    ]
}
PROMPT;

return $prompt;

    }
public function buildQuestionsForLeafNodesPrompt(
    array $plan,
    array $nodesWithoutQuestions,
    array $allNodes
): string {

    $planJson = json_encode(
        $plan,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    $nodesWithoutQuestionsJson = json_encode(
        $nodesWithoutQuestions,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    $allNodesJson = json_encode(
        $allNodes,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    return <<<PROMPT

Eres un asesor académico especializado en elaboración de tesis.

Debes generar preguntas académicas para los nodos finales que actualmente
no tienen preguntas asociadas.

Debes analizar el PLAN COMPLETO y la ESTRUCTURA COMPLETA para comprender
el contexto de cada nodo.

PLAN COMPLETO:

{$planJson}


NODOS QUE NECESITAN PREGUNTAS:

{$nodesWithoutQuestionsJson}


ESTRUCTURA COMPLETA DEL PLAN:

{$allNodesJson}


REGLAS:

1. Genera preguntas únicamente para los nodos indicados en
   "NODOS QUE NECESITAN PREGUNTAS".

2. No generes preguntas para otros nodos.

3. Analiza la posición jerárquica de cada nodo mediante:
   - parent_id
   - nivel
   - titulo
   - codigo
   - orden

4. Utiliza el contexto de los nodos padre y hermanos para comprender
   correctamente qué información debe obtenerse.

5. Las preguntas deben estar relacionadas directamente con el contenido
   académico del nodo.

6. Las preguntas deben permitir desarrollar contenido suficiente para una tesis.

7. Evita preguntas cuya respuesta sea únicamente "sí" o "no".

8. No repitas preguntas.

9. No inventes información específica del negocio.

10. No agregues capítulos ni nodos nuevos.

11. Genera entre 2 y 5 preguntas por nodo.

12. Cada pregunta debe ser clara, específica y académicamente útil.

13. No agregues explicaciones.

14. No uses Markdown.

15. Devuelve únicamente JSON válido.


FORMATO DE RESPUESTA:

{
    "nodes": [
        {
            "node_id": 10,
            "questions": [
                {
                    "description": "Pregunta relacionada con el nodo"
                },
                {
                    "description": "Otra pregunta relacionada con el nodo"
                }
            ]
        },
        {
            "node_id": 15,
            "questions": [
                {
                    "description": "Pregunta relacionada con el nodo"
                }
            ]
        }
    ]
}

PROMPT;
}


    
}