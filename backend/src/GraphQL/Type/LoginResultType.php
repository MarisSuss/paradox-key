<?php

declare(strict_types=1);

namespace Src\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class LoginResultType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'LoginResult',
                'fields' => [
                    'success' => Type::nonNull(Type::boolean()),
                    'message' => Type::nonNull(Type::string()),
                ],
            ]);
        }

        return self::$type;
    }
}