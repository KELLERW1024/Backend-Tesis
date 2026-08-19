<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $table = 'user_subscriptions';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'package_id',
        'plan_id',
        'plan_variant_id',
        'provider_subscription_id',
        'status',
        'start_date',
        'end_date',
    ];

    // public function plan()
    // {
    //     return $this->belongsTo(Plan::class);
    // }
    public function payments()
    {
        return $this->hasMany(Payments::class, 'subscription_id');
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function conversations()
    {
        return $this->hasMany(
            Conversation::class,
            'subscription_id'
        );
    }


}