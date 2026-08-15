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
use App\Services\PromptService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;

class ConversationController extends Controller
{
    //
    private ConversationService $conversationService;

    public function __construct(ConversationService $conversationService, 
    protected PromptService $promptService, protected OpenAIService $openAIService )
    {
        $this->conversationService = $conversationService;
        
    }
    

    public function startConversation(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer'
        ]);

        $result = $this->conversationService->startConversation(
            auth()->id(),
            $validated['plan_id']
        );

        return response()->json($result);
    }
    public function saveAnswerDiagnostic(Request $request , ConversationService $conversationService)
    {
        $validated = $request->validate([
            'id_subscription_conversation' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.id_question' => 'required|integer',
            'questions.*.answer' => 'required|string|min:5',

        ]);
         \Log::info('DEBUG save diaganosti', [
             'id_subscription_conversation' => $validated['id_subscription_conversation'],
             'questions' => $validated['questions'],
            ]);

            $conversation = Conversation::find($validated['id_subscription_conversation']);

            $userPlanId = $conversation?->user_plan_id;

            $userPlan = UserPlan::find( $userPlanId );

            $planId= $userPlan->plan_id;

        
        try {
            $user = auth()->user();

            // Consultar con la IA
            $diagnosticText = '';

            foreach ($validated['questions'] as $question) {
                $diagnosticText .= "Pregunta ID: {$question['id_question']}\n";
                $diagnosticText .= "Respuesta: {$question['answer']}\n\n";
            }

            $prompt = $this->promptService->diagnosticRubroPlan( $diagnosticText ); 

            $result = $this->openAIService->json($prompt);

            \Log::info('RESULTdiaganostic: ', $result);

            $rubro = $result['rubro'];

            $plansNode = PlanNode::whereNull('user_plan_id')
                                ->where('plan_id', $planId)
                                ->where('id', '<=', 123)
                                ->oldest('id')
                                ->get();

            foreach ($plansNode as $planNode) {

                \Log::info('Plan Node', [
                    'id' => $planNode->id,
                    'titulo' => $planNode->titulo,
                ]);

            }
            $nodesForAI = $plansNode->map(function ($node) {
                return [
                    'id' => $node->id,
                    'titulo' => $node->titulo,
                ];
            })->values()->toArray();

            $promptFiltroNodes = $this->promptService->promptFiltroNode( $rubro, $nodesForAI ); 

            $resultFiltro = $this->openAIService->json($promptFiltroNodes);

            \Log::info('RESULTADO FILTRO NODES IA:', [
                'result' => $resultFiltro
            ]);
            $nodeIdsFiltrados = collect($resultFiltro['nodes'] ?? [])
            ->pluck('id')
            ->toArray();

            $planNodes = PlanNode::whereIn('id', $nodeIdsFiltrados)
                ->orderBy('id', 'asc')
                ->get();

            $questions = Question::whereIn('plan_node_id', $planNodes->pluck('id'))
                ->orderBy('plan_node_id', 'asc')
                ->orderBy('order_index', 'asc')
                ->get();
                // Hasta aca tengo los nodos filtrados y susu respuestas 
             
            $conversationService->registerPlanNodesUser( $planNodes, $questions , $planId, $userPlanId);
            

            foreach ($validated['questions'] as $question) {

                \Log::info('Pregunta y respuesta', [
                    'id_question' => $question['id_question'],
                    'answer' => $question['answer'],
                ]);

                UserAnswers::create([
                    'conversation_id' => $validated['id_subscription_conversation'],
                    'user_id' => $user->id,
                    'question_id' => $question['id_question'],
                    'answer' => trim($question['answer']),
                ]);
            }

            Conversation::where('id', $conversation->id )
                ->update([
                    'summary' => $rubro,
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

    
    public function conversationSaveReply(Request $request, ConversationService $conversationService)
    {
        $validated = $request->validate([
            'idPlan' => 'required|integer',
            'idSection' => 'nullable|integer',
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
        // \Log::error('OpenAI error: ' . $e->getMessage());
         \Log::info('DEBUG conversationSaveReply', [
                'metadata' => json_decode($request->metadata, true),
                'files' => $request->file('files'),
                'references' => json_decode($request->references, true),
                'all_request' => $request->all(),
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

    public function conversationsUser()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversations = Conversation::with([
                            'subscription.package',
                            'subscription.payments',
                            'sectionProgress.section',
                            'userPlan'
                        ])->whereHas('userPlan', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->get();


        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'conversations' => $conversations->map(function ($conversation) {

                $packageName = $conversation->subscription?->package?->name;

                $planName = $conversation->plan?->name;

                $paymentStatus = $conversation->subscription?->payments
                    ?->sortByDesc('created_at')
                    ->first()
                    ?->status;

                return [
                    'id' => $conversation->id,
                    'status' => $conversation->status,
                    'title' => $conversation->title,
                    'plan_name' => $planName,
                    'package_name' => $packageName,
                    'payment_status' => $paymentStatus,
                ];
            }),


            // 'conversations' => $conversations->map(function ($conversation) {

            //     $packageName = $conversation->subscription?->package?->name;
            //     $planName = $conversation -> plan?->name; 

            //     $paymentStatus = $conversation->subscription?->payments
            //                                                     ?->sortByDesc('created_at')
            //                                                     ->first()
            //                                                     ?->status;

            //     return [
            //         'id' => $conversation->id,
            //         'status' => $conversation->status,
            //         'title' => $conversation->title,
            //         'plan_name' => $planName,
            //         'package_name' => $packageName,
            //         'payment_status' => $paymentStatus,
            //     ];
            // }),
        ];

        return response()->json($data);
    }

    public function getVerficationDiagnosticExist( Request $request ){
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

        return response()->json([
            'exists' => true,
            'has_summary' => !empty($conversation->summary),
            'summary' => $conversation->summary,
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

        $userPlanId = $conversation->user_plan_id;

        $userPlan= UserPlan::findOrFail($userPlanId);

        $planId = $userPlan->plan_id;

        $planNode = PlanNode::whereNull('user_plan_id')
                                        ->where('plan_id', $planId )
                                        ->latest('id')
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

        // 1. Obtener la conversación y validar que pertenece al usuario
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

        // 2. Obtener el user_plan de la conversación
        $userPlanId = $conversation->user_plan_id;

        $planName = $conversation->userPlan->plan->name;

        // 3. Obtener los IDs de las preguntas que ya tienen respuesta
        $answeredQuestionIds = UserAnswers::where('conversation_id', $conversation->id)
            ->pluck('question_id')
            ->toArray();

        // 4. Obtener los nodos y sus preguntas
        $nodes = PlanNode::with([
            'parent',
            'questions' => function ($query) {
                $query->orderBy('order_index');
            }
        ])
        ->where('user_plan_id', $userPlanId)
        ->orderBy('orden')
        ->get();

        // 5. Buscar la primera pregunta que todavía NO tiene respuesta
        foreach ($nodes as $node) {

            foreach ($node->questions as $question) {

                if (!in_array($question->id, $answeredQuestionIds)) {

                    return response()->json([
                        'completed' => false,
                        'plan_name' => $planName,
                        'node' => $node,
                        'parent_node' => $node->parent,
                        'question' => $question
                    ]);
                }
            }
        }

        // 6. Si no quedan preguntas
        return response()->json([
            'completed' => true,
            'plan_name' => $planName,
            'node' => null,
            'parent_node' => null,
            'question' => null,
            'message' => 'Todas las preguntas han sido respondidas'
        ]);
    }



public function getConversationPlan1111(Request $request)
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

    $userPlanId = $conversation->user_plan_id;

    $nodes = PlanNode::with([
        'questions' => function ($query) {
            $query->orderBy('order_index');
        }
    ])
    ->where('user_plan_id', $userPlanId)
    ->orderBy('orden')
    ->get();

    return response()->json([
        'nodes' => $nodes
    ]);
}


    public function getConversationPlan1( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $idConversation = $request->get('idConversation');
        

        // $subscription = UserSubscription::with([
        //     'plan.sections' => function ($q) {
        //         $q->where('is_active', true)
        //         ->orderBy('order_index');
        //     },
        //     'plan.sections.questions',
        // ])
        // ->where('id', $idConversation)
        // ->where('user_id', auth()->id())
        // ->firstOrFail();
        //\
        $conversation = Conversation::with([  
            'plan.sections'=> function ($q) {
                $q->where('is_active', true)
                ->orderBy('order_index');
            },
            'plan.sections.questions',
         ])
        ->where('id', $idConversation)
        ->where('user_id', auth()->id())
        ->firstOrFail();
        //

        $answers = UserAnswers::where('conversation_id', $idConversation)
            ->where('user_id', auth()->id())
            ->get();

        $progressSectionConversation = ConversationSectionProgress::where('conversation_id', $idConversation)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('section_id');

        $answersSections = $answers->whereNotNull('section_id')->keyBy('section_id');

        $answersQuestions = $answers->whereNotNull('question_id')->keyBy('question_id');

        $conversation->plan->sections->each(function ($section) use ($answersSections, $answersQuestions, $progressSectionConversation) {

            // answer de section
            $section->setAttribute(
                'answer',
                $answersSections[$section->id]->answer_text ?? null
            );
            //progress de section
            $section->setAttribute(
                'progress',
                $progressSectionConversation[$section->id]->status ?? null
            );


            // answers de questions
            $section->questions->each(function ($question) use ($answersQuestions) {

                $question->setAttribute(
                    'answer',
                    $answersQuestions[$question->id]->answer_text ?? null
                );

            });

        });

        

        return new SubscriptionResource($conversation);
    //return response()->json($progress);
    }

// =====================================================================
// ========================================================


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
                ->orderBy('id', 'asc')
                ->get();

        return response()->json([
            'success' => true,
            'data' => $planNodes,
        ]);

       

    //     $conversation = Conversation::with([  
    //         'plan.sections'=> function ($q) {
    //             $q->where('is_active', true)
    //             ->orderBy('order_index');
    //         },
    //         'plan.sections.questions',
    //      ])
    //     ->where('id', $idConversation)
    //     ->where('user_id', auth()->id())
    //     ->firstOrFail();

    //     $answers = UserAnswers::with('files')
    //     ->where('conversation_id', $idConversation)
    //     ->where('user_id', auth()->id())
    //     ->get();

    //     $progressSectionConversation = ConversationSectionProgress::where('conversation_id', $idConversation)
    //         ->where('user_id', auth()->id())
    //         ->get()
    //         ->keyBy('section_id');

    //     $answersSections = $answers->whereNotNull('section_id')->keyBy('section_id');

    //     $answersQuestions = $answers->whereNotNull('question_id')->keyBy('question_id');

    //     $conversation->plan->sections->each(function ($section) use ($answersSections, $answersQuestions, $progressSectionConversation) {

    //     // answer de section
    //     $section->setAttribute(
    //         'answer',
    //         $answersSections[$section->id]->answer_text ?? null
    //     );
    //     //progress de section
    //     $section->setAttribute(
    //         'progress',
    //         $progressSectionConversation[$section->id]->status ?? null
    //     );


    //     // answers de questions
    //     $section->questions->each(function ($question) use ($answersQuestions) {

    //         $answer = $answersQuestions->get($question->id);

    //         $question->setAttribute(
    //             'answer',
    //             $answer->answer_text ?? null
    //         );

    //         $question->setAttribute(
    //             'files',
    //             $answer ? $answer->files
    //             ->whereIn('file_type', 'image')
    //             ->map(function ($file) {
    //                 return [
    //                     'id' => $file->id,
    //                     'file_type' => $file->file_type,
    //                     'file_url' => asset('storage/' . $file->file_path),
    //                     'description' =>  $file->description,
    //                     'fuente' =>  $file->fuente,
    //                 ];
    //             }) : []
    //         );      

    //         $question->setAttribute(
    //                 'tables',
    //                 $answer
    //                     ? $answer->tables->map(function ($table) {
    //                         return [
    //                             'id' => $table->id,
    //                             'title' => $table->nombre,
    //                             'data' => json_decode($table->data, true),
    //                             'created_at' => $table->created_at,
    //                         ];
    //                     })
    //                     : []
    //         );

    //     });

    // });

        

    // return new SubscriptionResource($conversation);
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
}
