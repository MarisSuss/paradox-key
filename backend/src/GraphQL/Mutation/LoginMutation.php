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

                if (!$user || !User::verifyPassword($args['password'], $user['password_hash'])) {
                    throw new ClientSafeException('Invalid credentials.');
                }

                unset($user['password_hash']);

                $_SESSION['user_id'] = $user['id'];

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user
                ];
            },
        ];
    }

    public static function type(): Type
    {
        return LoginResultType::type();
    }
}