<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\PlanNode;
use App\Models\Question;
use App\Services\Prompts\PlanPromptResolver;
use App\Services\Prompts\PlanPrompts;

class AnswerValidationService
{
    public function __construct(
        protected PromptService $promptService,
        protected OpenAIService $openAIService,
        protected ThesisContextService $thesisContextService,
        protected ConversationService $conversationService,
        protected PlanPromptResolver $planPromptResolver,
    ) {}

    /**
     * @param array $data
     * @return array{
     *     valid: bool,
     *     inconsistent: bool,
     *     result: array,
     *     plan_prompts: PlanPrompts,
     *     section_node: ?PlanNode,
     *     parent_node: ?PlanNode,
     *     objective: ?string,
     *     history: array
     * }
     */
    public function validate(Conversation $conversation, array $data, string $documentContent = '', string $imageContent = ''): array
    {
        // Prompts propios del plan de la conversación (Negocio, Mejora, ...)
        $planPrompts = $this->planPromptResolver->forConversation($conversation);

        // Sección (nodo) de la pregunta y su capítulo raíz
        $sectionNode = PlanNode::find(Question::find($data['idQuestion'])?->plan_node_id);
        $parentNode = $sectionNode;
        while ($parentNode && $parentNode->parent_id !== null) {
            $parentNode = $parentNode->parent;
        }

        $objective = $parentNode?->objective ?? $sectionNode?->objective;
        $history = $this->conversationService->getHistoryLimit10($conversation->id);

        // 1. ¿Responde a la pregunta?
        $prompt = $this->promptService->buildValidationPrompt($planPrompts, [
            ...$data,
            'file_content' => $documentContent,
            'image_content' => $imageContent,
            'objective' => $objective,
            'history' => $history,
        ]);

        $result = $this->openAIService->json($prompt) ?? [];

        // 2. ¿Contradice lo registrado antes?
        $context = $this->thesisContextService->validateResponseAgainstContext($conversation->id, $data['response'] ?? '');
        $inconsistent = (bool) ($context['is_inconsistent'] ?? false);

        return [
            'valid' => !empty($result['is_valid']) && !$inconsistent,
            'inconsistent' => $inconsistent,
            'result' => $result,
            'plan_prompts' => $planPrompts,
            'section_node' => $sectionNode,
            'parent_node' => $parentNode,
            'objective' => $objective,
            'history' => $history,
        ];
    }
}
