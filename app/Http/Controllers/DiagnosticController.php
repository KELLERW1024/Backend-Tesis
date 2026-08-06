<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserAnswerDiagnostic;
use App\Models\QuestionMaster;


class DiagnosticController extends Controller
{

    public function nextQuestion()
    {
        $userId = auth()->id();

        $question = QuestionMaster::where('active', true)
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('question_master_id')
                    ->from('user_answers_diagnostic')
                    ->where('user_id', $userId);
            })
            ->orderBy('order_index')
            ->first();

        if (!$question) {
            return response()->json([
                'completed' => true,
                'message' => 'El diagnóstico fue completado'
            ]);
        }

        return response()->json([
            'completed' => false,
            'question' => $question
        ]);
    }

    public function saveAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions_masters,id',
            'answer' => 'required|string'
        ]);

        $answer = UserAnswerDiagnostic::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'question_master_id' => $request->question_id
            ],
            [
                'answer_text' => $request->answer
            ]
        );

        return response()->json([
            'message' => 'Respuesta guardada',
            'data' => $answer
        ]);
    }
    public function restart()
    {
        UserAnswerDiagnostic::where('user_id', auth()->id())
            ->delete();

        return response()->json([
            'message' => 'Nuevo diagnóstico iniciado'
        ]);
    }


}
