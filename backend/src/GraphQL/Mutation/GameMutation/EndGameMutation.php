<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation\GameMutation;

use GraphQL\Type\Definition\Type;
use Src\Service\GameService;
use Src\GraphQL\Type\GameType\GameResultType;
use Src\Exception\ClientSafeException;

class EndGameMutation
{
    public static function get()
    {
        return [
            'type' => GameResultType::type(),
            'args' => [
                'gameStateId' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($root, $args) {
                // Validate arguments
                if ($args['gameStateId'] <= 0) {
                    throw new ClientSafeException('Invalid game state ID.');
                }

                try {
                    error_log("Attempting to complete game with ID: " . $args['gameStateId']);
                    
                    $result = GameService::completeGame($args['gameStateId']);
                    
                    error_log("Game completed successfully. Timeline accuracy: " . $result['timeline_accuracy']);
                    
                    return [
                        'gameStateId' => $args['gameStateId'],
                        'timelineAccuracy' => $result['timeline_accuracy'],
                        'eventResults' => array_map(function($eventResult) {
                            return $eventResult['event'] . ' (' . $eventResult['date'] . '): ' . $eventResult['accuracy'] . '% accuracy';
                        }, $result['event_results']),
                        'peopleSaved' => count($result['people_saved']),
                        'totalPeople' => $result['total_people'],
                        'message' => self::getCompletionMessage($result['timeline_accuracy'], $result['people_saved'])
                    ];
                } catch (\Exception $e) {
                    error_log("Error completing game: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    throw new ClientSafeException('Failed to activate Paradox Key: ' . $e->getMessage());
                }
            },
        ];
    }

    private static function getCompletionMessage(float $accuracy, array $peopleSaved): string
    {
        $savedCount = count($peopleSaved);
        
        if ($accuracy >= 100.0) {
            return "PARADOX KEY ACTIVATED! Timeline successfully merged with 100% accuracy! Winston Churchill survived and led the Allies to victory in World War II. History has been forever changed.";
        } elseif ($accuracy >= 50.0) {
            return "Paradox Key activated! Timeline merge achieved {$accuracy}% accuracy. You saved {$savedCount} people. The ripple effects of your actions have altered the course of history.";
        } else {
            return "Paradox Key activated with {$accuracy}% timeline accuracy. Though the changes were subtle, every action in time creates ripples that shape the future.";
        }
    }
}