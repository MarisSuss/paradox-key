<?php

declare(strict_types=1);

namespace Src\Service;

use Src\Model\GameState;
use Src\Model\HistoricPerson;
use Src\Model\HistoricPersonTemplate;
use Src\Model\HistoricEvent;

class GameService
{
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

        // Get a random alternate death date from templates if available
        $newDeathDate = self::getNewDeathDateForPerson($person, $gameState->getCampaignId());
        
        $person->setDeathDate($newDeathDate);
        
        return $person->save();
    }

    private static function getNewDeathDateForPerson(HistoricPerson $person, int $campaignId): string
    {
        // Try to find a matching template for this person
        $templates = HistoricPersonTemplate::findByCampaignId($campaignId);
        
        foreach ($templates as $template) {
            if ($template->getName() === $person->getName()) {
                return $template->getRandomAlternateDeathDate();
            }
        }
        
        // Fallback: use the person's current alternate death date or default
        return !empty($person->getAlternateDeathDate()) 
            ? $person->getAlternateDeathDate() 
            : '1965-01-24';
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
     * Gets the current state of a game including people and their status
     */
    public static function getGameStatus(int $gameStateId): array
    {
        $gameState = GameState::find($gameStateId);
        if (!$gameState) {
            throw new \Exception('Game not found');
        }

        $people = HistoricPerson::findByGameState($gameStateId);
        $events = HistoricEvent::findByCampaignId($gameState->getCampaignId());

        $peopleStatus = [];
        foreach ($people as $person) {
            $peopleStatus[] = [
                'id' => $person->getId(),
                'name' => $person->getName(),
                'death_date' => $person->getDeathDate(),
                'alternate_death_date' => $person->getAlternateDeathDate(),
                'is_alive_at_ww2' => $person->isAliveAt('1939-09-01'),
                'can_be_saved' => !$person->isAliveAt('1939-09-01')
            ];
        }

        return [
            'game_id' => $gameState->getId(),
            'user_id' => $gameState->getUserId(),
            'campaign_id' => $gameState->getCampaignId(),
            'is_completed' => $gameState->isCompleted(),
            'timeline_accuracy' => $gameState->getTimelineAccuracy(),
            'people' => $peopleStatus,
            'events' => array_map(fn($e) => [
                'name' => $e->getName(),
                'date' => $e->getDate()
            ], $events)
        ];
    }

    /**
     * Checks if a person can be saved in the current game state
     */
    public static function canSavePerson(int $gameStateId, int $personId): bool
    {
        $gameState = GameState::find($gameStateId);
        if (!$gameState || $gameState->isCompleted()) {
            return false;
        }

        $person = HistoricPerson::findById($personId);
        if (!$person || $person->getGameStateId() !== $gameStateId) {
            return false;
        }

        // Can only save people who are currently dead before the critical event
        return !$person->isAliveAt('1939-09-01');
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
