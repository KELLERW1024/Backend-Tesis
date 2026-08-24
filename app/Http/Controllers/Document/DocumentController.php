<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\UserAnswers;
use App\Models\PlanNode;
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

        // reemplaza entidades peligrosas para XML
        $text = str_replace('&', 'and', $text);

        return $text;
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

            $conversation = Conversation::with([
                'userPlan.plan',
            ])
            ->where('id', $idConversation)
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
            ->orderBy('orden')
            ->get();


Log::info('NODOS NIVEL 1', [
    'nodes' => $nodes
        ->where('nivel', 1)
        ->map(function ($node) {
            return [
                'id' => $node->id,
                'titulo' => $node->titulo,
                'parent_id' => $node->parent_id,
                'orden' => $node->orden,
            ];
        })
        ->values()
        ->toArray()
]);


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

            Log::info('GENERAR DOCUMENTO', [
                'conversation_id' => $idConversation,
                'plan_id' => $plan->id,
                'total_nodes' => $nodes->count(),
                'root_nodes' => $nodeTree->count(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREAR WORD
            |--------------------------------------------------------------------------
            */

            $phpWord = new PhpWord();

            $phpWord->setDefaultFontName('Arial');
            $phpWord->setDefaultFontSize(12);

            $section = $phpWord->addSection([
                'marginTop'    => 1440,
                'marginBottom' => 1440,
                'marginLeft'   => 1440,
                'marginRight'  => 1440,
            ]);

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

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

            $phpWord->addTitleStyle(
                1,
                [
                    'bold' => true,
                    'size' => 16,
                    'name' => 'Arial',
                ]
            );

            $phpWord->addTitleStyle(
                2,
                [
                    'bold' => true,
                    'size' => 14,
                    'name' => 'Arial',
                ]
            );

            $phpWord->addTitleStyle(
                3,
                [
                    'bold' => true,
                    'size' => 13,
                    'name' => 'Arial',
                ]
            );

            $phpWord->addTitleStyle(
                4,
                [
                    'bold' => true,
                    'size' => 12,
                    'name' => 'Arial',
                ]
            );

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

            $section->addTitle(
                $this->cleanText($plan->name ?? 'Plan de Tesis'),
                1
            );

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
            foreach ($nodeTree as $index => $node) {

                $numero = (string) ($index + 1);

                $this->addNodeToDocument(
                    $section,
                    $node,
                    $numero,
                    $paragraphStyle,
                    $contadorFigura,
                    $contadorTabla
                );
            }

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
   private function addNodeToDocument(
        $section,
        $node,
        $numero, // NEW
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

        if (!empty($titulo)) {

            $section->addTitle(
                $numero . ' ' . $this->cleanText($titulo),
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

                $section->addText(
                    $this->cleanText($answer->answer_text),
                    [
                        'name' => 'Arial',
                        'size' => 11,
                    ],
                    $paragraphStyle
                );
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

                $section->addText(
                    'Figura ' . $contadorFigura . '.',
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
                            'bold' => false,
                            'size' => 10,
                        ],
                        [
                            'alignment' => Jc::START,
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

                    $section->addText(
                        'Fuente: ' .
                        $this->cleanText($file->fuente),
                        [
                            'italic' => true,
                            'size' => 10,
                        ],
                        [
                            'alignment' => Jc::START,
                        ]
                    );
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
                    'Tabla ' . $contadorTabla . '.',
                    [
                        'bold' => true,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | NOMBRE DE TABLA
                |--------------------------------------------------------------------------
                */

                if (!empty($tableInfo->nombre)) {

                    $section->addText(
                        $this->cleanText($tableInfo->nombre),
                        [
                            'bold' => false,
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CREAR TABLA
                |--------------------------------------------------------------------------
                */

                $table = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                ]);

                /*
                |--------------------------------------------------------------------------
                | ANCHO DE COLUMNAS
                |--------------------------------------------------------------------------
                */

                $pageWidth = 9000;

                $cellWidth = intval(
                    $pageWidth / $numColumns
                );

                /*
                |--------------------------------------------------------------------------
                | ENCABEZADO
                |--------------------------------------------------------------------------
                */

                $table->addRow();

                foreach ($tableData['columns'] as $column) {

                    $table->addCell($cellWidth, [
                            'bgColor' => 'D9D9D9',
                        ])->addText(
                            $this->cleanText(
                                (string) $column
                            ),
                            [
                                'bold' => true,
                                'size' => 11,
                            ]
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | FILAS
                |--------------------------------------------------------------------------
                */

                foreach ($tableData['rows'] as $row) {

                    $table->addRow();

                    /*
                    | Nos aseguramos de respetar el número de columnas
                    */

                    for ($i = 0; $i < $numColumns; $i++) {

                        $cell = $row[$i] ?? '';

                        $table->addCell($cellWidth)
                            ->addText(
                                $this->cleanText(
                                    (string) $cell
                                )
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | FUENTE
                |--------------------------------------------------------------------------
                */

                if (!empty($tableInfo->fuente)) {

                    $section->addText(
                        'Fuente: ' .
                        $this->cleanText(
                            $tableInfo->fuente
                        ),
                        [
                            'italic' => true,
                            'size' => 10,
                        ]
                    );
                }

                $contadorTabla++;
            }

            $section->addTextBreak();
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
        foreach ($node->children as $index => $child) {

            $numeroHijo = $numero . '.' . ($index + 1);

            $this->addNodeToDocument(
                $section,
                $child,
                $numeroHijo,
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
