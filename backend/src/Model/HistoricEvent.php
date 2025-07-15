<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class HistoricEvent
{
    private int $id;
    private string $name;
    private string $date;
    private string $createdAt;

    public function __construct(
        int $id,
        string $name,
        string $date,
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
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

    public function getDate(): string
    {
        return $this->date;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
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
                    $result['name'],
                    $result['date'],
                    $result['created_at']
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
                    $result['name'],
                    $result['date'],
                    $result['created_at']
                );
            }
        } catch (PDOException $e) {
            error_log("Error fetching historic event: " . $e->getMessage());
        }

        return null;
    }
}