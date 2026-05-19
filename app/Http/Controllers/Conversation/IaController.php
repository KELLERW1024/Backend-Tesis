<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Services\FileTextExtractorService;
use App\Services\PromptService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ConversationService;
use App\Services\OpenAIService;
use App\Models\Section;



class IaController extends Controller
{
//
    public function __construct(
    protected ConversationService $conversationService,
    protected OpenAIService $openAIService,
    protected PromptService $promptService,
    protected UploadService $uploadService,

    ) {}
    public function validateAnswer(
        Request $request,
        FileTextExtractorService $fileProcessor
    ) {

        $data = $request->validate([
            'pregunta' => 'required|string',
            'detail' => 'nullable|string',
            'evidence' => 'nullable|string',
            'respuesta' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $files = $request->file('files');

        $imageBase64 = null;
        $imageMime = null;

        $documentContent = '';

        if ($files) {

            foreach ($files as $file) {
                if (str_starts_with($file->getMimeType(), 'image')) {

                    if (!$imageBase64) {

                        $imageBase64 = base64_encode(
                            file_get_contents($file->getRealPath())
                        );

                        $imageMime = $file->getMimeType();
                    }
                }
                else {

                   
                    $extracted = $fileProcessor->extract($file);

                    \Log::info('FILE MIME: ' . $file->getMimeType());
                    \Log::info('EXTRACTED LENGTH: ' . strlen($extracted ?? ''));
                    \Log::info('EXTRACTED CONTENT: ' . ($extracted ?? 'NULL'));

                    $documentContent .= $extracted . "\n\n";
                    
                }
            }
        }

        $fileType = 'none';

        if ($imageBase64 && $documentContent) {
            $fileType = 'mixed';
        }
        elseif ($imageBase64) {
            $fileType = 'image';
        }
        elseif ($documentContent) {
            $fileType = 'document';
        }

        $prompt = $this->promptService->buildValidationPrompt([
            ...$data,
            'file_type' => $fileType,
            'file_content' => $documentContent,
        ]);

        \Log::info('PROMPT: ' . $prompt);

        if ($imageBase64) {
            $imageDataUri = "data:$imageMime;base64,$imageBase64";

            $result = $this->openAIService->imageInputJsonOutput(
                prompt: $prompt,
                image: $imageDataUri
            );
        }

        else {
            $result = $this->openAIService->json($prompt);
           
        }
        return response()->json($result);
    }

     public function conversation(Request $request)
    {
        $data = $request->validate([
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

        $objective = Section::where('id', $data['idSection'] )->value('objective');

        $history = $this->conversationService->getConversation($data['idConversation']);

        $messages = $this->promptService->buildMessages( $data,  $history);

        $reply = $this->openAIService->chat($messages);

        $this->conversationService->saveMessage(
            $data,
            $data['response'],
            'user'
        );

        $this->conversationService->saveMessage(
            $data,
            $reply,
            'system'
        );

        return response()->json([
            'reply' => $reply
        ]);
    }

    public function AAvalidateAnswer(Request $request)
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

       public function Conversatioresponse (Request $request, FileTextExtractorService $fileProcessor)
    {

        $data = $request->validate([
            'imageOutput' => 'required|boolean',
            'pregunta' => 'required|string',
            'detail' => 'nullable|string',
            'evidence' => 'nullable|string',
            'respuesta' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', 
        ]);

        // 👇 obtener archivo
        $files = $request->file('files');

        $imageUrl = null;
        $docUrl = null;
        $documentContent = '';

         // =========================
        // 🔁 PROCESAR TODOS LOS ARCHIVOS
        // =========================
        if ($files) {

            foreach ($files as $file) {

                // 🖼️ IMAGEN
                if (str_starts_with($file->getMimeType(), 'image')) {

                    // solo una imagen (si quieres limitar)
                    if (!$imageUrl) {
                        $imageUrl = $this->uploadService->upload($file);
                    }

                } 
                // 📄 DOCUMENTO
                else {

                    $documentContent .= $fileProcessor->extract($file) . "\n\n";

                    // opcional: guardar archivo
                    $docUrl = $this->uploadService->upload($file);
                }
            }
        }
         // =========================
        // 🧠 CASO: IMAGEN + DOCUMENTO
        // =========================
        if ($imageUrl && $documentContent) {

            $prompt = $this->promptService->buildValidationPrompt([
                ...$data,
                'file_type' => 'mixed',
                'file_content' => $documentContent
            ]);

            $result = $this->openAIService->imageJson(
                $prompt,
                $imageUrl
            );
        }
         // =========================
        // 🖼️ SOLO IMAGEN
        // =========================
        elseif ($imageUrl) {

            $prompt = $this->promptService->buildValidationPrompt([
                ...$data,
                'file_type' => 'image'
            ]);

            $result = $this->openAIService->imageJson(
                $prompt,
                $imageUrl
            );
        }
            // =========================
        // 📄 SOLO DOCUMENTO
        // =========================
        elseif ($documentContent) {

            $prompt = $this->promptService->buildValidationPrompt([
                ...$data,
                'file_type' => 'document',
                'file_content' => $documentContent
            ]);

            $result = $this->openAIService->json($prompt);
        }
        // =========================
        // ❌ SIN ARCHIVOS
        // =========================
        else {

            $prompt = $this->promptService->buildValidationPrompt([
                ...$data,
                'file_type' => 'none'
            ]);

           
            $result = $this->openAIService->json($prompt);// Repuesta solo texto

            

           
        }

        

       
        return response()->json($result);
        // $data = $request->validate([
        //     'pregunta' => 'required|string',
        //     'detail' => 'nullable|string',
        //     'evidence' => 'nullable|string',
        //     'respuesta' => 'required|string',
        // ]);

        // $prompt = $this->promptService
        //     ->buildValidationPrompt($data);

        // $result = $this->openAIService
        //     ->json($prompt);

        // return response()->json($result);
    }

    
}
