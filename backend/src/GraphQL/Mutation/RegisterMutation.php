<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use Src\Database\Connection;
use Src\GraphQL\Type\LoginResultType;

class RegisterMutation
{
    public static function get(): array
    {
        return [
            'register' => [
                'type' => LoginResultType::get(),
                'args' => [
                    'email' => Type::nonNull(Type::string()),
                    'password' => Type::nonNull(Type::string()),
                ],
                'resolve' => function ($root, $args) {
                    $pdo = Connection::getInstance();

                    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                    $check->execute(['email' => $args['email']]);
                    if ($check->fetch()) {
                        return ['success' => false, 'message' => 'Email already registered.'];
                    }

                    $passwordHash = password_hash($args['password'], PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
                    $stmt->execute([
                        'email' => $args['email'],
                        'password_hash' => $passwordHash,
                    ]);

                    return ['success' => true, 'message' => 'User registered successfully.'];
                }
            ]
        ];
    }
}