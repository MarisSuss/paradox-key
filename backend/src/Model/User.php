<?php

declare(strict_types=1);

namespace Src\Model;

use Src\Database\Connection;
use PDO;
use PDOException;

class User
{
    private int $id;
    private string $email;
    private string $username;
    private string $passwordHash;
    private string $createdAt;

    /**
     * Constructor for User model
     */
    public function __construct(
        int $id,
        string $email,
        string $username,
        string $passwordHash,
        string $createdAt
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Convert User object to array (for GraphQL responses)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * Create User object from database row
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['id'],
            $data['email'],
            $data['username'],
            $data['password_hash'],
            $data['created_at']
        );
    }

    /**
     * Find user by email or username
     */
    public static function findByEmailOrUsername(string $email, string $username): ?self
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email OR username = :username');
            $stmt->execute(['email' => $email, 'username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ? self::fromArray($user) : null;
        } catch (PDOException $e) {
            error_log("Database error in findByEmailOrUsername: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new user
     */
    public static function create(string $email, string $username, string $password): ?self
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
            $stmt = Connection::getInstance()->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
            
            Connection::commit();
            
            return $user ? self::fromArray($user) : null;
        } catch (PDOException $e) {
            Connection::rollback();
            error_log("Database error in create: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?self
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? self::fromArray($user) : null;
        } catch (PDOException $e) {
            error_log("Database error in findByEmail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find user by ID
     */
    public static function findById(int $id): ?self
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? self::fromArray($user) : null;
        } catch (PDOException $e) {
            error_log("Database error in findById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify password against user's hash
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * Update user's password
     */
    public function updatePassword(string $newPassword): bool
    {
        try {
            $pdo = Connection::getInstance();

            $stmt = $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            $result = $stmt->execute([
                'id' => $this->id,
                'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);

            if ($result) {
                $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Database error in updatePassword: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email is already taken
     */
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

    /**
     * Check if username is already taken
     */
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