<?php

declare(strict_types=1);

namespace Src\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class LoginResultType
{
    public static function type(): ObjectType
    {
        return new ObjectType([
            'name' => 'LoginResult',
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
}