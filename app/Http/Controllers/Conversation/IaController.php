<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use App\Constants\Prompts;

class IaController extends Controller
{
    //
    public function conversation(Request $request)
    {
        $validated = $request->validate([
                'idPlan' => 'required|integer',
                'idSection' => 'required|integer',
                'idConversation' => 'required|integer',
                'plan' => 'required|string',
                'title' => 'required|string',
                'description' => 'required|string',

                'responses' => 'required|array|min:1',
                'responses.*.question' => 'required|string',
                'responses.*.answer' => 'required|string',
        ]);

        $promptIni = Prompts::PROMPT_INICIAL;
        $promptEsp = Prompts::PROMPT_ESPECIFICO;

        $promptIni = str_replace( '[ESPECIALIDAD:]', $validated['plan'],  $promptIni );
        
        $questions = collect($validated['responses'])
                                                    ->pluck('question')
                                                    ->implode(', ');
        $answers = collect($validated['responses'])
                                                    ->pluck('answer')
                                                    ->implode(', ');
        
        $promptEsp = str_replace(
                        [
                            '[Capítulo]',
                            '[Descripcion Capítulo]',
                            '[Preguntas]',
                            '[Respuestas]'
                        ],
                        [
                            $validated['title'],
                            $validated['description'],
                            $questions,
                            $answers
                        ],
                        $promptEsp
                    );
        
        $input = [];

        $input[] = [
            'role' => 'system',
            'content' => $promptIni
        ];
        $input[] = [
            'role' => 'user',
            'content' => $promptEsp
        ];


        app(\App\Services\ConversationService::class)->saveMessage($validated, $promptEsp, "user");
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => $input
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'body' => $response->json()
            ], 500);
        }

        $reply = $response->json()['output'][0]['content'][0]['text'] ?? null;

        
        app(\App\Services\ConversationService::class)->saveMessage($validated, $reply, "system");

        return response()->json([
            'reply' => $reply
        ]);
    }

    
}
