<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Plans;

class Section extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class, 'section_id');
    }

    public function plans()
    {
        return $this->belongsToMany(
            Plans::class,
            'plan_sections',
            'section_id',
            'plan_id'
        );
    }
}