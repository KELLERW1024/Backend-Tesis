<?php

namespace App\Models;

use App\Models\UserAnswers;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'user_plan_id',
        'subscription_id',
        'title',
        'summary',
        'status',
        'last_activity_at'
    ];

    public function userPlan()
    {
        return $this->belongsTo(UserPlan::class, 'user_plan_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function answers()
    {
        return $this->hasMany(UserAnswers::class);
    }

    public function sectionProgress()
    {
        return $this->hasMany(ConversationSectionProgress::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    // public function plan()
    // {
    //     return $this->belongsTo(Plan::class);
    // }
}