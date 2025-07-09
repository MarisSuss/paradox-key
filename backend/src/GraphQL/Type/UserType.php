<?php

declare(strict_types=1);

namespace Src\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'User',
                'fields' => [
                    'id' => Type::nonNull(Type::int()),
                    'email' => Type::nonNull(Type::string()),
                    'created_at' => Type::string(),
                ],
            ]);
        }

        return self::$type;
    }
}