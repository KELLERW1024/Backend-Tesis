<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Services\ConversationService;
use Illuminate\Http\Request;

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
}
