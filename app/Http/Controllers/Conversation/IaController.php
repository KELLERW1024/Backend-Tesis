<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\PlanNode;
use App\Models\Question;
use App\Services\FileTextExtractorService;
use App\Services\PromptService;
use App\Services\Prompts\PlanPromptResolver;
use App\Services\ThesisContextService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ConversationService;
use App\Services\AnswerValidationService;
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
    private ReplicateService $replicateService, 
    protected ThesisContextService $thesisContextService,
    protected PlanPromptResolver $planPromptResolver,
    protected AnswerValidationService $answerValidationService,

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

            // =====================================================================================
            // INICIO DONDE SE  VALIDA SI LA RESPUESTA CORRESPONDE O TIENE UNA DIRECTIVA VALIDA
            // Las revisiones están en AnswerValidationService, que también valida la
            // respuesta sugerida del modal antes de mostrarla.
            // ======================================================================================

            $validation = $this->answerValidationService->validate($conversation, $data, $documentContent, $imageContent);

            $planPrompts = $validation['plan_prompts'];
            $sectionNode = $validation['section_node'];
            $parentNode = $validation['parent_node'];
            $objective = $validation['objective'];
            $history = $validation['history'];
            $result = $validation['result'];

            \Log::info('RESULT IACONTROLLER', [
                '$result => ' => $result ?? null
            ]);

            if (!$validation['valid']) {

                $result['response'] = '';
                $result['images'] = [];

                 \Log::info('RESPONSE VALIDACION ', [
                    '$result => ' => $result ?? null ,
                    'inconsistent' => $validation['inconsistent']
                ]);

                if ($validation['inconsistent']) {
                     $mensajeError = 'La respuesta no tiene congruencia con la información previamente registrada del proyecto.';
                     return response()->json([
                        'is_valid' => false,
                        'score' => 0,
                        'feedback' => $mensajeError,
                        'message' => $mensajeError, 
                        'response' => $mensajeError,
                    ]);
                }
               

                return response()->json($result);
            }

            //===========
            // FIN
            // ===========

            $messages = $this->promptService->buildMessagesBitacoraResponseCurrent( $planPrompts,
                                                                                    [  ...$data,    'file_content' => $documentContent,
                                                                                                    'image_content' => $imageContent,
                                                                                                    'objective' => $objective,
                                                                                                    'parent_node' => $parentNode,
                                                                                                    'section_node' => $sectionNode,
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

                // Con la pregunta, la sección y el objetivo, para que las columnas sigan lo que se pregunta
                $messageTable = $this->promptService->buildMessageTable(
                    $planPrompts,
                    $response,
                    $data['question'],
                    $sectionNode,
                    $objective
                );

                // Temperatura 0: convertir datos en tabla es una tarea mecánica; con los mismos datos debe salir la misma tabla
                $tableResponse= $this->openAIService->chat( $messageTable, ['temperature' => 0] );
                \Log::info('TABLE RESPONSE RAW', [
                    'tableResponse' => $tableResponse
                ]);

                $replyTable = json_decode($tableResponse, true);
                \Log::info('TABLE JSON DECODED', [
                    'replyTable' => $replyTable,
                    'json_error' => json_last_error_msg()
                ]);

                // JSON inválido, o la IA indicó que no hay datos para una tabla (sin columnas o sin filas)
                if (json_last_error() !== JSON_ERROR_NONE || empty($replyTable['columns']) || empty($replyTable['rows'])) {
                    $replyTable = null;
                }

            }

            // CONDICIONAL PARA CREAR LA IMAGEN
            $image = null;

            $count_ia_image = $this->conversationService->IAimagesXQuestion( $data  );

            if( $data['is_visual'] && $count_ia_image < 100  ){

                $datax = json_decode($reply, true);
                $promptImagen = str_replace(
                    '[Contenido]',
                    $datax['response'] ?? '',
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

            // Sin conexión con el servicio externo (corte de internet o de DNS) tras los reintentos
            if ($e instanceof \Illuminate\Http\Client\ConnectionException) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'No se pudo conectar. Inténtalo de nuevo en unos segundos.',
                ], 503);
            }

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
            $userMessageParts[] = ConversationService::LABEL_RESPUESTA . "\n" . $data['response'];
        }

        if (!empty($imageContent)) {
            $userMessageParts[] = ConversationService::LABEL_IMAGEN . "\n" . $imageContent;
        }

        if (!empty( $documentContent  )) {
            $userMessageParts[] = ConversationService::LABEL_DOCUMENTO . "\n" . $documentContent;
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

        //  ACTUALIZA EL CONTEXTO SEGUN LA RESPUESTA OBTENIDA
        $conversation = Conversation::find($data['idConversation']);

        $this->thesisContextService->updateContextFromAnswer ( $data['idConversation'], $conversation->user_plan_id  , $data['idQuestion'],  $reply);

    }

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
