<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTokenQuota extends Model
{
    protected $table = 'user_token_quota';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'tokens_used',
        'tokens_limit',
        'period_start',
        'period_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
