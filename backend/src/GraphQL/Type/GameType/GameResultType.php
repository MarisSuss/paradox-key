<?php

declare(strict_types=1);

namespace Src\GraphQL\Type\GameType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class GameResultType
{
    private static ?ObjectType $type = null;

    public static function type(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'GameResult',
                'fields' => [
                    'gameStateId' => Type::nonNull(Type::int()),
                    'timelineAccuracy' => Type::nonNull(Type::float()),
                    'eventResults' => Type::listOf(Type::string()),
                    'peopleSaved' => Type::nonNull(Type::int()),
                    'totalPeople' => Type::nonNull(Type::int()),
                    'message' => Type::nonNull(Type::string()),
                ],
            ]);
        }

        return self::$type;
    }
}
