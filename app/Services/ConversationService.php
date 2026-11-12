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
use App\Models\ThesisContext;
use App\Models\Annex;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class ConversationService
{
    // Etiquetas del mensaje del tesista en la bitácora (conversation_messages).
    public const LABEL_RESPUESTA = 'RESPUESTA DEL TESISTA:';
    public const LABEL_IMAGEN = 'EVIDENCIA VISUAL:';
    public const LABEL_DOCUMENTO = 'DOCUMENTACIÓN:';

    // copias de "Volver a la pregunta anterior", por pregunta
    public const RESTORE_KEY = 'retroceso';
    // pregunta en la que Impulsa extrajo cada dato de assistant_context
    public const ORIGIN_KEY = 'assistant_context_origen';

    public $excelService;

    protected AIService $aiService;

    public function __construct(ExcelService $excelService, AIService   $aiService,
                                private StructureService $structureService)
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
            // 3. RECALCULAR SECUENCIALMENTE CÓDIGOS, TÍTULOS Y NIVELES (todos los niveles)
            $this->structureService->recalculateStructure($nuevoPlanId, $userPlanId);

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

        $this->clearRestoredAnswer($conversationId, $questionId);

        // Dispara la consolidación si esta respuesta automática completa el capítulo
        $this->checkAndConsolidateChapter($conversationId, $questionId);
    }

    public function saveUserAnswer(
        array $data,
        array $files = [],
        array $metadata = [],
        array $references = []
    ) {

        // Del Excel solo se usa la primera hoja: con varias, la tabla podría no ser la esperada
        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['xls', 'xlsx']) && ($sheets = $this->excelService->sheetsCount($file->getRealPath())) > 1) {
                throw new \DomainException(
                    'El archivo tiene más de una hoja. Deja solo la que contiene tu información y vuelve a intentarlo.'
                );
            }
        }

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

            // Si se había vuelto a esta pregunta, la copia de lo recuperado ya no hace falta
            $this->clearRestoredAnswer($conversationId, $questionId);

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

            // Solo se consolidan los capítulos de la Fase 1. Las secciones de la Fase 2
            // (resumen ejecutivo, introducción, conclusiones, recomendaciones) no están en la
            // numeración cap_1, cap_2...: antes se guardaban como cap_1 y lo sobrescribían.
            if ((int) $chapterNode->execution_phase !== 1) return;

            // 2. Obtener los IDs de todos los subnodos del capítulo, en cualquier nivel
            $allNodeIds = $this->subtreeNodeIds($chapterNode->user_plan_id, $chapterNode->id);

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

    /**
     * @throws \DomainException
     */
    public function deleteAnswersFrom(int $conversationId, ?int $startNodeId, ?int $startQuestionId = null): array
    {
        return DB::transaction(function () use ($conversationId, $startNodeId, $startQuestionId) {

            $conversation = Conversation::findOrFail($conversationId);
            $userPlanId = $conversation->user_plan_id;

            // Solo contiene preguntas de la Fase 1 de esta estructura: una pregunta ajena no se encuentra
            $ordered = $this->orderedQuestions($userPlanId, 1);

            // 1. Desde el punto de inicio hasta el final de la Fase 1
            $startIndex = $startQuestionId !== null
                ? $this->questionStartIndex($ordered, $startQuestionId)
                : $this->nodeStartIndex($ordered, $userPlanId, (int) $startNodeId);

            $phase1Items = array_slice($ordered, $startIndex);
            $affectedRootIds = array_unique(array_column($phase1Items, 'root_id'));

            // 2. Más todas las preguntas de la Fase 2
            $questionIds = array_merge(
                array_map(fn ($item) => $item['question']->id, $phase1Items),
                array_map(fn ($item) => $item['question']->id, $this->orderedQuestions($userPlanId, 2))
            );

            $deletedAnswers = UserAnswers::where('conversation_id', $conversationId)
                ->whereIn('question_id', $questionIds)
                ->count();

            // 3. Eliminar las respuestas de esas preguntas y todo lo asociado
            $this->deleteAnswersOf($conversationId, $questionIds);

            // El plan deja de estar completo: los anexos ya no corresponden al contenido
            $this->deleteAnnexesOf($conversationId);

            // 4. Limpiar la memoria del proyecto (thesis_context)
            $this->cleanContextAfterDelete($conversationId, $userPlanId, $affectedRootIds, $questionIds);

            return [
                'deleted_answers_count' => $deletedAnswers,
                'affected_chapters_count' => count($affectedRootIds),
            ];
        });
    }

    // El plan está completo cuando no queda ninguna pregunta sin responder, ni de la Fase 1 ni de la Fase 2
    public function isPlanCompleted(Conversation $conversation): bool
    {
        $answered = array_flip(
            UserAnswers::where('conversation_id', $conversation->id)
                ->whereNotNull('question_id')
                ->pluck('question_id')
                ->all()
        );

        foreach ([1, 2] as $phase) {
            $ordered = $this->orderedQuestions($conversation->user_plan_id, $phase);

            if ($this->firstUnansweredIndex($ordered, $answered) !== null) {
                return false;
            }
        }

        return true;
    }

    // Elimina los anexos de la conversación con sus archivos
    public function deleteAnnexesOf(int $conversationId): void
    {
        $annexes = Annex::where('conversation_id', $conversationId)->get(['id', 'file_path']);

        Storage::disk('public')->delete($annexes->pluck('file_path')->all());
        Annex::whereIn('id', $annexes->pluck('id'))->delete();
    }

    // Posición de esa pregunta en el orden de la Fase 1
    private function questionStartIndex(array $ordered, int $questionId): int
    {
        foreach ($ordered as $index => $item) {
            if ((int) $item['question']->id === $questionId) {
                return $index;
            }
        }

        throw new \DomainException('La pregunta seleccionada no pertenece a la fase de desarrollo de capítulos.');
    }

    // Posición de la primera pregunta del nodo (o de sus subnodos) en el orden de la Fase 1
    private function nodeStartIndex(array $ordered, int $userPlanId, int $nodeId): int
    {
        $startNode = PlanNode::where('id', $nodeId)
            ->where('user_plan_id', $userPlanId)
            ->where('execution_phase', 1)
            ->first();

        if (!$startNode) {
            throw new \DomainException('La sección seleccionada no pertenece a la fase de desarrollo de capítulos.');
        }

        $subtreeIds = array_flip($this->subtreeNodeIds($userPlanId, $startNode->id));

        foreach ($ordered as $index => $item) {
            if (isset($subtreeIds[$item['question']->plan_node_id])) {
                return $index;
            }
        }

        throw new \DomainException('La sección seleccionada no tiene preguntas.');
    }

    // Id del nodo y de todos sus descendientes, en cualquier nivel
    private function subtreeNodeIds(int $userPlanId, int $nodeId): array
    {
        $childrenByParent = PlanNode::where('user_plan_id', $userPlanId)
            ->get(['id', 'parent_id'])
            ->groupBy('parent_id');

        $ids = [];
        $pending = [$nodeId];

        while ($pending) {
            $current = array_pop($pending);
            $ids[] = $current;

            foreach ($childrenByParent->get($current, collect()) as $child) {
                $pending[] = $child->id;
            }
        }

        return $ids;
    }

    private function cleanContextAfterDelete(int $conversationId, int $userPlanId, array $affectedRootIds, array $questionIds): void
    {
        $context = ThesisContext::where('conversation_id', $conversationId)->first();

        if (!$context) {
            return;
        }

        $planContext = $context->plan_context ?? [];

        // Capítulos de la Fase 1 en el mismo orden que usa checkAndConsolidateChapter (cap_1, cap_2...)
        $rootNodes = PlanNode::where('user_plan_id', $userPlanId)
            ->whereNull('parent_id')
            ->where('execution_phase', 1)
            ->orderBy('orden', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingSummaries = [];

        foreach ($rootNodes->values() as $index => $root) {
            $capKey = 'cap_' . ($index + 1);

            if (in_array($root->id, $affectedRootIds)) {
                $planContext[$capKey] = null;
            } elseif (!empty($planContext[$capKey])) {
                $remainingSummaries[] = "{$root->titulo}:\n{$planContext[$capKey]}";
            }
        }

        $planContext['resumen_general'] = $remainingSummaries ? implode("\n\n", $remainingSummaries) : null;

        // Datos de Impulsa extraídos en las preguntas borradas
        $assistantData = $context->assistant_context ?? [];
        $origins = $planContext[self::ORIGIN_KEY] ?? [];

        foreach ($origins as $key => $originQuestionId) {
            if (in_array((int) $originQuestionId, $questionIds)) {
                unset($assistantData[$key], $origins[$key]);
            }
        }

        $planContext[self::ORIGIN_KEY] = $origins;

        // Copias de "Volver a la pregunta anterior" de las preguntas borradas
        foreach ($questionIds as $questionId) {
            unset($planContext[self::RESTORE_KEY][$questionId]);
        }

        $context->assistant_context = $assistantData;
        $context->plan_context = $planContext;
        $context->save();
    }

    // Elimina las respuestas de esas preguntas con sus archivos, tablas, referencias y mensajes de la bitácora
    private function deleteAnswersOf(int $conversationId, array $questionIds): void
    {
        if (empty($questionIds)) {
            return;
        }

        $answerIds = UserAnswers::where('conversation_id', $conversationId)
            ->whereIn('question_id', $questionIds)
            ->pluck('id')
            ->toArray();

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

    /**
     * @return array<int, array{question: Question, root_id: int}>
     */
    public function orderedQuestions(int $userPlanId, int $phase): array
    {
        $nodes = PlanNode::with(['questions' => fn ($query) => $query->orderBy('order_index')])
            ->where('user_plan_id', $userPlanId)
            ->where('execution_phase', $phase)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        $nodesByParent = $nodes->groupBy('parent_id');
        $ordered = [];

        $walk = function ($parentId, ?int $rootId) use (&$walk, &$ordered, $nodesByParent) {
            $children = $nodesByParent->get($parentId, collect())
                ->sortBy([['orden', 'asc'], ['id', 'asc']]);

            foreach ($children as $node) {
                $chapterId = $rootId ?? $node->id;

                foreach ($node->questions as $question) {
                    $ordered[] = ['question' => $question, 'root_id' => $chapterId];
                }

                $walk($node->id, $chapterId);
            }
        };

        $walk(null, null);

        return $ordered;
    }

    /**
     * Indica si se puede volver desde la pregunta actual
     * @return array{allowed: bool, reason: ?string, previous: ?Question, current: ?Question}
     */
    public function goBackInfo(Conversation $conversation, ?int $questionId = null): array
    {
        $deny = fn (string $reason) => ['allowed' => false, 'reason' => $reason, 'previous' => null, 'current' => null];
        $closedMessage = 'Estás en la primera pregunta de este capítulo. El capítulo anterior ya está cerrado.';

        $answered = array_flip(
            UserAnswers::where('conversation_id', $conversation->id)
                ->whereNotNull('question_id')
                ->pluck('question_id')
                ->all()
        );

        // Misma regla que getConversationPlan: la Fase 2 empieza cuando la Fase 1 está completa
        $phase = 1;
        $ordered = $this->orderedQuestions($conversation->user_plan_id, 1);
        $currentIndex = $this->firstUnansweredIndex($ordered, $answered);

        if ($currentIndex === null) {
            $phase = 2;
            $ordered = $this->orderedQuestions($conversation->user_plan_id, 2);
            $currentIndex = $this->firstUnansweredIndex($ordered, $answered);
        }

        if ($currentIndex === null) {
            return $deny('El plan ya está completo.');
        }

        $current = $ordered[$currentIndex];

        if ($questionId !== null && (int) $current['question']->id !== $questionId) {
            return $deny('La pregunta que estás viendo ya no es la actual. Recarga la página.');
        }

        if ($currentIndex === 0) {
            return $deny($phase === 1 ? 'Estás en la primera pregunta del plan.' : $closedMessage);
        }

        $previous = $ordered[$currentIndex - 1];

        // Solo dentro del capítulo actual: un capítulo cerrado ya está en el resumen general
        if ($previous['root_id'] !== $current['root_id']) {
            return $deny($closedMessage);
        }

        return [
            'allowed'  => true,
            'reason'   => null,
            'previous' => $previous['question'],
            'current'  => $current['question'],
        ];
    }

    /**
     * @throws \DomainException
     */
    public function goBack(Conversation $conversation, int $questionId): array
    {
        return DB::transaction(function () use ($conversation, $questionId) {

            $info = $this->goBackInfo($conversation, $questionId);

            if (!$info['allowed']) {
                throw new \DomainException($info['reason']);
            }

            $previous = $info['previous'];
            $current = $info['current'];

            $answer = UserAnswers::with(['files', 'tables'])
                ->where('conversation_id', $conversation->id)
                ->where('question_id', $previous->id)
                ->first();

            // Respuesta automática de la IA: no hay texto del tesista, se carga la respuesta guardada
            $copy = [
                'user_input' => $this->lastUserInput($conversation->id, $previous->id)
                    ?? (string) ($answer?->answer_text ?? ''),
                'table'      => $this->aiTableOf($answer),
                'files'      => $this->attachedFileNames($answer),
                'created_at' => now()->toDateTimeString(),
            ];

            $this->deleteAnswersOf($conversation->id, [$previous->id]);

            $context = ThesisContext::firstOrCreate(
                ['conversation_id' => $conversation->id],
                ['user_plan_id' => $conversation->user_plan_id, 'confidence' => 0]
            );

            $planContext = $context->plan_context ?? [];
            $assistantData = $context->assistant_context ?? [];
            $origins = $planContext[self::ORIGIN_KEY] ?? [];

            // Datos de Impulsa extraídos en la pregunta borrada o en la actual, que también se descarta
            foreach ($origins as $key => $originQuestionId) {
                if (in_array((int) $originQuestionId, [(int) $previous->id, (int) $current->id], true)) {
                    unset($assistantData[$key], $origins[$key]);
                }
            }

            $planContext[self::ORIGIN_KEY] = $origins;
            // Solo la pregunta a la que se vuelve recupera su texto. Las copias anteriores se
            // descartan: si se vuelve de la 4 a la 3, al responder la 3 la 4 aparece vacía.
            $planContext[self::RESTORE_KEY] = [$previous->id => $copy];

            $context->assistant_context = $assistantData;
            $context->plan_context = $planContext;
            $context->save();

            return ['question_id' => $previous->id];
        });
    }

    // Copia guardada al volver a esta pregunta, o null si no hay
    public function restoredAnswer(int $conversationId, int $questionId): ?array
    {
        $planContext = ThesisContext::where('conversation_id', $conversationId)->first()?->plan_context;

        return $planContext[self::RESTORE_KEY][$questionId] ?? null;
    }

    // La pregunta ya se volvió a responder: la copia no hace falta
    private function clearRestoredAnswer(int $conversationId, int $questionId): void
    {
        $context = ThesisContext::where('conversation_id', $conversationId)->first();
        $planContext = $context?->plan_context ?? [];

        if (!isset($planContext[self::RESTORE_KEY][$questionId])) {
            return;
        }

        unset($planContext[self::RESTORE_KEY][$questionId]);
        $context->plan_context = $planContext;
        $context->save();
    }

    private function firstUnansweredIndex(array $ordered, array $answered): ?int
    {
        foreach ($ordered as $index => $item) {
            if (!isset($answered[$item['question']->id])) {
                return $index;
            }
        }

        return null;
    }

    // Lo que escribió el tesista en su última validación de esa pregunta (null si no validó)
    private function lastUserInput(int $conversationId, int $questionId): ?string
    {
        $message = DB::table('conversation_messages')
            ->where('conversation_id', $conversationId)
            ->where('question_id', $questionId)
            ->where('role', 'user')
            ->orderByDesc('id')
            ->value('message_text');

        if ($message === null) {
            return null;
        }

        $message = trim($message);

        if ($message === '[El tesista no proporcionó información]') {
            return '';
        }

        $start = mb_strpos($message, self::LABEL_RESPUESTA);

        if ($start === false) {
            // Solo adjuntó archivos; o es un mensaje anterior a las etiquetas, que se carga completo
            $onlyFiles = str_starts_with($message, self::LABEL_IMAGEN) || str_starts_with($message, self::LABEL_DOCUMENTO);
            return $onlyFiles ? '' : $message;
        }

        $text = mb_substr($message, $start + mb_strlen(self::LABEL_RESPUESTA));

        foreach ([self::LABEL_IMAGEN, self::LABEL_DOCUMENTO] as $label) {
            $end = mb_strpos($text, "\n\n" . $label);
            if ($end !== false) {
                $text = mb_substr($text, 0, $end);
            }
        }

        return trim($text);
    }

    // Tabla generada con "Generar cuadro"
    private function aiTableOf(?UserAnswers $answer): ?array
    {
        foreach ($answer?->tables ?? [] as $tabla) {
            $data = is_string($tabla->data) ? json_decode($tabla->data, true) : $tabla->data;

            if (is_array($data) && isset($data['columns'], $data['rows'])) {
                return [
                    'title'   => $data['title'] ?? $tabla->nombre,
                    'columns' => $data['columns'],
                    'rows'    => $data['rows'],
                ];
            }
        }

        return null;
    }

    // Nombres de los archivos guardados con la respuesta, para avisar que hay que volver a adjuntarlos
    private function attachedFileNames(?UserAnswers $answer): array
    {
        return collect($answer?->files ?? [])
            ->filter(fn ($file) => !empty($file->original_name))
            ->map(fn ($file) => $file->original_name === 'imagen_ia.webp' ? 'Imagen generada por la IA' : $file->original_name)
            ->values()
            ->all();
    }
}