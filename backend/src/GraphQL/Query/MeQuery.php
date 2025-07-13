<?php

declare(strict_types=1);

namespace Src\GraphQL\Query;

use Src\Database\Connection;
use Src\GraphQL\Type\UserType;

class MeQuery
{
    public static function resolve()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public static function type()
    {
        return UserType::get();
    }
}