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
                
                // Validate input
                if (!User::isValidEmail($args['email'])) {
                    throw new ClientSafeException("Invalid email format.");
                }
                
                if (!User::isValidUsername($args['username'])) {
                    throw new ClientSafeException("Username must be 3-20 characters long and contain only letters, numbers, underscores, and hyphens.");
                }
                
                if (!User::isValidPassword($args['password'])) {
                    throw new ClientSafeException("Password must be at least 8 characters long and contain at least one letter and one number.");
                }

                // Check if email or username already exists
                if (User::isEmailTaken($args['email'])) {
                    throw new ClientSafeException("Email is already in use.");
                }
                
                if (User::isUsernameTaken($args['username'])) {
                    throw new ClientSafeException("Username is already in use.");
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