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

            Tu función es evaluar la respuesta proporcionada por el tesista o validar que la respuesta sea una directica válida de interacción con la data que 
            tenemos en el historial de respuetas.  

            La evaluación debe considerar:
            - La pregunta actual.
            - Los criterios de validación definidos.
            - La información obtenida previamente en otras preguntas.

            Debes analizar si la respuesta:
            - Es hasta un 60% consistente con las respuestas anteriores o si es una directiva válida para la generación de la arespuesta.

            Si la respuesta es incompleta, complétalo siguiendo la data de las respuestas anteriores.
            Si la respuesta contradice información previa, indica la inconsistencia esto es crítico.
            Si la respuesta no responde la pregunta y no es una directica válida para la generación de la respuesta a esta pregunta, explica brevemente el motivo.
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

            85
            Directiva válida  para la  construcción de la respuesta

            90-100:
            Respuesta completa, académica, coherente y lista para integrarse en la tesis.

            75-89:
            Respuesta válida, por lo que debes completar la información faltante tomando la data hitorica.

            50-74:
            Respuesta parcialmente válida, por lo que debes completar la información faltante.

            0-49:
            Respuesta inválida, no responde la pregunta o no tiene contenido suficiente.


            REGLAS IMPORTANTES:

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

   public function promptValidationRedundanceQuestion( array $history,  string $question  ): string {

    $sections = [];

    $sections[] = <<<PROMPT
Eres un asesor académico especializado en investigación, elaboración y sustentación de tesis universitarias.

Tu función es determinar si la pregunta actual necesita ser presentada nuevamente al usuario o si puede ser respondida utilizando exclusivamente la información que el usuario ya proporcionó anteriormente en esta conversación.

No debes evaluar únicamente si la pregunta es similar o parecida a una pregunta anterior.

Debes determinar si la información histórica disponible contiene evidencia suficiente, específica y coherente para construir una respuesta académica válida a la pregunta actual.
PROMPT;

    $sections[] = <<<PROMPT
PREGUNTA ACTUAL:

{$question}
PROMPT;

    if (!empty($history)) {

        $historyJson = json_encode(
            $history,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        $sections[] = <<<PROMPT
HISTORIAL DE LA CONVERSACIÓN:

La siguiente información corresponde a respuestas, datos y contenido proporcionado anteriormente por el usuario durante esta conversación.

Utiliza este historial como fuente principal para determinar si la pregunta actual puede ser respondida.

No asumas que una información es verdadera si no está presente en el historial.
No inventes datos.
No agregues información externa.
No completes información faltante mediante suposiciones.

HISTORIAL:

{$historyJson}
PROMPT;
    } else {

        $sections[] = <<<PROMPT
HISTORIAL DE LA CONVERSACIÓN:

No existe información histórica disponible.

Por lo tanto, no debes intentar responder la pregunta utilizando conocimientos externos o suposiciones.
PROMPT;
    }

    $sections[] = <<<PROMPT
REGLAS DE EVALUACIÓN:

1. Debes determinar si la pregunta actual puede responderse de manera suficiente utilizando EXCLUSIVAMENTE la información contenida en el historial.

2. Si el historial contiene información suficiente, específica y coherente para responder la pregunta:
   - Genera la respuesta utilizando únicamente dicha información.
   - No solicites nuevamente al usuario información que ya fue proporcionada.
   - Retorna "show": false.
   - Genera la respuesta en lenguaje académico y formal.
   - La respuesta debe tener un desarrollo suficiente para responder completamente la pregunta.

3. Si el historial NO contiene información suficiente para responder la pregunta:
   - No inventes información.
   - No hagas suposiciones.
   - No utilices conocimientos externos.
   - No generes una respuesta especulativa.
   - Retorna "show": true.
   - "resp" debe ser una cadena vacía.

4. Si el historial contiene información parcialmente relacionada, pero esta no es suficiente para responder correctamente la pregunta:
   - Considera que la información NO es suficiente.
   - Retorna "show": true.
   - "resp" debe ser una cadena vacía.

5. Una pregunta NO debe considerarse redundante únicamente porque trate sobre el mismo tema que una respuesta anterior.

   La pregunta solamente puede omitirse cuando la información histórica disponible permita construir una respuesta suficientemente completa y sustentada.

6. Si existen varias respuestas históricas relacionadas con la pregunta:
   - Integra la información relevante.
   - Evita repetir literalmente las respuestas anteriores.
   - Construye una respuesta coherente y académicamente estructurada.

7. La respuesta generada debe responder directamente la pregunta, pero NO debe limitarse innecesariamente a una sola oración cuando exista suficiente información histórica para desarrollar la idea.

8. La extensión de la respuesta debe ser proporcional a la información disponible:

   - Si existe poca información, genera una respuesta breve pero completa.
   - Si existe suficiente información, desarrolla la respuesta en un párrafo académico bien estructurado.
   - Si existe abundante información relevante, integra los elementos necesarios sin generar contenido repetitivo.

9. Cuando la información histórica permita identificar relaciones entre elementos, puedes explicarlas.

   Por ejemplo:
   - problema y sus características;
   - causas y efectos;
   - situación actual y consecuencias;
   - características y dificultades;
   - procesos y problemas asociados.

   Sin embargo, estas relaciones deben estar sustentadas por la información histórica.

10. No agregues causas, consecuencias, características, cifras, porcentajes, fechas, nombres, resultados, interpretaciones o cualquier otro dato que no pueda sustentarse directamente con la información histórica.

11. Puedes reformular, sintetizar, organizar e integrar la información histórica para producir una redacción académica mejor estructurada.

12. No debes copiar textualmente el historial salvo que sea estrictamente necesario.

13. No utilices:
   - conocimientos generales;
   - conocimiento académico externo;
   - información de internet;
   - información obtenida de otras fuentes;
   - suposiciones;
   - datos inventados.

   Solo utiliza la información proporcionada en el historial.

14. La respuesta debe mantener coherencia con la información proporcionada anteriormente y no debe contradecir respuestas previas.

15. La respuesta debe estar redactada en lenguaje académico, formal y natural, apropiado para una tesis universitaria.

16. Evita respuestas excesivamente genéricas.

17. No menciones expresiones como:
   - "según el historial";
   - "según la información proporcionada";
   - "el usuario indicó";
   - "basándome en la conversación".

   La respuesta debe presentarse directamente como contenido académico.

18. Si la información histórica permite responder la pregunta, no vuelvas a solicitar esa información al usuario.

19. Si para responder correctamente sería necesario inventar, asumir o completar información que no está presente en el historial, debes retornar "show": true.

DECISIÓN:

"show": true
Significa que la pregunta DEBE mostrarse al usuario porque la información histórica NO es suficiente para responderla correctamente.

"show": false
Significa que la pregunta NO debe mostrarse al usuario porque la información histórica es suficiente para generar una respuesta válida.

RESPUESTA:

Si "show" es false:
- "resp" debe contener una respuesta académica desarrollada.
- La respuesta debe utilizar exclusivamente información sustentada en el historial.
- La extensión debe ser proporcional a la información disponible.

Si "show" es true:
- "resp" debe ser una cadena vacía.

Devuelve ÚNICAMENTE JSON válido.

No utilices Markdown.
No agregues explicaciones fuera del JSON.

FORMATO OBLIGATORIO:

{
    "show": true,
    "resp": ""
}

O:

{
    "show": false,
    "resp": "Respuesta académica desarrollada utilizando exclusivamente la información histórica disponible."
}
PROMPT;

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