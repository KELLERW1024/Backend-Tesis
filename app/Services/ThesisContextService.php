<?php

namespace App\Services;

use App\Models\ThesisContext;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Question;
use App\Services\Prompts\PlanPromptResolver;
use App\Services\Prompts\PlanPrompts;

class ThesisContextService
{

    public function __construct( private OpenAIService $openAIService ,   private PromptService $promptService ,
                                 private PlanPromptResolver $planPromptResolver   ) {

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
            $this->planPromptResolver->forConversation($conversationId),
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

    
    // Construir el contexto que será enviado a la IA
     
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
     * Convertir el contexto a un texto compacto para validar incongruencias.
     * Si hay resumen_general (Cap 2+), lo usa junto con los datos específicos.
     * Si no hay resumen (Cap 1), usa las últimas 5 respuestas del usuario.
     */
    public function buildValidationText(
        int $conversationId
    ): string {
        $context = $this->getContext($conversationId);

        $resumenGeneral = $context?->plan_context['resumen_general'] ?? null;

        // Si ya hay resumen general consolidado (Capítulo 2 en adelante)
        if (!empty($resumenGeneral)) {
            $texto = "RESUMEN CONSOLIDADO DEL PROYECTO:\n" . $resumenGeneral . "\n\n";
            $texto .= $this->knownDataText($context, $this->planPromptResolver->forConversation($conversationId));

            return trim($texto);
        }

        // Diagnóstico inicial, con cada pregunta y su respuesta
        $textoDiagnostico = $this->diagnosticText($context);
        $textoDiagnostico = $textoDiagnostico !== '' ? $textoDiagnostico . "\n\n" : '';


        // Si estamos en el Capítulo 1 (aún no hay resumen general):
        // Obtenemos las últimas 5 respuestas usando ConversationService
        $conversationService = app(ConversationService::class);
        $historial5 = $conversationService->getHistoryLimit10($conversationId);

        $textoHistorial = "";
        if (!empty($historial5)) {
            $textoHistorial = "RESPUESTAS RECIENTES DADAS POR EL USUARIO (CAPÍTULO 1):\n";
            foreach ($historial5 as $item) {
                $textoHistorial .= "- {$item['content']}\n\n";
            }
        }

        $textoCompleto = trim($textoDiagnostico . $textoHistorial);

        if (!empty($textoCompleto)) {
            return $textoCompleto;
        }

        return '';
    }

    // ===========================================
    // FIN OBNTENER CONTEXTO COMPARAR
    // ==========================================

    public function structureText(int $conversationId): string
    {
        $userPlanId = \App\Models\Conversation::whereKey($conversationId)->value('user_plan_id');

        if (!$userPlanId) {
            return '';
        }

        $nodes = \App\Models\PlanNode::where('user_plan_id', $userPlanId)
            ->where('execution_phase', '!=', 0)
            ->whereIn('nivel', [1, 2])
            ->orderBy('orden')
            ->orderBy('id')
            ->get(['id', 'parent_id', 'nivel', 'titulo']);

        $children = $nodes->where('nivel', 2)->groupBy('parent_id');
        $clean = fn ($title) => preg_replace('/(?<!\.)\.\s*$/u', '', trim((string) $title));
        $lines = [];

        foreach ($nodes->where('nivel', 1) as $chapter) {
            $lines[] = '- ' . $clean($chapter->titulo);

            foreach ($children->get($chapter->id, collect()) as $section) {
                $lines[] = '   · ' . $clean($section->titulo);
            }
        }

        return $lines
            ? "ESTRUCTURA DEL DOCUMENTO (capítulos y apartados que tiene esta tesis, en orden; no menciones otros):\n" . implode("\n", $lines)
            : '';
    }

    public function coverData(\App\Models\Conversation $conversation, PlanPrompts $plan): array
    {
        $fields = ['titulo', 'ciudad', 'universidad', 'facultad', 'escuela', 'titulo_profesional', 'asesor'];
        $context = $this->getContext($conversation->id);
        $cover = $context?->plan_context['portada'] ?? null;

        if (!is_array($cover)) {
            $cover = array_fill_keys($fields, '');
            $diagnostic = $this->diagnosticText($context);

            if ($diagnostic !== '') {
                try {
                    $result = $this->openAIService->json($this->promptService->promptCoverData($plan, $diagnostic)) ?? [];

                    foreach ($fields as $field) {
                        $cover[$field] = is_scalar($result[$field] ?? null) ? trim((string) $result[$field]) : '';
                    }

                    // Solo se guarda si la IA respondió: si falla, se reintenta en la próxima descarga
                    if ($context) {
                        $planContext = $context->plan_context ?? [];
                        $planContext['portada'] = $cover;
                        $context->plan_context = $planContext;
                        $context->save();
                    }
                } catch (\Throwable $e) {
                    \Log::warning('No se pudieron obtener los datos de la portada: ' . $e->getMessage());
                }
            }
        }

        // El nombre que el usuario le dio a su conversación manda sobre el título generado
        $customTitle = trim((string) $conversation->title);
        if ($customTitle !== '' && !in_array(mb_strtolower($customTitle), ['nuevo plan', 'proyecto de tesis'], true)) {
            $cover['titulo'] = $customTitle;
        }

        return array_merge(array_fill_keys($fields, ''), $cover);
    }

    public function knownData(?ThesisContext $context, PlanPrompts $plan): array
    {
        $data = $context?->assistant_context ?? [];
        $result = [];

        foreach ($plan->assistantContextCatalog() as $key => $item) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value !== '') {
                $result[] = ['key' => $key, 'label' => $item['label'], 'value' => $value];
            }
        }

        return $result;
    }

    // Texto de los datos conocidos para los prompts
    public function knownDataText(?ThesisContext $context, PlanPrompts $plan, string $title = 'DATOS CLAVE REGISTRADOS'): string
    {
        $lines = array_map(fn ($item) => "- {$item['label']}: {$item['value']}", $this->knownData($context, $plan));

        return $lines ? "{$title}:\n" . implode("\n", $lines) : '';
    }

    // Catálogo completo con el valor actual de cada clave, para el asistente de co-creación
    public function catalogForPrompt(?ThesisContext $context, PlanPrompts $plan): string
    {
        $data = $context?->assistant_context ?? [];
        $lines = [];

        foreach ($plan->assistantContextCatalog() as $key => $item) {
            $value = trim((string) ($data[$key] ?? ''));
            $lines[] = "- {$key} ({$item['label']}): {$item['description']} Valor actual: " . ($value !== '' ? $value : '(vacío)');
        }

        return implode("\n", $lines);
    }

    // Diagnóstico inicial con cada pregunta y su respuesta
    public function diagnosticText(?ThesisContext $context): string
    {
        $diagnostic = $context?->plan_context['diagnostico_inicial'] ?? [];

        if (empty($diagnostic)) {
            return '';
        }

        $questions = Question::whereIn('id', array_column($diagnostic, 'id_question'))->pluck('question_text', 'id');
        $lines = [];

        foreach ($diagnostic as $item) {
            $question = trim((string) ($questions[$item['id_question'] ?? 0] ?? ''));
            $lines[] = $question !== ''
                ? "- {$question} → {$item['answer']}"
                : "- {$item['answer']}";
        }

        return "DIAGNÓSTICO INICIAL DEL PROYECTO:\n" . implode("\n", $lines);
    }

    // Guarda los datos del catálogo que cambiaron; $allowReplace (Modificar respuesta) permite reemplazar
    public function saveKnownData(ThesisContext $context, PlanPrompts $plan, array $updates, int $questionId, bool $allowReplace = false): array
    {
        $catalog = $plan->assistantContextCatalog();
        $data = $context->assistant_context ?? [];
        $planContext = $context->plan_context ?? [];
        $history = $planContext[ConversationService::ORIGIN_KEY] ?? [];
        $changed = [];

        foreach ($updates as $key => $value) {
            if (!isset($catalog[$key]) || !is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);
            $current = trim((string) ($data[$key] ?? ''));

            if ($value === '' || $value === $current) {
                continue;
            }

            if ($current !== '' && !$allowReplace && !$this->extendsValue($current, $value)) {
                \Log::info('DATO CONOCIDO NO ACTUALIZADO: el valor nuevo no amplía el registrado', [
                    'conversation_id' => $context->conversation_id,
                    'question_id' => $questionId,
                    'clave' => $key,
                    'valor_actual' => $current,
                    'valor_dicho' => $value,
                ]);
                continue;
            }

            $entries = $this->historyEntries($history[$key] ?? null, $current);

            if (empty($entries) && $current !== '') {
                $entries[] = ['question' => 0, 'value' => $current];
            }

            $entries[] = ['question' => $questionId, 'value' => $value];

            $history[$key] = $entries;
            $data[$key] = $value;
            $changed[] = $key;
        }

        if ($changed) {
            $planContext[ConversationService::ORIGIN_KEY] = $history;
            $context->assistant_context = $data;
            $context->plan_context = $planContext;
            $context->save();
        }

        return $changed;
    }

    // Devuelve cada clave al valor que tenía antes de esas preguntas (quien llama guarda el contexto)
    public function revertKnownData(array &$assistantData, array &$planContext, array $questionIds): void
    {
        $questionIds = array_map('intval', $questionIds);
        $history = $planContext[ConversationService::ORIGIN_KEY] ?? [];

        foreach ($history as $key => $raw) {
            $entries = $this->historyEntries($raw, (string) ($assistantData[$key] ?? ''));
            $remaining = array_values(array_filter($entries, fn ($entry) => !in_array((int) $entry['question'], $questionIds, true)));

            if (count($remaining) === count($entries)) {
                continue;
            }

            if ($remaining) {
                $assistantData[$key] = end($remaining)['value'];
                $history[$key] = $remaining;
            } else {
                unset($assistantData[$key], $history[$key]);
            }
        }

        $planContext[ConversationService::ORIGIN_KEY] = $history;
    }

    // Prellena los datos conocidos con el diagnóstico (si falla, se llenan después en el chat)
    public function prefillKnownDataFromDiagnostic(ThesisContext $context, PlanPrompts $plan): void
    {
        try {
            $diagnostic = $this->diagnosticText($context);

            if ($diagnostic === '') {
                return;
            }

            $result = $this->openAIService->json($this->promptService->promptExtractKnownData(
                $plan,
                $this->catalogForPrompt($context, $plan),
                'TEXTO DEL USUARIO',
                $diagnostic
            )) ?? [];
            $catalog = $plan->assistantContextCatalog();
            $data = $context->assistant_context ?? [];

            foreach ((array) ($result['datos'] ?? []) as $key => $value) {
                // Solo claves del catálogo y solo si están vacías: el diagnóstico es el punto de partida
                if (isset($catalog[$key]) && is_scalar($value) && trim((string) $value) !== '' && trim((string) ($data[$key] ?? '')) === '') {
                    $data[$key] = trim((string) $value);
                }
            }

            $context->assistant_context = $data;
            $context->save();
        } catch (\Throwable $e) {
            \Log::warning('No se pudo prellenar los datos conocidos desde el diagnóstico: ' . $e->getMessage());
        }
    }

    // Modificar respuesta (de fondo): registra los datos del texto y reemplaza los que contradice
    public function knownDataFromModification(ThesisContext $context, PlanPrompts $plan, string $text, int $questionId): void
    {
        if (trim($text) === '') {
            return;
        }

        try {
            $result = $this->openAIService->json($this->promptService->promptExtractKnownData(
                $plan,
                $this->catalogForPrompt($context, $plan),
                'TEXTO MODIFICADO POR EL USUARIO',
                $text
            )) ?? [];

            $updates = is_array($result['datos'] ?? null) ? $result['datos'] : [];

            foreach ((array) ($result['contradicciones'] ?? []) as $item) {
                if (is_array($item) && !empty($item['clave']) && trim((string) ($item['valor_dicho'] ?? '')) !== '') {
                    $updates[$item['clave']] = $item['valor_dicho'];
                }
            }

            if ($updates) {
                $this->saveKnownData($context, $plan, $updates, $questionId, true);
            }
        } catch (\Throwable $e) {
            \Log::warning('No se pudieron actualizar los datos conocidos de la modificación: ' . $e->getMessage());
        }
    }

    // Registra los datos que el usuario dio en un mensaje del chat (llamada aparte; si falla, no interrumpe)
    public function extractKnownDataFromMessage(ThesisContext $context, PlanPrompts $plan, string $message, int $questionId): void
    {
        if (trim($message) === '') {
            return;
        }

        try {
            $result = $this->openAIService->json($this->promptService->promptExtractKnownData(
                $plan,
                $this->catalogForPrompt($context, $plan),
                'MENSAJE DEL USUARIO',
                $message
            )) ?? [];

            if (!empty($result['datos']) && is_array($result['datos'])) {
                $this->saveKnownData($context, $plan, $result['datos'], $questionId);
            }

            // Lo que el usuario dijo distinto a lo registrado no cambia la memoria; queda en el log
            if (!empty($result['contradicciones'])) {
                \Log::info('DATO CONOCIDO CONTRADICHO EN EL CHAT (no se cambió)', [
                    'conversation_id' => $context->conversation_id,
                    'question_id' => $questionId,
                    'contradicciones' => $result['contradicciones'],
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('No se pudieron registrar los datos del mensaje del asistente: ' . $e->getMessage());
        }
    }

    private function extendsValue(string $current, string $new): bool
    {
        $normalize = fn (string $text) => strtr(mb_strtolower($text), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u']);
        $current = $normalize($current);
        $new = $normalize($new);

        preg_match_all('/\d+(?:[.,]\d+)?/u', $current, $numbers);
        preg_match_all('/\d+(?:[.,]\d+)?/u', $new, $newNumbers);
        if (array_diff($numbers[0], $newNumbers[0])) {
            return false;
        }

        preg_match_all('/\p{L}{4,}/u', $current, $words);
        $words = array_unique($words[0]);
        if (empty($words)) {
            return true;
        }

        $kept = count(array_filter($words, fn ($word) => str_contains($new, $word)));

        return $kept / count($words) >= 0.6;
    }

    // Historial de una clave. El formato anterior guardaba solo el id de la pregunta
    private function historyEntries($raw, string $currentValue): array
    {
        if (is_array($raw)) {
            return array_values(array_filter($raw, fn ($entry) => is_array($entry) && isset($entry['question'])));
        }

        if (is_numeric($raw)) {
            return [['question' => (int) $raw, 'value' => $currentValue]];
        }

        return [];
    }

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
     * Construir el prompt para la IA según el plan de la conversación.
     */
    $prompt = $this->promptService->promptCoherenceContext(
        $this->planPromptResolver->forConversation($conversationId),
        $contextText,
        $response
    );

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