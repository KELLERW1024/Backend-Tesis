<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\UserAnswers;

class DocumentController extends Controller
{
    //
    public function generar(Request $request)
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
                $answer['answer_text']
            );
        }

        // Guardar archivo
        $fileName = 'reporte.docx';
        $tempFile = storage_path($fileName);

        $template->saveAs($tempFile);

        // Descargar
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}
