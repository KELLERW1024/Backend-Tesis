<?php 

namespace App\Services;

use App\Models\UserPlan;
use Illuminate\Support\Facades\DB;
use App\Models\UserSubscription;
use App\Models\Conversation; 
use App\Models\UserAnswers;
use App\Models\Question; 
use App\Models\ConversationSectionProgress; 
use App\Models\Tabla; 
use App\Models\PlanNode;
use App\Models\TablaColumna; 
use App\Models\TablaFila; 
use App\Models\TablaCelda; 

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ConversationService
{
    public $excelService;

    protected AIService $aiService;

    public function __construct(ExcelService $excelService, AIService   $aiService)
    {
        $this->excelService = $excelService;
        $this->aiService = $aiService;
    }

    public function registerUserSuscription( $userId, $packageId ){

        return DB::transaction(function () use ($userId, $packageId) {

            $subscription = UserSubscription::create([
                'user_id' => $userId,
                'package_id' => $packageId,
                'status' => 'active',
            ]);

            return [
                'status' => 200,
                'subscription_id' => $subscription->id
            ];
        });

    }
    public function registerUserPlan( $userId, $planId ){

        return DB::transaction(function () use ($userId, $planId) {

            $plan_user = UserPlan::create([
                'user_id' => $userId,
                'plan_id' => $planId
            ]);

            return [
                'status' => 200,
                'plan_user_id' => $plan_user->id
            ];
        });

    }

    public function registerPlanNodesUser(    
        Collection $planNodes, Collection $questions, int $nuevoPlanId, int $userPlanId 
    ) {
        DB::transaction(function () use (
            $planNodes,
            $questions,
            $nuevoPlanId,
            $userPlanId
        ) {
            $nodeIdMap = [];

            // 1. Clonar nodos manteniendo la jerarquía básica
            foreach ($planNodes as $planNode) {
                
            $newPlanNode = $planNode->replicate();
                
                // Asociar al nuevo Plan
                $newPlanNode->plan_id = $nuevoPlanId;
                $newPlanNode->user_plan_id = $userPlanId;
                $newPlanNode->save();

                // Guardar relación original -> nuevo
                $nodeIdMap[$planNode->id] = $newPlanNode->id;
            }

            // 2. Actualizar los parent_id utilizando el mapa de IDs clonados
            foreach ($planNodes as $planNode) {
                if ($planNode->parent_id !== null && isset($nodeIdMap[$planNode->parent_id])) {
                    PlanNode::where('id', $nodeIdMap[$planNode->id])->update([
                        'parent_id' => $nodeIdMap[$planNode->parent_id]
                    ]);
                }
            }
            // 3. RECALCULAR SECUENCIALMENTE
            $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

            $allRootNodes = PlanNode::where('user_plan_id', $userPlanId)
                ->where('plan_id', $nuevoPlanId)
                ->whereNull('parent_id')
                ->get();

            // Separar y ordenar:
            $preliminares = $allRootNodes->filter(function($n) {
                $t = mb_strtolower($n->titulo);
                return str_contains($t, 'resumen ejecutivo') || str_contains($t, 'introducción');
            })->sortBy('id');

            $finales = $allRootNodes->filter(function($n) {
                $t = mb_strtolower($n->titulo);
                return str_contains($t, 'conclusiones') || str_contains($t, 'recomendaciones');
            })->sortBy('id');

            $capitulosCentrales = $allRootNodes->reject(function($n) use ($preliminares, $finales) {
                return $preliminares->contains('id', $n->id) || $finales->contains('id', $n->id);
            })->sortBy('id');

            // Unir en el orden correcto
            $rootNodes = $preliminares->concat($capitulosCentrales)->concat($finales);

            $globalOrden = 1;
            $capIndex = 1;

            foreach ($rootNodes as $root) {
                $tituloLower = mb_strtolower($root->titulo);
                
                // Detectar si es un apartado especial
                $esEspecial = str_contains($tituloLower, 'resumen ejecutivo') || 
                              str_contains($tituloLower, 'introducción') || 
                              str_contains($tituloLower, 'conclusiones') || 
                              str_contains($tituloLower, 'recomendaciones');

                if ($esEspecial) {
                    // Limpiar prefijos previos y dejar título limpio sin romanos
                    $tituloLimpio = preg_replace('/^(Capítulo\s+[I|V|X|L|C|D|M|\d]+\.?\s*|-*\s*)/i', '', $root->titulo);
                    $root->update([
                        'titulo' => trim($tituloLimpio),
                        'orden'  => $globalOrden,
                        'codigo' => null
                    ]);
                    $globalOrden++;
                } else {
                    $numeroRomano = $romanos[$capIndex] ?? $capIndex;

                    // Renumerar como Capítulo X
                    $tituloLimpio = preg_replace('/^Capítulo\s+[I|V|X|L|C|D|M|\d]+\.?\s*/i', '', $root->titulo);
                    $root->update([
                        'titulo' => "Capítulo {$numeroRomano}. " . ltrim($tituloLimpio, '. '),
                        'orden'  => $globalOrden,
                        'codigo' => (string)$capIndex
                    ]);

                    // Renumerar subnodos de Nivel
                    $childrenL2 = PlanNode::where('parent_id', $root->id)
                        ->orderBy('id', 'asc')
                        ->get();

                    $l2Index = 1;
                    foreach ($childrenL2 as $childL2) {
                        $childL2->update([
                            'orden' => $l2Index,
                            'codigo' => "{$capIndex}.{$l2Index}"
                        ]);

                        // Renumerar subnodos de Nivel 3
                        $childrenL3 = PlanNode::where('parent_id', $childL2->id)
                            ->orderBy('id', 'asc')
                            ->get();

                        $l3Index = 1;
                        foreach ($childrenL3 as $childL3) {
                            $childL3->update([
                                'orden' => $l3Index,
                                'codigo' => "{$capIndex}.{$l2Index}.{$l3Index}"
                            ]);
                            $l3Index++;
                        }

                        $l2Index++;
                    }

                    $capIndex++;
                    $globalOrden++;
                }
            }

            // 4. Clonar preguntas asociadas de forma masiva
            $newQuestions = $questions->map(function ($question) use ($nodeIdMap) {
                if (!isset($nodeIdMap[$question->plan_node_id])) {
                    return null;
                }

                return [
                    'plan_node_id'      => $nodeIdMap[$question->plan_node_id],
                    'question_text'     => $question->question_text,
                    'question_detail'   => $question->question_detail,
                    'question_example'  => $question->question_example,
                    'validation_detail' => $question->validation_detail,
                    'order_index'       => $question->order_index,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            })->filter()->values()->toArray();

            if (!empty($newQuestions)) {
                Question::insert($newQuestions);
            }
        });
    }
    public function startConversation($userId, $userPlanId, $suscriptionId )
    {
         return DB::transaction(function () use ($userId,  $userPlanId, $suscriptionId) {

            // Crear conversación asociada a esa suscripción
            $conversation = Conversation::create([
                // 'user_id' => $userId,
                'user_plan_id' => $userPlanId,
                'subscription_id' => $suscriptionId ,
                'status' => 'active',
                'title' => 'Nuevo plan',
                'summary' => null,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);

            // SOLO ARRAY (NO RESPONSE)
            return [
                'status' => 200,
                'conversation_id' => $conversation->id
            ];
        });
    }
    public function getConversation(int $idConversation): array
    {
         return UserAnswers::where('conversation_id', $idConversation)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function ($row) {
                return !empty($row->answer_text);
            })
            ->map(function ($row) {
                return [
                    'role' => 'user',
                    'content' => $row->answer_text,
                ];
        })->values()->toArray();
    }

    public function getHistoryLimit10(int $idConversation): array
    {
         return UserAnswers::where('conversation_id', $idConversation)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->filter(function ($row) {
                    return !empty($row->answer_text);
                })
                ->reverse()
                ->map(function ($row) {
                    return [
                        'role' => 'user',
                        'content' => $row->answer_text,
                    ];
                })
                ->values()
                ->toArray();
    }

   public function getHistorysentens(int $idConversation): array
{
    return UserAnswers::where('user_answers.conversation_id', $idConversation)

        ->join('questions', 'questions.id', '=', 'user_answers.question_id')
        ->join('plan_node', 'plan_node.id', '=', 'questions.plan_node_id')
        ->select('user_answers.*', 'plan_node.parent_id', 'plan_node.id as node_id')
        ->orderBy('user_answers.created_at', 'asc')
        ->get()

        ->filter(function ($row) {
            return !empty($row->answer_text);
        })

        ->groupBy(function ($row) {
            return $row->parent_id ?? $row->node_id;
        })
        ->flatMap(function ($group) {
            return $group->take(4);
        })

        ->map(function ($row) {
            return [
                'role' => 'user',
                'content' => $row->answer_text,
            ];
        })
        ->values()
        ->toArray();
}

    public function saveMessage(array $data, String $message , String $role )
    {
        return DB::transaction(function () use ($data, $message, $role) {

            $conversationId = $data['idConversation'];
            $questionId = $data['idQuestion'];

            // Agregar image
            if (!empty($data['is_visual']) && $data['is_visual']) {

                $newImageCount = 1;

            } else {
                $newImageCount = 0;
            }

            // Guardar mensaje 
            DB::table('conversation_messages')->insert([
                'conversation_id' => $conversationId,
                // 'section_id' => $sectionId,
                'question_id' => $questionId,
                'role' => $role,
                'message_text' => $message,
                'image_ia_count' => $newImageCount,
                'created_at' => now()
            ]);

            return response()->json([
                'message' => 'Conversation Save'
            ]);
        });
    }
    public function IAimagesXQuestion( array $data ){

        $conversationId = $data['idConversation'];
        $questionId = $data['idQuestion'];
         // Contar imágenes IA usadas para esta pregunta
        $imageCount = DB::table('conversation_messages')
        ->where('conversation_id', $conversationId)
        ->where('question_id', $questionId)
        ->where('role', 'user')
        ->sum('image_ia_count');

        return  $imageCount ;
    }

    public function saveUserAnswerAutomatic($conversationId, $questionId, $reply)
    {
        UserAnswers::updateOrCreate(
            [
                'conversation_id' => $conversationId,
                'question_id' => $questionId,
            ],
            [
                'user_id' => auth()->id(),
                'answer_text' => $reply,
            ]
        );

        // Dispara la consolidación si esta respuesta automática completa el capítulo
        $this->checkAndConsolidateChapter($conversationId, $questionId);
    }

    public function saveUserAnswer(
        array $data,
        array $files = [],
        array $metadata = [],
        array $references = []
    ) {

        DB::beginTransaction();

        try {

            $conversationId = $data['idConversation'];
            // $sectionId = $data['idSection'];
            $questionId = $data['idQuestion'];
            $reply = $data['reply'] ?? null;

            /**
             * ============================================
             * GUARDAR / ACTUALIZAR RESPUESTA
             * ============================================
             */
            $userAnswer = UserAnswers::updateOrCreate(
                [
                    'conversation_id' => $conversationId,
                    'question_id' => $questionId,
                ],
                [
                    'user_id' => auth()->id(),
                    // 'section_id' => $sectionId,
                    'answer_text' => $reply,
                ]
            );
           
            /**
             * ============================================
             * GUARDAR ARCHIVOS
             * ============================================
             */
            $path = '';

            if (!empty($data['url_imagen_ia'])) {

                $imageResponse = Http::get($data['url_imagen_ia']);

                if ($imageResponse->successful()) {

                    $extension = 'webp';

                    $filename = 'imagen_ia_' . time() . '.' . $extension;

                    $path = "answers/{$userAnswer->id}/{$filename}";

                    Storage::disk('public')->put(
                        $path,
                        $imageResponse->body()
                    );
                    $mimeType = 'image/webp';
                    $fileSize = Storage::disk('public')->size($path);

                    DB::table('answer_files')->insert([
                        'answer_id' => $userAnswer->id,

                        'file_type' => 'image',

                        'file_path' => $path,

                        'original_name' => 'imagen_ia.webp',

                        'mime_type' => $mimeType,

                        'size' => $fileSize,
                        
                        'description' => $data['desc_imagen_ia'],
                        'fuente' => 'Elaboración propia',

                        'created_at' => now(),
                    ]);
                }
            }
            foreach ($files as $index => $file) {

                $meta = $metadata[$index] ?? [];

                // Guarda el archivo solo si es imagen
                $path = '';
                if ($file && in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $path = $file->store(
                        "answers/{$userAnswer->id}",
                        'public'
                    );
                }
                $extension = strtolower($file->getClientOriginalExtension());

                $parsedData = null;
                 // SOLO documentos procesables
                if (in_array($extension, ['xls', 'xlsx'])) {

                    $parsedData = $this->excelService->extractTable($file->getRealPath());

                    $sheet = $parsedData['sheets'][0] ?? [];

                    \Log::info('USER ANSWER ID', [
                        'id' => $userAnswer->id
                    ]);

                    $tabla = Tabla::create([
                        'nombre' => pathinfo(  $file->getClientOriginalName(), PATHINFO_FILENAME ) ,
                        'answer_id' => $userAnswer->id ,
                        'data' => json_encode($parsedData)
                    ]);
                }

                DB::table('answer_files')->insert([
                    'answer_id' => $userAnswer->id,

                    'file_type' => str_contains(
                        $file->getMimeType(),
                        'image'
                    ) ? 'image' : 'document',

                    'file_path' => $path,

                    'original_name' => $file->getClientOriginalName(),

                    'mime_type' => $file->getMimeType(),

                    'size' => $file->getSize(),
                    
                    'description' => $meta['description'] ?? null,
                    'fuente' => $meta['fuente'] ?? null,

                    'metadata'=> json_encode([
                        'type' => 'excel',

                        'file_info' => [
                            'rows_total' => $parsedData['rows_total'] ?? 0,
                            'sheets_count' => $parsedData['sheets_count'] ?? 0,
                        ],

                        'data' => $parsedData['sheets'] ?? [],

                        'headers' => $parsedData['sheets'][0][0] ?? null,

                        'ai_ready' => true
                    ]),

                    'created_at' => now(),
                ]);
            }

            // TABLA GENERADA X IA
            $table = null;

            if (!empty($data['table'])) {

                $table = json_decode($data['table'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::error('Error decodificando tabla', [
                        'table' => $data['table'],
                        'error' => json_last_error_msg()
                    ]);

                    $table = null;
                }
            }

            if (
                is_array($table) &&
                isset($table['title']) &&
                isset($table['columns']) &&
                isset($table['rows'])
            ) {

                Tabla::create([
                    'nombre' => $table['title'],
                    'fuente' => 'Elaboración propia',
                    'answer_id' => $userAnswer->id,
                    'data' => json_encode($table, JSON_UNESCAPED_UNICODE)
                ]);

                DB::table('answer_files')->insert([
                    'answer_id' => $userAnswer->id,
                    'file_path' => $table['title'] ,
                    'file_type' => 'document',
                    
                    'description' => $table['title'] ?? null,
                    'fuente' => 'Elaboración propia',
                    'created_at' => now(),
                ]);
            }

            /**
             * ============================================
             * GUARDAR REFERENCIAS
             * ============================================
             */
            if (!empty($references)) {
                foreach ($references as $index => $ref) {

                    $hash = hash(
                        'sha256',
                        strtolower(
                            ($ref['title'] ?? '') .
                            ($ref['year'] ?? '') .
                            implode(',', $ref['authors'] ?? [])
                        )
                    );

                    $reference = DB::table('references_library')
                        ->where('reference_hash', $hash)
                        ->first();

                    if (!$reference) {

                        $referenceId = DB::table('references_library')
                            ->insertGetId([
                                'authors' => json_encode(
                                    $ref['authors'] ?? []
                                ),

                                'publication_year' => $ref['year'] ?? null,

                                'title' => $ref['title'] ?? '',

                                'publisher' => $ref['publisher'] ?? null,

                                'url' => $ref['url'] ?? null,

                                'source_type' => $ref['source_type'] ?? 'book',

                                'doi' => $ref['doi'] ?? null,

                                // 'apa_citation' => $ref['apa_citation'] ?? null,

                                'reference_hash' => $hash,

                                'created_at' => now(),

                                'updated_at' => now(),
                            ]);

                    } else {

                        $referenceId = $reference->id;
                    }

                    /**
                     * RELACIONAR RESPUESTA ↔ REFERENCIA
                     */
                    DB::table('answer_reference_rel')
                        ->updateOrInsert(
                            [
                                'answer_id' => $userAnswer->id,
                                'reference_id' => $referenceId,
                            ],
                            [
                                'citation_order' => $index + 1,
                                'created_at' => now(),
                            ]
                        );
                }
            }

            // =========================================================================
            // VERIFICACIÓN DE CAPÍTULO COMPLETADO PARA ACTUALIZAR THESIS_CONTEXT JSON
            // =========================================================================
            $this->checkAndConsolidateChapter($conversationId, $questionId);

            DB::commit();

            return [
                'success' => true,
                'answer_id' => $userAnswer->id
            ];

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('saveUserAnswer ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]); 

            throw $e;
        }
    }

    public function sectionCompleted( $sectionId, $conversationId){

        $questionIds = Question::where('section_id', $sectionId)->pluck('id');

        $answeredIds = UserAnswers::where('conversation_id', $conversationId)
                                    ->whereIn('question_id', $questionIds)
                                    ->pluck('question_id');

        $allAnswered = $questionIds->diff($answeredIds)->isEmpty();

        return $allAnswered;

    }

    private function checkAndConsolidateChapter(int $conversationId, int $questionId): void
    {
        try {
            $question = Question::find($questionId);
            if (!$question || !$question->plan_node_id) return;

            $currentNode = PlanNode::find($question->plan_node_id);
            if (!$currentNode) return;

            // 1. Determinar el nodo raíz del capítulo
            $chapterNode = $currentNode;
            while ($chapterNode->parent_id !== null) {
                $parent = PlanNode::find($chapterNode->parent_id);
                if (!$parent) break;
                $chapterNode = $parent;
            }

            // 2. Obtener los IDs de subnodos del capítulo
            $descendantIds = PlanNode::where('parent_id', $chapterNode->id)->pluck('id')->toArray();
            $allNodeIds = array_unique(array_merge([$chapterNode->id], $descendantIds));

            // Si existen sub-subnodos, los agregamos también
            if (!empty($descendantIds)) {
                $subDescendantIds = PlanNode::whereIn('parent_id', $descendantIds)->pluck('id')->toArray();
                $allNodeIds = array_unique(array_merge($allNodeIds, $subDescendantIds));
            }

            // 3. Obtener todas las preguntas del capítulo
            $chapterQuestions = Question::whereIn('plan_node_id', $allNodeIds)->get();
            if ($chapterQuestions->isEmpty()) return;

            $questionIds = $chapterQuestions->pluck('id');

            // 4. Contar respuestas válidas del usuario en esta conversación
            $answeredList = UserAnswers::where('conversation_id', $conversationId)
                ->whereIn('question_id', $questionIds)
                ->whereNotNull('answer_text')
                ->where('answer_text', '!=', '')
                ->get();

            // 5. Si todas las preguntas del capítulo ya están respondidas
            if ($answeredList->count() >= $questionIds->count()) {

                // Obtenemos solo los capítulos (nivel 1) de este plan ordenados
            $rootNodes = PlanNode::where('user_plan_id', $chapterNode->user_plan_id)
                ->whereNull('parent_id')
                ->where('execution_phase', 1)
                ->orderBy('orden', 'asc')
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();

            $index = array_search($chapterNode->id, $rootNodes);
            $numeroCapitulo = ($index !== false) ? ($index + 1) : 1;

                $qaData = [];
                foreach ($chapterQuestions as $q) {
                    $ans = $answeredList->firstWhere('question_id', $q->id);
                    if ($ans) {
                        $qaData[] = [
                            'pregunta' => $q->question_text,
                            'respuesta' => $ans->answer_text,
                        ];
                    }
                }

                // Disparamos la consolidación del plan_context
                $this->aiService->consolidarCapituloPlanContext(
                    $conversationId,
                    $numeroCapitulo,
                    $chapterNode->titulo ?? "Capítulo {$numeroCapitulo}",
                    $qaData
                );
            }
        } catch (\Throwable $e) {
            \Log::error("Error en checkAndConsolidateChapter: " . $e->getMessage());
        }
    }

    /*
      Elimina en cascada las respuestas desde el capítulo seleccionado en adelante (Solo Fase 1)
    */
    public function deleteChaptersAnswersFrom(int $conversationId, int $startChapterNodeId): array
    {
        return DB::transaction(function () use ($conversationId, $startChapterNodeId) {

            $conversation = Conversation::findOrFail($conversationId);
            $userPlanId = $conversation->user_plan_id;

            // 1. Obtener SOLO los capítulos raíz de Fase 1
            $rootNodes = PlanNode::where('user_plan_id', $userPlanId)
                ->whereNull('parent_id')
                ->where('execution_phase', 1)
                ->orderBy('orden', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $startNode = $rootNodes->firstWhere('id', $startChapterNodeId);

            if (!$startNode) {
                throw new \Exception('El capítulo seleccionado no pertenece a la fase de desarrollo de capítulos.');
            }

            // 2. Identificar todos los capítulos de Fase 1 desde el seleccionado en adelante
            $startDeleting = false;
            $targetRootIds = [];

            foreach ($rootNodes as $root) {
                if ($root->id == $startNode->id) {
                    $startDeleting = true;
                }
                if ($startDeleting) {
                    $targetRootIds[] = $root->id;
                }
            }

            // 3. Obtener (Nivel 2 y Nivel 3) de todos los capítulos afectados
            $level2Ids = PlanNode::whereIn('parent_id', $targetRootIds)->pluck('id')->toArray();
            $level3Ids = !empty($level2Ids)
                ? PlanNode::whereIn('parent_id', $level2Ids)->pluck('id')->toArray()
                : [];

            $allNodeIds = array_unique(array_merge($targetRootIds, $level2Ids, $level3Ids));
            // 4. Obtener todas las preguntas de esos nodos
            $questionIds = Question::whereIn('plan_node_id', $allNodeIds)->pluck('id')->toArray();

            if (!empty($questionIds)) {
                // 5. Obtener las respuestas registradas en esta conversación
                $answers = UserAnswers::where('conversation_id', $conversationId)
                    ->whereIn('question_id', $questionIds)
                    ->get();

                $answerIds = $answers->pluck('id')->toArray();

                if (!empty($answerIds)) {
                    // Eliminar carpetas de archivos físicos en storage
                    foreach ($answerIds as $ansId) {
                        Storage::disk('public')->deleteDirectory("answers/{$ansId}");
                    }

                    // Eliminar registros dependientes de las respuestas
                    DB::table('answer_files')->whereIn('answer_id', $answerIds)->delete();
                    DB::table('answer_reference_rel')->whereIn('answer_id', $answerIds)->delete();
                    Tabla::whereIn('answer_id', $answerIds)->delete();

                    // Eliminar las respuestas del usuario
                    UserAnswers::whereIn('id', $answerIds)->delete();
                }

                // Eliminar historial de mensajes IA de esas preguntas
                DB::table('conversation_messages')
                    ->where('conversation_id', $conversationId)
                    ->whereIn('question_id', $questionIds)
                    ->delete();
            }

            // 6. Limpiar ThesisContext (cap_X consolidados de los capítulos eliminados)
            $thesisContext = \App\Models\ThesisContext::where('conversation_id', $conversationId)->first();

            if ($thesisContext && is_array($thesisContext->plan_context)) {
                $planContext = $thesisContext->plan_context;

                foreach ($rootNodes->values() as $idx => $p1Node) {
                    if (in_array($p1Node->id, $targetRootIds)) {
                        $capNum = $idx + 1;
                        $capKey = "cap_{$capNum}";
                        if (array_key_exists($capKey, $planContext)) {
                            $planContext[$capKey] = null;
                        }
                    }
                }

                $thesisContext->plan_context = $planContext;
                $thesisContext->save();
            }

            return [
                'deleted_chapters_count' => count($targetRootIds),
                'deleted_questions_count' => count($questionIds)
            ];
        });
    }
    
}