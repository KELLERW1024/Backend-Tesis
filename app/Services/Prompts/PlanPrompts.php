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

    // Claves iniciales de assistant_context.
    abstract public function assistantContextDefaults(): array;

    // Qué datos debe destacar el resumen de cada capítulo.
    abstract public function consolidationFocus(): string;

    // Nodos obligatorios del filtro de estructura según el diagnóstico.
    abstract public function mandatoryNodes(): string;

    // Prompt específico con los marcadores
    public function specificPrompt(): string
    {
        return self::CONTEXTO_PREGUNTA . $this->specificInstructions() . self::ALCANCE_EVIDENCIA . self::CITAS_Y_FORMATO;
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

- Si no existen fuentes identificables utilizadas en response, retorna references como null.
- Solo genera referencias cuando una fuente real haya sido utilizada o identificada y exista información suficiente para registrarla.
- No generes referencias plausibles, hipotéticas o inventadas.
- No completes mediante suposiciones los datos bibliográficos que no estén disponibles.
- Si faltan datos esenciales para identificar una fuente, no generes esa referencia.
- Utiliza formato APA 7.ª edición.
- Cada elemento de references debe corresponder directamente a una fuente citada en response.
- Si agregas una referencia, debe existir la cita correspondiente dentro de response.
- Toda cita presente en response que corresponda a una fuente externa debe tener su fuente correspondiente en references cuando sea posible identificarla.


FORMATO DE RESPUESTA:

Devuelve únicamente JSON válido.

{
    "is_valid": true,
    "response": "",
    "references": null
}

ó en caso se tenga referencias

{
    "is_valid": true,
    "response": "",
    "references": [
        {
            "authors": [],
            "title": "",
            "year": "",
            "source_type": "book | journal_article | web_page | report",
            "url": "",
            "apa_citation": ""
        }
    ]
}


RESTRICCIONES:

- No expliques el proceso.
- No agregues texto fuera del JSON.
- El campo response debe contener únicamente la respuesta académica final.
- Mantén coherencia entre response y references.

TEXT;
}
