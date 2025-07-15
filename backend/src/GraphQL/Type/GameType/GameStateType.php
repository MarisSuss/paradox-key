<?php

declare(strict_types=1);

namespace Src\GraphQL\Type\GameType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Src\Model\HistoricPerson;

class GameStateType
{
    private static ?ObjectType $type = null;

    public static function type(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'GameState',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::int()),
                        'resolve' => function ($gameState) {
                            return $gameState->getId();
                        }
                    ],
                    'userId' => [
                        'type' => Type::nonNull(Type::int()),
                        'resolve' => function ($gameState) {
                            return $gameState->getUserId();
                        }
                    ],
                    'timelineAccuracy' => [
                        'type' => Type::nonNull(Type::float()),
                        'resolve' => function ($gameState) {
                            return $gameState->getTimelineAccuracy();
                        }
                    ],
                    'isCompleted' => [
                        'type' => Type::nonNull(Type::boolean()),
                        'resolve' => function ($gameState) {
                            return $gameState->isCompleted();
                        }
                    ],
                    'createdAt' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => function ($gameState) {
                            return $gameState->getCreatedAt();
                        }
                    ],
                    'completedAt' => [
                        'type' => Type::string(),
                        'resolve' => function ($gameState) {
                            return $gameState->getCompletedAt();
                        }
                    ],
                    'people' => [
                        'type' => Type::listOf(HistoricPersonType::type()),
                        'resolve' => function ($gameState) {
                            return HistoricPerson::findByGameState($gameState->getId());
                        }
                    ],
                ],
            ]);
        }

        return self::$type;
    }
}