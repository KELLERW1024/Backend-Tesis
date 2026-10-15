<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisContext extends Model
{
    protected $table = 'thesis_context';

    protected $fillable = [
        'conversation_id',
        'user_plan_id',
        'business_name',
        'business_sector',
        'product_service',
        'target_customer',
        'main_problem',
        'value_proposition',
        'context_summary',
        'embedding',
        'confidence',
        'assistant_context',
        'plan_context',
    ];

    protected $casts = [
        'embedding' => 'array',
        'confidence' => 'decimal:2',
        'assistant_context' => 'array',
        'plan_context' => 'array',
    ];

    /**
     * Conversación a la que pertenece este contexto.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            Conversation::class,
            'conversation_id'
        );
    }

    /**
     * Plan del usuario al que pertenece este contexto.
     */
    public function userPlan(): BelongsTo
    {
        return $this->belongsTo(
            UserPlan::class,
            'user_plan_id'
        );
    }
}