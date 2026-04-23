<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentController extends Controller
{
    //
    public function generar(Request $request)
    {
        //$user = Auth::user();

        // Ejemplo: puedes traer esto desde BD
        $respuestas = [
            [
                'pregunta' => '¿Qué es Laravel?',
                'respuesta' => 'Laravel es un framework de PHP moderno.'
            ],
            [
                'pregunta' => '¿Qué es PHPWord?',
                'respuesta' => 'Es una librería para generar documentos Word.'
            ]
        ];

        // Construir contenido dinámico
        $contenido = "Introduccion del plan";

        // foreach ($respuestas as $r) {
        //     $contenido .= "Pregunta: {$r['pregunta']}\n";
        //     $contenido .= "Respuesta: {$r['respuesta']}\n\n";
        // }

        // Ruta de la plantilla
        $templatePath = storage_path('app/templates/Plantilla.docx');

        $template = new TemplateProcessor($templatePath);

        // Reemplazar variable
        $template->setValue('contenido', $contenido);

        // Guardar archivo
        $fileName = 'reporte.docx'; //'reporte_'.$user->id.'.docx';
        $tempFile = storage_path($fileName);

        $template->saveAs($tempFile);

        // Descargar
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}
