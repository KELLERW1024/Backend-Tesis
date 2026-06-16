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

            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx|max:20480'
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

    $progressSectionConversation = ConversationSectionProgress::where('conversation_id', $idSuscriptionConversation)
        ->where('user_id', auth()->id())
        ->get()
        ->keyBy('section_id');

    $answersSections = $answers->whereNotNull('section_id')->keyBy('section_id');

    $answersQuestions = $answers->whereNotNull('question_id')->keyBy('question_id');

    $subscription->plan->sections->each(function ($section) use ($answersSections, $answersQuestions, $progressSectionConversation) {

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

        

    return new SubscriptionResource($subscription);
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

    // $answers = UserAnswers::where('conversation_id', $idSuscriptionConversation)
    //     ->where('user_id', auth()->id())
    //     ->get();
    $answers = UserAnswers::with('files')
    ->where('conversation_id', $idSuscriptionConversation)
    ->where('user_id', auth()->id())
    ->get();

    $progressSectionConversation = ConversationSectionProgress::where('conversation_id', $idSuscriptionConversation)
        ->where('user_id', auth()->id())
        ->get()
        ->keyBy('section_id');

    $answersSections = $answers->whereNotNull('section_id')->keyBy('section_id');

    $answersQuestions = $answers->whereNotNull('question_id')->keyBy('question_id');

    $subscription->plan->sections->each(function ($section) use ($answersSections, $answersQuestions, $progressSectionConversation) {

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

            $answer = $answersQuestions->get($question->id);

            $question->setAttribute(
                'answer',
                $answer->answer_text ?? null
            );

            $question->setAttribute(
                'files',
                $answer ? $answer->files
                ->whereIn('file_type', 'image')
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_type' => $file->file_type,
                        'file_url' => asset('storage/' . $file->file_path),
                        'description' =>  $file->description,
                        'fuente' =>  $file->fuente,
                    ];
                }) : []
            );      

            $question->setAttribute(
                    'tables',
                    $answer
                        ? $answer->tables->map(function ($table) {
                            return [
                                'id' => $table->id,
                                'title' => $table->nombre,
                                'data' => json_decode($table->data, true),
                                'created_at' => $table->created_at,
                            ];
                        })
                        : []
            );

        });

    });

        

    return new SubscriptionResource($subscription);
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
