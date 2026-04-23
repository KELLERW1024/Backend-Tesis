<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationSectionProgress extends Model
{
     protected $table = 'conversation_section_progress';

    public $timestamps = false; // porque solo tienes updated_at

    protected $fillable = [
        'user_id',
        'conversation_id',
        'section_id',
        'current_question_id',
        'status',
        'progress_percentage',
        'started_at',
        'completed_at'
    ];

    /*
    |-----------------------------
    | RELACIONES
    |-----------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function currentQuestion()
    {
        return $this->belongsTo(Question::class, 'current_question_id');
    }
}
