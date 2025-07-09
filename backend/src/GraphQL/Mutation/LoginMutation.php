<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use Src\Model\User;
use Src\GraphQL\Type\LoginResultType;

class LoginMutation
{
    public static function get(): array
    {
        return [
            'login' => [
                'type' => LoginResultType::get(),
                'args' => [
                    'email' => Type::nonNull(Type::string()),
                    'password' => Type::nonNull(Type::string()),
                ],
                'resolve' => function ($root, $args) {
                    $user = User::findByEmail($args['email']);

                    if (!$user) {
                        return ['success' => false, 'message' => 'User not found.'];
                    }

                    if (!password_verify($args['password'], $user['password_hash'])) {
                        return ['success' => false, 'message' => 'Failed login.'];
                    }
                    
                    $_SESSION['user_id'] = $user['id'];
                    return ['success' => true, 'message' => 'Login successful.'];
                }
            ]
        ];
    }
}