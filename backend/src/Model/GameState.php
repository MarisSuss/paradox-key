<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class GameState
{
    private int $id;
    private int $userId;
    private int $campaignId;
    private float $timelineAccuracy;
    private bool $isCompleted;
    private string $createdAt;
    private ?string $completedAt;

    public function __construct(
        int $id,
        int $userId,
        int $campaignId,
        float $timelineAccuracy = 0.0,
        bool $isCompleted = false,
        string $createdAt = '',
        ?string $completedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->campaignId = $campaignId;
        $this->timelineAccuracy = $timelineAccuracy;
        $this->isCompleted = $isCompleted;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
        $this->completedAt = $completedAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getTimelineAccuracy(): float
    {
        return (float)$this->timelineAccuracy;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?string
    {
        return $this->completedAt;
    }

    // Business logic methods
    public function completeGame(float $timelineAccuracy): bool
    {
        $this->timelineAccuracy = $timelineAccuracy;
        $this->isCompleted = true;
        $this->completedAt = date('Y-m-d H:i:s');

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("
            UPDATE game_states 
            SET timeline_accuracy = :accuracy, is_completed = :completed, completed_at = :completed_at 
            WHERE id = :id
        ");
        
        $stmt->bindParam(':accuracy', $this->timelineAccuracy, PDO::PARAM_STR);
        $stmt->bindParam(':completed', $this->isCompleted, PDO::PARAM_BOOL);
        $stmt->bindParam(':completed_at', $this->completedAt, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error completing game: " . $e->getMessage());
            return false;
        }
    }

    public function save(): bool
    {
        $pdo = Connection::getInstance();
        
        if ($this->id === 0) {
            // Insert new game
            $stmt = $pdo->prepare("
                INSERT INTO game_states (user_id, campaign_id, timeline_accuracy, is_completed, created_at) 
                VALUES (:user_id, :campaign_id, :accuracy, :completed, :created_at)
            ");
            $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
            $stmt->bindParam(':campaign_id', $this->campaignId, PDO::PARAM_INT);
            $stmt->bindParam(':accuracy', $this->timelineAccuracy, PDO::PARAM_STR);
            $stmt->bindParam(':completed', $this->isCompleted, PDO::PARAM_BOOL);
            $stmt->bindParam(':created_at', $this->createdAt, PDO::PARAM_STR);
        } else {
            // Update existing game
            $stmt = $pdo->prepare("
                UPDATE game_states 
                SET timeline_accuracy = :accuracy, is_completed = :completed, completed_at = :completed_at 
                WHERE id = :id
            ");
            $stmt->bindParam(':accuracy', $this->timelineAccuracy, PDO::PARAM_STR);
            $stmt->bindParam(':completed', $this->isCompleted, PDO::PARAM_BOOL);
            $stmt->bindParam(':completed_at', $this->completedAt, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        }
        
        try {
            $stmt->execute();
            if ($this->id === 0) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error saving game state: " . $e->getMessage());
            return false;
        }
    }

    public static function find(int $id): ?GameState
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM game_states WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['user_id'],
                    (int)$result['campaign_id'],
                    (float)$result['timeline_accuracy'],
                    (bool)$result['is_completed'],
                    $result['created_at'],
                    $result['completed_at']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching game state: " . $e->getMessage());
        }

        return null;
    }

    public static function findIncompleteByUserId(int $userId): ?GameState
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM game_states WHERE user_id = :user_id AND is_completed = 0 ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['user_id'],
                    (int)$result['campaign_id'],
                    (float)$result['timeline_accuracy'],
                    (bool)$result['is_completed'],
                    $result['created_at'],
                    $result['completed_at']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching incomplete game state: " . $e->getMessage());
        }

        return null;
    }
}