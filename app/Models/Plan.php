<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use App\Models\Coupon;
class Plan extends Model
{
     protected $table = 'plans';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'duration_days',
        'max_sections',
        'max_messages',
        'max_exports',
        'billing_cycle',
        'is_active'
    ];

    public function sections()
    {
        return $this->belongsToMany(
            Section::class,
            'plan_sections',
            'plan_id',
            'section_id'
        );
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_plans');
    }
}
