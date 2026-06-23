<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\UserAnswers;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

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

    // SIN PLANTILLA 
    public function generarDocument(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'idConversation' => 'required|integer'
        ]);

        $idConversation = $request->idConversation;

        $subscription = UserSubscription::with([
                        'plan.sections' => function ($q) {
                            $q->where('is_active', true)
                            ->orderBy('order_index');
                        },
                        'plan.sections.userAnswers.files',
                        'plan.sections.userAnswers.tables',
                    ])
                    ->where('id', $idConversation)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();



        // $answers = UserAnswers::with(['section', 'files', 'tables'])
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
        //                     'url' => $file->file_path,
        //                     'name' => $file->description,
        //                     'fuente' => $file->fuente,
        //                 ];
        //             })->toArray(),

        //             'tables' => $item->tables->map(function ($table) {
        //                 return [
        //                     'id' => $table->id,
        //                     'data' => $table->data,
        //                     'name' => $table->nombre,
        //                     'fuente' => $table->fuente,
        //                 ];
        //             })->toArray(),
        //         ];
        //     });

        // if ($answers->isEmpty()) {
        //     return response()->json([
        //         'message' => 'No existen respuestas para esta conversación'
        //     ], 404);
        // }

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

        $firstSection = true;
        foreach( $subscription->plan->sections as $sectionT ){

            if (!$firstSection) {
                $section->addPageBreak();
            }

            $firstSection = false;

            $titulo = trim(
                ($sectionT->title ?? '') .
                ' : ' .
                ($sectionT->description ?? '')
            );

            $section->addTitle(
                $this->cleanText($titulo),
                2
            );

            foreach ( $sectionT->userAnswers as $answer) {



                $section->addText(
                    $this->cleanText($answer->answer_text ),
                    [
                        'name' => 'Arial',
                        'size' => 11,
                    ],
                    $paragraphStyle
                );

                /*
                |--------------------------------------------------------------------------
                | IMAGENES
                |--------------------------------------------------------------------------
                */

                foreach ($answer->files as $file) {

                    if (empty($file->file_path )) {
                        continue;
                    }

                    $fullPath = storage_path('app/public/' . $file->file_path );

                    if (!file_exists($fullPath)) {
                        continue;
                    }

                    $section->addTextBreak();

                    $noBreakStyle = [
                        'keepNext' => true,
                        'keepLines' => true,
                    ];

                    // Figura X
                    $section->addText(
                        'Figura ' . $contadorFigura.'.',
                        ['bold' => true],
                        $noBreakStyle
                    );

                    if (!empty($file->description)) {
                        $section->addText(
                            $this->cleanText($file->description),
                            ['bold' => false, 'size' => 10],
                            ['alignment' => Jc::START],
                            $noBreakStyle
                        );
                    }

                    $section->addImage(
                        $fullPath,
                        [
                            'width' => 350,
                            'alignment' => Jc::CENTER,
                            'wrappingStyle' => 'inline'
                        ]
                    );

                    
                    if (!empty($file->fuente )) {
                        $section->addText(
                            'Fuente: ' . $this->cleanText($file->fuente ),
                            [
                                'italic' => true,
                                'size' => 10
                            ], ['alignment' => Jc::START]
                        );
                    }

                    $contadorFigura++;
                }

                /*
                |--------------------------------------------------------------------------
                | TABLAS
                |--------------------------------------------------------------------------
                */

                foreach ($answer->tables  as $tableInfo) {

                    if (empty($tableInfo->data )) {
                        continue;
                    }

                    $tableData = json_decode($tableInfo->data , true);

                    if (
                        !$tableData ||
                        !isset($tableData['columns'] ) ||
                        !isset($tableData['rows'] )
                    ) {
                        continue;
                    }

                    $section->addTextBreak();

                    $section->addText(
                        'Tabla ' . $contadorTabla. '.',
                        ['bold' => true]
                    );

                    if (!empty($tableInfo->nombre )) {
                        $section->addText(
                            $this->cleanText($tableInfo->nombre ),
                            ['bold' => false]
                        );
                    }

                    $table = $section->addTable([
                        'borderSize' => 6,
                        'borderColor' => '000000',
                        'cellMargin' => 80,
                    ]);

                    /*
                    | Encabezado
                    */

                    $table->addRow();

                    foreach ($tableData['columns'] as $column) {

                        $table->addCell(2000)->addText(
                            $this->cleanText($column),
                            ['bold' => true, 'size' => 11]
                        );
                    }

                    /*
                    | Filas
                    */

                    foreach ($tableData['rows'] as $row) {

                        $table->addRow();

                        foreach ($row as $cell) {

                            $table->addCell(2000)->addText(
                                $this->cleanText((string) $cell)
                            );
                        }
                    }

                    if (!empty($tableInfo->fuente )) {

                        $section->addText(
                            'Fuente: ' . $this->cleanText($tableInfo->fuente ),
                            [
                                'italic' => true,
                                'size' => 10
                            ]
                        );
                    }

                    $contadorTabla++;
                }

                $section->addTextBreak(2);
            }
        }
            

        // foreach ($answers as $answer) {

        //     $titulo = trim(
        //         ($answer['section_title'] ?? '') .
        //         ' : ' .
        //         ($answer['section_description'] ?? '')
        //     );

        //     $section->addTitle(
        //         $this->cleanText($titulo),
        //         2
        //     );

        //     $section->addText(
        //         $this->cleanText($answer['answer_text']),
        //         [
        //             'name' => 'Arial',
        //             'size' => 11,
        //         ],
        //         $paragraphStyle
        //     );

        //     /*
        //     |--------------------------------------------------------------------------
        //     | IMAGENES
        //     |--------------------------------------------------------------------------
        //     */

        //     foreach ($answer['files'] as $file) {

        //         if (empty($file['url'])) {
        //             continue;
        //         }

        //         $fullPath = storage_path('app/public/' . $file['url']);

        //         if (!file_exists($fullPath)) {
        //             continue;
        //         }

        //         $section->addTextBreak();

        //         // Figura X
        //         $section->addText(
        //             'Figura ' . $contadorFigura,
        //             ['bold' => true]
        //         );

        //         if (!empty($file['name'])) {
        //             $section->addText(
        //                 $this->cleanText($file['name']),
        //                 ['bold' => true, 'size' => 10],
        //                 ['alignment' => Jc::CENTER]
        //             );
        //         }

        //         $section->addImage(
        //             $fullPath,
        //             [
        //                 'width' => 350,
        //                 'alignment' => Jc::CENTER
        //             ]
        //         );

                
        //         if (!empty($file['fuente'])) {
        //             $section->addText(
        //                 'Nota. ' . $this->cleanText($file['fuente']),
        //                 [
        //                     'italic' => true,
        //                     'size' => 10
        //                 ], ['alignment' => Jc::START]
        //             );
        //         }

        //         $contadorFigura++;
        //     }

        //     /*
        //     |--------------------------------------------------------------------------
        //     | TABLAS
        //     |--------------------------------------------------------------------------
        //     */

        //     foreach ($answer['tables'] as $tableInfo) {

        //         if (empty($tableInfo['data'])) {
        //             continue;
        //         }

        //         $tableData = json_decode($tableInfo['data'], true);

        //         if (
        //             !$tableData ||
        //             !isset($tableData['columns']) ||
        //             !isset($tableData['rows'])
        //         ) {
        //             continue;
        //         }

        //         $section->addTextBreak();

        //         $section->addText(
        //             'Tabla ' . $contadorTabla,
        //             ['bold' => true]
        //         );

        //         if (!empty($tableInfo['name'])) {
        //             $section->addText(
        //                 $this->cleanText($tableInfo['name']),
        //                 ['bold' => true]
        //             );
        //         }

        //         $table = $section->addTable([
        //             'borderSize' => 6,
        //             'borderColor' => '000000',
        //             'cellMargin' => 80,
        //         ]);

        //         /*
        //         | Encabezado
        //         */

        //         $table->addRow();

        //         foreach ($tableData['columns'] as $column) {

        //             $table->addCell(2000, [
        //                 'bgColor' => 'D9D9D9'
        //             ])->addText(
        //                 $this->cleanText($column),
        //                 ['bold' => true]
        //             );
        //         }

        //         /*
        //         | Filas
        //         */

        //         foreach ($tableData['rows'] as $row) {

        //             $table->addRow();

        //             foreach ($row as $cell) {

        //                 $table->addCell(2000)->addText(
        //                     $this->cleanText((string) $cell)
        //                 );
        //             }
        //         }

        //         if (!empty($tableInfo['fuente'])) {

        //             $section->addText(
        //                 'Fuente: ' . $this->cleanText($tableInfo['fuente']),
        //                 [
        //                     'italic' => true,
        //                     'size' => 10
        //                 ]
        //             );
        //         }

        //         $contadorTabla++;
        //     }

        //     $section->addTextBreak(2);
        // }
        

        

        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        $fileName = 'reporte_'. time() . '.docx';

        $tempFile = storage_path('app/' . $fileName);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');

        $writer->save($tempFile);

        while (ob_get_level()) {
            ob_end_clean();
        }

        return response()->download(
            $tempFile,
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]
        )->deleteFileAfterSend(true);
    }

    // FUNCIONA CON PLANTILLA
     public function generarDocument0(Request $request)
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

        $answers = UserAnswers::with( ['section',  'files', 'tables'] )
        ->where('conversation_id', $idConversation)
        ->get()
        ->map(function ($item) {
            return [
                'section_id' => $item->section_id,
                'section_title' => $item->section?->title,
                'section_description' => $item->section?->description,
                'answer_text' => $item->answer_text,

                'files' => $item->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'url' =>  $file->file_path, 
                        'name' => $file->description ?? null,
                        'fuente' => $file->fuente ?? null,
                    ];
                }),

                'tables' => $item->tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'data' => $table->data ?? null, 
                        'name' => $table->nombre ?? null, 
                        'fuente' => $table->fuente ?? null, 
                    ];
                }),
                    ];
                });

        // Ruta de la plantilla
        $templatePath = resource_path('templates/Plantilla TPN.docx');
        if (!file_exists($templatePath)) {
            dd('No existe la plantilla en: ' . $templatePath);
        }
        $template = new TemplateProcessor($templatePath);

        // Clonar bloque según cantidad de respuestas
        $template->cloneBlock('bloque', count($answers), true, true);

        foreach ($answers as $index => $answer) {
            $i = $index + 1;

            $template->setValue("capitulo#{$i}", 
                $answer['section_title'] . ' : ' . $answer['section_description']
            );

            $template->setValue("contenido#{$i}", 
                $this->cleanText( $answer['answer_text'] )
            );
            // ADD imagen
            $imagePath = $item['url'] ?? null;
            if ($imagePath) {

                $fullPath = storage_path('app/public/' . $imagePath);

                if (file_exists($fullPath)) {
                    $section->addImage($fullPath, [
                        'width' => 350,
                        'wrappingStyle' => 'inline'
                    ]);
                }
            }
            // ADD TABLE
            
            if (!empty($item['table_data'])) {

                $tableData = json_decode($item['table_data'], true);

                if ($tableData) {

                    $table = $section->addTable();

                    // 🔵 encabezados
                    if (!empty($tableData['columns'])) {
                        $table->addRow();

                        foreach ($tableData['columns'] as $col) {
                            $table->addCell(2000)->addText($this->cleanText($col));
                        }
                    }

                    // 🔵 filas
                    foreach ($tableData['rows'] as $row) {
                        $table->addRow();

                        foreach ($row as $cell) {
                            $table->addCell(2000)->addText($this->cleanText($cell));
                        }
                    }
                }
            }

        }

        // Guardar archivo
        $fileName = 'reporte.docx';
        $tempFile = storage_path($fileName);

        $template->saveAs($tempFile);

        // Descargar
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}
