<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Query;

use Tests\TestCase;
use Src\GraphQL\Query\MeQuery;
use GraphQL\Type\Definition\ObjectType;

class MeQueryTest extends TestCase
{
    public function testTypeReturnsCorrectStructure(): void
    {
        $type = MeQuery::type();
        
        $this->assertInstanceOf(ObjectType::class, $type);
    }

    public function testResolveWithoutSession(): void
    {
        // Clear any existing session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // This should handle the type error gracefully when session is not set
        $this->expectException(\TypeError::class);
        
        MeQuery::resolve(null, [], null);
    }

    public function testResolveWithSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'ramona';
        
        $result = MeQuery::resolve(null, [], null);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('ramona', $result['username']);
    }

    public function testResolveWithPartialSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 1;
        unset($_SESSION['username']);
        
        $result = MeQuery::resolve(null, [], null);
        
        // This should still return a result since it looks up from database
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('username', $result);
    }
}
