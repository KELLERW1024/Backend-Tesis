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
   public function analizarIncongruencia(int $conversationId, string $pregunta, string $respuesta): array {

        $context = $this->obtenerContexto($conversationId);

        // Pasamos $conversationId aquí
        $contexto = $this->construirContexto($context, $conversationId);

        \Log::info('EVALUANDO INCONGRUENCIA CON IA', [
            'conversation_id' => $conversationId,
            'pregunta' => $pregunta,
            'respuesta' => $respuesta,
            'contexto_usado' => $contexto,
        ]);

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

        \Log::info('RESULTADO INCONGRUENCIA IA', [
            'resultado' => $resultado
        ]);

        return $this->normalizarResultado($resultado);
    }

    private function construirContexto(?ThesisContext $context, int $conversationId): string {

        $resumenGeneral = $context?->plan_context['resumen_general'] ?? null;
        $assistantData = $context?->assistant_context ?? [];

        // Si ya hay resumen general
        if (!empty($resumenGeneral)) {
            $texto = "RESUMEN CONSOLIDADO DEL PROYECTO:\n" . $resumenGeneral . "\n\n";

            $datosValidos = array_filter($assistantData, fn($v) => !empty($v));
            if (!empty($datosValidos)) {
                $texto .= "DATOS CLAVE REGISTRADOS:\n";
                foreach ($datosValidos as $k => $v) {
                    $texto .= "- " . ucfirst(str_replace('_', ' ', $k)) . ": {$v}\n";
                }
            }
            return trim($texto);
        }

        // ====================================================
        // Incluir el Diagnóstico Inicial + Historial reciente
        // ====================================================
        $diagnosticoInicial = $context?->plan_context['diagnostico_inicial'] ?? [];
        $textoDiagnostico = "";
        
        if (!empty($diagnosticoInicial)) {
            $textoDiagnostico = "DIAGNÓSTICO INICIAL DEL PROYECTO (8 PREGUNTAS):\n";
            foreach ($diagnosticoInicial as $diag) {
                $textoDiagnostico .= "- Respuesta: {$diag['answer']}\n";
            }
            $textoDiagnostico .= "\n";
        }


        // Capítulo 1: Usamos app() para no romper dependencias circulares
        $conversationService = app(ConversationService::class);
        $historial5 = $conversationService->getHistoryLimit10($conversationId);

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
                'assistant_context' => [
                    'nombre_proyecto_empresa' => null,
                    'alcance_geografico' => null,
                    'capacidad_operativa' => null,
                    'precio_o_tarifa' => null,
                    'costos_o_inversion' => null,
                    'demanda_o_volumen' => null,
                    'impacto_o_meta_financiera' => null,
                ],
                'plan_context' => $basePlanContext
            ]
        );
        
        $planContext = array_merge($basePlanContext, $thesisContext->plan_context ?? []);
        $resumenGeneralAnterior = $planContext['resumen_general'] ?? null;

        $prompt = <<<PROMPT
            Eres un director académico y metodológico de planes de negocio y proyectos de tesis.
            Se ha completado el capítulo {$numeroCapitulo}: "{$chapterTitle}".

            RESPUESTAS COMPLETADAS EN ESTE CAPÍTULO:
            {$contenidoCapitulo}

            RESUMEN GENERAL ACUMULADO PREVIO (Capítulos anteriores):
            {$resumenGeneralAnterior}

            Tu objetivo:
            1. "resumen_capitulo": Redacta un resumen breve, preciso y sustancial de lo respondido en este capítulo (máximo 120-180 palabras), destacando datos clave, decisiones y cifras.
            2. "nuevo_resumen_general": Si no había resumen general previo, usa el resumen de este capítulo. Si ya existía uno previo, intégralos en un solo texto fluido y unificado (máximo 120-1200 palabras) que sintetice toda la columna vertebral del proyecto hasta el momento.

            RESPONDE ÚNICAMENTE CON ESTE FORMATO JSON:
            {
                "resumen_capitulo": "Texto del resumen del capítulo actual...",
                "nuevo_resumen_general": "Texto consolidado de todos los capítulos hasta el actual..."
            }
            PROMPT;

        try {
            $resultado = $this->openAIService->json($prompt);

            $claveCap = "cap_{$numeroCapitulo}";
            $planContext[$claveCap] = $resultado['resumen_capitulo'] ?? '';
            $planContext['resumen_general'] = $resultado['nuevo_resumen_general'] ?? $resultado['resumen_capitulo'] ?? '';

            $thesisContext->plan_context = $planContext;
            $thesisContext->save();

            \Log::info("Capítulo {$numeroCapitulo} consolidado en plan_context", [
                'clave' => $claveCap,
                'conversation_id' => $conversationId
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error al consolidar plan_context capítulo {$numeroCapitulo}: " . $e->getMessage());
        }
    }

}