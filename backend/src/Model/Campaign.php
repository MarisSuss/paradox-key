<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class Campaign
{
    private int $id;
    private string $name;
    private string $description;

    public function __construct(
        int $id,
        string $name,
        string $description = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    // Setters
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    // Database operations
    public function save(): bool
    {
        $pdo = Connection::getInstance();
        
        try {
            if ($this->id > 0) {
                // Update existing campaign
                $stmt = $pdo->prepare("
                    UPDATE campaigns 
                    SET name = :name, description = :description
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            } else {
                // Insert new campaign
                $stmt = $pdo->prepare("
                    INSERT INTO campaigns (name, description) 
                    VALUES (:name, :description)
                ");
            }
            
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
            
            $result = $stmt->execute();
            
            if ($this->id === 0) {
                $this->id = (int)$pdo->lastInsertId();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error saving campaign: " . $e->getMessage());
            return false;
        }
    }

    // Get all campaigns
    public static function getAll(): array
    {
        $pdo = Connection::getInstance();
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM campaigns ORDER BY name");
            $stmt->execute();
            
            $campaigns = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $campaigns[] = new self(
                    $row['id'],
                    $row['name'],
                    $row['description'],
                    (bool)$row['is_active'],
                    $row['created_at']
                );
            }
            
            return $campaigns;
        } catch (PDOException $e) {
            error_log("Error getting campaigns: " . $e->getMessage());
            return [];
        }
    }

    // Get campaign by ID
    public static function getById(int $id): ?self
    {
        $pdo = Connection::getInstance();
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new self(
                    $row['id'],
                    $row['name'],
                    $row['description'],
                    (bool)$row['is_active'],
                    $row['created_at']
                );
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error getting campaign by ID: " . $e->getMessage());
            return null;
        }
    }

    // Get events for this campaign
    public function getEvents(): array
    {
        $pdo = Connection::getInstance();
        
        try {
            $stmt = $pdo->prepare("
                SELECT he.* 
                FROM historic_events he
                JOIN campaign_events ce ON he.id = ce.event_id
                WHERE ce.campaign_id = :campaign_id
                ORDER BY he.date
            ");
            $stmt->bindParam(':campaign_id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            
            $events = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = new HistoricEvent(
                    $row['id'],
                    $row['name'],
                    $row['date'],
                    $row['created_at']
                );
            }
            
            return $events;
        } catch (PDOException $e) {
            error_log("Error getting events for campaign: " . $e->getMessage());
            return [];
        }
    }

    // Add event to campaign
    public function addEvent(int $eventId): bool
    {
        $pdo = Connection::getInstance();
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO campaign_events (campaign_id, event_id) 
                VALUES (:campaign_id, :event_id)
            ");
            $stmt->bindParam(':campaign_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding event to campaign: " . $e->getMessage());
            return false;
        }
    }

    // Remove event from campaign
    public function removeEvent(int $eventId): bool
    {
        $pdo = Connection::getInstance();
        
        try {
            $stmt = $pdo->prepare("
                DELETE FROM campaign_events 
                WHERE campaign_id = :campaign_id AND event_id = :event_id
            ");
            $stmt->bindParam(':campaign_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error removing event from campaign: " . $e->getMessage());
            return false;
        }
    }

    // Convert to array for JSON serialization
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
