<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class HistoricPersonTemplate
{
    private int $id;
    private int $campaignId;
    private string $name;
    private string $originalDeathDate;
    private array $alternateDeathDates;

    public function __construct(
        int $id,
        int $campaignId,
        string $name,
        string $originalDeathDate,
        array $alternateDeathDates
    ) {
        $this->id = $id;
        $this->campaignId = $campaignId;
        $this->name = $name;
        $this->originalDeathDate = $originalDeathDate;
        $this->alternateDeathDates = $alternateDeathDates;
    }

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

    public function getOriginalDeathDate(): string
    {
        return $this->originalDeathDate;
    }

    public function getAlternateDeathDates(): array
    {
        return $this->alternateDeathDates;
    }

    public function getRandomAlternateDeathDate(): string
    {
        if (empty($this->alternateDeathDates)) {
            return $this->originalDeathDate;
        }
        
        $randomIndex = array_rand($this->alternateDeathDates);
        return $this->alternateDeathDates[$randomIndex];
    }

    public function getAlternateDeathDate(): string
    {
        return $this->getRandomAlternateDeathDate();
    }

    public static function findByCampaignId(int $campaignId): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_person_templates WHERE campaign_id = :campaign_id");
        $stmt->bindParam(':campaign_id', $campaignId, PDO::PARAM_INT);
        
        $templates = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $alternateDeathDates = json_decode($result['alternate_death_dates'], true) ?? [];
                $templates[] = new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['name'],
                    $result['original_death_date'],
                    $alternateDeathDates
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic person templates: " . $e->getMessage());
        }

        return $templates;
    }

    public static function findById(int $id): ?HistoricPersonTemplate
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_person_templates WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $alternateDeathDates = json_decode($result['alternate_death_dates'], true) ?? [];
                return new self(
                    (int)$result['id'],
                    (int)$result['campaign_id'],
                    $result['name'],
                    $result['original_death_date'],
                    $alternateDeathDates
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic person template: " . $e->getMessage());
        }

        return null;
    }
}
