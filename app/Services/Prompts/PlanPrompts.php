<?php

namespace App\Services\Prompts;
use Illuminate\Support\Facades\Log;

abstract class PlanPrompts
{
    public function __construct(
        public readonly ?int $planId = null,
        public readonly ?string $planName = null,
        public readonly ?int $conversationId = null,
        public readonly bool $isFallback = false,
    ) {
    }

    // Identificador del conjunto de prompts
    abstract public function key(): string;

    // Tipo de plan
    abstract public function tipoTesis(): string;

    // Descripción y lógica del plan, reutilizada en los demás prompts.
    abstract public function enfoque(): string;

    // Prompt de sistema del asesor que redacta las respuestas.
    abstract public function systemPrompt(): string;

    // Instrucciones de redacción propias del plan.
    abstract protected function specificInstructions(): string;

    // Criterios adicionales para evaluar la respuesta del tesista.
    abstract public function validationCriteria(): string;

    // Rol del asistente de co-creación.
    abstract public function coCreationRole(): string;

    // Guía de brechas y variables cuantitativas típicas del plan.
    abstract public function coCreationGuide(): string;

    // Ejemplo de salida JSON del asistente de co-creación:
    abstract public function coCreationExample(): array;

    // Significado de cada columna de thesis_contexts para este plan.
    abstract public function contextLabels(): array;

    //Ejemplos de incongruencia / compatibilidad para validar coherencia.
    abstract public function coherenceExamples(): string;

    abstract public function assistantContextCatalog(): array;

    public function assistantContextDefaults(): array
    {
        return array_fill_keys(array_keys($this->assistantContextCatalog()), null);
    }

    // Qué datos debe destacar el resumen de cada capítulo.
    abstract public function consolidationFocus(): string;

    // Nodos obligatorios ("3.2" = ese nodo, "3.2.*" = sus descendientes); los de fase 2 también lo son
    abstract public function mandatoryCodes(): array;

    public function isMandatory(?string $code): bool
    {
        $code = trim((string) $code);

        if ($code === '') {
            return false;
        }

        foreach ($this->mandatoryCodes() as $pattern) {
            if ($code === $pattern
                || (str_ends_with($pattern, '.*') && str_starts_with($code, substr($pattern, 0, -1)))) {
                return true;
            }
        }

        return false;
    }

    // Prompt específico con los marcadores
    public function specificPrompt(): string
    {
        return self::CONTEXTO_PREGUNTA . $this->specificInstructions() . self::ALCANCE_EVIDENCIA . self::CIFRAS_DEL_TESISTA . self::REDACCION_SIN_REPETIR
            . "\n\n\nCÁLCULOS:\n\n" . $this->reglaCalculos() . "\n" . self::CITAS_Y_FORMATO;
    }

    // Mejorar la redacción no debe borrar datos: "S/ 15 por alumno en grupo" no puede quedar como "costo diferenciado"
    private const CIFRAS_DEL_TESISTA = <<<'TEXT'


CIFRAS DEL TESISTA (REGLA CRÍTICA):

Esta regla tiene prioridad sobre las instrucciones de estilo y de extensión.

- Incluye en la respuesta TODAS las cifras de la INFORMACIÓN PROPORCIONADA POR EL TESISTA: precios, tarifas, cantidades, capacidades, porcentajes, plazos, horarios y montos, cada una con su unidad, su periodo y la condición a la que se aplica.
- Puedes mejorar la redacción alrededor de la cifra, pero no la reemplaces por una descripción general. Incorrecto: "se aplicará un costo diferenciado para grupos". Correcto: "se aplicará una tarifa de S/ 15 por alumno en las clases grupales".
- Si el tesista da varias cifras para casos distintos (individual y grupal, temporada alta y baja, mañana y tarde), conserva cada una con su caso; no las unas en un rango ni en un promedio.
- No cambies el valor, la unidad ni el periodo: "S/ 15 por alumno" no es "S/ 15 por grupo", y "por semana" no es "por mes".
TEXT;

    // Las cifras calculadas las resuelve CalculationService: la IA escribe la operación, no el resultado
    private const REGLA_CALCULOS = <<<'TEXT'
- Cuando una cifra sea el resultado de una operación, NO escribas el resultado: escribe la operación entre llaves dobles con las cifras de origen, y el sistema la calculará. Ejemplos: "una capacidad de {{6*22}} turnos al mes", "un total ponderado de {{0.20*4 + 0.15*3 + 0.25*5}}", "un VAN de S/ {{van(0.12, 10000, 3000, 4000, 5000)}}", "una TIR de {{redondeo(tir(10000, 3000, 4000, 5000)*100, 2)}} %".
- Dentro de las llaves usa solo cifras, + - * / ^, paréntesis, % (20% equivale a 0.20) y las funciones suma, promedio, redondeo(valor, decimales), min, max, van(tasa, inversión, flujo1, flujo2…) y tir(inversión, flujo1, flujo2…). Escribe las cifras sin separador de miles y con punto decimal (2800, 1.5). No pongas palabras, unidades ni "S/" dentro de las llaves.
- Si un resultado depende de otro, escribe la operación completa desde las cifras originales (por ejemplo, {{6*22*12}} y no el resultado de 6*22 multiplicado por 12).
- Las cifras que dio el estudiante o que vienen de una fuente se copian tal cual, sin llaves. No recalcules ni redondees lo que el estudiante ya calculó.
- Si la pregunta pide un cálculo, un total o un indicador (por ejemplo, el total de una matriz ponderada, ingresos, punto de equilibrio, VAN o TIR) y tienes las cifras, escribe siempre la operación entre llaves: no describas el cálculo sin dar su resultado (incorrecto: "al multiplicar los pesos por las calificaciones se obtiene un total ponderado"; correcto: "el total ponderado es {{0.20*4 + 0.15*3}}"). En una matriz ponderada, da también el puntaje de cada factor ({{0.20*4}}) y luego el total.
- Interpreta el resultado con prudencia y sin adelantar su valor (por ejemplo, "este resultado se compara con el punto medio de la escala"); el sistema revisará la interpretación cuando conozca el resultado.
- No uses notación LaTeX (\frac, \dfrac, ^{ }). Si muestras una fórmula, escríbela en texto simple: "VAN = −inversión + Σ flujo / (1 + tasa)^año".
- No conviertas en operación lo que no lo es: años, fechas, códigos y cantidades dadas no llevan llaves.
TEXT;

    // Regla de cálculo y glosario (catálogo con "glosario"): redacción, sugerencia automática e Impulsa
    public function reglaCalculos(): string
    {
        $glossary = [];

        foreach ($this->assistantContextCatalog() as $item) {
            if (!empty($item['glosario'])) {
                $glossary[] = "- {$item['label']}: {$item['description']}";
            }
        }

        if (empty($glossary)) {
            return self::REGLA_CALCULOS;
        }

        return self::REGLA_CALCULOS . "\n\nCONCEPTOS CON CIFRAS (no mezcles las cifras de un concepto con las de otro; por ejemplo, la meta de ventas no es el tamaño del mercado):\n" . implode("\n", $glossary);
    }

    // Proyecciones en lugar de promesas. También la usan la sugerencia automática y el agente Impulsa
    private const REGLA_PROYECCIONES = '- Las metas, los beneficios y los resultados esperados se redactan como proyecciones o metas ("se espera", "se proyecta", "se estima", "tiene como meta"), nunca como garantías. No uses "garantizar", "asegurar", "eliminar por completo", "erradicar" ni "sin duda" para referirte a resultados futuros.';

    // Lenguaje académico: sin promesas (las metas son proyecciones) y sin repeticiones entre respuestas
    private const REDACCION_SIN_REPETIR = "\n\n\nPROYECCIONES Y REPETICIONES:\n\n" . self::REGLA_PROYECCIONES . "\n" . <<<TEXT
- No vuelvas a presentar el proyecto (nombre de la empresa o del negocio, ubicación, rubro, problema general) al inicio de la respuesta: el lector ya lo conoce por las secciones anteriores. Menciónalo solo si la pregunta lo requiere, y en pocas palabras.
- Empieza directamente por el contenido que pide la pregunta. No abras con fórmulas que repiten la pregunta, como "Para considerar completa y adecuadamente delimitada la sección de…" o "Para atender…".
- No repitas datos ni ideas que ya están en las respuestas anteriores del historial, salvo que la pregunta los necesite; en ese caso, menciónalos brevemente sin volver a desarrollarlos. Esto no se aplica a la INFORMACIÓN PROPORCIONADA POR EL TESISTA en esta pregunta: sus cifras siempre se incluyen (ver CIFRAS DEL TESISTA).
- Si un tema ya se desarrolló en un capítulo anterior (ver LO YA DESARROLLADO EN EL DOCUMENTO), no lo vuelvas a explicar: remite a ese capítulo en una frase (por ejemplo, "como se describió en el capítulo de Reseña y Contexto de la Empresa, …") y desarrolla solo lo nuevo que pide la pregunta.
- Excepción: en el Resumen ejecutivo y en la Introducción sí se presenta el proyecto, porque son las secciones que lo introducen.
TEXT;

    // Regla de proyecciones (sin promesas) para los demás prompts que redactan contenido de la tesis
    public function reglaProyecciones(): string
    {
        return self::REGLA_PROYECCIONES;
    }

    // Regla sobre el tamaño y alcance de la evidencia; también la usan el asistente y el resumen del capítulo.
    public function alcanceEvidencia(): string
    {
        return trim(self::ALCANCE_EVIDENCIA);
    }

    // para verificar en el log
    public function log(string $tipoPrompt, string $prompt, array $extra = []): void
    {
        $context = [
            'tipo_prompt'     => $tipoPrompt,
            'prompt_usado'    => static::class,
            'conversation_id' => $this->conversationId,
            'plan_id'         => $this->planId,
            'plan_nombre'     => $this->planName,
            'es_respaldo'     => $this->isFallback,
            'longitud'        => mb_strlen($prompt),
            'inicio_prompt'   => mb_substr(preg_replace('/\s+/', ' ', trim($prompt)), 0, 200),
            ...$extra,
        ];

        $message = "[{$this->key()}] {$tipoPrompt} | plan {$this->planId} ({$this->planName})";

        if ($this->isFallback) {
            Log::channel('prompts')->warning($message . ' | plan sin prompt propio, se usa el de respaldo', $context);
            return;
        }

        Log::channel('prompts')->info($message, $context);
    }

    private const CONTEXTO_PREGUNTA = <<<TEXT

CONTEXTO:

La pregunta pertenece al capítulo:
[Capítulo]

Sección del capítulo:
[Sección]

No menciones el nombre del capítulo ni de la sección dentro de la respuesta final.


PREGUNTA DE INVESTIGACIÓN:

[Pregunta]


OBJETIVO DEL CAPÍTULO:

[Objetivo]


CRITERIOS ESPERADOS DE RESPUESTA:

[Validacion]


INFORMACIÓN PROPORCIONADA POR EL TESISTA:

[Respuesta]


TEXT;

    private const ALCANCE_EVIDENCIA = <<<TEXT


TAMAÑO Y ALCANCE DE LA EVIDENCIA (REGLA CRÍTICA):

Esta regla tiene prioridad sobre las instrucciones de estilo. Mejorar la redacción no debe aumentar lo que la evidencia permite afirmar.

- Conserva el número exacto y el tipo de fuente que indicó el tesista: "un mototaxista", "tres conversaciones informales". Menciónalos en la respuesta.
- No cambies el singular por el plural ni le atribuyas al grupo lo que dijo una persona: "un mototaxista manifestó" no puede pasar a "los mototaxistas reportan".
- No cambies el método: una conversación no es una entrevista, una encuesta ni un estudio.
- No agregues calificativos que el tesista no dio, como "estratégico", "recurrente" o "significativo".
- Si la evidencia es pequeña o informal, preséntala como un indicio inicial de carácter exploratorio. No la presentes como prueba: no uses "confirma", "demuestra", "valida", "se validó", "evidencia concreta" ni "evidencia sólida", aunque la pregunta hable de validar. En ese caso escribe, por ejemplo, "se exploró la necesidad mediante tres conversaciones informales".
- No extiendas esa evidencia a todo el segmento o mercado, ni concluyas a partir de ella que existe demanda, una demanda no satisfecha o viabilidad. Si necesitas relacionarla con el proyecto, usa formas como "sugiere que podría existir" o "es un indicio que requiere mayor validación".
- Si el historial presenta una evidencia con mayor alcance que la información del tesista, usa el alcance menor.
- Lo anterior sobre evidencia pequeña o informal NO se aplica a una encuesta, un cuestionario, registros o un estudio formal. En esos casos informa el método, el tamaño de la muestra y el resultado tal como los indicó el tesista, sin llamarlos "indicio", "preliminar" ni "exploratorio", y sin extender el resultado más allá de quienes respondieron. Ejemplo: "el 68 % de los 210 mototaxistas encuestados manifestó que usaría un taller cercano".
TEXT;

    private const CITAS_Y_FORMATO = <<<TEXT


REFERENCIAS Y CITAS:

REGLAS PARA LAS CITAS ACADÉMICAS:

* La necesidad de una cita depende del origen y naturaleza de la información utilizada, no del capítulo ni de si el texto fue generado por el tesista o por la IA.
* Cuando una afirmación requiera respaldo académico y exista una fuente identificable en la información disponible, incorpora la cita correspondiente directamente dentro del texto de response.
* Utiliza exclusivamente el formato de citación APA 7 para las citas dentro del texto.
* Cuando una fuente se mencione por primera vez, utiliza preferentemente la forma narrativa completa, seguida de su abreviatura entre corchetes cuando corresponda. Ejemplo: Instituto Nacional de Estadística e Informática [INEI] (2022).
* Después de haber introducido la abreviatura de una institución, puedes utilizar la forma abreviada en citas posteriores. Ejemplo: (INEI, 2022).
* Cuando la información de una fuente respalde una afirmación concreta, coloca la cita inmediatamente después de la afirmación respaldada.
* No coloques una cita al final de un párrafo si la fuente no respalda todas las afirmaciones contenidas en dicho párrafo.
* Diferencia claramente entre la información respaldada por la fuente y las interpretaciones, análisis o conclusiones propias del proyecto.
* No atribuyas a una fuente conclusiones que no estén respaldadas directamente por ella.
* No inventes autores, instituciones, años ni fuentes.
* Si no existe una fuente identificable para una afirmación, no inventes una cita.
* La cita debe formar parte naturalmente de la redacción académica y no debe aparecer como un elemento aislado.
* No agregues la referencia bibliográfica completa dentro de response. En esta etapa solamente deben generarse las citas dentro del texto.


REGLAS PARA REFERENCES:

- Las fuentes solo pueden provenir de lo que el tesista indicó en su respuesta o en sus archivos adjuntos. No agregues fuentes por tu cuenta.
- Si no existen fuentes identificables utilizadas en response, retorna references como null.
- No generes referencias plausibles, hipotéticas o inventadas.
- No completes mediante suposiciones los datos bibliográficos que no estén disponibles. Deja vacío el campo que no se conozca.
- Datos esenciales: autor (persona o institución) y título. Si falta el año, usa "s.f.".
- Si el tesista menciona una fuente pero falta el autor o el título, no generes esa referencia ni su cita, y explica en reference_warning, en una frase breve y dirigida al tesista, qué dato falta. Ejemplo: "Falta el título del documento del INEI que mencionas. Complétalo en tu respuesta." Si no hay nada que advertir, retorna reference_warning como null.
- Cada elemento de references debe corresponder a una cita presente en response, y toda cita de una fuente externa en response debe tener su elemento en references.
- authors: cada autor como "Apellido, I. I." (por ejemplo "Pérez, J. A."); si el autor es una institución, su nombre completo sin abreviar (por ejemplo "Instituto Nacional de Estadística e Informática").
- source_type: book (libro), journal_article (artículo de revista), web_page (página o documento web), thesis (tesis), report (informe o reporte institucional), conference_paper (ponencia).
- container_title: nombre de la revista (artículos) o del sitio web (páginas web).
- publisher: editorial (libros), institución que publica (informes) o universidad (tesis).
- in_text_citation: la cita exactamente como la escribiste en response, por ejemplo "(INEI, 2022)" o "Pérez y Gómez (2021)".
- unused_sources: si recibes FUENTES QUE AGREGÓ EL TESISTA y no usas alguna, inclúyela con su título y el motivo, en una frase breve dirigida al tesista. Ejemplo: {"title": "Turismo receptivo en Cusco", "reason": "Trata sobre el turismo en Cusco, no sobre el tema de tu respuesta."}. Si usaste todas o no hay fuentes agregadas, retorna unused_sources como [].


FORMATO DE RESPUESTA:

Devuelve únicamente JSON válido.

{
    "is_valid": true,
    "response": "",
    "references": null,
    "reference_warning": null,
    "unused_sources": []
}

ó en caso se tenga referencias

{
    "is_valid": true,
    "response": "",
    "references": [
        {
            "authors": [],
            "year": "",
            "title": "",
            "source_type": "book | journal_article | web_page | thesis | report | conference_paper",
            "container_title": "",
            "volume": "",
            "issue": "",
            "pages": "",
            "publisher": "",
            "url": "",
            "doi": "",
            "in_text_citation": ""
        }
    ],
    "reference_warning": null,
    "unused_sources": []
}

RESTRICCIONES:

- No expliques el proceso.
- No agregues texto fuera del JSON.
- El campo response debe contener únicamente la respuesta académica final.
- Mantén coherencia entre response y references.

VERIFICACIÓN FINAL DE FUENTES (OBLIGATORIA ANTES DE RESPONDER):

1. Por cada elemento de references, su in_text_citation debe aparecer escrita tal cual dentro de response, inmediatamente después de la afirmación que respalda. Si no aparece, agrégala en response.
2. Si el tesista atribuye un dato a una fuente en su respuesta actual (por ejemplo "según un informe del Minsa") y no se puede registrar porque falta el autor o el título, reference_warning no puede ser null: indica qué dato falta. Esto se aplica aunque el dato también aparezca en el historial.

TEXT;
}
