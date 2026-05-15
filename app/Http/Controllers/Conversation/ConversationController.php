<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\UserSubscription;
use App\Models\Conversation;
use App\Models\ConversationSectionProgress;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;

class ConversationController extends Controller
{
    //
    private ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
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

    
    public function conversationSaveReply(Request $request)
    {
        $validated = $request->validate([
            'idPlan' => 'required|integer',
            'idConversation' => 'required|integer',
            'idSection' => 'required|integer',
            'reply' => 'required|string',
        ]);

        $result = app(\App\Services\ConversationService::class)
        ->saveUserAnswer($validated);

        return response()->json($result);


    }

    public function conversationsUser()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

       $conversations = Conversation::with([
            'subscription.plan',
            'sectionProgress.section'
        ])
        ->where('user_id', $user->id)
        ->get();

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],

            'conversations' => $conversations->map(function ($conversation) {

                $planName = $conversation->subscription?->plan?->name;

                return [
                    'id' => $conversation->id,
                    'status' => $conversation->status,
                    'title' => $conversation->title,
                    'plan_name' => $planName,
                ];
            }),
        ];

        return response()->json($data);
    }

     public function getSuscriptionConversation( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $idSuscriptionConversation = $request->get('idConversation');
        

       $subscription = UserSubscription::with([
            'plan.sections' => function ($q) {
                $q->where('is_active', true)
                ->orderBy('order_index');
            },
            'plan.sections.questions',
        ])
        ->where('id', $idSuscriptionConversation)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        $answers = UserAnswers::where('conversation_id', $idSuscriptionConversation)
            ->where('user_id', auth()->id())
            ->get();

        $answersSections = $answers->whereNotNull('section_id')->keyBy('section_id');

        $answersQuestions = $answers->whereNotNull('question_id')->keyBy('question_id');

        $subscription->plan->sections->each(function ($section) use ($answersSections, $answersQuestions) {

            // answer de section
            $section->setAttribute(
                'answer',
                $answersSections[$section->id]->answer_text ?? null
            );

            // answers de questions
            $section->questions->each(function ($question) use ($answersQuestions) {

                $question->setAttribute(
                    'answer_text',
                    $answersQuestions[$question->id]->answer_text ?? null
                );

            });

        });

            

        return new SubscriptionResource($subscription);
        //return response()->json($progress);
    }
}
