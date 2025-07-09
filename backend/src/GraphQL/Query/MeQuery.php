<?php

declare(strict_types=1);

namespace Src\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Src\Database\Connection;
use Src\GraphQL\Type\UserType;

class MeQuery
{
    public static function get(): array
    {
        return [
            'me' => [
                'type' => UserType::get(),
                'resolve' => function () {
                    if (!isset($_SESSION['user_id'])) {
                        return null;
                    }

                    $pdo = Connection::getInstance();
                    $stmt = $pdo->prepare("SELECT id, email, created_at FROM users WHERE id = :id");
                    $stmt->execute(['id' => $_SESSION['user_id']]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                    return $user ?: null;
                }
            ]
        ];
    }
}