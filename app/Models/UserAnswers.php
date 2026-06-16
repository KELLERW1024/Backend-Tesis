<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswers extends Model
{
    protected $table = 'user_answers';

    protected $fillable = [
        'user_id',
        'section_id',
        'question_id',
        'conversation_id',
        'answer_text',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function files()
    {
        return $this->hasMany(AnswerFile::class, 'answer_id');
    }
    public function tables()
    {
        return $this->hasMany(Tabla::class, 'answer_id');
    }
}
