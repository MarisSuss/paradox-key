<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use Src\Model\User;
use Src\GraphQL\Type\UserType;
use Src\GraphQL\Type\LoginResultType;
use Src\Exception\ClientSafeException;

class LoginMutation
{
    public static function get()
    {
        return [
            'type' => LoginResultType::type(),
            'args' => [
                'email' => Type::nonNull(Type::string()),
                'password' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, $args) {
                $user = User::findByEmail($args['email']);

                if (!$user || !$user->verifyPassword($args['password'])) {
                    throw new ClientSafeException('Invalid credentials.');
                }

                $_SESSION['user_id'] = $user->getId();

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user->toArray()
                ];
            },
        ];
    }

    public static function type(): Type
    {
        return LoginResultType::type();
    }
}