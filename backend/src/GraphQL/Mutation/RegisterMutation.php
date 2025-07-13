<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use Src\GraphQL\Type\RegisterResultType;
use Src\Model\User;
use Src\Exception\ClientSafeException;

class RegisterMutation
{
    public static function get(): array
    {
        return [
            'type' => RegisterResultType::type(),
            'args' => [
                'email' => Type::nonNull(Type::string()),
                'password' => Type::nonNull(Type::string()),
                'username' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, $args) {

                $existingUser = User::findByEmailOrUsername($args['email'], $args['username']);

                if ($existingUser) {
                    throw new ClientSafeException("Email or username is already in use."); // TODO: Change it to return what's existing
                }

                $user = User::create(
                    $args['email'],
                    $args['username'],
                    $args['password']
                );

                if (!$user) {
                    throw new ClientSafeException("Failed to create user.");
                }

                $_SESSION['user_id'] = $user['id'];

                return [
                    'success' => true,
                    'message' => 'Registration successful.',
                    'user' => $user,
                ];
            }
        ];
    }

    public static function type(): Type
    {
        return RegisterResultType::type();
    }
}