<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswerDiagnostic extends Model
{
    protected $table = 'user_answers_diagnostic';

    protected $fillable = [
        'user_id',
        'question_master_id',
        'answer_text'
    ];

    /**
     * Usuario dueño de la respuesta
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pregunta respondida
     */
    public function question()
    {
        return $this->belongsTo(
            QuestionMaster::class,
            'question_master_id'
        );
    }
}
