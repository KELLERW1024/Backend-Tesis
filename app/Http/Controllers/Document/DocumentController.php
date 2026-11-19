<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\UserAnswers;
use App\Models\PlanNode;
use App\Models\Annex;
use App\Services\ExcelService;
use App\Services\ReferenceService;
use App\Services\ThesisContextService;
use App\Services\Prompts\PlanPromptResolver;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpParser\Node\Stmt\Catch_;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class DocumentController extends Controller
{

    public function generar(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'idConversation' => 'required|integer'
        ]);

        $idConversation = $request->idConversation;

        $answers = UserAnswers::with('section')
            ->where( 'conversation_id' , $idConversation)
            ->get()
            ->map(function ($item) {
                return [
                    'section_title' => $item->section?->title ?? '',
                    'section_description' => $item->section?->description ?? '',
                    'answer_text' => $item->answer_text ?? '',
                ];
            });

        if ($answers->isEmpty()) {
            return response()->json([
                'message' => 'No existen respuestas para esta conversación'
            ], 404);
        }

        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $section->addTitle('Reporte de Conversación', 1);

        foreach ($answers as $item) {

            $titulo = trim(
                $item['section_title'] 
                .
                ' : ' .
                $item['section_description']
            );

            $section->addTitle($titulo, 2);

            $text = $this->cleanText($item['answer_text']);

            foreach (str_split($text, 2000) as $part) {
                $section->addText($part, [
                    'name' => 'Arial',
                    'size' => 11
                ]);
            }
            $section->addTextBreak(1);
        }

        $fileName = 'reporte_' . time() . '.docx';
        $tempFile = storage_path('app/' . $fileName);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        clearstatcache();

        // validar archivo antes de descargar
        if (!file_exists($tempFile) || filesize($tempFile) < 1000) {
            return response()->json([
                'message' => 'Error generando el documento'
            ], 500);
        }

        // 👉 guardar copia visible en el proyecto
        $publicPath = public_path('docs/' . $fileName);

        if (!file_exists(public_path('docs'))) {
            mkdir(public_path('docs'), 0777, true);
        }

        copy($tempFile, $publicPath);

        while (ob_get_level()) {
            ob_end_clean();
        }

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    // Cada línea es un párrafo; las viñetas ("- ", "• ", "1. ") van como lista con sangría francesa
    private function addParagraphs($section, string $text, array $paragraphStyle): void
    {
        $font = ['name' => 'Arial', 'size' => 11];
        $listStyle = [...$paragraphStyle, 'indentation' => ['left' => 720, 'hanging' => 360]];

        foreach (preg_split('/\R/u', $text) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (preg_match('/^(?:[-•*·]|\d{1,2}[.)])\s+/u', $line)) {
                // "- texto" o "* texto" → "• texto"; las numeradas se dejan como están
                $line = preg_replace('/^[-*·]\s+/u', '• ', $line);
                $section->addText($this->cleanText($line), $font, $listStyle);
                continue;
            }

            $section->addText($this->cleanText($line), $font, $paragraphStyle);
        }
    }

    // Portada en hoja propia; lo que falta va resaltado en amarillo para completarlo en Word
    private function addCover($section, array $cover, string $author): void
    {
        $center = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1.0];
        $line = function (string $value, string $placeholder, array $font = []) use ($section, $center) {
            $value = trim($value);
            $font = ['name' => 'Arial', 'size' => 11, ...$font];

            $section->addText(
                $this->cleanText($value !== '' ? $value : $placeholder),
                $value !== '' ? $font : [...$font, 'fgColor' => 'yellow'],
                $center
            );
        };
        $gap = fn (int $lines = 1) => $section->addTextBreak($lines, ['size' => 11], $center);

        $line(mb_strtoupper($cover['universidad'] ?? ''), '[NOMBRE DE LA UNIVERSIDAD]', ['bold' => true]);
        $line($cover['facultad'] ?? '', '[Facultad]');
        $line($cover['escuela'] ?? '', '[Escuela profesional]');
        $gap(4);

        $line($cover['titulo'] ?? '', '[Título de la tesis]', ['bold' => true, 'size' => 14]);
        $gap(3);

        $line('Tesis para optar el título profesional de', '');
        $line($cover['titulo_profesional'] ?? '', '[Título profesional]');
        $gap(3);

        $line('Autor', '', ['bold' => true]);
        $line($author, '[Nombre del autor]');
        $gap();

        $line('Asesor', '', ['bold' => true]);
        $line($cover['asesor'] ?? '', '[Nombre del asesor]');
        $gap(4);

        $line(($cover['ciudad'] ?? '') !== '' ? $cover['ciudad'] . ' – Perú' : '', '[Ciudad] – Perú');
        $line((string) now()->year, '');
    }

    // Índice (niveles 1 a 3) con vínculos; Word completa los números de página al abrir
    private function addTableOfContents($section): void
    {
        $section->addText(
            'Índice',
            ['name' => 'Arial', 'size' => 11, 'bold' => true],
            ['alignment' => Jc::CENTER, 'pageBreakBefore' => true, 'spaceAfter' => 240]
        );

        $section->addTOC(['name' => 'Arial', 'size' => 11], ['tabLeader' => 'dot'], 1, 3);
    }

    // Nota APA bajo tablas y figuras: "Nota." en cursiva y la fuente (o "Elaboración propia")
    private function addNote($section, ?string $source): void
    {
        $source = trim(preg_replace('/^(fuente|nota)\s*[:.]\s*/iu', '', trim((string) $source)));
        $source = $source !== '' ? $source : 'Elaboración propia';
        $source = preg_match('/[.!?]$/u', $source) ? $source : $source . '.';

        $textRun = $section->addTextRun(['alignment' => Jc::START]);
        $textRun->addText('Nota.', ['italic' => true, 'size' => 10]);
        $textRun->addText(' ' . $this->cleanText($source), ['size' => 10]);
    }

    // "ventas_2024-final" → "Ventas 2024 final" (título de tablas que vienen de un archivo Excel)
    public static function readableTitle(string $title): string
    {
        $title = trim(preg_replace('/\s+/u', ' ', str_replace(['_', '-'], ' ', $title)));

        return mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1);
    }
    function cleanText($text)
    {
        if (is_array($text) || is_object($text)) {
            $text = json_encode($text, JSON_UNESCAPED_UNICODE);
        }

        $text = (string) $text;

        // elimina caracteres invisibles rotos
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        // convierte UTF-8 real
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Escapa &, < y > para el XML del Word: así "&" se ve como "&" (antes se cambiaba por "and")
        return htmlspecialchars($text, ENT_XML1 | ENT_NOQUOTES, 'UTF-8');
    }
    public function generar2(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'idConversation' => 'required|integer'
        ]);

        $idConversation = $request->get('idConversation');

        if (!$idConversation) {
            return response()->json(['message' => 'idConversation is required'], 400);
        }

        $answers = UserAnswers::with('section')
        ->where('conversation_id', $idConversation)
        ->get()
        ->map(function ($item) {
            return [
                'section_id' => $item->section_id,
                'section_title' => $item->section?->title,
                'section_description' => $item->section?->description,
                'answer_text' => $item->answer_text,
            ];
        });

       $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $section->addTitle('Reporte de Conversación', 1);
        // $section->addTitle($answers[0]['section_title'], 2);
        // $section->addText('DATA');
        foreach ($answers as $item) {
            // $section->addTitle($answers[0]['section_title'], 2);
            $section->addTitle(
                ($item['section_title'] ?? '') . ' : ' . ($item['section_description'] ?? ''),
                2
            );
            // $section->addText('DATA');
            $section->addText( $this->cleanText( $item['answer_text']) ?? '');

            $section->addTextBreak(1);
        }

        // for ( $i = 0; $i < $answers->count() ; $i++ ){
        //     $section->addTitle($answers[$i]['section_title'] ?? '' . ' : ' . $answers[$i]['section_description'] ?? '' , 2);

        //     $section->addText( $answers[$i]['answer_text'] ?? '' );
         
        //     $section->addTextBreak(1);
        // }

        // foreach ($answers as $item) {

        //     $titulo = trim(
        //         $item['section_title'] 
        //         .
        //         ' : ' .
        //         $item['section_description']
        //     );

        //     $section->addTitle($titulo, 2);

        //     $text = $this->cleanText($item['answer_text']);

        //     foreach (str_split($text, 2000) as $part) {
        //         $section->addText($part, [
        //             'name' => 'Arial',
        //             'size' => 11
        //         ]);
        //     }
        //     $section->addTextBreak(1);
        // }

        $fileName = 'reporte_' . time() . '.docx';
        $tempFile = storage_path('app/' . $fileName);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile); 

       

        // Descargar
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    // SIN PLANTILLA USANDO
    public function generarDocument000(Request $request)
    {
        try{

                $user = auth()->user();

                if (!$user) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }

                $request->validate([
                    'idConversation' => 'required|integer'
                ]);

                $idConversation = $request->idConversation;

                $conversation = Conversation::with([
                    'userPlan.plan',
                    ])
                    ->where('id', $idConversation)
                    ->firstOrFail();

                $plan = $conversation->userPlan->plan;

               $nodes = PlanNode::with([
                        'questions' => function ($query) {
                            $query->orderBy('order_index');
                        },

                        'questions.answer' => function ($query) use ($idConversation) {
                            $query->where('conversation_id', $idConversation)
                                ->with([
                                    'files',
                                    'tables',
                                ]);
                        },

                    ])
                    ->where('plan_id', $plan->id)
                    ->orderBy('orden')
                    ->get();


                $nodeTree = $this->buildNodeTree($nodes);


                // $subscription = Conversation::with([
                //                                     // 'plan',
                //                                     // 'subscription.package',
                //                                     // 'plan.sections' => function ($q) {
                //                                     //     $q->where('is_active', true)
                //                                     //     ->orderBy('order_index');
                //                                     // },
                //                                     // 'subscription.plan',
                //                                     'plan.sections.userAnswers.files',
                //                                     'plan.sections.userAnswers.tables',
                //                                     ])  
                //                                     ->where('id', $idConversation)
                //                                     // ->where('user_id', auth()->id())
                //                                     ->firstOrFail();


                $phpWord = new PhpWord();

                $phpWord->setDefaultFontName('Arial');
                $phpWord->setDefaultFontSize(12);

                $section = $phpWord->addSection([
                    'marginTop'    => 1440,
                    'marginBottom' => 1440,
                    'marginLeft'   => 1440,
                    'marginRight'  => 1440,
                ]);

                // Encabezado de pagina
                $header = $section->addHeader();

                $header->addPreserveText(
                    '{PAGE}',
                    [
                        'name' => 'Times New Roman',
                        'size' => 12
                    ],
                    [
                        'alignment' => Jc::END
                    ]
                ); 

                /*
                |--------------------------------------------------------------------------
                | PIE DE PAGINA
                |--------------------------------------------------------------------------
                */

                // $footer = $section->addFooter();

                // $footer->addPreserveText(
                //     'Página {PAGE}',
                //     [
                //         'name' => 'Arial',
                //         'size' => 10
                //     ],
                //     [
                //         'alignment' => Jc::CENTER
                //     ]
                // );

                /*
                |--------------------------------------------------------------------------
                | ESTILOS
                |--------------------------------------------------------------------------
                */

                $paragraphStyle = [
                    'alignment' => Jc::BOTH,
                    'lineHeight' => 2.0,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'indentation' => [
                        'firstLine' => 720
                    ]
                ];

                /*
                |--------------------------------------------------------------------------
                | TITULO
                |--------------------------------------------------------------------------
                */

                // $section->addTitle('Reporte de Conversación', 1);
                // $section->addTextBreak();

                /*
                |--------------------------------------------------------------------------
                | CONTENIDO
                |--------------------------------------------------------------------------
                */

                $contadorFigura = 1;
                $contadorTabla = 1;

                $phpWord->addTitleStyle(
                    2,
                    [
                        'bold' => true,
                        'size' => 14,
                        'name' => 'Arial',
                    ]
                );

               foreach ($nodeTree as $node) {
                    $this->addNodeToDocument(
                        $section,
                        $node,
                        $paragraphStyle,
                        $contadorFigura,
                        $contadorTabla
                    );
                }


    } catch (ValidationException $e) {

        return response()->json([
            'message' => 'Error de validación.',
            'errors' => $e->errors()
        ], 422);

    } catch (ModelNotFoundException $e) {

        return response()->json([
            'message' => 'No se encontró la conversación.'
        ], 404);

    } catch (Throwable $e) {

        \Log::error('Error al generar documento', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'Ocurrió un error al generar el documento.',
            'error'   => $e->getMessage(), // Puedes quitar esto en producción
        ], 500);
    }
    }
    public function generarDocument(Request $request)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $request->validate([
                'idConversation' => 'required|integer'
            ]);

            $idConversation = $request->idConversation;

            /*
            |--------------------------------------------------------------------------
            | CONVERSACIÓN
            |--------------------------------------------------------------------------
            */

            // Solo el dueño de la conversación puede descargar su plan
            $conversation = Conversation::with([
                'userPlan.plan',
            ])
            ->where('id', $idConversation)
            ->whereHas('userPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | PLAN
            |--------------------------------------------------------------------------
            */

            $plan = $conversation->userPlan?->plan;

            if (!$plan) {
                return response()->json([
                    'message' => 'La conversación no tiene un plan asociado.'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | NODOS + PREGUNTAS + RESPUESTAS + FILES + TABLAS
            |--------------------------------------------------------------------------
            */

           $nodes = PlanNode::with([
                'questions' => function ($query) {
                    $query->orderBy('order_index');
                },

                'questions.answer' => function ($query) use ($idConversation) {
                    $query
                        ->where('conversation_id', $idConversation)
                        ->with([
                            'files',
                            'tables',
                        ]);
                },

            ])
            ->where('plan_id', $plan->id)
            ->where('user_plan_id', $conversation->user_plan_id)
            // ->whereNotNull('user_plan_id')
            ->where('execution_phase', '!=', 0)
            ->orderBy('orden')
            ->get();



            /*
            |--------------------------------------------------------------------------
            | CONSTRUIR ÁRBOL
            |--------------------------------------------------------------------------
            */

            $nodeTree = $this->buildNodeTree($nodes);

            /*
            |--------------------------------------------------------------------------
            | DEBUG
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | CREAR WORD
            |--------------------------------------------------------------------------
            */

            $phpWord = new PhpWord();

            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(11);

            $section = $phpWord->addSection([
                
                'pageSizeW'    => 11906,
                'pageSizeH'    => 16838,
                'marginTop'    => 1440,
                'marginBottom' => 1440,
                'marginLeft'   => 1440,
                'marginRight'  => 1440,
            ]);

            /*
            |--------------------------------------------------------------------------
            | NÚMERO DE PÁGINA (encabezado, a la derecha: APA 7)
            |--------------------------------------------------------------------------
            */

            // La portada (primera página) va sin número: encabezado vacío para la primera página
            $section->addHeader(\PhpOffice\PhpWord\Element\Header::FIRST);

            $header = $section->addHeader();

            $header->addPreserveText(
                '{PAGE}',
                [
                    'name' => 'Arial',
                    'size' => 11
                ],
                [
                    'alignment' => Jc::END
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | ESTILOS
            |--------------------------------------------------------------------------
            */

            $paragraphStyle = [
                'alignment' => Jc::BOTH,
                'lineHeight' => 2.0,
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'indentation' => [
                    'firstLine' => 720
                ]
            ];

            /*
            |--------------------------------------------------------------------------
            | ESTILOS DE TÍTULO
            |--------------------------------------------------------------------------
            */

            // Títulos APA 7: Arial 11, doble espacio; nivel 1 centrado y del 2 al 5 según APA
            $titleParagraph = ['lineHeight' => 2.0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true];

            $apaHeadings = [
                1 => [['bold' => true], ['alignment' => Jc::CENTER, 'pageBreakBefore' => true]],
                2 => [['bold' => true], ['alignment' => Jc::START]],
                3 => [['bold' => true, 'italic' => true], ['alignment' => Jc::START]],
                4 => [['bold' => true], ['alignment' => Jc::START, 'indentation' => ['firstLine' => 720]]],
                5 => [['bold' => true, 'italic' => true], ['alignment' => Jc::START, 'indentation' => ['firstLine' => 720]]],
                6 => [['bold' => true, 'italic' => true], ['alignment' => Jc::START, 'indentation' => ['firstLine' => 720]]],
            ];

            foreach ($apaHeadings as $depth => [$font, $paragraph]) {
                $phpWord->addTitleStyle(
                    $depth,
                    [...$font, 'size' => 11, 'name' => 'Arial'],
                    [...$paragraph, ...$titleParagraph]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CONTADORES
            |--------------------------------------------------------------------------
            */

            $contadorFigura = 1;
            $contadorTabla = 1;

            /*
            |--------------------------------------------------------------------------
            | TITULO GENERAL
            |--------------------------------------------------------------------------
            */

            // Portada (datos del diagnóstico; lo que falta queda como marcador para completar)
            // e índice general con vínculos. El título genérico del plan ya no se usa.
            $owner = $conversation->userPlan->user;
            $this->addCover(
                $section,
                app(ThesisContextService::class)->coverData(
                    $conversation,
                    app(PlanPromptResolver::class)->forConversation($conversation)
                ),
                trim(($owner->name ?? '') . ' ' . ($owner->last_name ?? ''))
            );
            $this->addTableOfContents($section);

            // Word actualiza los campos al abrir el archivo: así el índice muestra los números de página
            $phpWord->getSettings()->setUpdateFields(true);

            /*
            |--------------------------------------------------------------------------
            | NODOS
            |--------------------------------------------------------------------------
            */

            // foreach ($nodeTree as $node) {

            //     $this->addNodeToDocument(
            //         $section,
            //         $node,
            //         $paragraphStyle,
            //         $contadorFigura,
            //         $contadorTabla
            //     );
            // }
            
            // APA 7: los títulos no se numeran. Cada título principal (nivel 1) empieza en una hoja nueva
            // por su estilo (pageBreakBefore), sin un párrafo de salto que pueda dejar una hoja en blanco
            foreach ($nodeTree as $node) {

                $this->addNodeToDocument(
                    $section,
                    $node,
                    $paragraphStyle,
                    $contadorFigura,
                    $contadorTabla
                );
            }

   

            // APA 7: la lista de referencias va después del contenido y antes de los anexos
            $this->addReferencesToDocument($section, $conversation->id);

            $this->addAnexosToDocument($section, $conversation->id);

            /*
            |--------------------------------------------------------------------------
            | GUARDAR
            |--------------------------------------------------------------------------
            */

            $fileName = 'reporte_' . time() . '.docx';

            $tempFile = storage_path(
                'app/' . $fileName
            );

            $writer = IOFactory::createWriter(
                $phpWord,
                'Word2007'
            );

            $writer->save($tempFile);

            /*
            |--------------------------------------------------------------------------
            | VALIDAR
            |--------------------------------------------------------------------------
            */

            if (
                !file_exists($tempFile) ||
                filesize($tempFile) < 1000
            ) {

                Log::error('Documento vacío o inválido', [
                    'file' => $tempFile,
                    'size' => file_exists($tempFile)
                        ? filesize($tempFile)
                        : 0,
                    'nodes' => $nodes->count(),
                    'root_nodes' => $nodeTree->count(),
                ]);

                return response()->json([
                    'message' => 'Error generando el documento.'
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | DESCARGAR
            |--------------------------------------------------------------------------
            */

            while (ob_get_level()) {
                ob_end_clean();
            }

            return response()
                ->download(
                    $tempFile,
                    $fileName,
                    [
                        'Content-Type' =>
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ]
                )
                ->deleteFileAfterSend(true);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'No se encontró la conversación.'
            ], 404);

        } catch (Throwable $e) {

            Log::error('Error al generar documento', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al generar el documento.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function buildNodeTree($nodes)
    {
        $grouped = $nodes->groupBy(function ($node) {
            return $node->parent_id ?? 0;
        });

        $build = function ($parentId) use (&$build, $grouped) {

            $children = $grouped->get($parentId, collect())
            ->sortBy('orden')// agregado
            ->values();// agregado

            return $children->map(function ($node) use (&$build) {

                $node->children = $build($node->id);

                return $node;

            })->values();
        };

        return $build(0);
    }
    // Tabla con encabezado gris y una fila por registro ($tableData: columns y rows)
    private function addDataTable($section, array $tableData): void
    {
        $numColumns = count($tableData['columns']);

        // APA 7: sin líneas verticales ni sombreado; solo una línea arriba, otra bajo el encabezado y otra al final
        // (Sin bordes de tabla: declararlos con grosor 0 dejaba líneas blancas que siguen existiendo)
        $table = $section->addTable([
            'cellMargin' => 80,
        ]);
        $line = ['size' => 8, 'color' => '000000'];
        $headerBorders = ['borderTopSize' => $line['size'], 'borderTopColor' => $line['color'], 'borderBottomSize' => $line['size'], 'borderBottomColor' => $line['color']];
        $lastRowBorders = ['borderBottomSize' => $line['size'], 'borderBottomColor' => $line['color']];
        $lastRow = count($tableData['rows']) - 1;

        // Ancho de columnas
        $pageWidth = 9000;
        $cellWidth = intval($pageWidth / $numColumns);

        // Encabezado
        $table->addRow();

        foreach ($tableData['columns'] as $column) {
            $table->addCell($cellWidth, $headerBorders)->addText(
                    $this->cleanText((string) $column),
                    [
                        'bold' => true,
                        'size' => 11,
                    ]
                );
        }

        // Filas, respetando el número de columnas
        foreach (array_values($tableData['rows']) as $rowIndex => $row) {
            $table->addRow();

            for ($i = 0; $i < $numColumns; $i++) {
                $cell = $row[$i] ?? '';

                $table->addCell($cellWidth, $rowIndex === $lastRow ? $lastRowBorders : [])
                    ->addText($this->cleanText((string) $cell));
            }
        }
    }

    // APA 7: página nueva, título "Referencias" (nivel 1), orden alfabético,
    // doble espacio y sangría francesa de 1,27 cm; títulos de obras y revistas en cursiva
    private function addReferencesToDocument($section, int $conversationId): void
    {
        $referenceService = app(ReferenceService::class);
        $references = $referenceService->forConversation($conversationId);

        if ($references->isEmpty()) {
            return;
        }

        $section->addTitle('Referencias', 1);

        foreach ($references as $reference) {
            $textRun = $section->addTextRun([
                'alignment' => Jc::START,
                'lineHeight' => 2.0,
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'indentation' => ['left' => 720, 'hanging' => 720],
            ]);

            foreach ($referenceService->formatRuns($reference) as $run) {
                $font = ['name' => 'Arial', 'size' => 11, 'italic' => $run['italic']];

                if ($run['url']) {
                    $textRun->addLink(
                        $run['url'],
                        htmlspecialchars($run['text'], ENT_XML1 | ENT_NOQUOTES, 'UTF-8'),
                        [...$font, 'color' => '0563C1', 'underline' => 'single']
                    );
                    continue;
                }

                $textRun->addText($this->cleanText($run['text']), $font);
            }
        }
    }

    private function addAnexosToDocument($section, int $conversationId): void
    {
        $anexos = Annex::where('conversation_id', $conversationId)->orderBy('sort_order')->orderBy('id')->get();

        if ($anexos->isEmpty()) {
            return;
        }

        $section->addTitle('Anexos', 1);

        $numero = 1;

        foreach ($anexos as $anexo) {
            $fullPath = storage_path('app/public/' . $anexo->file_path);

            if (!file_exists($fullPath)) {
                continue;
            }

            $section->addTextBreak();

            $section->addText(
                'Anexo ' . $numero . '. ' . $this->cleanText($anexo->title),
                ['bold' => true],
                ['keepNext' => true, 'keepLines' => true]
            );

            if ($anexo->type === 'excel') {
                $tableData = app(ExcelService::class)->extractTable($fullPath);

                if (empty($tableData['columns']) || !isset($tableData['rows'])) {
                    continue;
                }

                $this->addDataTable($section, $tableData);
            } else {
                if (!@getimagesize($fullPath)) {
                    continue;
                }

                $section->addImage($fullPath, [
                    'width' => 350,
                    'alignment' => Jc::CENTER,
                    'wrappingStyle' => 'inline',
                ]);
            }

            $this->addNote($section, $anexo->source);

            $numero++;
        }
    }

   private function addNodeToDocument(
        $section,
        $node,
        $paragraphStyle,
        &$contadorFigura,
        &$contadorTabla, 
    ) {
        /*
        |--------------------------------------------------------------------------
        | NIVEL DEL NODO
        |--------------------------------------------------------------------------
        */

        $nivel = (int) ($node->nivel ?? 1);

        // PhpWord trabaja mejor con niveles 1-6
        $nivelTitulo = max(1, min($nivel, 6));

        $titulo = trim($node->titulo ?? '');

        // if (!empty($titulo)) {

        //     $section->addTitle(
        //         $this->cleanText($titulo),
        //         $nivelTitulo
        //     );
        // }

        // APA 7: el título va tal como está en el plan, sin numeración agregada.
        // Los niveles 1 a 3 no llevan punto final (se quita un punto suelto, no los puntos suspensivos)
        if ($nivelTitulo <= 3) {
            $titulo = preg_replace('/(?<!\.)\.\s*$/u', '', $titulo);
        }

        if (!empty($titulo)) {

            $section->addTitle(
                $this->cleanText($titulo),
                $nivelTitulo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OBJECTIVE DEL NODO NO VA
        |--------------------------------------------------------------------------
        */

        // if (!empty($node->objective)) {

        //     $section->addText(
        //         $this->cleanText($node->objective),
        //         [
        //             'name' => 'Arial',
        //             'size' => 10,
        //             'italic' => true,
        //         ],
        //         [
        //             'spaceAfter' => 100,
        //         ]
        //     );
        // }

        /*
        |--------------------------------------------------------------------------
        | PREGUNTAS DEL NODO
        |--------------------------------------------------------------------------
        */

        foreach ($node->questions as $question) {

            /*
            |--------------------------------------------------------------------------
            | RESPUESTA
            |--------------------------------------------------------------------------
            |
            | Question tiene:
            |
            | public function answer()
            | {
            |     return $this->hasOne(
            |         UserAnswers::class,
            |         'question_id',
            |         'id'
            |     );
            | }
            |
            */

            $answer = $question->answer;

            /*
            |--------------------------------------------------------------------------
            | SI LA PREGUNTA NO TIENE RESPUESTA
            |--------------------------------------------------------------------------
            */

            if (!$answer) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SI NO TIENE CONTENIDO
            |--------------------------------------------------------------------------
            */

            if (
                empty($answer->answer_text) &&
                $answer->files->isEmpty() &&
                $answer->tables->isEmpty()
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPUESTA DE TEXTO
            |--------------------------------------------------------------------------
            */

            if (!empty($answer->answer_text)) {
                $this->addParagraphs($section, $answer->answer_text, $paragraphStyle);
            }

            /*
            |--------------------------------------------------------------------------
            | IMÁGENES
            |--------------------------------------------------------------------------
            */

            foreach ($answer->files as $file) {

                if (empty($file->file_path)) {
                    continue;
                }

                $fullPath = storage_path(
                    'app/public/' . $file->file_path
                );

                if (!file_exists($fullPath)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDAR QUE REALMENTE SEA UNA IMAGEN
                |--------------------------------------------------------------------------
                */

                if (!@getimagesize($fullPath)) {
                    continue;
                }

                $section->addTextBreak();

                $noBreakStyle = [
                    'keepNext' => true,
                    'keepLines' => true,
                ];

                /*
                |--------------------------------------------------------------------------
                | FIGURA
                |--------------------------------------------------------------------------
                */

                // APA 7: "Figura 1" en negrita y, debajo, el título en cursiva
                $section->addText(
                    'Figura ' . $contadorFigura,
                    [
                        'bold' => true,
                    ],
                    $noBreakStyle
                );

                /*
                |--------------------------------------------------------------------------
                | DESCRIPCIÓN
                |--------------------------------------------------------------------------
                */

                if (!empty($file->description)) {

                    $section->addText(
                        $this->cleanText($file->description),
                        [
                            'italic' => true,
                        ],
                        [
                            'alignment' => Jc::START,
                            'keepNext' => true,
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | IMAGEN
                |--------------------------------------------------------------------------
                */

                $section->addImage(
                    $fullPath,
                    [
                        'width' => 350,
                        'alignment' => Jc::CENTER,
                        'wrappingStyle' => 'inline',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | FUENTE
                |--------------------------------------------------------------------------
                */

                if (!empty($file->fuente)) {
                    $this->addNote($section, $file->fuente);
                }

                $contadorFigura++;
            }

            /*
            |--------------------------------------------------------------------------
            | TABLAS
            |--------------------------------------------------------------------------
            */

            foreach ($answer->tables as $tableInfo) {

                if (empty($tableInfo->data)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | DECODIFICAR JSON
                |--------------------------------------------------------------------------
                */

                $tableData = is_array($tableInfo->data)
                    ? $tableInfo->data
                    : json_decode($tableInfo->data, true);

                if (
                    !$tableData ||
                    !isset($tableData['columns']) ||
                    !isset($tableData['rows'])
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDAR COLUMNAS
                |--------------------------------------------------------------------------
                */

                $numColumns = count($tableData['columns']);

                if ($numColumns === 0) {
                    continue;
                }

                $section->addTextBreak();

                /*
                |--------------------------------------------------------------------------
                | TÍTULO DE TABLA
                |--------------------------------------------------------------------------
                */

                $section->addText(
                    'Tabla ' . $contadorTabla,
                    [
                        'bold' => true,
                    ],
                    [
                        'keepNext' => true,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | NOMBRE DE TABLA
                |--------------------------------------------------------------------------
                */

                if (!empty($tableInfo->nombre)) {

                    $section->addText(
                        $this->cleanText(self::readableTitle($tableInfo->nombre)),
                        [
                            'italic' => true,
                        ],
                        [
                            'keepNext' => true,
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CREAR TABLA
                |--------------------------------------------------------------------------
                */

                $this->addDataTable($section, $tableData);

                /*
                |--------------------------------------------------------------------------
                | FUENTE
                |--------------------------------------------------------------------------
                */

                // APA 7: la tabla lleva su nota; sin fuente registrada (tablas de Excel antiguas), "Elaboración propia"
                $this->addNote($section, $tableInfo->fuente);

                $contadorTabla++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HIJOS DEL NODE
        |--------------------------------------------------------------------------
        |
        | Un node puede tener:
        |
        | Node
        | ├── preguntas
        | ├── hijos
        | │   ├── preguntas
        | │   └── hijos
        | │       └── ...
        |
        */

        // foreach ($node->children as $child) {

        //     $this->addNodeToDocument(
        //         $section,
        //         $child,
        //         $paragraphStyle,
        //         $contadorFigura,
        //         $contadorTabla
        //     );
        // }

        foreach ($node->children as $child) {

            $this->addNodeToDocument(
                $section,
                $child,
                $paragraphStyle,
                $contadorFigura,
                $contadorTabla
            );
        }

    }




    // FUNCIONA CON PLANTILLA
    //  public function generarDocument0(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     $validated = $request->validate([
    //         'idConversation' => 'required|integer'
    //     ]);

    //     $idConversation = $request->get('idConversation');

    //     if (!$idConversation) {
    //         return response()->json(['message' => 'idConversation is required'], 400);
    //     }

    //     $answers = UserAnswers::with( ['section',  'files', 'tables'] )
    //     ->where('conversation_id', $idConversation)
    //     ->get()
    //     ->map(function ($item) {
    //         return [
    //             'section_id' => $item->section_id,
    //             'section_title' => $item->section?->title,
    //             'section_description' => $item->section?->description,
    //             'answer_text' => $item->answer_text,

    //             'files' => $item->files->map(function ($file) {
    //                 return [
    //                     'id' => $file->id,
    //                     'url' =>  $file->file_path, 
    //                     'name' => $file->description ?? null,
    //                     'fuente' => $file->fuente ?? null,
    //                 ];
    //             }),

    //             'tables' => $item->tables->map(function ($table) {
    //                 return [
    //                     'id' => $table->id,
    //                     'data' => $table->data ?? null, 
    //                     'name' => $table->nombre ?? null, 
    //                     'fuente' => $table->fuente ?? null, 
    //                 ];
    //             }),
    //                 ];
    //             });

    //     // Ruta de la plantilla
    //     $templatePath = resource_path('templates/Plantilla TPN.docx');
    //     if (!file_exists($templatePath)) {
    //         dd('No existe la plantilla en: ' . $templatePath);
    //     }
    //     $template = new TemplateProcessor($templatePath);

    //     // Clonar bloque según cantidad de respuestas
    //     $template->cloneBlock('bloque', count($answers), true, true);

    //     foreach ($answers as $index => $answer) {
    //         $i = $index + 1;

    //         $template->setValue("capitulo#{$i}", 
    //             $answer['section_title'] . ' : ' . $answer['section_description']
    //         );

    //         $template->setValue("contenido#{$i}", 
    //             $this->cleanText( $answer['answer_text'] )
    //         );
    //         // ADD imagen
    //         $imagePath = $item['url'] ?? null;
    //         if ($imagePath) {

    //             $fullPath = storage_path('app/public/' . $imagePath);

    //             if (file_exists($fullPath)) {
    //                 $section->addImage($fullPath, [
    //                     'width' => 350,
    //                     'wrappingStyle' => 'inline'
    //                 ]);
    //             }
    //         }
    //         // ADD TABLE
            
    //         if (!empty($item['table_data'])) {

    //             $tableData = json_decode($item['table_data'], true);

    //             if ($tableData) {

    //                 $table = $section->addTable();

    //                 // 🔵 encabezados
    //                 if (!empty($tableData['columns'])) {
    //                     $table->addRow();

    //                     foreach ($tableData['columns'] as $col) {
    //                         $table->addCell(2000)->addText($this->cleanText($col));
    //                     }
    //                 }

    //                 // 🔵 filas
    //                 foreach ($tableData['rows'] as $row) {
    //                     $table->addRow();

    //                     foreach ($row as $cell) {
    //                         $table->addCell(2000)->addText($this->cleanText($cell));
    //                     }
    //                 }
    //             }
    //         }

    //     }

    //     // Guardar archivo
    //     $fileName = 'reporte.docx';
    //     $tempFile = storage_path($fileName);

    //     $template->saveAs($tempFile);

    //     // Descargar
    //     return response()->download($tempFile)->deleteFileAfterSend(true);
    // }
}
