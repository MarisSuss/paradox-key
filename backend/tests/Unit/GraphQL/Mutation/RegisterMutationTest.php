<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\RegisterMutation;
use GraphQL\Type\Definition\ObjectType;

class RegisterMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = RegisterMutation::get();
        
        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('args', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertInstanceOf(ObjectType::class, $mutation['type']);
    }

    public function testArgsStructure(): void
    {
        $mutation = RegisterMutation::get();
        
        $this->assertArrayHasKey('email', $mutation['args']);
        $this->assertArrayHasKey('username', $mutation['args']);
        $this->assertArrayHasKey('password', $mutation['args']);
    }

    public function testResolveWithInvalidEmail(): void
    {
        $resolver = RegisterMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Invalid email format.');
        
        $resolver(null, [
            'email' => 'invalid-email',
            'username' => 'validuser',
            'password' => 'validpass123'
        ], null);
    }

    public function testResolveWithShortUsername(): void
    {
        $resolver = RegisterMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Username must be 3-20 characters long');
        
        $resolver(null, [
            'email' => 'test@example.com',
            'username' => 'ab',
            'password' => 'validpass123'
        ], null);
    }

    public function testResolveWithWeakPassword(): void
    {
        $resolver = RegisterMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long');
        
        $resolver(null, [
            'email' => 'test@example.com',
            'username' => 'validuser',
            'password' => 'weak'
        ], null);
    }
}
