<?php

declare(strict_types=1);

namespace Src\Service;

use Src\Model\DialoguePrompt;
use Src\Model\DialogueResponse;
use Src\Model\GameState;
use Src\Collection\DialoguePromptCollection;
use Src\Collection\DialogueResponseCollection;

class DialogueService
{
    public static function getRandomPrompt(int $campaignId, string $difficulty = 'medium'): ?DialoguePrompt
    {
        return DialoguePrompt::getRandomByDifficulty($campaignId, $difficulty);
    }

    public static function getResponsesForPrompt(int $promptId): DialogueResponseCollection
    {
        return DialogueResponseCollection::fromPrompt($promptId);
    }

    public static function getPromptsForCampaign(int $campaignId): DialoguePromptCollection
    {
        return DialoguePromptCollection::fromCampaign($campaignId);
    }

    public static function processPlayerChoice(int $gameStateId, int $responseId): array
    {
        $gameState = GameState::find($gameStateId);
        if (!$gameState || $gameState->isCompleted()) {
            throw new \Exception('Game not found or already completed');
        }

        $response = DialogueResponse::findById($responseId);
        if (!$response) {
            throw new \Exception('Response not found');
        }

        // Apply timeline impact to game state
        $currentAccuracy = $gameState->getTimelineAccuracy();
        $newAccuracy = max(0, min(100, $currentAccuracy + $response->getTimelineImpact()));
        
        // Update game state with new accuracy
        // Note: You'll need to add an updateTimelineAccuracy method to GameState
        
        return [
            'response_text' => $response->getResponseText(),
            'outcome_type' => $response->getOutcomeType(),
            'timeline_impact' => $response->getTimelineImpact(),
            'new_accuracy' => $newAccuracy,
            'is_helpful' => $response->isHelpful(),
            'is_harmful' => $response->isHarmful()
        ];
    }

    public static function getDialogueForGame(int $gameStateId): array
    {
        $gameState = GameState::find($gameStateId);
        if (!$gameState) {
            throw new \Exception('Game not found');
        }

        $prompt = self::getRandomPrompt($gameState->getCampaignId());
        if (!$prompt) {
            throw new \Exception('No prompts available for this campaign');
        }

        $responses = self::getResponsesForPrompt($prompt->getId());

        return [
            'prompt' => [
                'id' => $prompt->getId(),
                'text' => $prompt->getPromptText(),
                'context' => $prompt->getContext(),
                'difficulty' => $prompt->getDifficultyLevel()
            ],
            'responses' => $responses->toArrayForPlayer()
        ];
    }
}
