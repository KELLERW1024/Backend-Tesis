<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\UserSubscription;
use App\Models\Conversation; 
use App\Models\UserAnswers; 
use App\Models\Question; 

class ConversationService
{
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

    public function saveUserAnswer( array $data ){
        
        return DB::transaction(function () use ( $data ) {
        
            $conversationId = $data['idConversation'];
            $sectionId = $data['idSection'];
            $questionId = $data['idQuestion'];
            $reply = $data['reply'];
            
            
            // 1. Obtener progreso actual
            $progress = DB::table('conversation_section_progress')
                ->where('conversation_id', $conversationId)
                ->where('section_id', $sectionId)
                ->where('current_question_id', $questionId)
                ->first();

            //if (!$progress) {
            //    throw new \Exception('No existe progreso para esta sección');
            //}

            $currentSectionId = $progress->section_id ?? $sectionId;
            $currentQuestionId = $progress->section_id->current_question_id ?? $questionId;

            /*if (!$currentQuestionId) {
                throw new \Exception('No hay pregunta actual');
            }*/

            // 2. Guardar respuesta estructurada
            DB::table('user_answers')->updateOrInsert(
                [
                    'conversation_id' => $conversationId,
                    'section_id' => $currentSectionId,
                    'question_id' => $currentQuestionId,
                ],
                [
                    'user_id' => auth()->id(),
                    'answer_text' => $reply,
                    'created_at' => now(),
                ]
            );
            // Este estado se modificara cunado se haya completado la construcion de la section ya que la tabla solo acepta una conversastion x section

            $allAnswered = $this->sectionCompleted($sectionId, $conversationId);

            if( $allAnswered ){
                DB::table('conversation_section_progress')
                    ->updateOrInsert(
                        [
                            'conversation_id' => $conversationId,
                            'section_id' => $sectionId,
                            // 'current_question_id' => $currentQuestionId
                        ],
                        [
                            'status' => 'completed' ,
                            'updated_at' => now(),
                            'user_id' => auth()->id(),
                        ]
                    );
            }

            

            // Actualizamos de estado conversations
            // DB::table('conversations')
            //         ->updateOrInsert(
            //             [
            //                 'id' => $conversationId,
            //             ],
            //             [
            //                 'status' => 'completed' ,
            //                 'updated_at' => now(),
            //                 'user_id' => auth()->id(),
            //             ]
            //         );
            
            return response()->json([
                'message' => 'Section Reply Save',
            ]);
        });
    }

    public function sectionCompleted( $sectionId, $conversationId){

        $questionIds = Question::where('section_id', $sectionId)->pluck('id');

        $answeredIds = UserAnswers::where('conversation_id', $conversationId)
                                    ->whereIn('question_id', $questionIds)
                                    ->pluck('question_id');

        $allAnswered = $questionIds->diff($answeredIds)->isEmpty();

        return $allAnswered;

    }
    
}