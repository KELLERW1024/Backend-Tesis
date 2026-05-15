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
                'idQuestion' => 'required|integer',
                'plan' => 'required|string',
                'title' => 'required|string',
                'description' => 'required|string',

                'question' => 'required|string',
                'response' => 'required|string',
        ]);

        $promptIni = Prompts::PROMPT_INICIAL;
        $promptEsp = Prompts::PROMPT_ESPECIFICO;

        $promptIni = str_replace( '[ESPECIALIDAD:]', $validated['plan'],  $promptIni );
        
        $promptEsp = str_replace(
                        [
                            '[Capítulo]',
                            '[Descripcion Capítulo]',
                            '[Pregunta]',
                            '[Respuesta]'
                        ],
                        [
                            $validated['title'],
                            $validated['description'],
                            $validated['question'],
                            $validated['response']
                        ],
                        $promptEsp
                    );

        $history = app(\App\Services\ConversationService::class)->getConversation($validated['idConversation']) ?? [];
        $messages = collect($history)->map(function ($msg) {
            return [
                'role' => $msg['role'],
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => $msg['content']
                    ]
                ]
            ];
        })->toArray();

        // limitar historial
        $messages = collect($messages)->take(-10)->values()->toArray();

        // system SIEMPRE primero
        array_unshift($messages, [
            'role' => 'system',
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => $promptIni
                ]
            ]
        ]);

        // nuevo mensaje SIEMPRE último
        $messages[] = [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => $promptEsp
                ]
            ]
        ];
        
        /*$input = [];

        $input[] = [
            'role' => 'system',
            'content' => $promptIni
        ];
        $input[] = [
            'role' => 'user',
            'content' => $promptEsp
        ];*/


        app(\App\Services\ConversationService::class)->saveMessage($validated, $promptEsp, "user");
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => $messages //$input
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'body' => $response->json()
            ], 500);
        }

        $reply = $response->json()['output'][0]['content'][0]['text'] ?? null;

        
       

        if (!$reply) {
            return response()->json([
                'error' => 'No se pudo obtener respuesta del modelo',
                'raw' => $response->json()
            ], 500);
        }
        
        app(\App\Services\ConversationService::class)->saveMessage($validated, $reply, "system");

        return response()->json([
            'reply' => $reply
        ]);
    }

    public function validateAnswer(Request $request)
    {
        $data = $request->validate([
            'pregunta' => 'required|string',
            'detail' => 'nullable|string',
            'evidence' => 'nullable|string',
            'respuesta' => 'required|string',
        ]);

        $prompt = "
        Evalúa si la respuesta del usuario responde correctamente la pregunta y si es suficiente para redactar.

        Pregunta:
        {$data['pregunta']}

        Detalle:
        {$data['detail']}

        Evidencia:
        {$data['evidence']}

        Respuesta del usuario:
        {$data['respuesta']}

        Devuelve únicamente un JSON válido con esta estructura:

        {
          \"is_valid\": true,
          \"score\": 85,
          \"feedback\": \"La respuesta responde correctamente la pregunta y considera la evidencia.\"
        }

        Reglas:
        - is_valid debe ser boolean
        - score debe ser un número entre 0 y 100
        - feedback debe ser corto
        ";

       $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => $prompt
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'body' => $response->json()
            ], 500);
        }

        $data = $response->json();

        $content = $data['output'][0]['content'][0]['text'];

        return response()->json(
            json_decode($content, true)
        );
    }

    
}
