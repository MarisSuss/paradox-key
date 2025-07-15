<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\LoginMutation;
use GraphQL\Type\Definition\ObjectType;

class LoginMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = LoginMutation::get();
        
        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('args', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertInstanceOf(ObjectType::class, $mutation['type']);
    }

    public function testArgsStructure(): void
    {
        $mutation = LoginMutation::get();
        
        $this->assertArrayHasKey('email', $mutation['args']);
        $this->assertArrayHasKey('password', $mutation['args']);
    }

    public function testResolveWithInvalidCredentials(): void
    {
        $resolver = LoginMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Invalid credentials.');
        
        $resolver(null, ['email' => 'nonexistent@example.com', 'password' => 'wrongpassword'], null);
    }

    public function testResolveWithEmptyCredentials(): void
    {
        $resolver = LoginMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Invalid credentials.');
        
        $resolver(null, ['email' => '', 'password' => ''], null);
    }
}
