<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annex extends Model
{
    protected $table = 'annexes';

    protected $fillable = [
        'conversation_id',
        'title',
        'source',
        'file_path',
        'sort_order',
    ];

    protected $appends = ['type', 'url'];

    public function getTypeAttribute(): string
    {
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, ['xls', 'xlsx']) ? 'excel' : 'image';
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
