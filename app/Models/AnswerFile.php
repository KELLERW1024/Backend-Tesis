<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerFile extends Model
{
    protected $table = 'answer_files';

    protected $fillable = [
        'answer_id',
        'file_type',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'description',
        'fuente',
        'metadata',
        'analysis',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // 🔥 IMPORTANTE si tu tabla tiene timestamps
    public $timestamps = true;

    public function answer()
    {
        return $this->belongsTo(UserAnswers::class, 'answer_id');
    }
}