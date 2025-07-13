<?php

declare(strict_types=1);

namespace Src\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType
{
    private static ?ObjectType $instance = null;

    public static function get(): ObjectType
    {
        if (self::$instance === null) {
            self::$instance = new ObjectType([
                'name' => 'User',
                'fields' => [
                    'id' => Type::nonNull(Type::id()),
                    'email' => Type::nonNull(Type::string()),
                    'username' => Type::nonNull(Type::string()),
                ],
            ]);
        }

        return self::$instance;
    }
}