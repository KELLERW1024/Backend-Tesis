<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Question extends Model
{

    protected $fillable = [
        'plan_node_id',
        'question_text',
        'question_detail',
        'question_example',
        'validation_detail',
    ];
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function userAnswer()
    {
        return $this->hasOne(UserAnswers::class, 'question_id')
            ->where('user_id', auth()->id());
    }

    public function answer()
    {
        return $this->hasOne(UserAnswers::class, 'question_id', 'id');
    }

}
