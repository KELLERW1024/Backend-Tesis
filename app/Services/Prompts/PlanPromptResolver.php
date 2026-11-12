<?php
namespace App\Services\Prompts;

use App\Models\Conversation;
use App\Models\Plan;

class PlanPromptResolver
{
    /**
     * plans.id => conjunto de prompts.
     * Los planes que no figuran aquí usan BusinessPlanPrompts como respaldo.
     */
    private const PLAN_PROMPTS = [
        1 => BusinessPlanPrompts::class,  
        2 => ImprovementPlanPrompts::class, 
    ];

    public function forConversation(Conversation|int $conversation): PlanPrompts
    {
        if (is_int($conversation)) {
            $conversation = Conversation::with('userPlan.plan')->find($conversation);
        } else {
            $conversation->loadMissing('userPlan.plan');
        }

        $plan = $conversation?->userPlan?->plan;

        return $this->build($plan?->id, $plan?->name, $conversation?->id);
    }

    public function forPlan(int $planId, ?int $conversationId = null): PlanPrompts
    {
        return $this->build($planId, Plan::find($planId)?->name, $conversationId);
    }

    private function build(?int $planId, ?string $planName, ?int $conversationId): PlanPrompts
    {
        $class = self::PLAN_PROMPTS[$planId] ?? null;

        if ($class === null) {
            return new BusinessPlanPrompts($planId, $planName, $conversationId, isFallback: true);
        }

        return new $class($planId, $planName, $conversationId);
    }
}
