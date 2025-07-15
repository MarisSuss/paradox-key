<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class HistoricPerson
{
    private int $id;
    private int $gameStateId;
    private string $name;
    private string $deathDate;

    public function __construct(
        int $id,
        int $gameStateId,
        string $name,
        string $deathDate
    ) {
        $this->id = $id;
        $this->gameStateId = $gameStateId;
        $this->name = $name;
        $this->deathDate = $deathDate;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getGameStateId(): int
    {
        return $this->gameStateId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeathDate(): string
    {
        return $this->deathDate;
    }

    // Setters
    public function setDeathDate(string $deathDate): void
    {
        $this->deathDate = $deathDate;
    }

    // Business logic methods
    public function isAliveAt(string $date): bool
    {
        return $this->deathDate > $date;
    }

    public function save(): bool
    {
        $pdo = Connection::getInstance();
        
        if ($this->id === 0) {
            // Insert new person
            $stmt = $pdo->prepare("
                INSERT INTO historic_people (game_state_id, name, death_date) 
                VALUES (:game_state_id, :name, :death_date)
            ");
            $stmt->bindParam(':game_state_id', $this->gameStateId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':death_date', $this->deathDate, PDO::PARAM_STR);
        } else {
            // Update existing person
            $stmt = $pdo->prepare("
                UPDATE historic_people 
                SET death_date = :death_date 
                WHERE id = :id
            ");
            $stmt->bindParam(':death_date', $this->deathDate, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        }
        
        try {
            $stmt->execute();
            if ($this->id === 0) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error saving historic person: " . $e->getMessage());
            return false;
        }
    }

    public static function findByGameState(int $gameStateId): array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_people WHERE game_state_id = :game_state_id");
        $stmt->bindParam(':game_state_id', $gameStateId, PDO::PARAM_INT);
        
        $people = [];
        
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $result) {
                $people[] = new self(
                    (int)$result['id'],
                    (int)$result['game_state_id'],
                    $result['name'],
                    $result['death_date']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic people: " . $e->getMessage());
        }

        return $people;
    }

    public static function findById(int $id): ?HistoricPerson
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM historic_people WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return new self(
                    (int)$result['id'],
                    (int)$result['game_state_id'],
                    $result['name'],
                    $result['death_date']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic person: " . $e->getMessage());
        }

        return null;
    }
}