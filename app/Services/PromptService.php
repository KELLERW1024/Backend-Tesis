<?php 

namespace App\Services;

use App\Models\ThesisContext;
use App\Models\Question;
use App\Services\Prompts\PlanPrompts;

class PromptService
{

    // PROMPT DE VALIDACION DE RESPUESTA
    public function buildValidationPrompt(PlanPrompts $plan, array $data): string
    {
        $sections = [];

        $sections[] = "
            Eres un asesor académico especializado en elaboración y sustentación de tesis universitarias de {$plan->tipoTesis()}.

            {$plan->enfoque()}

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

Utiliza el historial para comprender el proyecto, recuperar datos ya proporcionados, resolver referencias ambiguas y completar información pertinente cuando sea posible.

No penalices al usuario por omitir información que ya esté claramente disponible en el historial y que permita comprender su respuesta.

Distingue entre una respuesta que necesita desarrollo académico y otra que realmente no responde a la pregunta.

No exijas que el usuario proporcione todos los detalles en una sola respuesta si estos pueden obtenerse legítimamente del contexto disponible.

No asignes automáticamente 85 puntos. La puntuación debe reflejar la calidad de la respuesta actual, considerando también la información contextual pertinente.

Tampoco reduzcas excesivamente la puntuación por omisiones menores, falta de formalidad o ausencia de detalles que no sean esenciales.

Si la respuesta contiene una idea válida, pero necesita mejoras, reconoce su valor y asigna una puntuación proporcional.

Si faltan datos esenciales que no pueden recuperarse del historial, reduce la puntuación según su importancia.

No inventes información para justificar una puntuación elevada.

El feedback debe ser breve, constructivo y específico. Indica qué puede mejorarse sin descalificar innecesariamente al usuario.

CRITERIOS PROPIOS DE UNA TESIS DE " . mb_strtoupper($plan->tipoTesis()) . ":

{$plan->validationCriteria()}

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


        $prompt = implode("\n\n", array_filter($sections));

        $plan->log('validacion_respuesta', $prompt);

        return $prompt;
    }

    public function promptFiltroNodePorDiagnostico(PlanPrompts $plan, array $diagnosticoRespuestas, array $nodesForAI)
    {
        $diagnosticoJson = json_encode($diagnosticoRespuestas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $nodesJson = json_encode($nodesForAI, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Nodos que la IA debe incluir siempre, identificados por su "codigo" en cada plan
        $nodosObligatorios = $plan->mandatoryNodes();

        $prompt = <<<PROMPT
Eres un experto metodológico y director de proyectos de tesis de {$plan->tipoTesis()}.

{$plan->enfoque()}

Tu tarea es analizar las respuestas del DIAGNÓSTICO INICIAL proporcionadas por el tesista y seleccionar,
EXCLUSIVAMENTE de la lista de PLAN NODES proporcionada, los nodos, capítulos y secciones que sean 
pertinentes para estructurar el proyecto de forma precisa y personalizada.

DIAGNÓSTICO INICIAL DEL USUARIO:
{$diagnosticoJson}

REGLAS OBLIGATORIAS:
1. Analiza a profundidad las 8 respuestas del diagnóstico para entender el enfoque, alcance, naturaleza y necesidades específicas del proyecto.
2. NO debes crear nuevos nodos.
3. NO debes modificar ningún título ni ID.
4. Solo puedes seleccionar IDs que aparezcan exactamente en la lista de PLAN NODES proporcionada.
5. REGLA CRÍTICA DE JERARQUÍA: Si seleccionas un subnodo o sección específica, DEBES incluir obligatoriamente el ID de su nodo padre y de su Capítulo principal correspondiente para no romper la estructura visual.
6. NODOS OBLIGATORIOS (REGLA CRÍTICA): Debes incluir SIEMPRE, sin excepción e independientemente del rubro, alcance o etapa indicados en el diagnóstico, los siguientes nodos, porque son apartados metodológicos exigidos en toda tesis de este tipo:
   a) Los nodos titulados "Resumen ejecutivo", "Introducción", "Conclusiones" y "Recomendaciones" (o sus títulos equivalentes en la lista).
{$nodosObligatorios}
7. NODOS OPCIONALES: Solo puedes excluir nodos que no estén comprendidos en la regla 6, y únicamente cuando por la naturaleza del diagnóstico claramente no apliquen al proyecto. Ante la duda, inclúyelo.
8. La propiedad "nodes" DEBE ser una lista plana de objetos que contengan únicamente la propiedad "id".
9. Mantén el orden original en el que los IDs aparecen en la lista de PLAN NODES.
10. No devuelvas explicaciones, comentarios ni Markdown. La respuesta DEBE ser exclusivamente un JSON válido.
11. Antes de responder, verifica que TODOS los nodos indicados en la regla 6 estén incluidos en tu lista "nodes".

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

        $plan->log('filtro_nodos_diagnostico', $prompt);

        return $prompt;
    }

    public function promptAssistantCoCreation(PlanPrompts $plan, array $data): string
    {
        $question = $data['question'] ?? '';
        $detail = $data['detail'] ?? '';
        $objective = $data['objective'] ?? '';
        $resumenGeneral = $data['resumen_general'] ?? 'No hay resumen consolidado previo.';
        $assistantContext = json_encode($data['assistant_context'] ?? [], JSON_UNESCAPED_UNICODE);
        $chatMessages = json_encode($data['chat_messages'] ?? [], JSON_UNESCAPED_UNICODE);

        $example = $plan->coCreationExample();
        $exampleBrecha = $example['brecha'];
        $exampleVariable = json_encode($example['variable'], JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
            Eres un {$plan->coCreationRole()}.
            Tu misión es entrevistar al usuario paso a paso para co-crear la respuesta de la pregunta actual de forma profesional, actuando como un "Resolutor de Brechas".

            {$plan->enfoque()}

            {$plan->coCreationGuide()}

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

            INSTRUCCIONES:
            1. "chat_message": Responde de forma interactiva y directa.
            - REGLA DE PREGUNTAS ABIERTAS (CRÍTICA): Pregunta sin sugerir la respuesta, porque los datos de la tesis deben venir del usuario. No incluyas opciones, ejemplos ni posibles respuestas dentro de la pregunta, y no busques un hecho del proyecto con una pregunta que se responda con "sí" o "no".
              Incorrecto: "¿Sufren por la falta de variedad o precios altos?"
              Correcto: "¿Qué problemas enfrentan hoy tus clientes al comprar este producto?"
            - REGLA DE HECHOS: Los hechos del proyecto (problemas, clientes, competidores, procesos, situación del negocio) solo pueden venir del usuario. Nunca los propongas, aunque diga que no los conoce: en ese caso, indícale cómo podría averiguarlos.
            - REGLA DE VALORES NUMÉRICOS: Si falta un valor numérico para calcular algo de la pregunta actual, primero pídeselo con una pregunta abierta, sin mencionar ninguna cifra. Solo si es una DECISIÓN DEL NEGOCIO (precio, margen, meta de ventas) y el usuario responde que no la conoce o te pide una propuesta, propón un valor en ese mismo mensaje usando los datos disponibles, aclarando que es un supuesto y registrándolo en "variables_negocio" con estado "Supuesto".
            - REGLA DE DATOS EXTERNOS (CRÍTICA): Los HECHOS EXTERNOS (población, tamaño de mercado, estadísticas, datos de instituciones o del sector) nunca se proponen ni se estiman. No tienes acceso a internet: si te piden investigar, buscar datos o dar un enlace, empieza tu mensaje con "No tengo acceso a internet, así que no puedo investigar ese dato." y regístralo como pendiente de investigación. No des cifras externas de memoria ni enlaces que el usuario no haya aportado. Si en el historial diste un dato externo sin fuente concreta, empieza tu mensaje diciendo que no tienes fuente para esa cifra y que la retiras, y regístrala como pendiente de investigación.
            2. "brechas_detectadas": Enumera EXCLUSIVAMENTE los datos precisos y específicos que faltan para responder a la "PREGUNTA ACTUAL".
            - REGLA DE PENDIENTES: Todo hecho externo que la pregunta necesite y que el usuario no haya dado con fuente concreta se registra como brecha, siempre con este formato y no como pregunta: "Pendiente de investigación: [dato] (fuente sugerida: [institución o tipo de documento])". Ejemplo: "Pendiente de investigación: población de Miraflores (fuente sugerida: INEI)". Indica dónde buscarlo, sin enlaces ni cifras. Esta regla tiene prioridad sobre la REGLA DE LENGUAJE.
            - REGLA DE ENFOQUE (CRÍTICA): NO incluyas brechas generales del proyecto. La brecha debe referirse solo a lo que pide la pregunta actual. Si ya tienes todos los datos para responder a la pregunta, devuelve un arreglo vacío [].
            - REGLA DE LENGUAJE: El texto descriptivo de la brecha debe ser EXTREMADAMENTE CLARO. Escríbelo como una petición corta o una pregunta directa y abierta, sin opciones ni posibles respuestas (Ejemplo: "{$exampleBrecha}").
            - REGLA DE ESTABILIDAD: Si el usuario ya te dio el dato, NO lo vuelvas a pedir.
            - FORMATO OBLIGATORIO: Cada brecha debe ser un objeto JSON con dos propiedades: "dato" y "criticidad" ("Crítica", "Importante", "Recomendable", "Opcional").
            3. "variables_negocio": Aquí colocarás TODOS los resultados matemáticos y SUPUESTOS. Cada variable será un objeto JSON.
            - REGLA DE ESTADOS: Define el "estado" como "Calculado" o "Supuesto". "Supuesto" solo se usa para decisiones del negocio, nunca para hechos externos. No calcules a partir de un hecho externo que siga pendiente de investigación.
            - REGLA DE FÓRMULA INDIVIDUAL: Para CADA variable, agrega un campo llamado "formula". Si es "Calculado", escribe la operación exacta. Si es "Supuesto", justifica brevemente. Si no hay cálculo, pon "".
            4. "draft_text": Es el borrador formal (Propuesta Académica) de la respuesta técnica.
            - REGLA DE CIFRAS EXTERNAS: Una cifra externa solo puede aparecer en el borrador si el usuario la dio junto con una fuente concreta (documento o publicación identificable; la portada de un sitio web no es una fuente concreta). Sin esa fuente, la cifra no entra al borrador ni se presenta el dato como resuelto.
            - REGLA DE CONTENIDO: Redacta siempre el borrador con lo que ya se sabe, aunque sea parcial: incluye todos los datos que dio el usuario y las cifras externas con fuente concreta, mencionando esa fuente. Omite solo lo que no tiene respaldo. Déjalo vacío únicamente si todavía no hay ningún dato del proyecto para esta pregunta.
            - REGLA DE EVIDENCIA: Aplica al borrador y a tus mensajes lo siguiente:
            {$plan->alcanceEvidencia()}
            5. "progress_percentage": Estimación (0-100) de qué tan completa está la respuesta.
            6. "is_complete": true si la respuesta ya está lista para usarse. Debe ser false si la respuesta depende de un dato pendiente de investigación.
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
                        "dato": "{$exampleBrecha}",
                        "criticidad": "Crítica"
                    }
                ],
                "variables_negocio": [
                    {$exampleVariable}
                ],
                "updated_specific_data": {}
            }
            PROMPT;

        $plan->log('asistente_cocreacion', $prompt);

        return $prompt;
    }

   public function promptValidationRedundanceQuestion( PlanPrompts $plan, string|array $history,  string $question, bool $isPhase2 = false  ): string {

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
Eres un asesor académico especializado en investigación, elaboración y sustentación de tesis universitarias de {$plan->tipoTesis()}.

{$plan->enfoque()}

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

    // Misma regla que la redacción del párrafo: la respuesta sugerida no debe exagerar la evidencia
    $sections[] = "AL REDACTAR LA RESPUESTA (\"resp\"), APLICA ESTA REGLA:\n\n" . $plan->alcanceEvidencia();

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

    $prompt = implode("\n\n", array_filter($sections));

    $plan->log('redundancia_pregunta', $prompt, ['fase_2' => $isPhase2]);

    return $prompt;
}

    public function buildMessagesBitacoraResponseCurrent( PlanPrompts $plan, array $data, array $history , string   $documentContent,string   $imageContent, bool $isApa): array {

        // Capítulo raíz y sección (nodo) a los que pertenece la pregunta
        $parentNode = $data['parent_node'] ?? [];
        $sectionNode = $data['section_node'] ?? [];
        $promptIni = $plan->systemPrompt();

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
                $plan->specificPrompt(),
                [
                    '[Capítulo]' => $parentNode['titulo'] ?? '',
                    '[Sección]' => trim(($sectionNode['codigo'] ?? '') . ' ' . ($sectionNode['titulo'] ?? '')),
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

        $plan->log('redaccion_respuesta', $promptIni, [
            'capitulo' => $parentNode['titulo'] ?? null,
            'seccion'  => $sectionNode['titulo'] ?? null,
        ]);

        return $messages;
    }

    // PARA INDICAR QUE LA INFORMACION SEA CONVERTIDA EN TABLA
    // La pregunta, la sección y el objetivo le indican a la IA qué debe organizar la tabla,
    // para que las columnas no se improvisen y cada dato quede en la columna que le corresponde
    public function buildMessageTable(PlanPrompts $plan, string $resp, string $question, $sectionNode = null, ?string $objective = null): string
    {
        $seccion = trim(($sectionNode['codigo'] ?? '') . ' ' . ($sectionNode['titulo'] ?? ''));
        $seccion = $seccion !== '' ? $seccion : 'No especificada';
        $objetivo = trim((string) $objective) !== '' ? $objective : 'No especificado';

        $prompt = <<<PROMPT
Eres un asistente que organiza en una tabla la información que dio un tesista
en su proyecto de tesis de {$plan->tipoTesis()}.

CONTEXTO:
- Sección: {$seccion}
- Pregunta: {$question}
- Objetivo de la sección: {$objetivo}

INFORMACIÓN DEL TESISTA (única fuente de datos):
{$resp}

CÓMO CONSTRUIR LA TABLA:
1. Define las columnas a partir de lo que pide la PREGUNTA. Antes de llenarlas, ten claro qué significa
   cada columna (por ejemplo, una "barrera" es un obstáculo o dificultad; un "requisito" es algo que
   el negocio debe tener o hacer).
2. Cada fila representa un elemento que el tesista mencionó.
3. Si la pregunta pide varias cosas distintas (por ejemplo, espacios no cubiertos y barreras), NO
   emparejes en una misma fila elementos que no tienen relación entre sí. En ese caso usa una columna
   "Tipo" que indique a qué corresponde cada elemento, con una fila por elemento.
4. Clasifica cada elemento por su SIGNIFICADO, no por cómo lo agrupó el tesista en su texto. Las acciones
   o requisitos que el negocio debe cumplir (por ejemplo, "ofrecer delivery" o "contratar personal") NO
   son barreras, aunque el tesista los mencione junto a ellas. Si un elemento no corresponde a ninguna de
   las categorías que pide la pregunta, clasifícalo con el tipo que realmente tiene (por ejemplo,
   "Requisito") en lugar de forzarlo en otra categoría.
5. NO inventes información: no agregues elementos, filas, causas, cifras ni datos que el tesista no
   mencionó, aunque parezcan lógicos o necesarios para completar la tabla.
6. Incluye TODOS los elementos que mencionó el tesista; no omitas ninguno.
7. Puedes resumir lo que dijo el tesista con palabras más breves, pero sin cambiar su sentido.
8. Si una celda no tiene información en la fuente, escribe "—".
9. El título debe describir el contenido de la tabla según la pregunta.

FORMATO DE RESPUESTA:
Devuelve únicamente un JSON válido, sin explicaciones, sin markdown y sin bloques de código:

{
    "title": "string",
    "columns": ["columna1", "columna2"],
    "rows": [
        ["valor1", "valor2"],
        ["valor1", "valor2"]
    ]
}

Si la información no puede representarse como una tabla o no contiene datos tabulares, devuelve:

{
    "title": null,
    "columns": [],
    "rows": []
}
PROMPT;

        $plan->log('generar_cuadro', $prompt, ['seccion' => $seccion]);

        return $prompt;
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
    PlanPrompts $planPrompts,
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

    $prompt = <<<PROMPT

Eres un asesor académico especializado en elaboración de tesis de {$planPrompts->tipoTesis()}.

{$planPrompts->enfoque()}

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

11. Genera EXACTAMENTE 3 preguntas por nodo: ni más ni menos.

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
                    "description": "Segunda pregunta relacionada con el nodo",
                    "detail": "Explicación clara de qué debe considerar el usuario para responder",
                    "example": "Ejemplo conciso de redacción de la respuesta"
                },
                {
                    "description": "Tercera pregunta relacionada con el nodo",
                    "detail": "Explicación clara de qué debe considerar el usuario para responder",
                    "example": "Ejemplo conciso de redacción de la respuesta"
                }
            ]
        }
    ]
}

Cada nodo de la lista debe aparecer una sola vez en "nodes", con sus 3 preguntas.

PROMPT;

    $planPrompts->log('preguntas_nodos_hoja', $prompt);

    return $prompt;
}

// =======================================================
 // PROMPT PARA VEFICAR SI ES NECESARIO ACTUALIZAR EL CONTEXTO
//  ==================================================
    public function promptUpdateThesisContext(
        PlanPrompts $plan,
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

        // Cada plan da su propio significado a las columnas de thesis_contexts
        $labels = $plan->contextLabels();

        $contextoActual = '';
        $significadoCampos = '';
        foreach ($labels as $field => $label) {
            $contextoActual .= "{$label}:\n{$context->{$field}}\n\n";
            $significadoCampos .= "- \"{$field}\": {$label}\n";
        }

        $prompt = <<<PROMPT

Eres un sistema encargado de mantener el contexto
estructurado de un proyecto de tesis de {$plan->tipoTesis()}.

{$plan->enfoque()}

Tu tarea NO es responder la pregunta.

Tu tarea es determinar si la respuesta del usuario
aporta información importante y estable sobre el
proyecto que debería incorporarse al contexto.

CONTEXTO ACTUAL:

{$contextoActual}
SIGNIFICADO DE CADA CAMPO DE "updates":

{$significadoCampos}

PREGUNTA DEL PLAN:

{$question->question_text}


RESPUESTA DEL USUARIO:

{$answer}


Analiza la respuesta.

Si la respuesta aporta información relevante
sobre el proyecto, devuelve los campos que deberían
actualizarse.

Si la respuesta NO aporta información importante
para el contexto general del proyecto, no actualices.

IMPORTANTE:

- No inventes información.
- No modifiques información sin evidencia.
- No copies información innecesariamente.
- No incluyas información específica que solamente
  sirve para responder esta pregunta.
- El contexto debe representar el proyecto completo.
- Cada campo debe contener únicamente la información
  que corresponde a su significado.
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

        $plan->log('actualizar_contexto', $prompt, ['question_id' => $question->id]);

        return $prompt;
    }

    // =======================================================
    // PROMPT PARA DETECTAR INCONGRUENCIAS CON EL CONTEXTO DEL PROYECTO
    // =======================================================
    public function promptCoherenceContext(PlanPrompts $plan, string $contextText, string $response): string
    {
        $elementosCentrales = '- ' . implode("\n- ", array_values($plan->contextLabels()));

        $prompt = <<<PROMPT

Eres un sistema de validación de coherencia para una tesis de {$plan->tipoTesis()}.

Tu objetivo es determinar si una nueva respuesta del usuario
presenta una INCONGRUENCIA IMPORTANTE con el contexto actual
de su proyecto.

{$plan->enfoque()}

CONTEXTO ACTUAL DE LA TESIS:

{$contextText}

NUEVA RESPUESTA DEL USUARIO:

{$response}

Analiza principalmente la relación entre:

{$elementosCentrales}

IMPORTANTE:

No consideres como incongruencia:

- información adicional compatible con el proyecto;
- detalles nuevos que complementan el contexto;
- cambios menores;
- explicaciones más específicas;
- nuevas estrategias o actividades compatibles con el proyecto.

Considera una incongruencia cuando la nueva respuesta
cambie de manera clara y significativa elementos centrales
del proyecto previamente establecido.

{$plan->coherenceExamples()}

Devuelve únicamente un JSON válido con esta estructura:

{
    "is_inconsistent": true
}

Si no existe una contradicción importante:

{
    "is_inconsistent": false
}

PROMPT;

        $plan->log('coherencia_contexto', $prompt);

        return $prompt;
    }

    // =======================================================
    // PROMPT PARA CONSOLIDAR UN CAPÍTULO TERMINADO EN EL RESUMEN GENERAL
    // =======================================================
    public function promptConsolidateChapter(
        PlanPrompts $plan,
        int $numeroCapitulo,
        string $chapterTitle,
        string $contenidoCapitulo,
        ?string $resumenGeneralAnterior
    ): string {
        $prompt = <<<PROMPT
            Eres un director académico y metodológico de proyectos de tesis de {$plan->tipoTesis()}.

            {$plan->enfoque()}

            Se ha completado el capítulo {$numeroCapitulo}: "{$chapterTitle}".

            RESPUESTAS COMPLETADAS EN ESTE CAPÍTULO:
            {$contenidoCapitulo}

            RESUMEN GENERAL ACUMULADO PREVIO (Capítulos anteriores):
            {$resumenGeneralAnterior}

            REGLAS DE FIDELIDAD DEL RESUMEN (tienen prioridad sobre el objetivo):
            - Usa únicamente lo que aparece en las respuestas y en el resumen previo. Los temas que se indican más abajo solo se destacan si aparecen: si un tema (por ejemplo, precio, costos, capacidad, inversión o resultados) no aparece, no lo menciones ni digas que se avanzó en él.
            - Conserva cada dato con su unidad y su periodo: "medio día por avería" no puede pasar a "pérdida diaria de medio día".
            - No afirmes decisiones que no estén en las respuestas.
            - Una encuesta de intención informa lo que respondieron los encuestados: no confirma la demanda ni la viabilidad del proyecto.
            {$plan->alcanceEvidencia()}

            Tu objetivo:
            1. "resumen_capitulo": Redacta un resumen breve, preciso y sustancial de lo respondido en este capítulo (máximo 120-180 palabras), {$plan->consolidationFocus()}.
            2. "nuevo_resumen_general": Si no había resumen general previo, usa el resumen de este capítulo. Si ya existía uno previo, intégralos en un solo texto fluido y unificado (máximo 120-1200 palabras) que sintetice toda la columna vertebral del proyecto hasta el momento.

            RESPONDE ÚNICAMENTE CON ESTE FORMATO JSON:
            {
                "resumen_capitulo": "Texto del resumen del capítulo actual...",
                "nuevo_resumen_general": "Texto consolidado de todos los capítulos hasta el actual..."
            }
            PROMPT;

        $plan->log('consolidar_capitulo', $prompt, ['capitulo' => $numeroCapitulo]);

        return $prompt;
    }
}