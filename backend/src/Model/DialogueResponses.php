<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class DialogueResponses
{
    private int $id;
    private int $promptId;
    private string $responseText;
    private string $outcomeType;
    private float $timelineImpact;

    public function __construct(
        int $id,
        int $promptId,
        string $responseText,
        string $outcomeType = 'neutral',
        float $timelineImpact = 0.0
    ) {
        $this->id = $id;
        $this->promptId = $promptId;
        $this->responseText = $responseText;
        $this->outcomeType = $outcomeType;
        $this->timelineImpact = $timelineImpact;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPromptId(): int
    {
        return $this->promptId;
    }

    public function getResponseText(): string
    {
        return $this->responseText;
    }

    public function getOutcomeType(): string
    {
        return $this->outcomeType;
    }

    public function getTimelineImpact(): float
    {
        return $this->timelineImpact;
    }

    public function isHelpful(): bool
    {
        return $this->outcomeType === 'helpful';
    }

    public function isHarmful(): bool
    {
        return $this->outcomeType === 'harmful';
    }

    public static function findByPromptId(int $promptId): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM dialogue_responses WHERE prompt_id = :prompt_id");
        $stmt->bindParam(':prompt_id', $promptId, PDO::PARAM_INT);
        
        $responses = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $responses[] = new self(
                    (int)$result['id'],
                    (int)$result['prompt_id'],
                    $result['response_text'],
                    $result['outcome_type'],
                    (float)$result['timeline_impact']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching dialogue responses: " . $e->getMessage());
        }

        return $responses;
    }

    public static function findById(int $id): ?DialogueResponses
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM dialogue_responses WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['prompt_id'],
                    $result['response_text'],
                    $result['outcome_type'],
                    (float)$result['timeline_impact']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching dialogue response: " . $e->getMessage());
        }

        return null;
    }
}
