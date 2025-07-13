<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class User
{
    public static function findByEmailOrUsername(string $email, string $username): ?array
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email OR username = :username');
            $stmt->execute(['email' => $email, 'username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Database error in findByEmailOrUsername: " . $e->getMessage());
            return null;
        }
    }

    public static function create(string $email, string $username, string $password): ?array
    {
        try {
            Connection::beginTransaction();

            $stmt = Connection::getInstance()->prepare('INSERT INTO users (email, username, password_hash) VALUES (:email, :username, :password_hash)');
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            $userId = (int)Connection::getInstance()->lastInsertId();
            
            // Fetch and return the created user
            $stmt = Connection::getInstance()->prepare('SELECT id, email, username, created_at FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
            
            Connection::commit();
            
            return $user ?: null;
        } catch (PDOException $e) {
            Connection::rollback();
            error_log("Database error in create: " . $e->getMessage());
            return null;
        }
    }

    public static function findByEmail(string $email): ?array
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Database error in findByEmail: " . $e->getMessage());
            return null;
        }
    }

    public static function findById(int $id): ?array
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT id, email, username, created_at FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Database error in findById: " . $e->getMessage());
            return null;
        }
    }

    public static function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    public static function updatePassword(int $userId, string $newPassword): bool
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            return $stmt->execute([
                'id' => $userId,
                'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            error_log("Database error in updatePassword: " . $e->getMessage());
            return false;
        }
    }

    public static function isEmailTaken(string $email): bool
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in isEmailTaken: " . $e->getMessage());
            return true; // Assume taken on error for safety
        }
    }

    public static function isUsernameTaken(string $username): bool
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in isUsernameTaken: " . $e->getMessage());
            return true; // Assume taken on error for safety
        }
    }

    /**
     * Validate email format
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate username format
     */
    public static function isValidUsername(string $username): bool
    {
        return preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username) === 1;
    }

    /**
     * Validate password strength
     */
    public static function isValidPassword(string $password): bool
    {
        // At least 8 characters, at least one letter and one number
        return strlen($password) >= 8 && 
               preg_match('/[A-Za-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
}