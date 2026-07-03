<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
     protected $fillable = [
        'name',
        'description',
        'duration_months',
        'num_plans',
        'local_price',
        'international_price',
        'unit_price',
        'benefits',
        'is_active',

    ];

    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            'package_plan',
            'package_id',
            'plan_id'
        );
    }
}
