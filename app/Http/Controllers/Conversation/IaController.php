<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\PlanNode;
use App\Services\FileTextExtractorService;
use App\Services\PromptService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ConversationService;
use App\Services\OpenAIService;
use App\Services\ReplicateService;
use App\Models\Section;
use App\Constants\Prompts;



class IaController extends Controller
{
//
    public function __construct(
    protected ConversationService $conversationService,
    protected OpenAIService $openAIService,
    protected PromptService $promptService,
    protected UploadService $uploadService,
      private ReplicateService $replicateService

    ) {}

    public function validateAnswerResponse(  Request $request, FileTextExtractorService $fileProcessor ) {

        $data = $request->validate([
            'generate_table' => 'boolean',
            'detail' => 'nullable|string',
            'validation' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
            // 'idPlan' => 'required|integer',
            // 'idSection' => 'required|integer',
            'idConversation' => 'required|integer',
            'idQuestion' => 'required|integer',
            // 'plan' => 'required|string',
            // 'title' => 'required|string',
            'description' => 'required|string',
            'question' => 'required|string',
            'response' => 'nullable|string',
            'is_visual' => 'boolean',
            // 'apa' => 'nullable|string',
        ]);

        \Log::info('REQUEST VALIDATED DATA', [
            'data' => $data,
        ]);

        //$generateTable = $request->boolean('generate_table');

        $files = $request->file('files');

        $imageBase64 = null;
        $imageMime = null;

        $documentContent = '';
        $imageContent = '';

        // VALIDAMOS LOS ACHIVOS Y EXTRAEMOS
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
                    $documentContent .= $extracted . "\n\n";
                }
            }
        }

        // ANALIZA LA IMAGEN Y OBTIENE SU DATA
        if ($imageBase64) {
            $imageDataUri = "data:$imageMime;base64,$imageBase64";

            try {
                $imageContent = $this->openAIService->imageInputStringOutput(
                    prompt: 'Debes obtener la informacion exacta de esta imagen sin comentarios extras. ',
                    image: $imageDataUri
                );
            } catch (\Throwable $e) {
                \Log::error('OpenAI error lectura: ' . $e->getMessage());
            }
        }
        if( $documentContent != ''){
             $documentContent = $this->openAIService->inputCadenaOutputString( $documentContent );
        }

        \Log::info('DATA IACONTROLLER', [
            '$imageContent => ' => $imageContent ?? null,
            '$documentContent => ' => $documentContent ?? null,
        ]);

        return $this->responseIA( $request, $data, $documentContent, $imageContent );
        

        // /////////////////////////////
        // $prompt = $this->promptService->buildValidationPrompt([ // ESTE METODO SOLO ARMA LAS RESPUESTA ACORDE A LA PREGUNTA
        //     ...$data,
        //     'file_content' => $documentContent,
        //     'image_content' => $imageContent,
        // ] ); 

        // \Log::info('PROMPT IACONTROLLER', [
        //     '$prompt => ' => $prompt ?? null 
        // ]);

        // $result = $this->openAIService->json($prompt); // ESTE METODO HACE LA VALUIDACION  CON LA IA 
        // if ( $result['is_valid'] == false ) {
            
        //     $result['response'] = '';
        //     $result['images'] = [];

        //     return response()->json($result);

        // }else{
        //     return $this->responseIA( $request, $data, $documentContent, $imageContent );
            
        // }

        
        // ANALIZA LA TABLA 
        // else {
            

        //     $prompt = $this->promptService->buildValidationPrompt([
        //         ...$data,
        //         'file_content' => $documentContent,
        //     ]);

        //     \Log::info('PROMT DATA TABLA : {}'. $prompt );

        //     $result = $this->openAIService->json($prompt);
        //     if ( $result['is_valid'] == false ) {
                
        //         $result['response'] = '';
        //         $result['images'] = [];

        //         return response()->json($result);

        //     }else{
        //         // RESPUESTA ++++>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

        //         return $this->responseIA( $request, $data, $documentContent, $imageContent ); //$isApa);
                
        //     }
        // }

           
       
    }

    public function responseIA( Request $request, array $data, string $documentContent, string $imageContent ){
            //  $isVisual = $request->boolean('is_visual');
        try {
            $conversation = Conversation::find($data['idConversation']);

            if (!$conversation) {
                return response()->json([
                    'message' => 'Conversación no encontrada'
                ], 404);
            }

            $parentNode = PlanNode::where('user_plan_id', $conversation->user_plan_id)
                ->whereNull('parent_id')
                ->first();

            $objective = $parentNode?->objective;
            \Log::info(' OBJECTIVE : ' . $objective );
            
            $history = $this->conversationService->getConversation($data['idConversation']);
            \Log::info(' HISTORY : ' , $history );

            // INICIO
            $prompt = $this->promptService->buildValidationPrompt([ // ESTE METODO SOLO ARMA LAS RESPUESTA ACORDE A LA PREGUNTA
                    ...$data,
                    'file_content' => $documentContent,
                    'image_content' => $imageContent,
                    'objective' => $objective,
                    'history' => $history,
            ] ); 

            \Log::info('PROMPT IACONTROLLER', [
                '$prompt => ' => $prompt ?? null 
            ]);

            $result = $this->openAIService->json($prompt); // ESTE METODO HACE LA VALUIDACION  CON LA IA 
            if ( $result['is_valid'] == false ) {
                
                $result['response'] = '';
                $result['images'] = [];

                return response()->json($result);
            }

        
            // FIN

            $messages = $this->promptService->buildMessagesBitacoraResponseCurrent( [  ...$data,    'file_content' => $documentContent,
                                                                                                    'image_content' => $imageContent,
                                                                                                    'objective' => $objective,
                                                                                                    'parent_node' => $parentNode,
                                                                                                    'history' => $history ],

                                                                                                    $history,  
                                                                                                    $documentContent,  
                                                                                                    $imageContent, 
                                                                                                    false );
            \Log::info(' MESSAGGES : ' , $messages );

            $reply = $this->openAIService->chat($messages);
           

            // OPCION GENERAR TABLA 
            $replyTable = null; 
            $studentInput = [];

            if (!empty($data['response'])) {
                $studentInput[] = "RESPUESTA TEXTUAL DEL TESISTA:\n" . $data['response'];
            }

            if (!empty($imageContent)) {
                $studentInput[] = "EVIDENCIA VISUAL:\n" . $imageContent;
            }

            if (!empty($documentContent)) {
                $studentInput[] = "DOCUMENTACIÓN:\n" . $documentContent;
            }

            $response = implode("\n\n", $studentInput);
            if($data['generate_table'] == true ){

                $messageTable = $this->promptService->buildMessageTable(  $response  ); //vWFIFICAR QUE RESPOUESRTA PONER ACA  
                
                $tableResponse= $this->openAIService->chat( $messageTable );
                \Log::info('TABLE RESPONSE RAW', [
                    'tableResponse' => $tableResponse
                ]);

                $replyTable = json_decode($tableResponse, true);
                \Log::info('TABLE JSON DECODED', [
                    'replyTable' => $replyTable,
                    'json_error' => json_last_error_msg()
                ]);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $replyTable = null;
                }

            }

            // CONDICIONAL PARA CREAR LA IMAGEN
            $image = null;

            $count_ia_image = $this->conversationService->IAimagesXQuestion( $data  );

            if( $data['is_visual'] && $count_ia_image < 100  ){
                $promptImagen = str_replace(
                    '[Contenido]',
                    $response,
                    Prompts::PROMPT_IMAGEN
                );
                 $image = $this->replicateService->generateImage(  $promptImagen  );
            }

            // ADD CONVERSATION A LA BITACORA
            $this->saveBitacoraConversation( $data, $documentContent , $imageContent ,  $reply);
            
           
            \Log::error('REPLY => {}' .  $reply );
            
            return response()->json([
                'is_valid' => true , 
                'response' => json_decode($reply, true),
                'table' => $replyTable ,
                'image' => $image,
                'count_ia_image' => $count_ia_image
            ]);
            //return response()-> json( json_decode($reply, true) );
         } catch (\Throwable $e) {

            \Log::error('Error en responseIA', [
                'exception' => $e,
                'conversation_id' => $data['idConversation'] ?? null,
            ]);

            return response()->json([
                'is_valid' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
            ], 500);
        }

    }

    private function saveBitacoraConversation(array $data, string $documentContent ,string $imageContent, string $reply): void
    {
        $userMessageParts = [];

        if (!empty($data['response'])) {
            $userMessageParts[] = $data['response'];
        }

        if (!empty($imageContent)) {
            $userMessageParts[] = $imageContent;
        }

        if (!empty( $documentContent  )) {
            $userMessageParts[] = $documentContent  ;
        }

        $userMessage = implode("\n\n", $userMessageParts);

        if (empty($userMessage)) {
            $userMessage = '[El tesista no proporcionó información]';
        }

        $this->conversationService->saveMessage(
            $data,
            $userMessage,
            'user'
        );

        $this->conversationService->saveMessage(
            $data,
            $reply,
            'system'
        );
    }

    // private function saveBitacoraConversation(array $data, string $reply): void
    // {
    //     $this->conversationService->saveMessage(
    //         $data,
    //         $data['response'],
    //         'user'
    //     );

    //     $this->conversationService->saveMessage(
    //         $data,
    //         $reply,
    //         'system'
    //     );
    // }

    public function getReplicatePrediction(string $id)
    {
        
        $prediction = $this->replicateService->getPrediction($id);

        return response()->json([
            'status' => $prediction['status'] ?? null,
            'output' => $prediction['output'] ?? null,
            'error' => $prediction['error'] ?? null,
            'prediction' => $prediction
        ]);
    }
}
