<?php

declare(strict_types=1);

namespace Src\GraphQL\Type\GameType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class HistoricPersonType
{
    private static ?ObjectType $type = null;

    public static function type(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'HistoricPerson',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::int()),
                        'resolve' => function ($person) {
                            return $person->getId();
                        }
                    ],
                    'gameStateId' => [
                        'type' => Type::nonNull(Type::int()),
                        'resolve' => function ($person) {
                            return $person->getGameStateId();
                        }
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => function ($person) {
                            return $person->getName();
                        }
                    ],
                    'deathDate' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => function ($person) {
                            return $person->getDeathDate();
                        }
                    ],
                ],
            ]);
        }

        return self::$type;
    }
}
