<?php

namespace App\Services;

use App\Models\ThesisContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Question;


class ThesisContextService
{

    public function __construct( private OpenAIService $openAIService ,   private PromptService $promptService   ) {

    }
    // ============ INICIO GUARDAR ACTUALIZAR CONTEXTO 
    // ======================================
    public function updateContextFromAnswer(
        int $conversationId,
        int $userPlanId,
        int $questionId,
        string $answer
    ): ThesisContext {

        $question = Question::findOrFail($questionId);

        $context = ThesisContext::firstOrCreate(
            [
                'conversation_id' => $conversationId,
            ],
            [
                'user_plan_id' => $userPlanId,
                'confidence' => 0,
            ]
        );

        if (trim($answer) === '') {
            return $context;
        }

        $prompt = $this->promptService->promptUpdateThesisContext(
            $context,
            $question,
            $answer
        );

        try {

            $result = $this->openAIService ->inputCadenaOutputString($prompt);

            $result = $this->parseJsonResponse($result);

            if (  !isset($result['update_context']) ||  $result['update_context'] !== true ) {
                return $context;
            }

            $updates = $result['updates'] ?? [];

            $updates = $this->filterAllowedFields($updates);

            if (empty($updates)) {
                return $context;
            }

            $context->update($updates);

            return $context->fresh();

        } catch (\Throwable $e) {

            \Log::error(
                'Error actualizando thesis_context',
                [
                    'conversation_id' => $conversationId,
                    'question_id' => $questionId,
                    'error' => $e->getMessage(),
                ]
            );

            return $context;
        }
    }


    private function filterAllowedFields(array $updates): array
    {
        $allowed = [
            'business_name',
            'business_sector',
            'product_service',
            'target_customer',
            'main_problem',
            'value_proposition',
        ];

        $filtered = [];

        foreach ($allowed as $field) {

            if (
                array_key_exists($field, $updates) &&
                $updates[$field] !== null &&
                trim((string) $updates[$field]) !== ''
            ) {
                $filtered[$field] = trim($updates[$field]);
            }
        }

        return $filtered;
    }


    private function parseJsonResponse(string $response): array
    {
        $response = trim($response);

        // Por si el modelo devuelve ```json ... ```
        $response = preg_replace(
            '/^```json\s*|\s*```$/i',
            '',
            $response
        );

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new \RuntimeException(
                'La IA no devolvió un JSON válido.'
            );
        }

        return $data;
    }
    // ============ FIN GUARDAR ACTUALIZAR CONTEXTO ======================================


 
    // ===========================================
    // INICIO OBNTENER CONTEXTO COMPARAR
    // ==========================================

    /**
     * Obtener el contexto de una conversación.
     */
    public function getContext(int $conversationId): ?ThesisContext
    {
        return ThesisContext::where(
            'conversation_id',
            $conversationId
        )->first();
    }

    /**
     * Construir el contexto que será enviado a la IA
     * para validar una nueva respuesta.
     *
     * No enviamos toda la conversación.
     */
    public function buildValidationContext(
        int $conversationId
    ): array {
        $context = $this->getContext($conversationId);

        if (!$context) {
            return [];
        }

        return [
            'business_name' => $context->business_name,
            'business_sector' => $context->business_sector,
            'product_service' => $context->product_service,
            'target_customer' => $context->target_customer,
            'main_problem' => $context->main_problem,
            'value_proposition' => $context->value_proposition,
            'context_summary' => $context->context_summary,
            'confidence' => $context->confidence,
        ];
    }

    /**
     * Convertir el contexto a un texto compacto.
     *
     * Este texto será útil para construir posteriormente
     * el prompt de validación.
     */
    public function buildValidationText(
        int $conversationId
    ): string {
        $context = $this->getContext($conversationId);

        if (!$context) {
            return '';
        }

        $lines = [];

        if ($context->business_name) {
            $lines[] = "Negocio: {$context->business_name}";
        }

        if ($context->business_sector) {
            $lines[] = "Sector: {$context->business_sector}";
        }

        if ($context->product_service) {
            $lines[] = "Producto o servicio: {$context->product_service}";
        }

        if ($context->target_customer) {
            $lines[] = "Cliente objetivo: {$context->target_customer}";
        }

        if ($context->main_problem) {
            $lines[] = "Problema principal: {$context->main_problem}";
        }

        if ($context->value_proposition) {
            $lines[] = "Propuesta de valor: {$context->value_proposition}";
        }

        if ($context->context_summary) {
            $lines[] = "Resumen: {$context->context_summary}";
        }

        return implode("\n", $lines);
    }

    // ===========================================
    // FIN OBNTENER CONTEXTO COMPARAR
    // ==========================================


public function validateResponseAgainstContext(
    int $conversationId,
    string $response
): array {

    /*
     * Obtener el contexto actual de la conversación.
     */
    $contextText = $this->buildValidationText($conversationId);

    /*
     * Si todavía no existe contexto,
     * no podemos realizar una comparación.
     */
    if (empty(trim($contextText))) {

        return [
            
            'is_inconsistent' => false
        ];
    }

    /*
     * Si la respuesta está vacía,
     * no tiene sentido realizar la validación.
     */
    if (empty(trim($response))) {

        return [
            'is_inconsistent' => false
        ];
    }

    /*
     * Construir el prompt para la IA.
     */
    $prompt = <<<PROMPT

Eres un sistema de validación de coherencia para una tesis.

Tu objetivo es determinar si una nueva respuesta del usuario
presenta una INCONGRUENCIA IMPORTANTE con el contexto actual
de su proyecto de negocio.

CONTEXTO ACTUAL DE LA TESIS:

{$contextText}

NUEVA RESPUESTA DEL USUARIO:

{$response}

Analiza principalmente la relación entre:

- Negocio
- Sector
- Producto o servicio
- Cliente objetivo
- Problema principal
- Propuesta de valor

IMPORTANTE:

No consideres como incongruencia:

- información adicional compatible con el negocio;
- detalles nuevos que complementan el contexto;
- cambios menores;
- explicaciones más específicas;
- nuevas estrategias o actividades compatibles con el negocio.

Considera una incongruencia cuando la nueva respuesta
cambie de manera clara y significativa elementos centrales
del negocio previamente establecido.

Por ejemplo:

Si el contexto indica:

"Negocio: Cafetería"
"Producto: Café y postres"
"Cliente: Estudiantes"

y la nueva respuesta indica:

"La empresa venderá maquinaria industrial para constructoras."

Esto debe considerarse una incongruencia importante.

En cambio, si la nueva respuesta indica:

"Implementaremos publicidad en Instagram para atraer estudiantes."

Esto es compatible con el contexto y NO debe considerarse
una incongruencia.

Devuelve únicamente un JSON válido con esta estructura:

{
    "is_inconsistent": true
}

Si no existe una contradicción importante:

{
    "is_inconsistent": false
}

PROMPT;

    /*
     * Consultar a OpenAI.
     */
    $result = $this->openAIService->json($prompt);

    /*
     * Normalizar el resultado para que el método
     * siempre devuelva la misma estructura.
     */
    return [
        'is_inconsistent' => (bool) ($result['is_inconsistent'] ?? false)
    ];
}



}