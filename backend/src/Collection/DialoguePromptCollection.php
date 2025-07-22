<?php

declare(strict_types=1);

namespace Src\Collection;

use Src\Model\DialoguePrompt;

class DialoguePromptCollection
{
    private array $prompts = [];

    public function __construct(array $prompts = [])
    {
        foreach ($prompts as $prompt) {
            $this->add($prompt);
        }
    }

    public function add(DialoguePrompt $prompt): void
    {
        $this->prompts[] = $prompt;
    }

    public function getAll(): array
    {
        return $this->prompts;
    }

    public function count(): int
    {
        return count($this->prompts);
    }

    public function isEmpty(): bool
    {
        return empty($this->prompts);
    }

    public function getById(int $id): ?DialoguePrompt
    {
        foreach ($this->prompts as $prompt) {
            if ($prompt->getId() === $id) {
                return $prompt;
            }
        }
        return null;
    }

    public function filterByDifficulty(string $difficulty): DialoguePromptCollection
    {
        $filtered = array_filter($this->prompts, function(DialoguePrompt $prompt) use ($difficulty) {
            return $prompt->getDifficultyLevel() === $difficulty;
        });
        
        return new self($filtered);
    }

    public function filterByCampaign(int $campaignId): DialoguePromptCollection
    {
        $filtered = array_filter($this->prompts, function(DialoguePrompt $prompt) use ($campaignId) {
            return $prompt->getCampaignId() === $campaignId;
        });
        
        return new self($filtered);
    }

    public function getRandom(): ?DialoguePrompt
    {
        if ($this->isEmpty()) {
            return null;
        }
        
        $randomIndex = array_rand($this->prompts);
        return $this->prompts[$randomIndex];
    }

    public function toArray(): array
    {
        return array_map(function(DialoguePrompt $prompt) {
            return [
                'id' => $prompt->getId(),
                'campaign_id' => $prompt->getCampaignId(),
                'prompt_text' => $prompt->getPromptText(),
                'context' => $prompt->getContext(),
                'difficulty_level' => $prompt->getDifficultyLevel()
            ];
        }, $this->prompts);
    }

    public static function fromCampaign(int $campaignId): DialoguePromptCollection
    {
        $prompts = DialoguePrompt::findByCampaignId($campaignId);
        return new self($prompts);
    }
}
