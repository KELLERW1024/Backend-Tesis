<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\PlanNode;
use App\Models\Question;
use App\Services\AttachmentTextService;
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
use App\Services\ReferenceService;
use App\Services\CalculationService;
use App\Services\Prompts\PlanPrompts;
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

    public function validateAnswerResponse(  Request $request, AttachmentTextService $attachmentText ) {

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
            // Fuentes que agregó el tesista con "Agregar fuente" (JSON)
            'sources' => 'nullable|string|max:30000',
            // 'apa' => 'nullable|string',
        ]);

        //$generateTable = $request->boolean('generate_table');

        // LEE LOS ARCHIVOS ADJUNTOS (documentos e imagen): mismo servicio que el adjunto del asistente
        $extraction = $attachmentText->extract($request->file('files') ?? []);

        if ($extraction['error']) {
            return $extraction['status'] === 503
                ? response()->json(['message' => $extraction['error']], 503)
                : $this->unreadableFile($extraction['error']);
        }

        $documentContent = $extraction['document'];
        $imageContent = $extraction['image'];

        // Fuentes que agregó el tesista: van con los adjuntos, así pasan por las mismas
        // revisiones (pertinencia y coherencia) y la IA las usa y las cita
        $sourcesText = $this->sourcesText($this->requestSources($request));
        if ($sourcesText !== '') {
            $documentContent = trim($documentContent . "\n\n" . $sourcesText);
        }

        return $this->responseIA( $request, $data, $documentContent, $imageContent );
   
       
    }

    private function requestSources(Request $request): array
    {
        $raw = json_decode((string) $request->input('sources', ''), true);
        $referenceService = app(ReferenceService::class);
        $sources = [];

        foreach (array_slice(is_array($raw) ? $raw : [], 0, 20) as $item) {
            if (is_array($item) && ($source = $referenceService->normalize($item))) {
                $source['excerpt'] = trim((string) ($item['excerpt'] ?? ''));
                $sources[] = $source;
            }
        }

        return $sources;
    }

    // Texto de las fuentes para los prompts, con todos sus datos para que la referencia salga exacta
    private function sourcesText(array $sources): string
    {
        if (empty($sources)) {
            return '';
        }

        $referenceService = app(ReferenceService::class);
        $lines = ["FUENTES QUE AGREGÓ EL TESISTA (úsalas para responder; si usas una, cítala en formato APA 7 e inclúyela en references copiando estos mismos datos):"];

        foreach ($sources as $index => $source) {
            $lines[] = ($index + 1) . '. ' . $referenceService->formatPlain($source);
            $lines[] = '   Datos: ' . json_encode(
                array_filter([
                    'authors' => $source['authors'],
                    'year' => $source['year'],
                    'title' => $source['title'],
                    'source_type' => $source['source_type'],
                    'container_title' => $source['container_title'],
                    'volume' => $source['volume'],
                    'issue' => $source['issue'],
                    'pages' => $source['pages'],
                    'publisher' => $source['publisher'],
                    'url' => $source['url'],
                    'doi' => $source['doi'],
                ]),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );

            if ($source['excerpt'] !== '') {
                $lines[] = '   Dato que aporta: ' . $source['excerpt'];
            }
        }

        return implode("\n", $lines);
    }

    private function unreadableFile(string $message)
    {
        return response()->json([
            'success' => false,
            'file_error' => true,
            'message' => $message,
        ], 422);
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
            $result = $validation['result'];

            if (!$validation['valid']) {

                $result['response'] = '';
                $result['images'] = [];

                if ($validation['inconsistent']) {
                     $mensajeError = ($documentContent !== '' || $imageContent !== '')
                        ? 'La respuesta o sus archivos adjuntos no tienen congruencia con la información previamente registrada del proyecto.'
                        : 'La respuesta no tiene congruencia con la información previamente registrada del proyecto.';
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

            // Historial del generador: resumen general (capítulos cerrados) + últimas 5 respuestas del capítulo actual
            $generatorHistory = $this->conversationService->chapterHistory($conversation->id, (int) $data['idQuestion']);
            $documentSummary = (string) (\App\Models\ThesisContext::where('conversation_id', $conversation->id)->first()?->plan_context['resumen_general'] ?? '');

            $messages = $this->promptService->buildMessagesBitacoraResponseCurrent( $planPrompts,
                                                                                    [  ...$data,    'file_content' => $documentContent,
                                                                                                    'image_content' => $imageContent,
                                                                                                    'objective' => $objective,
                                                                                                    'parent_node' => $parentNode,
                                                                                                    'section_node' => $sectionNode,
                                                                                                    'history' => $generatorHistory,
                                                                                                    // Estructura vigente de la tesis
                                                                                                    'structure' => $this->thesisContextService->structureText($conversation->id),
                                                                                                    'summary' => $documentSummary ],

                                                                                                    $generatorHistory,
                                                                                                    $documentContent,  
                                                                                                    $imageContent, 
                                                                                                    false );

            $reply = $this->openAIService->chat($messages);

            // Las cifras calculadas vienen como {{operación}}: se calculan aquí, antes de la tabla,
            // de la bitácora y de la respuesta, para que todos usen las mismas cifras
            $calculation = $this->resolveCalculations($planPrompts, $reply);
            $reply = $calculation['reply'];
           

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

                // La tabla se arma desde el texto generado (si viene vacío, desde lo que escribió el tesista)
                $generatedText = trim((string) (json_decode($reply, true)['response'] ?? ''));
                $tableSource = $generatedText !== '' ? $generatedText : $response;

                // Con la pregunta, la sección y el objetivo, para que las columnas sigan lo que se pregunta
                $messageTable = $this->promptService->buildMessageTable(
                    $planPrompts,
                    $tableSource,
                    $data['question'],
                    $sectionNode,
                    $objective
                );

                // Temperatura 0: convertir datos en tabla es una tarea mecánica; con los mismos datos debe salir la misma tabla
                $tableResponse= $this->openAIService->chat( $messageTable, ['temperature' => 0] );

                $replyTable = json_decode($tableResponse, true);

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
            
           
            $replyData = json_decode($reply, true);
            if (is_array($replyData)) {
                $referenceService = app(ReferenceService::class);
                $sources = $this->requestSources($request);
                $replyText = (string) ($replyData['response'] ?? '');

                // Referencias citadas en el texto, con los datos exactos de las fuentes que agregó el tesista
                $references = $referenceService->mergeWithSources(
                    array_values(array_filter(
                        $referenceService->withApaText($replyData['references'] ?? null),
                        fn ($ref) => $referenceService->isCited($ref, $replyText)
                    )),
                    $sources
                );

                // Fuente agregada que sí está citada en el texto, aunque la IA la omitió en sus referencias
                $references = $referenceService->addCitedSources($references, $sources, $replyText);

                $replyData['references'] = $references ?: null;

                // Fuentes agregadas que no se usaron, con su motivo (para avisar al tesista)
                $replyData['unused_sources'] = $referenceService->unusedSources(
                    $references,
                    $sources,
                    is_array($replyData['unused_sources'] ?? null) ? $replyData['unused_sources'] : []
                );

                // Operaciones que el motor no pudo calcular: quedaron escritas sin resultado
                $replyData['calculation_warning'] = empty($calculation['errors']) ? null
                    : (count($calculation['errors']) === 1
                        ? 'No se pudo calcular una cifra de la respuesta; revísala antes de guardar.'
                        : 'No se pudieron calcular algunas cifras de la respuesta; revísalas antes de guardar.');
            }

            return response()->json([
                'is_valid' => true ,
                'response' => $replyData,
                'table' => $replyTable ,
                'image' => $image,
                'count_ia_image' => $count_ia_image
            ]);
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

    // Calcula las marcas {{…}} del generador y revisa su interpretación
    private function resolveCalculations(PlanPrompts $plan, string $reply): array
    {
        $data = json_decode($reply, true);

        if (!is_array($data) || !str_contains($reply, '{{')) {
            return ['reply' => $reply, 'errors' => []];
        }

        $resolved = app(CalculationService::class)->resolveArray($data);
        $data = $resolved['data'];
        $text = (string) ($data['response'] ?? '');

        \Log::info('CALCULOS DE LA RESPUESTA', [
            'calculations' => $resolved['calculations'],
            'errors' => $resolved['errors'],
        ]);

        if (!empty($resolved['calculations']) && trim($text) !== '') {
            try {
                $review = $this->openAIService->json(
                    $this->promptService->promptReviewCalculations($plan, $text, $resolved['calculations'])
                );
                $revised = trim((string) ($review['response'] ?? ''));

                if ($revised !== '' && $this->sameNumbers($text, $revised)) {
                    $data['response'] = $revised;
                } else {
                    \Log::info('REVISION DE CALCULOS DESCARTADA (cambió cifras o vino vacía)', ['revised' => $revised]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Error en la revisión de cálculos: ' . $e->getMessage());
            }
        }

        return [
            'reply' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'errors' => $resolved['errors'],
        ];
    }

    // Las dos versiones del texto tienen exactamente las mismas cifras
    private function sameNumbers(string $a, string $b): bool
    {
        $numbers = function (string $text): array {
            preg_match_all('/\d+(?:[.,]\d+)*/u', $text, $match);
            sort($match[0]);

            return $match[0];
        };

        return $numbers($a) === $numbers($b);
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
