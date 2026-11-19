<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\PlanNode;
use App\Models\UserPlan;
use App\Models\UserSubscription;
use App\Models\Conversation;
use App\Models\Question;
use App\Models\ConversationSectionProgress;
use App\Services\ConversationService;
use App\Services\AnswerValidationService;
use App\Services\ThesisContextService;
use App\Services\AttachmentTextService;
use App\Services\CalculationService;
use App\Services\AnswerModificationService;
use App\Services\PromptService;
use App\Services\OpenAIService;
use App\Services\Prompts\PlanPromptResolver;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;

class ConversationController extends Controller
{
    // Máximo de caracteres del texto de un archivo adjunto en el asistente
    private const ASSISTANT_ATTACHMENT_MAX_CHARS = 8000;

    private ConversationService $conversationService;

    public function __construct(ConversationService $conversationService,
    protected PromptService $promptService, protected OpenAIService $openAIService,
    protected PlanPromptResolver $planPromptResolver,
    protected AnswerValidationService $answerValidationService,
    protected ThesisContextService $thesisContextService )
    {
        $this->conversationService = $conversationService;
    }
    
    // =====================================================================================
    // GUARDA LAS RESPUESTAS DIAGNOSTICO Y CALIFICA EL RUBRO DEL PLAN ADEMAS DE FILTRAR E INSERTAR LOS NODOS DEL PLAN 
    // ======================================================================================
    public function saveAnswerDiagnostic(Request $request , ConversationService $conversationService)
    {
        $validated = $request->validate([
            'id_subscription_conversation' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.id_question' => 'required|integer',
            'questions.*.answer' => 'required|string|min:5',
        ]);

        $conversation = Conversation::find($validated['id_subscription_conversation']);
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversación no encontrada'
            ], 404);
        }

        $userPlanId = $conversation?->user_plan_id;
        $userPlan = UserPlan::find( $userPlanId );
        $planId= $userPlan->plan_id;

        try {
            $user = auth()->user();

            // 1. Recolectar las respuestas del diagnóstico estructuradas
            $diagnosticoArray = [];
            foreach ($validated['questions'] as $qItem) {
                // Opcional: puedes buscar el texto de la pregunta master si deseas enriquecer el JSON
                $diagnosticoArray[] = [
                    'id_question' => $qItem['id_question'],
                    'answer' => trim($qItem['answer'])
                ];

                // Guardar en user_answers_diagnostic o user_answers según corresponda tu arquitectura
                UserAnswers::updateOrCreate(
                    [
                        'conversation_id' => $conversation->id,
                        'user_id' => $user->id,
                        'question_id' => $qItem['id_question'],
                    ],
                    [
                        'answer' => trim($qItem['answer']),
                    ]
                );
            }

            // 2. Guardar/Actualizar el diagnóstico inicial dentro del JSON plan_context en ThesisContext
            $basePlanContext = [
                'resumen_general' => null,
                'diagnostico_inicial' => $diagnosticoArray,
                'cap_1' => null, 'cap_2' => null, 'cap_3' => null, 'cap_4' => null,
                'cap_5' => null, 'cap_6' => null, 'cap_7' => null, 'cap_8' => null,
            ];

            $thesisContext = \App\Models\ThesisContext::firstOrCreate(
                ['conversation_id' => $conversation->id],
                [
                    'user_plan_id' => $userPlanId,
                    'plan_context' => $basePlanContext
                ]
            );

            $planContextData = array_merge($basePlanContext, $thesisContext->plan_context ?? []);
            $planContextData['diagnostico_inicial'] = $diagnosticoArray;
            // Los datos de la portada del Word salen del diagnóstico: con un diagnóstico nuevo se regeneran
            unset($planContextData['portada']);

            $thesisContext->plan_context = $planContextData;
            $thesisContext->save();

            $this->thesisContextService->prefillKnownDataFromDiagnostic(
                $thesisContext,
                $this->planPromptResolver->forConversation($conversation)
            );

            // 3. Obtener los nodos base del plan para que la IA los filtre basados en el diagnóstico completo
            $plansNode = PlanNode::whereNull('user_plan_id')
                ->where('plan_id', $planId)
                ->oldest('id')
                ->get();

            // Los nodos obligatorios (mandatoryCodes y fase 2) se incluyen siempre; la IA elige solo entre los opcionales
            $planPrompts = $this->planPromptResolver->forConversation($conversation);

            [$mandatoryNodes, $optionalNodes] = $plansNode->partition(
                fn ($node) => (int) $node->execution_phase === 2
                    || ((int) $node->execution_phase !== 0 && $planPrompts->isMandatory($node->codigo))
            );

            $nodesForAI = $optionalNodes->map(function ($node) {
                return [
                    'id' => $node->id,
                    'codigo' => $node->codigo,
                    'titulo' => $node->titulo,
                ];
            })->values()->toArray();

            // Títulos de capítulos y apartados obligatorios, para que la IA no duplique su contenido
            $includedTitles = $mandatoryNodes
                ->filter(fn ($node) => (int) $node->nivel <= 2 || substr_count((string) $node->codigo, '.') <= 1)
                ->map(fn ($node) => trim(($node->codigo ? $node->codigo . ' ' : '') . rtrim($node->titulo, '.')))
                ->implode('; ');

            // 4. Llamar a la IA con el nuevo prompt basado en el diagnóstico inicial completo
            // ... Recolectar respuestas del diagnóstico y obtener nodos maestros
            // SE REALIZARA UN FILTRO DE NODOS POR PLAN EN EL PROMPT

        $promptFiltroNodes = $this->promptService->promptFiltroNodePorDiagnostico(
            $planPrompts,
            $diagnosticoArray,
            $nodesForAI,
            $includedTitles
        );

        $resultFiltro = $this->openAIService->json($promptFiltroNodes);

            // Opcionales que eligió la IA (solo ids que existen en la lista enviada) + obligatorios siempre
            $nodeIdsFiltrados = array_values(array_unique(array_merge(
                array_intersect(
                    collect($resultFiltro['nodes'] ?? [])->pluck('id')->map(fn ($id) => (int) $id)->toArray(),
                    $optionalNodes->pluck('id')->toArray()
                ),
                $mandatoryNodes->pluck('id')->toArray()
            )));

            // =========================================================================
            // MEDIDA DE SEGURIDAD: ASEGURAR QUE NINGÚN SUBNODO SE QUEDE SIN SU PADRE/CAPÍTULO
            // =========================================================================
            $allValidIds = $nodeIdsFiltrados;
            foreach ($nodeIdsFiltrados as $id) {
                $current = PlanNode::find($id);
                // Subir recursivamente por el parent_id hasta llegar al capítulo raíz
                while ($current && $current->parent_id !== null) {
                    $allValidIds[] = $current->parent_id;
                    $current = PlanNode::find($current->parent_id);
                }
            }
            // Eliminar duplicados y ordenar para mantener la consistencia
            $nodeIdsFiltrados = array_values(array_unique($allValidIds));
            // =========================================================================

            $planNodes = PlanNode::whereIn('id', $nodeIdsFiltrados)
                ->orderBy('id', 'asc')
                ->get();

            $questions = Question::whereIn('plan_node_id', $planNodes->pluck('id'))
                ->orderBy('plan_node_id', 'asc')
                ->orderBy('order_index', 'asc')
                ->get();
             
            // Registrar y re-numerar con la estructura protegida
            $conversationService->registerPlanNodesUser($planNodes, $questions, $planId, $userPlanId);

            // Actualizar estado de conversación (sin usar el rubro anterior)
            $conversation->update([
                'summary' => 'Diagnóstico inicial procesado con IA',
            ]);

            return response()->json([
                'success' => true,
                'message' => ' Respuestas se guardaron correctamente ',
                'errors' => null
            ], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error saving reply',
                'errors' => [
                    'exception' => $e->getMessage()
                ]
            ], 500);
        }
    }
    // FILTRA LOS NODOS A REGISTRARSE
public function flattenNodeIds(array $nodes): array
{
    $ids = [];

    foreach ($nodes as $node) {

        if (!is_array($node)) {
            continue;
        }

        // Agregar el ID del nodo actual
        if (isset($node['id']) && is_numeric($node['id'])) {
            $ids[] = (int) $node['id'];
        }

        // Recorrer los hijos
        if (isset($node['subnodes']) && is_array($node['subnodes'])) {
            $ids = array_merge(
                $ids,
                $this->flattenNodeIds($node['subnodes'])
            );
        }
    }

    return array_values(array_unique($ids));
}

    public function assistantChat(Request $request)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer|exists:conversations,id',
            'idQuestion' => 'required|integer|exists:questions,id',
            'messages' => 'nullable|array',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string',
            'user_message' => 'nullable|string',
            'current_answer' => 'nullable|string|max:20000',
            // Archivos adjuntos en el asistente, ya leídos (assistantReadAttachment): nombre y texto
            'attachments' => 'nullable|array|max:2',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.text' => 'required|string|max:12000',
        ]);

        $conversation = Conversation::with(['userPlan.plan'])->findOrFail($validated['idConversation']);

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Pago no aprobado.'
            ], 403);
        }

        $question = Question::findOrFail($validated['idQuestion']);
        $planNode = \App\Models\PlanNode::find($question->plan_node_id);

        // =========================================================================
        // OBTENER CONTEXTOS LIGEROS DE THESIS_CONTEXT Y PREPARAR MEMORIA
        // =========================================================================
        $thesisContext = \App\Models\ThesisContext::firstOrCreate(
            ['conversation_id' => $conversation->id],
            ['user_plan_id' => $conversation->user_plan_id, 'confidence' => 0]
        );
        $planPrompts = $this->planPromptResolver->forConversation($conversation);

        $resumenGeneralAnterior = $thesisContext->plan_context['resumen_general'] ?? '';
        $textoMemoria = "";

        // 1. EL PASADO: Si hay resumen general, lo usamos. Si no, usamos el diagnóstico inicial
        //    (con cada pregunta y su respuesta).
        if (!empty(trim($resumenGeneralAnterior))) {
            $textoMemoria = "RESUMEN DE CAPÍTULOS COMPLETADOS:\n" . $resumenGeneralAnterior . "\n";
        } else {
            $diagnostico = $this->thesisContextService->diagnosticText($thesisContext);
            if ($diagnostico !== '') {
                $textoMemoria = $diagnostico . "\n";
            }
        }

        // 2. EL PRESENTE: SIEMPRE jalamos las últimas 5 respuestas formales del capítulo en curso
        $historialReciente = $this->conversationService->getHistoryLimit10($conversation->id);
        if (!empty($historialReciente)) {
            $textoMemoria .= "\nÚLTIMAS RESPUESTAS DEL CAPÍTULO EN CURSO:\n";
            foreach ($historialReciente as $item) {
                $textoMemoria .= "- {$item['content']}\n";
            }
        }

        $resumenGeneral = trim($textoMemoria);
        if (empty($resumenGeneral)) {
            $resumenGeneral = 'No hay datos previos.';
        }

        // Tomar únicamente los últimos 6 turnos del chat actual
        $chatMessages = $validated['messages'] ?? [];
        $lastMessage = end($chatMessages) ?: null;

        $alreadyIncluded = $lastMessage
            && ($lastMessage['role'] ?? '') === 'user'
            && trim($lastMessage['content'] ?? '') === trim((string) ($validated['user_message'] ?? ''));

        if (!empty($validated['user_message']) && !$alreadyIncluded) {
            $chatMessages[] = [
                'role' => 'user',
                'content' => $validated['user_message']
            ];
        }
        $chatMessages = array_slice($chatMessages, -6);

        // =========================================================================
        // LOG PARA DEPURAR QUÉ CONTEXTO ESTÁ RECIBIENDO REALMENTE LA IA
        // =========================================================================

        // Construir el prompt: el catálogo del plan con el valor actual de cada dato
        $prompt = $this->promptService->promptAssistantCoCreation($planPrompts, [
            'question' => $question->question_text,
            'detail' => $question->question_detail,
            'objective' => $planNode?->objective ?? $planNode?->titulo ?? '',
            'resumen_general' => $resumenGeneral,
            'assistant_context_text' => $this->thesisContextService->catalogForPrompt($thesisContext, $planPrompts),
            'chat_messages' => $chatMessages,
            'structure' => $this->thesisContextService->structureText($conversation->id),
            'current_answer' => $validated['current_answer'] ?? '',
            'attachments_text' => $this->attachmentsText($validated['attachments'] ?? []),
        ]);

        try {
            $aiResult = $this->openAIService->json($prompt);

            // =========================================================================
            // Al abrir el asistente se registra lo ya escrito en la respuesta; después, cada mensaje del chat
            $isOpening = empty($validated['messages']) && trim((string) ($validated['user_message'] ?? '')) === '';
            $this->thesisContextService->extractKnownDataFromMessage(
                $thesisContext,
                $planPrompts,
                $isOpening ? (string) ($validated['current_answer'] ?? '') : (string) ($validated['user_message'] ?? ''),
                $question->id
            );

            // Las cifras calculadas vienen como {{operación}}: las calcula CalculationService
            $calculator = app(CalculationService::class);

            return response()->json([
                'success' => true,
                'data' => [
                    'chat_message' => $calculator->resolve((string) ($aiResult['chat_message'] ?? ''))['text'],
                    'draft_text' => $calculator->resolve((string) ($aiResult['draft_text'] ?? ''))['text'],
                    'is_complete' => (bool)($aiResult['is_complete'] ?? false),
                    'progress_percentage' => (int)($aiResult['progress_percentage'] ?? 20),
                    'metrics_summary' => $aiResult['metrics_summary'] ?? null,
                    
                    'brechas_detectadas' => $aiResult['brechas_detectadas'] ?? [],
                    // El estado lo propone el modelo; aquí se verifica
                    'variables_negocio' => $this->checkBusinessVariables(
                        $this->calculateVariables((array) ($aiResult['variables_negocio'] ?? []), $calculator),
                        $this->userTextOfChat($validated)
                    ),
                    'formulas_utilizadas' => $aiResult['formulas_utilizadas'] ?? [],
                    
                    'contexto_general' => $resumenGeneral,
                    // [{key, label, value}] en el orden del catálogo
                    'datos_conocidos' => $this->thesisContextService->knownData($thesisContext, $planPrompts)
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::error('Assistant AI error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con el asistente de redacción.'
            ], 500);
        }
    }

    // El valor de una variable "Calculado" se obtiene de su fórmula, no del modelo
    private function calculateVariables(array $variables, CalculationService $calculator): array
    {
        foreach ($variables as &$variable) {
            if (!is_array($variable)) {
                continue;
            }

            $value = (string) ($variable['valor'] ?? '');
            $formula = str_replace(['{{', '}}'], '', (string) ($variable['formula'] ?? ''));
            $variable['formula'] = $formula;

            if (str_contains($value, '{{')) {
                $variable['valor'] = $calculator->resolve($value)['text'];
                continue;
            }

            if (($variable['estado'] ?? '') === 'Calculado' && ($result = $calculator->evaluateFormula($formula)) !== null) {
                $formatted = $calculator->format($result);
                $variable['valor'] = preg_match('/\d/', $value)
                    ? preg_replace('/\d[\d,]*(?:\.\d+)?/', $formatted, $value, 1)
                    : $formatted;
            }
        }
        unset($variable);

        return $variables;
    }

    // Corrige el estado de cada variable: solo cifras; "Calculado" exige operación; lo que dio el estudiante
    // es "Dato del usuario" y lo demás "Supuesto"
    private function checkBusinessVariables(array $variables, string $userText): array
    {
        $numbers = function (string $text): array {
            preg_match_all('/\d+(?:[.,]\d+)?/u', $text, $match);
            return array_map(fn ($number) => str_replace(',', '.', $number), $match[0]);
        };

        $userNumbers = $numbers($userText);
        $result = [];

        foreach ($variables as $variable) {
            if (!is_array($variable)) {
                continue;
            }

            $valueNumbers = $numbers((string) ($variable['valor'] ?? ''));

            if (empty($valueNumbers)) {
                continue;
            }

            // "S/" (soles) no es una división
            $formula = preg_replace('/\bS\/\.?/iu', '', (string) ($variable['formula'] ?? ''));
            $hasOperation = (bool) preg_match('/[+*\/×÷=]|\d\s*[-x%]\s*\d/u', $formula);
            $fromUser = !array_diff($valueNumbers, $userNumbers);
            $state = $variable['estado'] ?? '';

            if (!$hasOperation && $fromUser) {
                $state = 'Dato del usuario';
            } elseif ($state === 'Calculado' && !$hasOperation) {
                $state = 'Supuesto';
            } elseif ($state === 'Dato del usuario' && !$fromUser) {
                $state = $hasOperation ? 'Calculado' : 'Supuesto';
            } elseif (!in_array($state, ['Calculado', 'Supuesto', 'Dato del usuario'], true)) {
                $state = $hasOperation ? 'Calculado' : 'Supuesto';
            }

            $variable['estado'] = $state;

            if ($state === 'Dato del usuario') {
                $variable['formula'] = '';
            }

            $result[] = $variable;
        }

        return $result;
    }

    // Lee un adjunto de Impulsa una sola vez (el modal reenvía el texto) y completa los datos conocidos
    public function assistantReadAttachment(Request $request, AttachmentTextService $attachmentText)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer|exists:questions,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $conversation = Conversation::where('id', $validated['idConversation'])
            ->whereHas('userPlan', fn ($query) => $query->where('user_id', auth()->id()))
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversación no encontrada.'], 404);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // Del Excel solo se usa la primera hoja (mismo aviso que al guardar la respuesta)
        if (in_array($extension, ['xls', 'xlsx']) && app(\App\Services\ExcelService::class)->sheetsCount($file->getRealPath()) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo tiene varias hojas. Deja solo la que contiene tu información y vuelve a intentarlo.',
            ], 422);
        }

        $extraction = $attachmentText->extract([$file]);

        if ($extraction['error']) {
            return response()->json(['success' => false, 'message' => $extraction['error']], $extraction['status']);
        }

        $isImage = $extraction['image'] !== '';
        $text = trim($isImage ? $extraction['image'] : $extraction['document']);

        // Un texto muy largo encarecería cada mensaje del chat: se recorta
        $truncated = mb_strlen($text) > self::ASSISTANT_ATTACHMENT_MAX_CHARS;
        if ($truncated) {
            $text = mb_substr($text, 0, self::ASSISTANT_ATTACHMENT_MAX_CHARS);
        }

        $thesisContext = \App\Models\ThesisContext::firstOrCreate(
            ['conversation_id' => $conversation->id],
            ['user_plan_id' => $conversation->user_plan_id, 'confidence' => 0]
        );
        $planPrompts = $this->planPromptResolver->forConversation($conversation);

        $this->thesisContextService->extractKnownDataFromMessage($thesisContext, $planPrompts, $text, (int) $validated['idQuestion']);

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $file->getClientOriginalName(),
                'type' => $isImage ? 'image' : 'document',
                'text' => $text,
                'truncated' => $truncated,
                'datos_conocidos' => $this->thesisContextService->knownData($thesisContext->fresh(), $planPrompts),
            ],
        ]);
    }

    // Texto de los adjuntos del asistente para el prompt
    private function attachmentsText(array $attachments): string
    {
        $parts = [];

        foreach ($attachments as $attachment) {
            $parts[] = "Archivo \"{$attachment['name']}\":\n" . trim($attachment['text']);
        }

        return implode("\n\n", $parts);
    }

    // Todo lo que escribió el estudiante: el cuadro de respuesta, sus archivos y sus mensajes del chat
    private function userTextOfChat(array $validated): string
    {
        $parts = [(string) ($validated['current_answer'] ?? '')];

        foreach ($validated['attachments'] ?? [] as $attachment) {
            $parts[] = (string) ($attachment['text'] ?? '');
        }

        foreach ($validated['messages'] ?? [] as $message) {
            if (($message['role'] ?? '') === 'user') {
                $parts[] = (string) ($message['content'] ?? '');
            }
        }

        $parts[] = (string) ($validated['user_message'] ?? '');

        return implode("\n", $parts);
    }

    // =====================================================================================
    // GUARDA LAS RESPUESTAS DE CADA PREGUNTA
    // ======================================================================================
    public function conversationSaveReply(Request $request, ConversationService $conversationService)
    {
        $validated = $request->validate([
            // 'idPlan' => 'required|integer',
            // 'idSection' => 'nullable|integer',
            'idConversation' => 'nullable|integer',
            'idQuestion' => 'nullable|integer',
            
            'reply' => 'nullable|string',

            'metadata' => 'nullable|string',
            'references' => 'nullable|string',
            'table' => 'nullable|string',

            'url_imagen_ia' => 'nullable|string',
            'desc_imagen_ia' => 'nullable|string',

            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx|max:20480', 

        ]);

        $files = $request->file('files') ?? [];
        $metadata = json_decode($request->metadata, true) ?? [];
        $references = json_decode($request->references, true) ?? [];
        
        try {

            $result =  $conversationService->saveUserAnswer($validated, $files , $metadata, $references );

            return response()->json([
                'success' => true,
                'message' => 'Reply saved successfully',
                'data' => $result,
                'errors' => null
            ], 200);

        } catch (\DomainException $e) {
            // Archivo no válido (por ejemplo, un Excel con varias hojas): el mensaje es para el usuario
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'errors' => null
            ], 422);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error saving reply',
                'data' => null,
                'errors' => [
                    'exception' => $e->getMessage()
                ]
            ], 500);
        }
    }

    // =====================================================================================
    // OBTIENE LAS CONVERSACIONES DEL USUARIO 
    // ======================================================================================
    public function conversationsUser()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversations = Conversation::with([
                            'subscription.package',
                            'subscription.payments',
                            'userPlan.plan',
                            'sectionProgress.section'  
                        ])->whereHas('userPlan', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'conversations' => $conversations->map(function ($conversation) {

                $package = $conversation->subscription?->package;
                $packageName = $package?->name ?? 'Paquete general';

                $packageIsActive = $package ? (bool) $package->is_active : true;
                
                $planName = $conversation->userPlan?->plan?->name ?? 'Plan asignado';

                $paymentStatus = $conversation->subscription?->payments
                    ?->sortByDesc('created_at')
                    ->first()
                    ?->status ?? 'pending';

                return [
                    'id' => $conversation->id,
                    'status' => $conversation->status,
                    'package_is_active' => $packageIsActive,
                    'title' => $conversation->title,
                    'plan_name' => $planName,
                    'package_name' => $packageName,
                    'payment_status' => $paymentStatus,
                ];
            }),
        ];

        return response()->json($data);
    }

    // =====================================================================================
    // VERIFICA SI LA CONVERSACION YA CUENTA CON EL DIAGNOSTICO
    // ======================================================================================
    public function getVerficationDiagnosticExist( Request $request )
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $idConversation = $request->get('idConversation');

         $conversation = Conversation::where('id', $idConversation)
            ->first();

        if (!$conversation) {
            return response()->json([
                'exists' => false,
                'message' => 'Conversation not found'
            ], 404);
        }

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'
            ], 403);
        }

        return response()->json([
            'exists' => true,
            'has_summary' => !empty($conversation->summary),
            'summary' => $conversation->summary,
            'status_structure' => (int) ($conversation->status_structure ?? 0),
        ]);

    }

     public function getDiagnosticoPlan( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        
        $userId = auth()->id();
        $idConversation = $request->get('idConversation');

        $conversation = Conversation::findOrFail($idConversation);

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'
            ], 403);
        }

        $userPlanId = $conversation->user_plan_id;

        $userPlan= UserPlan::findOrFail($userPlanId);

        $planId = $userPlan->plan_id;

        $planNode = PlanNode::whereNull('user_plan_id')
                                        ->where('plan_id', $planId )
                                        ->oldest('id')
                                        ->first();

        if (!$planNode) {
            return response()->json([
                'message' => 'No existe estructura para este PLAN'
            ], 404);
        }

        $questions = Question::where('plan_node_id', $planNode->id)
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'status' => 200,
            // 'plan_node' => [
            //     'id' => $planNode->id,
            //     'titulo' => $planNode->titulo,
            //     'objective' => $planNode->objective,
            //     'nivel' => $planNode->nivel,
            //     'codigo' => $planNode->codigo,
            // ],
            'questions' => $questions
        ]);

    }

    // =====================================================================================
    // OBTIENE LA ULTIMA PREGUNTA PARA RESPONDER Y VERIFCA SI LA PREGUNTA PUEDE SER RESPONDIDA 
    // ======================================================================================
    public function getConversationPlan(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $idConversation = $request->get('idConversation');

        if (!$idConversation) {
            return response()->json([
                'message' => 'idConversation es requerido'
            ], 422);
        }

        // =========================================================
        // 1. OBTENER LA CONVERSACIÓN Y VALIDAR QUE PERTENECE AL USUARIO
        // =========================================================

        $conversation = Conversation::where('id', $idConversation)
            ->whereHas('userPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversación no encontrada'
            ], 404);
        }

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'
            ], 403);
        }
        
        // =========================================================
        // 2. OBTENER EL USER PLAN
        // =========================================================

        $userPlanId = $conversation->user_plan_id;

        $planName = $conversation->userPlan->plan->name;

        $planPrompts = $this->planPromptResolver->forConversation($conversation);

        // =========================================================
        // 3. OBTENER PREGUNTAS YA RESPONDIDAS
        // =========================================================

        $answeredQuestionIds = UserAnswers::where(
            'conversation_id',
            $conversation->id
        )
            ->whereNotNull('question_id')
            ->pluck('question_id')
            ->toArray();

        // =========================================================
        // 4. OBTENER TODOS LOS NODOS Y SUS PREGUNTAS
        // =========================================================

        $isPhase2 = false;

        $nodes = PlanNode::with([
            'questions' => function ($query) {
                $query->orderBy('order_index');
            }
        ])
            ->where('user_plan_id', $userPlanId)
            ->where('execution_phase', 1) 
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

            // Verificamos si en la Fase 1 aún queda alguna pregunta sin responder
        $hasPendingPhase1 = false;
        foreach ($nodes as $nodeItem) {
            foreach ($nodeItem->questions as $qItem) {
                if (!in_array($qItem->id, $answeredQuestionIds)) {
                    $hasPendingPhase1 = true;
                    break 2;
                }
            }
        }

        // Si la Fase 1 ya terminó por completo, pasamos automáticamente a la Fase 2
        if (!$hasPendingPhase1) {
            $isPhase2 = true;

            $nodes = PlanNode::with([
                'questions' => function ($query) {
                    $query->orderBy('order_index');
                }
            ])
                ->where('user_plan_id', $userPlanId)
                ->where('execution_phase', 2)
                ->orderBy('orden')
                ->orderBy('id')
                ->get();

        }

        // =========================================================
        // 5. AGRUPAR NODOS POR PADRE
        // =========================================================

        $nodesByParent = $nodes->groupBy('parent_id');

        // =========================================================
        // 6. FUNCIÓN PARA ENCONTRAR LA SIGUIENTE PREGUNTA
        // =========================================================

        $findNextQuestion = function ($parentId) use (
            &$findNextQuestion,
            $nodesByParent,
            &$answeredQuestionIds
        ) {

            $children = $nodesByParent->get($parentId, collect());

            $children = $children->sortBy([
                ['orden', 'asc'],
                ['id', 'asc'],
            ]);

            foreach ($children as $node) {

                // ---------------------------------------------
                // Revisar preguntas del nodo actual
                // ---------------------------------------------

                foreach ($node->questions as $question) {

                    if (!in_array($question->id, $answeredQuestionIds)) {

                        return [
                            'node' => $node,
                            'question' => $question,
                        ];
                    }
                }

                // ---------------------------------------------
                // Revisar hijos del nodo
                // ---------------------------------------------

                $result = $findNextQuestion($node->id);

                if ($result) {
                    return $result;
                }
            }

            return null;
        };

        // =========================================================
        // 7. BUSCAR PREGUNTAS HASTA ENCONTRAR UNA QUE NECESITE
        //    SER RESPONDIDA POR EL USUARIO
        // =========================================================
        $triggerAi = $request->boolean('trigger_ai', false);
        $automaticAnswerCount = 0;
        $maxAutomaticAnswers = 2;

        while (true) {

            $result = $findNextQuestion(null);

            // -----------------------------------------------------
            // NO QUEDAN PREGUNTAS
            // -----------------------------------------------------

            if (!$result) {

                return response()->json([
                    'completed' => true,
                    'plan_name' => $planName,
                    'node' => null,
                    'parent_node' => null,
                    'question' => null,
                    'message' => 'Todas las preguntas han sido respondidas'
                ]);
            }

            $node = $result['node'];
            $question = $result['question'];

            // Si se puede usar "Volver a la pregunta anterior" desde esta pregunta
            $goBack = $this->goBackStatus($conversation);

            // El tesista volvió a esta pregunta: se devuelve lo recuperado (primer cuadro y tabla)
            // y no se propone una respuesta automática
            $restored = $this->conversationService->restoredAnswer($conversation->id, $question->id);

            if ($restored) {
                return response()->json([
                    'completed' => false,
                    'plan_name' => $planName,
                    'node' => $node,
                    'parent_node' => $node->parent,
                    'question' => $question,
                    'restored' => $restored,
                    'go_back' => $goBack
                ]);
            }

            // Si la IA ya respondió 2 preguntas automáticamente
            if (!$triggerAi ||!$isPhase2 && $automaticAnswerCount >= $maxAutomaticAnswers) {
                return response()->json([
                    'completed' => false,
                    'plan_name' => $planName,
                    'node' => $node,
                    'parent_node' => $node->parent,
                    'question' => $question,
                    'go_back' => $goBack
                ]);
            }

            // =====================================================
            // 8. OBTENER HISTORIAL ACTUALIZADO
            // =====================================================

            $thesisContext = \App\Models\ThesisContext::where('conversation_id', $idConversation)->first();
            $resumenGeneral = $thesisContext?->plan_context['resumen_general'] ?? null;

            // Si ya existe resumen consolidado: resumen + datos conocidos del catálogo del plan
            if (!empty($resumenGeneral)) {
                $contextoParaValidar = "RESUMEN GENERAL CONSOLIDADO DEL PROYECTO:\n" . $resumenGeneral . "\n\n"
                    . $this->thesisContextService->knownDataText(
                        $thesisContext,
                        $planPrompts,
                        'DATOS ESPECÍFICOS Y CUANTITATIVOS REGISTRADOS'
                    );
            } else {
                $contextoParaValidar = $this->conversationService->getHistoryLimit10($idConversation);
            }
            
            // =====================================================
            // 9. PREGUNTAR A LA IA SI PUEDE RESPONDERLA
            // =====================================================

            $resultIA = null;

            try {

                $promptQuestion =
                    $this->promptService->promptValidationRedundanceQuestion(
                        $planPrompts,
                        $contextoParaValidar,
                        $question->question_text,
                        $isPhase2,
                        $this->thesisContextService->structureText($conversation->id)
                    );

                $resultIA = $this->openAIService->json($promptQuestion);

            } catch (\Throwable $e) {
                \Log::warning('Error o timeout al consultar IA en getConversationPlan: ' . $e->getMessage());

                return response()->json([
                    'completed' => false,
                    'plan_name' => $planName,
                    'node' => $node,
                    'parent_node' => $node->parent,
                    'question' => $question,
                    'go_back' => $goBack
                ]);
            }

            // =====================================================
            // 10. LA IA PUEDE RESPONDERLA AUTOMÁTICAMENTE
            // =====================================================

            if (($resultIA['show'] ?? true) === false) {

                $responseAutomatic = $resultIA['resp'] ?? '';

                // Cifras calculadas ({{operación}}). Si alguna no se pudo calcular, no se sugiere:
                // el usuario responde la pregunta
                $calculation = app(CalculationService::class)->resolve((string) $responseAutomatic);
                $responseAutomatic = empty($calculation['errors']) ? $calculation['text'] : '';

                if (!empty($calculation['errors'])) {
                    \Log::info('RESPUESTA SUGERIDA DESCARTADA: CÁLCULO NO VÁLIDO', ['errors' => $calculation['errors']]);
                }

                // Antes de mostrarla, la sugerencia pasa por las mismas revisiones de
                // "Validar información". Si no pasa, se muestra la pregunta para que la
                // responda el usuario. Solo en la Fase 1: en la Fase 2 el sistema debe
                // redactar sintetizando todo el plan.
                if (!empty(trim($responseAutomatic)) && !$isPhase2) {
                    $validation = $this->answerValidationService->validate($conversation, [
                        'idConversation' => $conversation->id,
                        'idQuestion' => $question->id,
                        'question' => $question->question_text,
                        'validation' => $question->validation_detail ?? '',
                        'response' => $responseAutomatic,
                    ]);

                    if (!$validation['valid']) {
                        \Log::info('RESPUESTA SUGERIDA DESCARTADA: NO PASÓ LA VALIDACIÓN', [
                            'conversation_id' => $idConversation,
                            'question_id' => $question->id,
                            'inconsistent' => $validation['inconsistent'],
                            'feedback' => $validation['result']['feedback'] ?? null,
                            'response' => $responseAutomatic,
                        ]);

                        $responseAutomatic = '';
                    }
                }

                // Validar que realmente exista una respuesta
                if (!empty(trim($responseAutomatic))) {

                    return response()->json([
                        'completed' => false,
                        'plan_name' => $planName,
                        'node' => $node,
                        'parent_node' => $node->parent,
                        'question' => $question,
                        'auto_generated' => true,
                        'suggested_answer' => $responseAutomatic,
                        'go_back' => $goBack
                    ]);
                }
            }

            // =====================================================
            // 11. LA IA NO PUEDE RESPONDERLA
            // =====================================================

            return response()->json([
                'completed' => false,
                'plan_name' => $planName,
                'node' => $node,
                'parent_node' => $node->parent,
                'question' => $question,
                'go_back' => $goBack
            ]);
        }
    }

    // =====================================================================================
    // XXXXXXXXXXXXXXXXXXXXXXXXXXX
    // ======================================================================================
    public function getConversationPlanUser( Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $idConversation = $request->get('idConversation');

        $conversation = Conversation::find( $idConversation );

        $userPlanId = $conversation?->user_plan_id;

        $planNodes = PlanNode::where('user_plan_id', $userPlanId )
                ->where('execution_phase', '!=', 0)  
                ->orderBy('id', 'asc')
                ->get();

        return response()->json([
            'success' => true,
            'status_structure' => (int) ($conversation?->status_structure ?? 0),
            'data' => $planNodes,
        ]);

    }

    public function getViewConversationPlanUser(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $idConversation = $request->get('idConversation');

        // Solo el dueño de la conversación puede ver su plan
        $conversation = Conversation::where('id', $idConversation)
            ->whereHas('userPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'
            ], 403);
        }

       $planNodes = PlanNode::where('user_plan_id', $conversation->user_plan_id)
            ->with([
                'questions' => function ($query) use ($idConversation) {
                    $query->with([
                        'answer' => function ($query) use ($idConversation) {
                            $query->where('conversation_id', $idConversation)
                                ->with([
                                    'files' => function ($query) {
                                        $query->where('file_type', 'image');
                                    },
                                    'tables'
                                ]);
                        }
                    ]);
                }
            ])
            ->orderBy('orden', 'asc')
            ->get();

            // Rutas de las imágenes, datos de las tablas, título legible y fuente por defecto (como en el Word)
            $planNodes->each(function ($node) {
                $node->questions->each(function ($question) {
                    if (!$question->answer) {
                        return;
                    }

                    $question->answer->files->each(function ($file) {
                        $file->file_path = asset('storage/' . $file->file_path);
                    });

                    $question->answer->tables->each(function ($table) {
                        if (is_string($table->data)) {
                            $table->data = json_decode($table->data, true);
                        }
                        $table->nombre = $table->nombre
                            ? \App\Http\Controllers\Document\DocumentController::readableTitle($table->nombre)
                            : $table->nombre;
                        $table->fuente = trim((string) $table->fuente) !== '' ? $table->fuente : 'Elaboración propia';
                    });
                });
            });

            // Numera figuras y tablas en el orden del documento (igual que el Word)
            $nodesByParent = $planNodes->groupBy(fn ($node) => $node->parent_id ?? 0);
            $imageOrden = 1;
            $tableOrden = 1;

            $numerar = function ($parentId) use (&$numerar, $nodesByParent, &$imageOrden, &$tableOrden) {
                $children = $nodesByParent->get($parentId, collect())
                    ->sortBy([['orden', 'asc'], ['id', 'asc']]);

                foreach ($children as $node) {
                    foreach ($node->questions->sortBy('order_index') as $question) {
                        if (!$question->answer) {
                            continue;
                        }

                        foreach ($question->answer->files as $file) {
                            $file->orden = $imageOrden++;
                        }

                        // Como el Word: solo cuentan las tablas con columnas y filas
                        foreach ($question->answer->tables as $table) {
                            $table->orden = !empty($table->data['columns']) && isset($table->data['rows'])
                                ? $tableOrden++
                                : null;
                        }
                    }

                    $numerar($node->id);
                }
            };

            $numerar(0);



        return response()->json([
            'success' => true,
            'data' => $planNodes,
        ]);
    }



    public function updateTitleConversation(Request $request)
    {
        try {

            $request->validate([
                'id' => 'required|integer|exists:conversations,id',
                'title' => 'required|string|max:255',
            ]);

            $conversation = Conversation::findOrFail($request->id);

            $conversation->update([
                'title' => $request->title,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Actualizado correctamente',
                'data' => $conversation
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function closeStructure(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'idConversation' => ['required', 'integer'],
        ]);

        $idConversation = $request->get('idConversation');

        /*
        |--------------------------------------------------------------------------
        | CONVERSACIÓN
        |--------------------------------------------------------------------------
        */

        $conversation = Conversation::with('userPlan.plan')
            ->where('id', $idConversation)
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR QUE LA CONVERSACIÓN PERTENEZCA AL USUARIO
        |--------------------------------------------------------------------------
        */

        if (!$conversation->userPlan ||
            $conversation->userPlan->user_id != $user->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para acceder a esta conversación.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | PLAN
        |--------------------------------------------------------------------------
        */

        $plan = $conversation->userPlan->plan;

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'La conversación no tiene un plan asociado.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | OBTENER NODOS
        |--------------------------------------------------------------------------
        */

        $nodes = PlanNode::withCount([
            'children',
            'questions'
        ])
        ->where('plan_id', $plan->id)
        ->where('user_plan_id', $conversation->user_plan_id)
        ->get();

        /*
        |--------------------------------------------------------------------------
        | BUSCAR NODOS HOJA SIN PREGUNTAS
        |--------------------------------------------------------------------------
        */

        $nodesWithoutQuestions = $nodes
            ->filter(function ($node) {

                // Es nodo final si no tiene hijos
                $isLeaf = $node->children_count === 0;

                // Verificar si tiene preguntas
                $hasQuestions = $node->questions_count > 0;

                return $isLeaf && !$hasQuestions;
            })
            ->values();

        // ===========================================================================
        // NUEVO: RECUPERAR PREGUNTAS OFICIALES PARA NODOS PREDEFINIDOS
        // ===========================================================================
        $nodesWithoutQuestions = $nodesWithoutQuestions->filter(function ($node) use ($plan) {
            
            // 1. Doble seguridad: si el nodo ya cuenta con preguntas, lo filtramos
            if (Question::where('plan_node_id', $node->id)->exists()) {
                return false; 
            }

            $masterNode = PlanNode::whereNull('user_plan_id')
                ->where('plan_id', $plan->id)
                ->where('titulo', $node->titulo)
                ->first();

            if ($masterNode) {
                $masterQuestions = Question::where('plan_node_id', $masterNode->id)->get();

                if ($masterQuestions->isNotEmpty()) {
                    $newQuestions = [];
                    foreach ($masterQuestions as $mq) {
                        
                        // 2. Verificar que no exista ya una pregunta con el mismo texto en este nodo
                        $exists = Question::where('plan_node_id', $node->id)
                            ->where('question_text', $mq->question_text)
                            ->exists();

                        if (!$exists) {
                            $newQuestions[] = [
                                'plan_node_id'      => $node->id,
                                'question_text'     => $mq->question_text,
                                'question_detail'   => $mq->question_detail,
                                'question_example'  => $mq->question_example,
                                'validation_detail' => $mq->validation_detail,
                                'order_index'       => $mq->order_index,
                                'created_at'        => now(),
                                'updated_at'        => now(),
                            ];
                        }
                    }

                    if (!empty($newQuestions)) {
                        Question::insert($newQuestions);
                        
                    }

                    return false; // Excluir de la generación por IA
                }
            }

            return true; // Es un nodo creado por el usuario de cero, la IA le generará preguntas
        })->values();
        // ===========================================================================

        if ($nodesWithoutQuestions->isEmpty()) {
            $conversation->update([
                'status_structure' => 1
            ]);
            
            return response()->json([
                'success' => true,
                'can_close' => true,
                'message' => 'La estructura está completa.'
            ]);
        }

            $nodosSinPreguntas = $nodesWithoutQuestions
                    ->map(function ($node) {
                        return [
                            'id' => $node->id,
                            'titulo' => $node->titulo,
                            'codigo' => $node->codigo,
                            'parent_id' => $node->parent_id,
                            'orden' => $node->orden,
                            'nivel' => $node->nivel,
                        ];
                    })
                    ->values()
                    ->toArray();


            $estructuraCompleta = $nodes
                ->map(function ($node) {
                    return [
                        'id' => $node->id,
                        'titulo' => $node->titulo,
                        'codigo' => $node->codigo,
                        'parent_id' => $node->parent_id,
                        'orden' => $node->orden,
                        'nivel' => $node->nivel,
                        'questions_count' => $node->questions_count,
                    ];
                })
                ->values()
                ->toArray();    

        $prompt = $this->promptService->buildQuestionsForLeafNodesPrompt(
            planPrompts: $this->planPromptResolver->forConversation($conversation),
            plan: $conversation->userPlan->plan->toArray(),
            nodesWithoutQuestions: $nodosSinPreguntas,
            allNodes: $estructuraCompleta
        );

        $respuesta = $this->openAIService->chat($prompt);

        $data = json_decode($respuesta, true);

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !isset($data['nodes']) ||
            !is_array($data['nodes'])
        ) {

            \Log::error('OpenAI devolvió una respuesta inválida', [
                'respuesta' => $respuesta,
                'json_error' => json_last_error_msg(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron generar las preguntas automáticamente.'
            ], 500);
        }

        // Cada nodo nuevo lleva exactamente 3 preguntas, como los del plan maestro
        $questionsPerNode = 3;

        // Solo los nodos que se pidieron: la IA no puede agregar preguntas a otros nodos
        $requestedNodeIds = array_flip(array_column($nodosSinPreguntas, 'id'));
        $savedNodeIds = [];

        foreach ($data['nodes'] as $nodeData) {

            if (
                !isset($nodeData['node_id']) ||
                !isset($nodeData['questions']) ||
                !is_array($nodeData['questions'])
            ) {
                continue;
            }

            $nodeId = (int) $nodeData['node_id'];

            // Nodo que no se pidió, o que la IA repitió en la respuesta
            if (!isset($requestedNodeIds[$nodeId]) || isset($savedNodeIds[$nodeId])) {
                continue;
            }
            $savedNodeIds[$nodeId] = true;

            $questions = array_values(array_filter(
                $nodeData['questions'],
                fn ($questionData) => !empty($questionData['description'])
            ));

            if (count($questions) !== $questionsPerNode) {
                \Log::warning('La IA no devolvió 3 preguntas para el nodo', [
                    'node_id' => $nodeId,
                    'cantidad' => count($questions),
                ]);
            }

            foreach (array_slice($questions, 0, $questionsPerNode) as $index => $questionData) {
                Question::create([
                    'plan_node_id' => $nodeId,
                    'question_text' => $questionData['description'],
                    'question_detail'  => $questionData['detail'] ?? null,
                    'question_example' => $questionData['example'] ?? null,
                    'order_index' => $index + 1,
                ]);
            }
        }

        $conversation->update([
            'status_structure' => 1
        ]);
        
        return response()->json([
            'success' => true,
            'can_close' => true,
            'message' => 'La estructura está completa y puede cerrarse.',
        ]);
    }

    // =====================================================================================
    // VALIDA SI LA CONVERSACIÓN CUENTA CON PAGO APROBADO
    // =====================================================================================
    private function isPaymentCompleted(Conversation $conversation): bool
    {
        $conversation->loadMissing('subscription.payments');

        $paymentStatus = $conversation->subscription?->payments
            ?->sortByDesc('created_at')
            ->first()
            ?->status;

        return in_array(strtolower($paymentStatus ?? ''), ['completed', 'paid']);
    }

    // =====================================================================================
    // ELIMINA EN CASCADA LAS RESPUESTAS DESDE UN CAPÍTULO SELECCIONADO
    // =====================================================================================
    public function deleteChaptersAnswers(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'idConversation' => 'required|integer|exists:conversations,id',
            // Punto de la Fase 1 desde el que se borra: una pregunta o un nodo (capítulo, sección
            // o subsección). El servicio valida que pertenezcan a la estructura de esta conversación.
            'startQuestionId' => 'nullable|integer',
            'startNodeId' => 'nullable|integer|required_without:startQuestionId',
        ]);

        $conversation = Conversation::where('id', $validated['idConversation'])
            ->whereHas('userPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversación no encontrada o sin permisos.'
            ], 404);
        }

        try {
            $result = $this->conversationService->deleteAnswersFrom(
                (int) $validated['idConversation'],
                isset($validated['startNodeId']) ? (int) $validated['startNodeId'] : null,
                isset($validated['startQuestionId']) ? (int) $validated['startQuestionId'] : null
            );

            return response()->json([
                'success' => true,
                'message' => 'Las respuestas fueron eliminadas correctamente.',
                'data' => $result
            ], 200);

        } catch (\DomainException $e) {
            // Nodo que no es de esta estructura, de la Fase 2 o sin preguntas
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Error en deleteChaptersAnswers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar las respuestas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function goBack(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer',
        ]);

        $conversation = Conversation::where('id', $validated['idConversation'])
            ->whereHas('userPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversación no encontrada o sin permisos.'
            ], 404);
        }

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'
            ], 403);
        }

        try {
            $result = $this->conversationService->goBack($conversation, (int) $validated['idQuestion']);

            return response()->json([
                'success' => true,
                'message' => 'Volviste a la pregunta anterior.',
                'data' => $result
            ], 200);

        } catch (\DomainException $e) {
            // No se puede volver (primera pregunta, capítulo cerrado, pregunta desactualizada)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Error en goBack: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al volver a la pregunta anterior.'
            ], 500);
        }
    }

    // Si desde la pregunta actual se puede volver a la anterior, y si no, por qué
    // ===== MODIFICAR RESPUESTA: editar una respuesta guardada; si el cambio es de fondo, se borra lo posterior =====

    // Preguntas ya respondidas, por capítulo, para elegir cuál modificar
    public function modificationQuestions(Request $request)
    {
        $validated = $request->validate(['idConversation' => 'required|integer']);

        return $this->withModificationConversation($validated['idConversation'], fn (Conversation $conversation) => [
            'chapters' => $this->conversationService->answeredForModification($conversation),
        ]);
    }

    // La respuesta guardada de la pregunta elegida
    public function modificationAnswer(Request $request)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer',
        ]);

        return $this->withModificationConversation($validated['idConversation'], fn (Conversation $conversation) =>
            $this->conversationService->modificationData($conversation, (int) $validated['idQuestion'])
        );
    }

    // "Validar" de Modificar respuesta: revisa la respuesta sin redactar texto nuevo y decide si es menor o de fondo
    // (contradecir lo registrado se toma como de fondo, no como rechazo)
    public function modificationAnalyze(Request $request, AnswerModificationService $modification)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer',
            'text' => 'required|string',
        ]);

        return $this->withModificationConversation($validated['idConversation'], function (Conversation $conversation) use ($validated, $modification) {
            $questionId = (int) $validated['idQuestion'];
            $data = $this->conversationService->modificationData($conversation, $questionId);

            if (preg_replace('/\s+/u', ' ', trim($data['answer_text'])) === preg_replace('/\s+/u', ' ', trim($validated['text']))) {
                return ['type' => AnswerModificationService::UNCHANGED, 'message' => ''];
            }

            $question = Question::find($questionId);
            $validation = $this->answerValidationService->validate($conversation, [
                'idConversation' => $conversation->id,
                'idQuestion' => $questionId,
                'question' => $question?->question_text ?? '',
                'validation' => $question?->validation_detail ?? '',
                'response' => $validated['text'],
            ]);

            if (empty($validation['result']['is_valid'])) {
                return [
                    'type' => 'invalido',
                    'message' => trim((string) ($validation['result']['feedback'] ?? '')) ?: 'La respuesta modificada no responde adecuadamente a la pregunta.',
                ];
            }

            if ($validation['inconsistent']) {
                return ['type' => AnswerModificationService::DEEP, 'message' => ''];
            }

            $impact = $modification->analyze($conversation, $questionId, $data['answer_text'], $validated['text']);

            return ['type' => $impact['type'], 'message' => ''];
        });
    }

    // Guarda la modificación que el estudiante confirmó en el aviso
    public function modificationSave(Request $request, AnswerModificationService $modification)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer',
            'text' => 'required|string',
            'type' => 'required|in:menor,fondo',
        ]);

        return $this->withModificationConversation($validated['idConversation'], function (Conversation $conversation) use ($validated, $modification) {
            $questionId = (int) $validated['idQuestion'];
            $before = $this->conversationService->modificationData($conversation, $questionId)['answer_text'];

            // Un cambio de cifras nunca se guarda como menor, aunque lo pida el cliente
            if ($validated['type'] === AnswerModificationService::MINOR && $modification->numbersChanged($before, $validated['text'])) {
                throw new \DomainException('Esta modificación cambia cifras e influye en lo avanzado. Vuelve a validarla para ver el aviso.');
            }

            return $this->conversationService->applyModification(
                $conversation,
                $questionId,
                $validated['text'],
                $validated['type'] === AnswerModificationService::DEEP
            );
        });
    }

    // Conversación del usuario con el pago aprobado; los errores de dominio van como 422
    private function withModificationConversation(int $conversationId, callable $action)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::where('id', $conversationId)
            ->whereHas('userPlan', fn ($query) => $query->where('user_id', $user->id))
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversación no encontrada o sin permisos.'], 404);
        }

        if (!$this->isPaymentCompleted($conversation)) {
            return response()->json(['success' => false, 'message' => 'Acceso denegado. El pago de esta suscripción no está aprobado.'], 403);
        }

        try {
            return response()->json(['success' => true, 'data' => $action($conversation)]);
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            \Log::error('Error en Modificar respuesta: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Ocurrió un error al modificar la respuesta.'], 500);
        }
    }

    private function goBackStatus(Conversation $conversation): array
    {
        $info = $this->conversationService->goBackInfo($conversation);

        return [
            'allowed' => $info['allowed'],
            'reason' => $info['reason'],
        ];
    }
    
}
