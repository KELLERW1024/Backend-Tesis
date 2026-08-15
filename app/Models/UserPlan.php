<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    protected $table = 'user_plan';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'plan_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }


    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'user_plan_id');
    }
    

}