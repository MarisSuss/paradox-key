<?php

declare(strict_types=1);

namespace Src\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class RegisterResultType
{
    private static ?ObjectType $type = null;

    public static function type(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'RegisterResult',
                'fields' => [
                    'success' => Type::nonNull(Type::boolean()),
                    'message' => Type::nonNull(Type::string()),
                    'user' => [
                        'type' => UserType::get(),
                        'resolve' => fn($root) => $root['user'] ?? null,
                    ],
                ],
            ]);
        }

        return self::$type;
    }
}