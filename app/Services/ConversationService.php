<?php 

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\UserSubscription;
use App\Models\Conversation; 

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


    public function saveMessage(array $data, String $message , String $role )
    {
        return DB::transaction(function () use ($data, $message, $role) {

            $conversationId = $data['idConversation'];
            $sectionId = $data['idSection'];
            //$reply = $data['reply'];


            // Guardar mensaje 
            DB::table('conversation_messages')->insert([
                'conversation_id' => $conversationId,
                'section_id' => $sectionId,
                'role' => $role,
                'message_text' => $message,
                'created_at' => now()
            ]);

            // Actualizar progreso
            DB::table('conversation_section_progress')
                ->updateOrInsert(
                    [
                        'conversation_id' => $conversationId,
                        'section_id' => $sectionId,
                    ],
                    [
                        'status' => 'in_progress' ,
                        'updated_at' => now(),
                        'user_id' => auth()->id(),
                    ]
                );

            return response()->json([
                'message' => 'Conversation Save',
            ]);
        });
    }

    public function saveUserAnswer( array $data ){
        
        return DB::transaction(function () use ( $data ) {
        
            $conversationId = $data['idConversation'];
            $sectionId = $data['idSection'];
            $reply = $data['reply'];
            
            
            // 1. Obtener progreso actual
            $progress = DB::table('conversation_section_progress')
                ->where('conversation_id', $conversationId)
                ->where('section_id', $sectionId)
                ->first();

            //if (!$progress) {
            //    throw new \Exception('No existe progreso para esta sección');
            //}

            $currentSectionId = $progress->section_id ?? $sectionId;

            /*if (!$currentQuestionId) {
                throw new \Exception('No hay pregunta actual');
            }*/

            // 2. Guardar respuesta estructurada
            DB::table('user_answers')->updateOrInsert(
                [
                    'conversation_id' => $conversationId,
                    'section_id' => $currentSectionId,
                ],
                [
                    'user_id' => auth()->id(),
                    'answer_text' => $reply,
                    'created_at' => now(),
                ]
            );

            DB::table('conversation_section_progress')
                    ->updateOrInsert(
                        [
                            'conversation_id' => $conversationId,
                            'section_id' => $sectionId,
                        ],
                        [
                            'status' => 'completed' ,
                            'updated_at' => now(),
                            'user_id' => auth()->id(),
                        ]
                    );
            
            return response()->json([
                'message' => 'Section Reply Save',
            ]);
        });
    }
    
}