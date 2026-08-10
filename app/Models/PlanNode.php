<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanNode extends Model
{
    protected $table = 'plan_node';
    public $timestamps = false;

   protected $fillable = [
        'user_plan_id',
        'plan_id',
        'parent_id',
        'titulo',
        'orden',
        'objective',
        'nivel',
        'codigo',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'user_plan_id');
    }
    

}