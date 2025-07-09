<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}