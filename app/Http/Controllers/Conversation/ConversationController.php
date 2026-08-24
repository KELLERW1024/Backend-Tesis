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
    

    // public function startConversation(Request $request)
    // {
    //     $validated = $request->validate([
    //         'plan_id' => 'required|integer'
    //     ]);

    //     $result = $this->conversationService->startConversation(
    //         auth()->id(),
    //         $validated['plan_id']
    //     );

    //     return response()->json($result);
    // }
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
                                // ->where('id', '<=', 123)  // Esto desafecta resuemn, introduccion y otros
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

    //====================================
    // PREGUNTA A SER RESPONDIDA
    //====================================
    public function getConversationPlan1(Request $request)
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

        // 4. Obtener todos los nodos y sus preguntas
        $nodes = PlanNode::with([
            'questions' => function ($query) {
                $query->orderBy('order_index');
            }
        ])
        ->where('user_plan_id', $userPlanId)
        ->orderBy('orden')
        ->orderBy('id')
        ->get();
        // Agrupar nodos por parent_id
        $nodesByParent = $nodes->groupBy('parent_id');

        // Función recursiva para recorrer el árbol en orden
        $findNextQuestion = function ($parentId) use (
            &$findNextQuestion,
            $nodesByParent,
            $answeredQuestionIds
        ) {
            $children = $nodesByParent->get($parentId, collect());

            // Ordenar los hijos por orden
            $children = $children
                ->sortBy([
                    ['orden', 'asc'],
                    ['id', 'asc'],
                ]);

            foreach ($children as $node) {

                // Primero las preguntas del nodo actual
                foreach ($node->questions as $question) {

                    if (!in_array($question->id, $answeredQuestionIds)) {
                        return [
                            'node' => $node,
                            'question' => $question,
                        ];
                    }
                }

                // Después recorrer sus hijos
                $result = $findNextQuestion($node->id);

                if ($result) {
                    return $result;
                }
            }

            return null;
        };


        // 5. Buscar la primera pregunta no respondida respetando la jerarquía
        $result = $findNextQuestion(null);

        if ($result) {

            $node = $result['node'];
            $question = $result['question'];



            //  ACA CONSULTAR SI LA PREGUNTA QPUEDE SER REPONDIDA CON L ADATA HISTORICA QUE TENEMOS
            $history = $this->conversationService->getConversation( $idConversation );
            \Log::info(' HISTORY : ' , $history );

            $promptQuestion = $this->promptService->promptValidationRedundanceQuestion( $history,  $question->question_text   ); 

            $resultIA = $this->openAIService->json($promptQuestion); // ESTE METODO HACE LA VALUIDACION  CON LA IA 
            if ( $resultIA['show'] == false ) {
                
                $this->conversationService->saveUserAnswerAutomatic( $idConversation, $question->id , $resultIA['resp'] );

                 \Log::info('RESPONSE VALIDACION ', [
                    '$resultIA => ' => $resultIA ?? null 
                ]);

            }



            return response()->json([
                'completed' => false,
                'plan_name' => $planName,
                'node' => $node,
                'parent_node' => $node->parent,
                'question' => $question
            ]);
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

        // =========================================================
        // 2. OBTENER EL USER PLAN
        // =========================================================

        $userPlanId = $conversation->user_plan_id;

        $planName = $conversation->userPlan->plan->name;

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

        $nodes = PlanNode::with([
            'questions' => function ($query) {
                $query->orderBy('order_index');
            }
        ])
            ->where('user_plan_id', $userPlanId)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

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

            // =====================================================
            // 8. OBTENER HISTORIAL ACTUALIZADO
            // =====================================================

            $history = $this->conversationService->getConversation(
                $idConversation
            );

            \Log::info('HISTORY', [
                'conversation_id' => $idConversation,
                'question_id' => $question->id,
                'history' => $history
            ]);

            // =====================================================
            // 9. PREGUNTAR A LA IA SI PUEDE RESPONDERLA
            // =====================================================

            $promptQuestion =
                $this->promptService->promptValidationRedundanceQuestion(
                    $history,
                    $question->question_text
                );

            $resultIA = $this->openAIService->json($promptQuestion);

            \Log::info('RESPONSE VALIDACION', [
                'question_id' => $question->id,
                'resultIA' => $resultIA
            ]);

            // =====================================================
            // 10. LA IA PUEDE RESPONDERLA AUTOMÁTICAMENTE
            // =====================================================

            if (($resultIA['show'] ?? true) === false) {

                $responseAutomatic = $resultIA['resp'] ?? '';

                // Validar que realmente exista una respuesta
                if (!empty(trim($responseAutomatic))) {

                    $this->conversationService->saveUserAnswerAutomatic(
                        $idConversation,
                        $question->id,
                        $responseAutomatic
                    );

                    // MUY IMPORTANTE:
                    // Agregamos la pregunta como respondida
                    // para que no vuelva a aparecer dentro
                    // del mismo request.

                    $answeredQuestionIds[] = $question->id;

                    \Log::info('PREGUNTA RESPONDIDA AUTOMATICAMENTE', [
                        'conversation_id' => $idConversation,
                        'question_id' => $question->id,
                        'response' => $responseAutomatic
                    ]);

                    // -------------------------------------------------
                    // CONTINUAR CON LA SIGUIENTE PREGUNTA
                    // -------------------------------------------------

                    continue;
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
                'question' => $question
            ]);
        }
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

    public function getViewConversationPlanUser(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $idConversation = $request->get('idConversation');

        $conversation = Conversation::find($idConversation);

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
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


            $imageOrden = 1;
            $tableOrden = 1;

            $planNodes->each(function ($node) use (&$imageOrden, &$tableOrden) {

                $node->questions->each(function ($question) use (&$imageOrden, &$tableOrden) {

                    if ($question->answer) {

                        // Enumeración global de imágenes
                        $question->answer->files->each(function ($file) use (&$imageOrden) {

                            $file->orden = $imageOrden++;

                            $file->file_path = asset(
                                'storage/' . $file->file_path
                            );
                        });

                        // Enumeración global de tablas
                        $question->answer->tables->each(function ($table) use (&$tableOrden) {

                            $table->orden = $tableOrden++;

                            if (is_string($table->data)) {
                                $table->data = json_decode(
                                    $table->data,
                                    true
                                );
                            }
                        });
                    }
                });
            });



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

            if ($nodesWithoutQuestions->isEmpty()) {
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
            plan: $conversation->userPlan->plan->toArray(),
            nodesWithoutQuestions: $nodosSinPreguntas,
            allNodes: $estructuraCompleta
        );

        \Log::info('NODOS SIN PREGUNTAS', [
            'nodes' => $nodosSinPreguntas,
        ]);

        \Log::info('ESTRUCTURA COMPLETA', [
            'nodes' => $estructuraCompleta,
        ]);

        $respuesta = $this->openAIService->chat($prompt);

        \Log::info('RESPUESTA OPENAI - GENERACIÓN DE PREGUNTAS', [
            'respuesta' => $respuesta,
        ]);

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

        foreach ($data['nodes'] as $nodeData) {

            if (
                !isset($nodeData['node_id']) ||
                !isset($nodeData['questions']) ||
                !is_array($nodeData['questions'])
            ) {
                continue;
            }

            foreach ($nodeData['questions'] as $questionData) {

                if (empty($questionData['description'])) {
                    continue;
                }

                Question::create([
                    'plan_node_id' => $nodeData['node_id'],
                    'question_text' => $questionData['description'],
                ]);
            }
        }





      

        /*
        |--------------------------------------------------------------------------
        | SI EXISTEN NODOS INCOMPLETOS
        |--------------------------------------------------------------------------
        */

        if ($nodesWithoutQuestions->isNotEmpty()) {

            return response()->json([
                'success' => false,
                'can_close' => false,
                'message' => 'Existen nodos finales que no tienen preguntas asociadas.',
                'nodes' => $nodesWithoutQuestions->map(function ($node) {

                    return [
                        'id' => $node->id,
                        'titulo' => $node->titulo,
                        'orden' => $node->orden,
                        'parent_id' => $node->parent_id,
                        'codigo' => $node->codigo,
                    ];

                })->values(),
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | ESTRUCTURA CORRECTA
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'can_close' => true,
            'message' => 'La estructura está completa y puede cerrarse.',
        ]);
    }


    
}
