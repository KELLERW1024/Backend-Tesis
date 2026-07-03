<?php

namespace App\Models;

use App\Models\Package;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'max_uses_per_user',
        'valid_from',
        'valid_until',
        'first_purchase_only',
        'is_active'
    ];

    public function packages()
    {
        return $this->belongsToMany(
            Package::class,
            'coupon_package',
            'coupon_id',
            'package_id'
        );
    }

}
