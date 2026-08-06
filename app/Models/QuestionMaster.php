<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Plans;


class QuestionMaster extends Model
{
     protected $table = 'questions_masters';

    protected $fillable = [
        'question',
        'detail',
        'example',
        'advertencia',
        'order_index',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Respuestas asociadas a esta pregunta
     */
    public function answers()
    {
        return $this->hasMany(UserAnswerDiagnostic::class, 'question_master_id');
    }
}
