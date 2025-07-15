<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Tests\TestCase;
use Src\Model\User;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $createdAt = '2025-01-01 12:00:00';
        $user = new User(1, 'test@example.com', 'testuser', 'hashedpassword', $createdAt);
        
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('hashedpassword', $user->getPasswordHash());
        $this->assertEquals($createdAt, $user->getCreatedAt());
    }

    public function testUserDefaultValues(): void
    {
        $createdAt = '2025-01-01 12:00:00';
        $user = new User(0, 'new@example.com', 'newuser', 'password', $createdAt);
        
        $this->assertEquals(0, $user->getId());
        $this->assertEquals('newuser', $user->getUsername());
        $this->assertEquals('new@example.com', $user->getEmail());
        $this->assertEquals('password', $user->getPasswordHash());
        $this->assertEquals($createdAt, $user->getCreatedAt());
    }

    public function testPasswordVerification(): void
    {
        $plainPassword = 'mypassword123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        $createdAt = '2025-01-01 12:00:00';
        
        $user = new User(1, 'test@example.com', 'testuser', $hashedPassword, $createdAt);
        
        $this->assertTrue($user->verifyPassword($plainPassword));
        $this->assertFalse($user->verifyPassword('wrongpassword'));
    }

    public function testUserWithEmptyValues(): void
    {
        $createdAt = '2025-01-01 12:00:00';
        $user = new User(0, '', '', '', $createdAt);
        
        $this->assertEquals(0, $user->getId());
        $this->assertEquals('', $user->getUsername());
        $this->assertEquals('', $user->getEmail());
        $this->assertEquals('', $user->getPasswordHash());
        $this->assertEquals($createdAt, $user->getCreatedAt());
    }

    public function testUserWithLongValues(): void
    {
        $longUsername = str_repeat('a', 100);
        $longEmail = str_repeat('b', 100) . '@example.com';
        $longPassword = str_repeat('c', 100);
        $createdAt = '2025-01-01 12:00:00';
        
        $user = new User(999, $longEmail, $longUsername, $longPassword, $createdAt);
        
        $this->assertEquals(999, $user->getId());
        $this->assertEquals($longUsername, $user->getUsername());
        $this->assertEquals($longEmail, $user->getEmail());
        $this->assertEquals($longPassword, $user->getPasswordHash());
        $this->assertEquals($createdAt, $user->getCreatedAt());
    }
}
