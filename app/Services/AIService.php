<?php

namespace App\Services;

use App\Models\ThesisContext;
use App\Services\Prompts\PlanPromptResolver;

class AIService
{
    public function __construct(
        private OpenAIService $openAIService,
        private PromptService $promptService,
        private PlanPromptResolver $planPromptResolver
    ) {
    }

    /**
     * Guarda o actualiza el contexto de una conversación.
     */
    public function guardarContexto(  int $conversationId,  int $userPlanId,  array $datos  ): ThesisContext {

        return ThesisContext::updateOrCreate(
            [
                'conversation_id' => $conversationId,
            ],
            [
                'user_plan_id'     => $userPlanId,
                'business_name'    => $datos['business_name'] ?? null,
                'business_sector'  => $datos['business_sector'] ?? null,
                'product_service'  => $datos['product_service'] ?? null,
                'target_customer'  => $datos['target_customer'] ?? null,
                'main_problem'     => $datos['main_problem'] ?? null,
                'value_proposition'=> $datos['value_proposition'] ?? null,
                'context_summary'  => $datos['context_summary'] ?? null,
                'embedding'        => $datos['embedding'] ?? null,
                'confidence'       => $datos['confidence'] ?? 0,
            ]
        );
    }


    /**
     * Obtiene el contexto de una conversación.
     */
    public function obtenerContexto(  int $conversationId ): ?ThesisContext {

        return ThesisContext::where(
            'conversation_id',
            $conversationId
        )->first();
    }


    /**
     * Analiza una respuesta utilizando el contexto
     * almacenado de la tesis.
     */
   public function analizarIncongruencia(int $conversationId, string $pregunta, string $respuesta): array {

        $context = $this->obtenerContexto($conversationId);

        // Pasamos $conversationId aquí
        $contexto = $this->construirContexto($context, $conversationId);

        if (empty(trim($contexto))) {
            return [
                'hay_incongruencia' => false,
                'nivel' => 'ninguno',
                'descripcion' => null,
                'elementos_contradictorios' => [],
            ];
        }

        $prompt = $this->construirPromptIncongruencia(
            $pregunta,
            $respuesta,
            $contexto
        );

        $resultado = $this->openAIService->json($prompt);

        return $this->normalizarResultado($resultado);
    }

    // Mismo contexto que usa el control de coherencia (resumen, datos conocidos y diagnóstico)
    private function construirContexto(?ThesisContext $context, int $conversationId): string {

        return app(ThesisContextService::class)->buildValidationText($conversationId);
    }

    /**
     * Construye el prompt para detectar incongruencias.
     */
    private function construirPromptIncongruencia( string $pregunta, string $respuesta,  string $contexto  ): string {

        return <<<PROMPT
Eres un asistente especializado en revisión de trabajos de investigación y tesis.

Debes detectar únicamente INCONGRUENCIAS CLARAS Y RELEVANTES.

No consideres como incongruencia:
- errores ortográficos;
- errores de redacción;
- diferencias de estilo;
- información adicional que no contradiga el contexto;
- información que simplemente no esté registrada en el contexto.

CONTEXTO DE LA TESIS:

{$contexto}

PREGUNTA:

{$pregunta}

RESPUESTA:

{$respuesta}

Analiza la respuesta comparándola con el contexto.

Si existe una contradicción clara, indícala.

Si no existe evidencia suficiente para afirmar una contradicción,
considera que no existe incongruencia.

RESPONDE ÚNICAMENTE EN JSON:

{
    "hay_incongruencia": true,
    "nivel": "alto",
    "descripcion": "Descripción concreta de la contradicción.",
    "elementos_contradictorios": [
        "Dato del contexto",
        "Dato de la respuesta"
    ]
}

Si no existe incongruencia:

{
    "hay_incongruencia": false,
    "nivel": "ninguno",
    "descripcion": null,
    "elementos_contradictorios": []
}

El campo nivel solamente puede ser:
"alto", "medio" o "ninguno".

PROMPT;
    }


    /**
     * Normaliza la respuesta de OpenAI.
     */
    private function normalizarResultado( array $resultado  ): array {

        return [
            'hay_incongruencia' => (bool) (
                $resultado['hay_incongruencia'] ?? false
            ),

            'nivel' => $resultado['nivel'] ?? 'ninguno',

            'descripcion' => $resultado['descripcion'] ?? null,

            'elementos_contradictorios' => is_array(
                $resultado['elementos_contradictorios'] ?? null
            )
                ? $resultado['elementos_contradictorios']
                : [],
        ];
    }

    /**
     * Procesa y consolida un capítulo terminado:
    */
    public function consolidarCapituloPlanContext(int $conversationId, int $numeroCapitulo, string $chapterTitle, array $qaList): void
    {
        $conversation = \App\Models\Conversation::find($conversationId);
        if (!$conversation) return;

        // Armamos el texto con las preguntas y respuestas del capítulo
        $contenidoCapitulo = "";
        foreach ($qaList as $item) {
            $contenidoCapitulo .= "P: {$item['pregunta']}\n";
            $contenidoCapitulo .= "R: {$item['respuesta']}\n\n";
        }

        $planPrompts = $this->planPromptResolver->forConversation($conversation);

       // 1. Plantilla base fija obligatoria
        $basePlanContext = [
            'resumen_general' => null,
            'cap_1' => null,
            'cap_2' => null,
            'cap_3' => null,
            'cap_4' => null,
            'cap_5' => null,
            'cap_6' => null,
            'cap_7' => null,
            'cap_8' => null,
            'cap_9' => null,
            'cap_10' => null,
        ];

        // 2. Obtener o crear el registro thesis_context
        $thesisContext = \App\Models\ThesisContext::firstOrCreate(
            ['conversation_id' => $conversationId],
            [
                'user_plan_id' => $conversation->user_plan_id,
                'assistant_context' => $planPrompts->assistantContextDefaults(),
                'plan_context' => $basePlanContext
            ]
        );
        
        $planContext = array_merge($basePlanContext, $thesisContext->plan_context ?? []);
        $resumenGeneralAnterior = $planContext['resumen_general'] ?? null;

        $prompt = $this->promptService->promptConsolidateChapter(
            $planPrompts,
            $numeroCapitulo,
            $chapterTitle,
            $contenidoCapitulo,
            $resumenGeneralAnterior
        );

        try {
            $resultado = $this->openAIService->json($prompt);

            $claveCap = "cap_{$numeroCapitulo}";
            $planContext[$claveCap] = $resultado['resumen_capitulo'] ?? '';
            $planContext['resumen_general'] = $resultado['nuevo_resumen_general'] ?? $resultado['resumen_capitulo'] ?? '';

            $thesisContext->plan_context = $planContext;
            $thesisContext->save();

        } catch (\Throwable $e) {
            \Log::error("Error al consolidar plan_context capítulo {$numeroCapitulo}: " . $e->getMessage());
        }
    }

}