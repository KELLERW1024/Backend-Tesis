<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Question;
use App\Models\ThesisContext;
use App\Services\Prompts\PlanPromptResolver;

class AnswerModificationService
{
    public const MINOR = 'menor';
    public const DEEP = 'fondo';
    public const UNCHANGED = 'sin_cambios';

    private const CONTEXT_MAX_CHARS = 4000;

    public function __construct(
        protected OpenAIService $openAIService,
        protected PromptService $promptService,
        protected ThesisContextService $thesisContextService,
        protected PlanPromptResolver $planPromptResolver,
    ) {}

    public function analyze(Conversation $conversation, int $questionId, string $before, string $after): array
    {
        if ($this->normalize($before) === $this->normalize($after)) {
            return ['type' => self::UNCHANGED, 'reason' => ''];
        }

        if ($this->numbersChanged($before, $after)) {
            return ['type' => self::DEEP, 'reason' => 'Cambió una cifra o un porcentaje.'];
        }

        try {
            $plan = $this->planPromptResolver->forConversation($conversation);
            $result = $this->openAIService->json($this->promptService->promptModificationImpact(
                $plan,
                (string) Question::where('id', $questionId)->value('question_text'),
                $before,
                $after,
                $this->projectContext($conversation, $plan)
            ));

            $type = ($result['tipo'] ?? '') === self::MINOR ? self::MINOR : self::DEEP;

            return ['type' => $type, 'reason' => trim((string) ($result['motivo'] ?? ''))];
        } catch (\Throwable $e) {
            // Sin respuesta de la IA no se puede asegurar que el cambio sea menor
            \Log::warning('No se pudo analizar la modificación: ' . $e->getMessage());

            return ['type' => self::DEEP, 'reason' => ''];
        }
    }

    public function numbersChanged(string $before, string $after): bool
    {
        $numbers = function (string $text): array {
            preg_match_all('/\d+(?:[.,]\d+)*/u', $text, $match);
            $list = array_map(fn ($number) => str_replace(',', '', $number), $match[0]);
            sort($list);

            return $list;
        };

        return $numbers($before) !== $numbers($after);
    }

    private function normalize(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    private function projectContext(Conversation $conversation, $plan): string
    {
        $context = ThesisContext::where('conversation_id', $conversation->id)->first();
        $summary = trim((string) ($context?->plan_context['resumen_general'] ?? ''));

        $text = $this->thesisContextService->knownDataText($context, $plan)
            . ($summary !== '' ? "\n\nRESUMEN DE LO AVANZADO:\n" . $summary : '');

        return mb_substr(trim($text), 0, self::CONTEXT_MAX_CHARS) ?: '(Sin datos registrados.)';
    }
}
