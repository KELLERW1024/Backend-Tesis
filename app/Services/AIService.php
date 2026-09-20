<?php

namespace App\Services;

use App\Models\ThesisContext;

class AIService
{
    public function __construct(
        private OpenAIService $openAIService
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
    public function analizarIncongruencia(  int $conversationId,  string $pregunta,  string $respuesta ): array {

        $context = $this->obtenerContexto($conversationId);

        if (!$context) {
            return [
                'hay_incongruencia' => false,
                'nivel' => 'ninguno',
                'descripcion' => null,
                'elementos_contradictorios' => [],
            ];
        }

        $contexto = $this->construirContexto($context);

        $prompt = $this->construirPromptIncongruencia(
            $pregunta,
            $respuesta,
            $contexto
        );

        $resultado = $this->openAIService->json($prompt);

        return $this->normalizarResultado($resultado);
    }


    /**
     * Convierte el registro de thesis_context
     * en texto para la IA.
     */
    private function construirContexto( ThesisContext $context  ): string {

        return <<<CONTEXTO
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

Resumen:
{$context->context_summary}
CONTEXTO;
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
}