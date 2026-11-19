<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\PlanNode;
use App\Models\Question;
use App\Services\Prompts\PlanPromptResolver;
use App\Services\Prompts\PlanPrompts;

class AnswerValidationService
{
    private const COHERENCE_MAX_CHARS = 4000;

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
        $planPrompts = $this->planPromptResolver->forConversation($conversation);

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
            'structure' => $this->thesisContextService->structureText($conversation->id),
        ]);

        $result = $this->openAIService->json($prompt) ?? [];

        // Un adjunto que no corresponde al proyecto invalida la respuesta aunque el texto esté bien
        $hasAttachments = trim($documentContent) !== '' || trim($imageContent) !== '';
        if ($hasAttachments && ($result['attachments_relevant'] ?? true) === false) {
            $result['is_valid'] = false;
            $result['feedback'] = trim($result['attachments_feedback'] ?? '') !== ''
                ? $result['attachments_feedback']
                : 'Revisa los archivos adjuntos: no corresponden a tu proyecto.';
        }

        // 2. ¿Contradice lo registrado antes?
        //    Se revisa también el texto extraído de la imagen y del documento adjuntos
        $context = $this->thesisContextService->validateResponseAgainstContext(
            $conversation->id,
            $this->textForCoherence($data['response'] ?? '', $documentContent, $imageContent)
        );
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

    // Texto del tesista y de sus adjuntos, con etiquetas, recortado para no exceder el prompt
    private function textForCoherence(string $response, string $documentContent, string $imageContent): string
    {
        $parts = [];

        if (trim($response) !== '') {
            $parts[] = ConversationService::LABEL_RESPUESTA . "\n" . trim($response);
        }

        if (trim($imageContent) !== '') {
            $parts[] = ConversationService::LABEL_IMAGEN . "\n" . trim($imageContent);
        }

        if (trim($documentContent) !== '') {
            $parts[] = ConversationService::LABEL_DOCUMENTO . "\n" . trim($documentContent);
        }

        return mb_substr(implode("\n\n", $parts), 0, self::COHERENCE_MAX_CHARS);
    }
}
