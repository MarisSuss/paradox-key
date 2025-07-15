<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\LogoutMutation;
use GraphQL\Type\Definition\ObjectType;

class LogoutMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = LogoutMutation::get();
        
        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertInstanceOf(ObjectType::class, $mutation['type']);
    }

    public function testResolveDestroysSsession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'testuser';
        
        $resolver = LogoutMutation::get()['resolve'];
        $result = $resolver(null, [], null);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        // Session should be destroyed, so these shouldn't exist
        $this->assertArrayNotHasKey('user_id', $_SESSION ?? []);
        $this->assertArrayNotHasKey('username', $_SESSION ?? []);
    }

    public function testResolveWithoutSession(): void
    {
        $resolver = LogoutMutation::get()['resolve'];
        $result = $resolver(null, [], null);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
    }
}
