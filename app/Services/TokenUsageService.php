<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokenUsageService
{
    // Campos con los que las peticiones envían el id de la conversación
    private const CONVERSATION_INPUTS = ['idConversation', 'id_subscription_conversation'];

    public function record(?array $usage): void
    {
        $tokens = (int) ($usage['total_tokens']
            ?? (($usage['input_tokens'] ?? 0) + ($usage['output_tokens'] ?? 0)));

        if ($tokens <= 0) {
            return;
        }

        // La medición nunca debe interrumpir el trabajo con la IA
        try {
            [$subscription, $planId] = $this->resolveOwner();

            if (!$subscription) {
                Log::warning('TokenUsage: no se pudo identificar la suscripción, consumo no registrado', [
                    'user_id' => auth('api')->id(),
                    'tokens'  => $tokens,
                    'ruta'    => request()->path(),
                ]);
                return;
            }

            $this->addTokens($subscription, $planId, $tokens);
        } catch (\Throwable $e) {
            Log::error('TokenUsage: error al registrar el consumo', [
                'tokens' => $tokens,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    // Suma los tokens a la fila del usuario, suscripción y plan (la crea si no existe).
    private function addTokens(UserSubscription $subscription, ?int $planId, int $tokens): void
    {
        $periodStart = $subscription->start_date ?? $subscription->created_at;

        if ($planId !== null) {
            DB::statement(
                'INSERT INTO user_token_quota (user_id, subscription_id, plan_id, tokens_used, period_start, period_end)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE tokens_used = tokens_used + VALUES(tokens_used)',
                [$subscription->user_id, $subscription->id, $planId, $tokens, $periodStart, $subscription->end_date]
            );
            return;
        }

        // Sin plan identificado: el índice único no agrupa valores NULL, así que se busca la fila a mano
        $updated = DB::table('user_token_quota')
            ->where('user_id', $subscription->user_id)
            ->where('subscription_id', $subscription->id)
            ->whereNull('plan_id')
            ->increment('tokens_used', $tokens);

        if ($updated === 0) {
            DB::table('user_token_quota')->insert([
                'user_id'         => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'plan_id'         => null,
                'tokens_used'     => $tokens,
                'period_start'    => $periodStart,
                'period_end'      => $subscription->end_date,
            ]);
        }
    }

    /**
     * Suscripción y plan del consumo: salen de la conversación de la petición.
     * @return array{0: ?UserSubscription, 1: ?int}
     */
    private function resolveOwner(): array
    {
        foreach (self::CONVERSATION_INPUTS as $input) {
            $conversationId = request()->input($input);

            if ($conversationId) {
                $conversation = Conversation::with('userPlan')->find($conversationId);

                if ($conversation?->subscription_id) {
                    return [
                        UserSubscription::find($conversation->subscription_id),
                        $conversation->userPlan?->plan_id,
                    ];
                }
            }
        }

        $userId = auth('api')->id();

        return [
            $userId ? UserSubscription::where('user_id', $userId)->latest('id')->first() : null,
            null,
        ];
    }
}
