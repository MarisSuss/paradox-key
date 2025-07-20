<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class HistoricEvent
{
    private int $id;
    private int $campaignId;
    private string $name;
    private string $date;

    public function __construct(
        int $id,
        int $campaignId,
        string $name,
        string $date
    ) {
        $this->id = $id;
        $this->campaignId = $campaignId;
        $this->name = $name;
        $this->date = $date;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getCampaignId(): int
    {
        return $this->campaignId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public static function findAll(): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_events ORDER BY date ASC");
        
        $events = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $events[] = new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['name'],
                    $result['date']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic events: " . $e->getMessage());
        }

        return $events;
    }

    public static function findById(int $id): ?HistoricEvent
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_events WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['name'],
                    $result['date']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic event: " . $e->getMessage());
        }

        return null;
    }

    public static function findByCampaignId(int $campaignId): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_events WHERE campaign_id = :campaign_id ORDER BY date ASC");
        $stmt->bindParam(':campaign_id', $campaignId, PDO::PARAM_INT);
        
        $events = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $events[] = new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['name'],
                    $result['date']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic events by campaign: " . $e->getMessage());
        }

        return $events;
    }
}