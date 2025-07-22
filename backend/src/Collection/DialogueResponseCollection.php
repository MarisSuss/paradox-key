<?php

declare(strict_types=1);

namespace Src\Collection;

use Src\Model\DialogueResponse;

class DialogueResponseCollection
{
    private array $responses = [];

    public function __construct(array $responses = [])
    {
        foreach ($responses as $response) {
            $this->add($response);
        }
    }

    public function add(DialogueResponse $response): void
    {
        $this->responses[] = $response;
    }

    public function getAll(): array
    {
        return $this->responses;
    }

    public function count(): int
    {
        return count($this->responses);
    }

    public function isEmpty(): bool
    {
        return empty($this->responses);
    }

    public function getById(int $id): ?DialogueResponse
    {
        foreach ($this->responses as $response) {
            if ($response->getId() === $id) {
                return $response;
            }
        }
        return null;
    }

    public function filterByOutcome(string $outcomeType): DialogueResponseCollection
    {
        $filtered = array_filter($this->responses, function(DialogueResponse $response) use ($outcomeType) {
            return $response->getOutcomeType() === $outcomeType;
        });
        
        return new self($filtered);
    }

    public function getHelpful(): DialogueResponseCollection
    {
        return $this->filterByOutcome('helpful');
    }

    public function getHarmful(): DialogueResponseCollection
    {
        return $this->filterByOutcome('harmful');
    }

    public function getNeutral(): DialogueResponseCollection
    {
        return $this->filterByOutcome('neutral');
    }

    public function filterByPositiveImpact(): DialogueResponseCollection
    {
        $filtered = array_filter($this->responses, function(DialogueResponse $response) {
            return $response->getTimelineImpact() > 0;
        });
        
        return new self($filtered);
    }

    public function filterByNegativeImpact(): DialogueResponseCollection
    {
        $filtered = array_filter($this->responses, function(DialogueResponse $response) {
            return $response->getTimelineImpact() < 0;
        });
        
        return new self($filtered);
    }

    public function getTotalTimelineImpact(): float
    {
        return array_sum(array_map(function(DialogueResponse $response) {
            return $response->getTimelineImpact();
        }, $this->responses));
    }

    public function getAverageTimelineImpact(): float
    {
        if ($this->isEmpty()) {
            return 0.0;
        }
        
        return $this->getTotalTimelineImpact() / $this->count();
    }

    public function getRandom(): ?DialogueResponse
    {
        if ($this->isEmpty()) {
            return null;
        }
        
        $randomIndex = array_rand($this->responses);
        return $this->responses[$randomIndex];
    }

    public function toArray(): array
    {
        return array_map(function(DialogueResponse $response) {
            return [
                'id' => $response->getId(),
                'prompt_id' => $response->getPromptId(),
                'response_text' => $response->getResponseText(),
                'outcome_type' => $response->getOutcomeType(),
                'timeline_impact' => $response->getTimelineImpact(),
                'is_helpful' => $response->isHelpful(),
                'is_harmful' => $response->isHarmful()
            ];
        }, $this->responses);
    }

    public function toArrayForPlayer(): array
    {
        return array_map(function(DialogueResponse $response) {
            return [
                'id' => $response->getId(),
                'text' => $response->getResponseText(),
                'outcome_type' => $response->getOutcomeType()
                // Don't reveal timeline_impact to player
            ];
        }, $this->responses);
    }

    public static function fromPrompt(int $promptId): DialogueResponseCollection
    {
        $responses = DialogueResponse::findByPromptId($promptId);
        return new self($responses);
    }
}
