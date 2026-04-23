<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
    use App\Models\Section;
class Plans extends Model
{
    public function sections()
    {
        return $this->belongsToMany(
            Section::class,
            'plan_sections',
            'plan_id',
            'section_id'
        );
    }
}
