<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\UserSubscription;
use App\Models\Conversation; 
use App\Models\UserAnswers;
use App\Models\Question; 

class ConversationService
{
    public $excelService;
    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
    }
    public function startConversation($userId, $planId)
    {
         return DB::transaction(function () use ($userId, $planId) {

            $subscription = UserSubscription::firstOrCreate([
                'user_id' => $userId,
                'plan_id' => $planId,
                'status' => 'active',
            ]);

           $conversation = Conversation::firstOrCreate(
                [
                    'user_id' => $userId,
                    'subscription_id' => $subscription->id,
                    'status' => 'active',
                ],
                [
                    'title' => 'Nueva conversación',
                    'summary' => null,
                    'started_at' => now(),
                    'last_activity_at' => now(),
                ]
            );

            return [
                'subscription_id' => $subscription->id,
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


    public function saveMessage(array $data, String $message , String $role )
    {
        return DB::transaction(function () use ($data, $message, $role) {

            $conversationId = $data['idConversation'];
            $sectionId = $data['idSection'];
            $questionId = $data['idQuestion'];
            //$reply = $data['reply'];


            // Guardar mensaje 
            DB::table('conversation_messages')->insert([
                'conversation_id' => $conversationId,
                'section_id' => $sectionId,
                'question_id' => $questionId,
                'role' => $role,
                'message_text' => $message,
                'created_at' => now()
            ]);

            // Actualizar progreso
            // DB::table('conversation_section_progress')
            //     ->updateOrInsert(
            //         [
            //             'conversation_id' => $conversationId,
            //             'section_id' => $sectionId,
            //             'current_question_id' => $questionId,
            //         ],
            //         [
            //             'status' => 'in_progress' ,
            //             'updated_at' => now(),
            //             'user_id' => auth()->id(),
            //         ]
            //     );

            return response()->json([
                'message' => 'Conversation Save',
            ]);
        });
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
            $sectionId = $data['idSection'];
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
                    'section_id' => $sectionId,
                    'answer_text' => $reply,
                ]
            );

            /**
             * ============================================
             * GUARDAR ARCHIVOS
             * ============================================
             */
            foreach ($files as $index => $file) {

                $meta = $metadata[$index] ?? [];

                $path = $file->store(
                    "answers/{$userAnswer->id}",
                    'public'
                );
                $extension = strtolower($file->getClientOriginalExtension());

                $parsedData = null;
                 // 🧠 SOLO documentos procesables
                if (in_array($extension, ['xls', 'xlsx'])) {

                     $parsedData = $this->excelService->extractTable($file->getRealPath());
                    
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

            /**
             * ============================================
             * GUARDAR REFERENCIAS
             * ============================================
             */
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

    // public function saveUserAnswer( array $data,  $files = [], $metadata = [], $references = [] ){
        
    //     return DB::transaction(function () use ( $data ) {
        
    //         $conversationId = $data['idConversation'];
    //         $sectionId = $data['idSection'];
    //         $questionId = $data['idQuestion'];
    //         $reply = $data['reply'];
            
            
    //         // 1. Obtener progreso actual
    //         $progress = DB::table('conversation_section_progress')
    //             ->where('conversation_id', $conversationId)
    //             ->where('section_id', $sectionId)
    //             ->where('current_question_id', $questionId)
    //             ->first();

    //         //if (!$progress) {
    //         //    throw new \Exception('No existe progreso para esta sección');
    //         //}

    //         $currentSectionId = $progress->section_id ?? $sectionId;
    //         $currentQuestionId = $progress->section_id->current_question_id ?? $questionId;

    //         /*if (!$currentQuestionId) {
    //             throw new \Exception('No hay pregunta actual');
    //         }*/

    //         // 2. Guardar respuesta estructurada y obtenemos el ID 
    //         $userAnswer = UserAnswers::updateOrCreate(
    //             [
    //                 'conversation_id' => $conversationId,
    //                 'question_id' => $questionId,
    //             ],
    //             [
    //                 'user_id' => auth()->id(),
    //                 'section_id' => $sectionId,
    //                 'answer_text' => $reply,
    //             ]
    //         );

    //         $userAnswerId = $userAnswer->id;
           
    //         // Este estado se modificara cunado se haya completado la construcion de la section ya que la tabla solo acepta una conversastion x section

    //         $allAnswered = $this->sectionCompleted($sectionId, $conversationId);

    //         if( $allAnswered ){
    //             DB::table('conversation_section_progress')
    //                 ->updateOrInsert(
    //                     [
    //                         'conversation_id' => $conversationId,
    //                         'section_id' => $sectionId,
    //                         // 'current_question_id' => $currentQuestionId
    //                     ],
    //                     [
    //                         'status' => 'completed' ,
    //                         'updated_at' => now(),
    //                         'user_id' => auth()->id(),
    //                     ]
    //                 );
    //         }

            

    //         // Actualizamos de estado conversations
    //         // DB::table('conversations')
    //         //         ->updateOrInsert(
    //         //             [
    //         //                 'id' => $conversationId,
    //         //             ],
    //         //             [
    //         //                 'status' => 'completed' ,
    //         //                 'updated_at' => now(),
    //         //                 'user_id' => auth()->id(),
    //         //             ]
    //         //         );
            
    //         return response()->json([
    //             'message' => 'Section Reply Save',
    //         ]);
    //     });
    // }

    public function sectionCompleted( $sectionId, $conversationId){

        $questionIds = Question::where('section_id', $sectionId)->pluck('id');

        $answeredIds = UserAnswer::where('conversation_id', $conversationId)
                                    ->whereIn('question_id', $questionIds)
                                    ->pluck('question_id');

        $allAnswered = $questionIds->diff($answeredIds)->isEmpty();

        return $allAnswered;

    }
    
}