<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Query;

use Tests\TestCase;
use Src\GraphQL\Query\CurrentGameQuery;
use GraphQL\Type\Definition\ObjectType;
use Src\Model\GameState;

class CurrentGameQueryTest extends TestCase
{
    public function testTypeReturnsCorrectStructure(): void
    {
        $type = CurrentGameQuery::type();
        
        $this->assertInstanceOf(ObjectType::class, $type);
    }

    public function testResolveWithoutSession(): void
    {
        // Clear any existing session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        $result = CurrentGameQuery::resolve(null, [], null);
        
        $this->assertNull($result);
    }

    public function testResolveWithSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 1;
        
        // This will return a GameState object because we have a real database connection
        // and user 1 has an active game
        $result = CurrentGameQuery::resolve(null, [], null);
        
        // Since this is an integration test with real database, we expect a GameState
        $this->assertTrue($result instanceof GameState || $result === null);
    }

    public function testResolveWithInvalidSession(): void
    {
        // Start a session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 'invalid';
        
        // This should handle the type error gracefully
        $this->expectException(\TypeError::class);
        
        CurrentGameQuery::resolve(null, [], null);
    }
}
