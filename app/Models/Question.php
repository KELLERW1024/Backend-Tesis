<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section;

class Question extends Model
{
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
