<?php

declare(strict_types=1);

namespace Src\Service;

use Src\Model\GameState;
use Src\Model\HistoricPerson;
use Src\Model\HistoricEvent;

class GameService
{
    /**
     * Creates a new game and generates historic people for it
     */
    public static function createNewGame(int $userId): GameState
    {
        error_log("Creating new game for user: " . $userId);
        
        // Create new game state with default campaign (ID 1)
        $gameState = new GameState(0, $userId, 1);
        
        error_log("GameState created, attempting to save...");
        
        if (!$gameState->save()) {
            error_log("Failed to save game state");
            throw new \Exception('Failed to create new game');
        }
        
        error_log("GameState saved with ID: " . $gameState->getId());

        // Generate Winston Churchill with death date before WW2
        $winston = new HistoricPerson(
            0,
            $gameState->getId(),
            'Winston Churchill',
            '1938-01-01' // Dies before WW2 starts (1939-09-01)
        );
        
        error_log("HistoricPerson created, attempting to save...");
        
        if (!$winston->save()) {
            error_log("Failed to save Winston Churchill");
            throw new \Exception('Failed to create Winston Churchill');
        }
        
        error_log("Winston Churchill saved with ID: " . $winston->getId());
        
        return $gameState;
    }

    /**
     * Saves a historic person's life by extending their death date
     */
    public static function savePersonLife(int $gameStateId, int $personId): bool
    {
        // Verify game exists and is not completed
        $gameState = GameState::find($gameStateId);
        if (!$gameState || $gameState->isCompleted()) {
            return false;
        }

        // Find the person
        $person = HistoricPerson::findById($personId);
        if (!$person || $person->getGameStateId() !== $gameStateId) {
            return false;
        }

        // Extend their death date to after WW2 (save their life)
        $person->setDeathDate('1965-01-24'); // Winston's actual death date
        
        return $person->save();
    }

    /**
     * Calculates timeline accuracy and completes the game
     */
    public static function completeGame(int $gameStateId): array
    {
        // Find the game
        $gameState = GameState::find($gameStateId);
        if (!$gameState || $gameState->isCompleted()) {
            throw new \Exception('Game not found or already completed');
        }

        // Get all people in this game
        $people = HistoricPerson::findByGameState($gameStateId);
        
        // Get all historic events for this campaign
        $events = HistoricEvent::findByCampaignId($gameState->getCampaignId());
        
        // Calculate timeline accuracy
        $totalAccuracy = 0.0;
        $eventResults = [];
        
        foreach ($events as $event) {
            $eventAccuracy = self::calculateEventAccuracy($event, $people);
            $totalAccuracy += $eventAccuracy;
            
            $eventResults[] = [
                'event' => $event->getName(),
                'date' => $event->getDate(),
                'accuracy' => $eventAccuracy
            ];
        }
        
        // Average accuracy across all events
        $finalAccuracy = count($events) > 0 ? $totalAccuracy / count($events) : 0.0;
        
        // Complete the game
        $gameState->completeGame($finalAccuracy);
        
        return [
            'timeline_accuracy' => $finalAccuracy,
            'event_results' => $eventResults,
            'people_saved' => array_filter($people, fn($p) => $p->isAliveAt('1939-09-01')), // WW2 start date
            'total_people' => count($people)
        ];
    }

    /**
     * Calculates accuracy for a specific event based on alive people
     */
    private static function calculateEventAccuracy(HistoricEvent $event, array $people): float
    {
        // For this demo, we only check World War II
        if ($event->getName() === 'World War II') {
            // Find Winston Churchill
            $winston = null;
            foreach ($people as $person) {
                if ($person->getName() === 'Winston Churchill') {
                    $winston = $person;
                    break;
                }
            }
            
            if ($winston) {
                // Check if Winston is alive during WW2 start (1939-09-01)
                $isAliveAtWW2Start = $winston->isAliveAt('1939-09-01');
                
                // If Winston is alive at WW2 start, user succeeded
                if ($isAliveAtWW2Start) {
                    return 100.0; // 100% accuracy
                } else {
                    return 0.0; // 0% accuracy - Winston died before WW2
                }
            }
        }
        
        // Default accuracy for other events
        return 50.0;
    }
}
