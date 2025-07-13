<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;

class User
{
    public static function findByEmailOrUsername(string $email, string $username): ?array
    {
        $pdo = Connection::getInstance();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email OR username = :username');
        $stmt->execute(['email' => $email, 'username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function create(string $email, string $username, string $password): ?array
    {
        $pdo = Connection::getInstance();

        $stmt = $pdo->prepare('INSERT INTO users (email, username, password_hash) VALUES (:email, :username, :password_hash)');
        $stmt->execute([
            'email' => $email,
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $userId = (int)$pdo->lastInsertId();
        
        // Fetch and return the created user
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Connection::getInstance();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
}