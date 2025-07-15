<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\TestCase;
use Src\Service\GameService;
use Src\Model\GameState;
use Src\Model\HistoricPerson;

class GameServiceTest extends TestCase
{
    private function createTestUser(): int
    {
        // For testing purposes, we'll assume user ID 1 exists
        // In a real test, you'd create a test user in the database
        return 1;
    }

    public function testCreateNewGameCreatesGameState(): void
    {
        $userId = $this->createTestUser();
        
        $gameState = GameService::createNewGame($userId);
        
        $this->assertInstanceOf(GameState::class, $gameState);
        $this->assertEquals($userId, $gameState->getUserId());
        $this->assertEquals(0.0, $gameState->getTimelineAccuracy());
        $this->assertFalse($gameState->isCompleted());
        $this->assertGreaterThan(0, $gameState->getId());
    }

    public function testCreateNewGameCreatesHistoricPeople(): void
    {
        $userId = $this->createTestUser();
        
        $gameState = GameService::createNewGame($userId);
        $people = HistoricPerson::findByGameState($gameState->getId());
        
        $this->assertCount(1, $people);
        $this->assertEquals('Winston Churchill', $people[0]->getName());
        $this->assertEquals('1938-01-01', $people[0]->getDeathDate());
    }

    public function testCreateNewGameWithInvalidUserId(): void
    {
        $this->expectException(\Exception::class);
        
        GameService::createNewGame(0);
    }

    public function testSavePersonUpdatesDeathDate(): void
    {
        $userId = $this->createTestUser();
        $gameState = GameService::createNewGame($userId);
        $people = HistoricPerson::findByGameState($gameState->getId());
        
        $winston = $people[0];
        $originalDeathDate = $winston->getDeathDate();
        
        $result = GameService::savePersonLife($gameState->getId(), $winston->getId());
        
        $this->assertTrue($result);
        
        // Refresh person from database
        $updatedWinston = HistoricPerson::findById($winston->getId());
        $this->assertNotEquals($originalDeathDate, $updatedWinston->getDeathDate());
        $this->assertEquals('1965-01-24', $updatedWinston->getDeathDate());
    }
}
