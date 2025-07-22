<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class DialoguePrompts
{
    private int $id;
    private int $campaignId;
    private string $promptText;
    private ?string $context;
    private string $difficultyLevel;

    public function __construct(
        int $id,
        int $campaignId,
        string $promptText,
        ?string $context = null,
        string $difficultyLevel = 'medium'
    ) {
        $this->id = $id;
        $this->campaignId = $campaignId;
        $this->promptText = $promptText;
        $this->context = $context;
        $this->difficultyLevel = $difficultyLevel;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getPromptText(): string
    {
        return $this->promptText;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getDifficultyLevel(): string
    {
        return $this->difficultyLevel;
    }

    public static function findByCampaignId(int $campaignId): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM dialogue_prompts WHERE campaign_id = :campaign_id");
        $stmt->bindParam(':campaign_id', $campaignId, PDO::PARAM_INT);
        
        $prompts = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $prompts[] = new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['prompt_text'],
                    $result['context'],
                    $result['difficulty_level']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching dialogue prompts: " . $e->getMessage());
        }

        return $prompts;
    }

    public static function findById(int $id): ?DialoguePrompts
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM dialogue_prompts WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['prompt_text'],
                    $result['context'],
                    $result['difficulty_level']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching dialogue prompt: " . $e->getMessage());
        }

        return null;
    }

    public static function getRandomByDifficulty(int $campaignId, string $difficulty = 'medium'): ?DialoguePrompts
    {
        $prompts = self::findByCampaignId($campaignId);
        $filteredPrompts = array_filter($prompts, fn($p) => $p->getDifficultyLevel() === $difficulty);
        
        if (empty($filteredPrompts)) {
            return null;
        }
        
        $randomIndex = array_rand($filteredPrompts);
        return $filteredPrompts[$randomIndex];
    }
}
