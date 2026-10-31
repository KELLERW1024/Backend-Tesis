<?php 

namespace App\Services;

use App\Constants\Prompts;
use App\Models\ThesisContext;
use App\Models\Question;

class PromptService
{

    // PROMPT DE VALIDACION DE RESPUESTA
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
            - Es hasta un 80% consistente con las respuestas anteriores o si es una directiva válida para la generación de la respuesta.

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
            CRITERIOS DE EVALUACIÓN DE LA RESPUESTA DEL USUARIO:

Evalúa exclusivamente la calidad, pertinencia y suficiencia de la respuesta actual del usuario en relación con la pregunta formulada.

La puntuación debe representar qué tan adecuada es la respuesta ORIGINAL del usuario, no qué tan bien podría redactarse o completarse posteriormente.

ESCALA DE EVALUACIÓN:

90-100:
La respuesta es correcta, pertinente, suficientemente desarrollada y coherente con la pregunta. Contiene la información necesaria para cumplir el objetivo solicitado y puede integrarse en la tesis con ajustes mínimos de redacción, si fueran necesarios.

85-89
Directiva válida  para la  construcción de la respuesta

90-100:
Respuesta completa, académica, coherente y lista para integrarse en la tesis.

85-89:
Respuesta válida, por lo que debes completar la información faltante tomando la data hitorica.

75-84:
Respuesta parcialmente válida, por lo que debes completar la información faltante solo si tenemos la información disponible, recuerda que no debes inventar información.

0-74:
Respuesta inválida, no responde la pregunta o no tiene contenido suficiente.

REGLAS OBLIGATORIAS:

Evalúa la respuesta de manera contextual, sin exigir una estructura rígida ni palabras específicas.

Interpreta la intención del usuario, incluso cuando se exprese informalmente, con frases breves, errores de redacción o ideas todavía poco desarrolladas.

Considera que una respuesta breve puede ser válida si responde adecuadamente a lo solicitado.

Utiliza el historial para comprender el negocio, recuperar datos ya proporcionados, resolver referencias ambiguas y completar información pertinente cuando sea posible.

No penalices al usuario por omitir información que ya esté claramente disponible en el historial y que permita comprender su respuesta.

Distingue entre una respuesta que necesita desarrollo académico y otra que realmente no responde a la pregunta.

No exijas que el usuario proporcione todos los detalles en una sola respuesta si estos pueden obtenerse legítimamente del contexto disponible.

No asignes automáticamente 85 puntos. La puntuación debe reflejar la calidad de la respuesta actual, considerando también la información contextual pertinente.

Tampoco reduzcas excesivamente la puntuación por omisiones menores, falta de formalidad o ausencia de detalles que no sean esenciales.

Si la respuesta contiene una idea válida, pero necesita mejoras, reconoce su valor y asigna una puntuación proporcional.

Si faltan datos esenciales que no pueden recuperarse del historial, reduce la puntuación según su importancia.

No inventes información para justificar una puntuación elevada.

El feedback debe ser breve, constructivo y específico. Indica qué puede mejorarse sin descalificar innecesariamente al usuario.

FORMATO DE SALIDA:

is_valid debe ser true cuando score sea igual o mayor a 75, y false cuando sea inferior a 75.

La validez indica si la respuesta tiene suficiente pertinencia y contenido aprovechable para continuar el proceso de elaboración de la tesis. No significa necesariamente que esté terminada ni que pueda incorporarse sin modificaciones.

Devuelve únicamente un objeto JSON válido con esta estructura:

{
    \"is_valid\": false,
    \"score\": 0,
    \"feedback\": \"La respuesta no aborda los elementos esenciales solicitados.\"
}

El objeto anterior es exclusivamente un ejemplo de formato. No copies sus valores. Calcula is_valid, score y feedback de acuerdo con la respuesta real evaluada.

No incluyas explicaciones, texto adicional, Markdown ni bloques de código fuera del JSON.
            ";


        return implode("\n\n", array_filter($sections));
    }

    public function promptFiltroNodePorDiagnostico(array $diagnosticoRespuestas, array $nodesForAI)
    {
        $diagnosticoJson = json_encode($diagnosticoRespuestas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $nodesJson = json_encode($nodesForAI, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
Eres un experto metodológico y director de proyectos de tesis y planes de negocio.

Tu tarea es analizar las respuestas del DIAGNÓSTICO INICIAL proporcionadas por el tesista y seleccionar, EXCLUSIVAMENTE de la lista de PLAN NODES proporcionada, los nodos, capítulos y secciones que sean estrictamente necesarios y pertinentes para estructurar el proyecto de forma precisa y personalizada.

DIAGNÓSTICO INICIAL DEL USUARIO:
{$diagnosticoJson}

REGLAS OBLIGATORIAS:
1. Analiza a profundidad las 8 respuestas del diagnóstico para entender el enfoque, alcance, naturaleza y necesidades específicas del proyecto.
2. NO debes crear nuevos nodos.
3. NO debes modificar ningún título ni ID.
4. Solo puedes seleccionar IDs que aparezcan exactamente en la lista de PLAN NODES proporcionada.
5. REGLA CRÍTICA DE JERARQUÍA: Si seleccionas un subnodo o sección específica, DEBES incluir obligatoriamente el ID de su nodo padre y de su Capítulo principal correspondiente para no romper la estructura visual.
6. EXENCIÓN Y OBLIGATORIEDAD CRÍTICA: Debes incluir OBLIGATORIAMENTE en la selección los nodos correspondientes al "Resumen Ejecutivo", "Introducción", "Conclusiones" y "Recomendaciones" (o sus títulos equivalentes en la lista), ya que son apartados metodológicos obligatorios para todos los proyectos, independientemente del rubro.
7. Excluye únicamente aquellos contenidos que por la naturaleza del diagnóstico no apliquen al proyecto.
8. La propiedad "nodes" DEBE ser una lista plana de objetos que contengan únicamente la propiedad "id".
9. Mantén el orden original en el que los IDs aparecen en la lista de PLAN NODES.
10. No devuelvas explicaciones, comentarios ni Markdown. La respuesta DEBE ser exclusivamente un JSON válido.

FORMATO DE SALIDA OBLIGATORIO:
{
    "nodes": [
        {"id": 1},
        {"id": 2},
        {"id": 5}
    ]
}

LISTA DE PLAN NODES:
{$nodesJson}
PROMPT;
    }

    public function promptAssistantCoCreation(array $data): string
    {
        $question = $data['question'] ?? '';
        $detail = $data['detail'] ?? '';
        $objective = $data['objective'] ?? '';
        $resumenGeneral = $data['resumen_general'] ?? 'No hay resumen consolidado previo.';
        $assistantContext = json_encode($data['assistant_context'] ?? [], JSON_UNESCAPED_UNICODE);
        $chatMessages = json_encode($data['chat_messages'] ?? [], JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
            Eres un consultor experto y redactor académico de tesis y planes de negocio, especializado en modelamiento cuantitativo, costos y operaciones.
            Tu misión es entrevistar al usuario paso a paso para co-crear la respuesta de la pregunta actual de forma profesional, actuando como un "Resolutor de Brechas".

            INFORMACIÓN DE LA PREGUNTA ACTUAL:
            - Pregunta: {$question}
            - Detalle / Guía: {$detail}
            - Objetivo del nodo: {$objective}

            MEMORIA CRUDA DEL PROYECTO (Resumen General e Historial):
            {$resumenGeneral}

            DICCIONARIO DE DATOS YA CONOCIDOS (Inmutables):
            {$assistantContext}

            HISTORIAL DEL CHAT ACTUAL:
            {$chatMessages}

            // ... (código anterior de la función)

            INSTRUCCIONES:
            1. "chat_message": Responde de forma interactiva y directa. Si faltan datos numéricos para calcular algo de la pregunta actual, ofrécele al usuario calcularlo juntos o sugerir un valor.
            2. "brechas_detectadas": Enumera EXCLUSIVAMENTE los datos precisos y específicos que faltan para responder a la "PREGUNTA ACTUAL". 
            - REGLA DE ENFOQUE (CRÍTICA): NO incluyas brechas generales del negocio. Si la pregunta es sobre el "precio de sesión", la brecha debe ser solo sobre el precio. Si ya tienes todos los datos para responder a la pregunta, devuelve un arreglo vacío [].
            - REGLA DE LENGUAJE: El texto descriptivo de la brecha debe ser EXTREMADAMENTE CLARO. Escríbelo como una petición corta o una pregunta directa (Ejemplo: "¿Cuánto cobrarás por cada sesión?" o "Número estimado de atenciones al mes").
            - REGLA DE ESTABILIDAD: Si el usuario ya te dio el dato, NO lo vuelvas a pedir.
            - FORMATO OBLIGATORIO: Cada brecha debe ser un objeto JSON con dos propiedades: "dato" y "criticidad" ("Crítica", "Importante", "Recomendable", "Opcional").
            3. "variables_negocio": Aquí colocarás TODOS los resultados matemáticos y SUPUESTOS. Cada variable será un objeto JSON.
            - REGLA DE ESTADOS: Define el "estado" como "Calculado" o "Supuesto".
            - REGLA DE FÓRMULA INDIVIDUAL: Para CADA variable, agrega un campo llamado "formula". Si es "Calculado", escribe la operación exacta (ej. "18 usuarios * 26 días"). Si es "Supuesto", justifica brevemente. Si no hay cálculo, pon "".
            4. "draft_text": Es el borrador formal (Propuesta Académica) de la respuesta técnica.
            5. "progress_percentage": Estimación (0-100) de qué tan completa está la respuesta.
            6. "is_complete": true si la respuesta ya está lista para usarse.
            7. "updated_specific_data": ¡TAREA CRÍTICA DE EXTRACCIÓN DE DATOS CRUDOS INMUTABLES! Extrae de la memoria ÚNICAMENTE HECHOS REALES dados por el usuario. 
            - PROHIBICIÓN ABSOLUTA: NO guardes aquí estimaciones ni resultados de cálculos. Esos van en "variables_negocio".
            - REGLA DE LÍMITE: No generes más de 5 a 8 claves dinámicas. 
            - REGLA DE INMUTABILIDAD: Si un dato ya está en el "DICCIONARIO DE DATOS YA CONOCIDOS", no lo sobreescribas.

            RESPONDE ÚNICAMENTE CON ESTE FORMATO JSON:
            {
                "chat_message": "...",
                "draft_text": "...",
                "progress_percentage": 20,
                "is_complete": false,
                "brechas_detectadas": [
                    {
                        "dato": "¿Cuánto cobrarás por sesión?",
                        "criticidad": "Crítica"
                    }
                ],
                "variables_negocio": [
                    {"nombre": "capacidad_maxima", "valor": "468", "estado": "Calculado", "formula": "18 diarios * 26 días laborables"}
                ],
                "updated_specific_data": {}
            }
            PROMPT;
    }

   public function promptValidationRedundanceQuestion( string|array $history,  string $question, bool $isPhase2 = false  ): string {

    $sections = [];

$modoFase2 = "";
    if ($isPhase2) {
        $modoFase2 = <<<FASE2

INSTRUCCIÓN ESPECIAL PRIORITARIA - FASE 2 (SÍNTESIS FINAL):
Esta pregunta corresponde a una sección de cierre (Resumen ejecutivo, Introducción, Conclusiones o Recomendaciones).
Para esta pregunta NO aplican de forma restrictiva las reglas de "no asumir" o "no completar".
TU TAREA ACADÉMICA ES: Redactar, sintetizar, deducir y articular la respuesta utilizando como base todo el contenido consolidado en el RESUMEN GENERAL y los datos clave cuantitativos.
No esperes que el usuario haya contestado esta pregunta antes: debes redactarla tú como asesor experto a partir del avance de los capítulos previos.
Por lo tanto, prioriza generar la redacción académica y retornar "show": false con el contenido en el campo "resp". Únicamente retorna "show": true si el historial carece de los elementos indispensables para formular una respuesta coherente.
FASE2;
    }

    $sections[] = <<<PROMPT
Eres un asesor académico especializado en investigación, elaboración y sustentación de tesis universitarias.

Tu función es determinar si la pregunta actual necesita ser presentada nuevamente al usuario o si puede ser respondida utilizando exclusivamente la información que el usuario ya proporcionó anteriormente en esta conversación.
{$modoFase2}
No debes evaluar únicamente si la pregunta es similar o parecida a una pregunta anterior.

Debes determinar si la información histórica disponible contiene evidencia suficiente, específica y coherente para construir una respuesta académica válida a la pregunta actual.
PROMPT;

    $sections[] = <<<PROMPT
PREGUNTA ACTUAL:

{$question}
PROMPT;

    if (!empty($history)) {

        $historyContent = is_array($history)
            ? json_encode($history, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : $history;

        $sections[] = <<<PROMPT
HISTORIAL DE LA CONVERSACIÓN:

La siguiente información corresponde a respuestas, datos y contenido proporcionado anteriormente por el usuario durante esta conversación.

Utiliza este historial como fuente principal para determinar si la pregunta actual puede ser respondida.

No asumas que una información es verdadera si no está presente en el historial.
No inventes datos.
No agregues información externa.
No completes información faltante mediante suposiciones.

HISTORIAL:

{$historyContent}
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
        
            $promptEsp = strtr(
                Prompts::PROMPT_ESPECIFICO,
                [
                    '[Capítulo]' => $parentNode['titulo'] ?? '',
                    '[Descripcion Capítulo]' => $parentNode['titulo'] ?? '',
                    '[Pregunta]' => $data['question'],
                    '[Objetivo]' => $data['objective'] ?? '',
                    '[Validacion]' => $data['validation'],
                    '[Respuesta]' => $response,
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

    // PARA INDICAR QUE LA INFORMACION SEA CONVERTIDA EN TABLA
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
    
    // DIAGNOSTICO ESPECIFICO PARA VEFICAR EL RUBRO EY LOS NODOS DEL PLAN
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

    public function promptFiltroNode(string $rubro, array $nodesForAI)
{
    $nodesJson = json_encode(
        $nodesForAI,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    $prompt = <<<PROMPT

Eres un experto en estructuración de planes de negocio y proyectos de tesis.

Tu tarea es seleccionar, EXCLUSIVAMENTE de la lista de PLAN NODES proporcionada,
los nodos que sean necesarios y pertinentes para construir un plan de negocio
cuyo rubro principal es:

RUBRO: {$rubro}

OBJETIVO:

Analiza los títulos disponibles y selecciona únicamente los nodos que sean
pertinentes para el rubro indicado.

La jerarquía de los nodos debe ser considerada ÚNICAMENTE para tomar la
decisión de selección.

IMPORTANTE: la jerarquía NO debe representarse en la respuesta.

REGLAS OBLIGATORIAS:

1. NO debes crear nuevos nodos.

2. NO debes modificar ningún título.

3. NO debes cambiar ningún ID.

4. NO debes inventar IDs.

5. Solo puedes seleccionar IDs que aparezcan exactamente en la lista
   de PLAN NODES proporcionada.

6. Debes considerar la relación entre capítulos, títulos y subtítulos
   para determinar qué contenidos son pertinentes para el rubro.

7. Si un capítulo o sección es necesario para desarrollar correctamente
   el plan de negocio, debes incluir también los nodos dependientes que
   sean necesarios.

8. Los contenidos generales y fundamentales de un plan de negocio deben
   conservarse cuando sean aplicables al rubro.

9. Debes excluir únicamente los contenidos que claramente no correspondan
   al rubro indicado.

10. NO debes representar relaciones padre-hijo en la respuesta.

11. NO debes utilizar la propiedad "children".

12. NO debes anidar objetos dentro de otros objetos.

13. NO debes agrupar nodos por capítulos.

14. NO debes devolver una estructura jerárquica.

15. La propiedad "nodes" DEBE ser siempre una lista PLANA.

16. Cada elemento dentro de "nodes" DEBE contener únicamente la propiedad
    "id".

17. Todos los IDs seleccionados deben estar directamente dentro de
    "nodes", incluso cuando un nodo sea hijo o nieto de otro nodo.

18. Mantén el orden original en el que los IDs aparecen en la lista
    de PLAN NODES.

19. No devuelvas explicaciones, comentarios, texto adicional ni Markdown.

20. La respuesta DEBE ser exclusivamente un JSON válido.

FORMATO DE SALIDA OBLIGATORIO:

{
    "rubro": "{$rubro}",
    "nodes": [
        {
            "id": 1
        },
        {
            "id": 2
        },
        {
            "id": 5
        }
    ]
}

EJEMPLO DE FORMATO CORRECTO:

{
    "rubro": "{$rubro}",
    "nodes": [
        {"id": 1},
        {"id": 2},
        {"id": 4},
        {"id": 5},
        {"id": 6},
        {"id": 10},
        {"id": 11}
    ]
}

EJEMPLO DE FORMATO INCORRECTO:

{
    "rubro": "{$rubro}",
    "nodes": [
        {
            "id": 4,
            "children": [
                {"id": 5},
                {"id": 6}
            ]
        }
    ]
}

El formato anterior es INCORRECTO.

También es INCORRECTO devolver:

{
    "rubro": "{$rubro}",
    "nodes": [
        {
            "id": 4,
            "children": [
                {
                    "id": 5
                }
            ]
        }
    ]
}

La única estructura permitida es una lista PLANA de objetos con la propiedad
"id".

LISTA DE PLAN NODES:

{$nodesJson}

RECUERDA:

- Selecciona únicamente nodos existentes.
- Respeta los IDs originales.
- Usa la jerarquía solamente para decidir qué nodos seleccionar.
- NO devuelvas la jerarquía.
- NO utilices "children".
- NO anides nodos.
- Todos los IDs seleccionados deben estar directamente dentro de "nodes".
- Mantén el orden original.
- Devuelve exclusivamente JSON válido.

PROMPT;

    return $prompt;
}
    public function promptFiltroNode1( string $rubro,  array $nodesForAI ){
        $nodesJson = json_encode(
    $nodesForAI,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

$prompt = <<<PROMPT
Eres un experto en estructuración de planes de negocio y proyectos de tesis.

Tu tarea es seleccionar estrictamente, de una lista de capítulos, títulos y subtítulos
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
                    "description": "Pregunta relacionada con el nodo",
                    "detail": "Explicación clara de qué debe considerar el usuario para responder",
                    "example": "Ejemplo conciso de redacción de la respuesta"
                },
                {
                    "description": "Otra pregunta relacionada con el nodo",
                    "detail": "Explicación clara de qué debe considerar el usuario para responder",
                    "example": "Ejemplo conciso de redacción de la respuesta"
                }
            ]
        },
        {
            "node_id": 15,
            "questions": [
                {
                    "description": "Pregunta relacionada con el nodo",
                    "detail": "Explicación clara de qué debe considerar el usuario para responder",
                    "example": "Ejemplo conciso de redacción de la respuesta"
                }
            ]
        }
    ]
}

PROMPT;
}

// =======================================================
 // PROMPT PARA VEFICAR SI ES NECESARIO ACTUALIZAR EL CONTEXTO 
//  ==================================================
    public function promptUpdateThesisContext(
        ThesisContext $context,
        Question $question,
        string $answer
    ): string {
        \Log::info('=== INICIO promptUpdateThesisContext ===');

        \Log::info('Datos recibidos en promptUpdateThesisContext:', [
            'context_id' => $context->id ?? null,
            'conversation_id' => $context->conversation_id ?? null,
            'question_id' => $question->id ?? null,
            'question' => $question->question ?? null,
            'answer_length' => strlen($answer),
            'answer' => $answer,
        ]);

        return <<<PROMPT

Eres un sistema encargado de mantener el contexto
estructurado de un proyecto de plan de negocio.

Tu tarea NO es responder la pregunta.

Tu tarea es determinar si la respuesta del usuario
aporta información importante y estable sobre el
negocio que debería incorporarse al contexto.

CONTEXTO ACTUAL:

Nombre del negocio:
{$context->business_name}

Sector:
{$context->business_sector}

Producto o servicio:
{$context->product_service}

Cliente objetivo:
{$context->target_customer}

Problema principal:
{$context->main_problem}

Propuesta de valor:
{$context->value_proposition}


PREGUNTA DEL PLAN:

{$question->question_text}


RESPUESTA DEL USUARIO:

{$answer}


Analiza la respuesta.

Si la respuesta aporta información relevante
sobre el negocio, devuelve los campos que deberían
actualizarse.

Si la respuesta NO aporta información importante
para el contexto general del negocio, no actualices.

IMPORTANTE:

- No inventes información.
- No modifiques información sin evidencia.
- No copies información innecesariamente.
- No incluyas información específica que solamente
  sirve para responder esta pregunta.
- El contexto debe representar el negocio completo.
- Si la respuesta contradice claramente el contexto
  actual, indícalo.
- Devuelve únicamente JSON.

Formato:

{
    "update_context": true,
    "context_conflict": false,
    "conflict_reason": null,
    "updates": {
        "business_name": null,
        "business_sector": null,
        "product_service": null,
        "target_customer": null,
        "main_problem": null,
        "value_proposition": null
    }
}

PROMPT;
    }

    
}