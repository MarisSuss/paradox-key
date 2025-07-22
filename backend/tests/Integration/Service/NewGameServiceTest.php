<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\TestCase;
use Src\Service\NewGameService;
use Src\Model\GameState;
use Src\Model\HistoricPerson;

class NewGameServiceTest extends TestCase
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
        
        $gameState = NewGameService::createNewGame($userId);
        
        $this->assertInstanceOf(GameState::class, $gameState);
        $this->assertEquals($userId, $gameState->getUserId());
        $this->assertEquals(1, $gameState->getCampaignId()); // Default campaign
        $this->assertEquals(0.0, $gameState->getTimelineAccuracy());
        $this->assertFalse($gameState->isCompleted());
        $this->assertGreaterThan(0, $gameState->getId());
    }

    public function testCreateNewGameWithCustomCampaign(): void
    {
        $userId = $this->createTestUser();
        $campaignId = 2;
        
        $gameState = NewGameService::createNewGame($userId, $campaignId);
        
        $this->assertInstanceOf(GameState::class, $gameState);
        $this->assertEquals($userId, $gameState->getUserId());
        $this->assertEquals($campaignId, $gameState->getCampaignId());
    }

    public function testCreateNewGameCreatesHistoricPeople(): void
    {
        $userId = $this->createTestUser();
        
        $gameState = NewGameService::createNewGame($userId);
        $people = HistoricPerson::findByGameState($gameState->getId());
        
        $this->assertCount(1, $people);
        
        $winston = $people[0];
        $this->assertEquals('Winston Churchill', $winston->getName());
        $this->assertEquals('1938-01-01', $winston->getDeathDate());
        $this->assertEquals('1965-01-24', $winston->getAlternateDeathDate());
        $this->assertEquals($gameState->getId(), $winston->getGameStateId());
    }

    public function testCanCreateNewGameReturnsTrue(): void
    {
        $userId = $this->createTestUser();
        
        $canCreate = NewGameService::canCreateNewGame($userId);
        
        $this->assertTrue($canCreate);
    }

    public function testGetAvailableCampaignsReturnsArray(): void
    {
        $campaigns = NewGameService::getAvailableCampaigns();
        
        $this->assertIsArray($campaigns);
        $this->assertNotEmpty($campaigns);
        
        // Check structure of first campaign
        $firstCampaign = $campaigns[0];
        $this->assertArrayHasKey('id', $firstCampaign);
        $this->assertArrayHasKey('name', $firstCampaign);
        $this->assertArrayHasKey('description', $firstCampaign);
        $this->assertArrayHasKey('difficulty', $firstCampaign);
        
        $this->assertEquals(1, $firstCampaign['id']);
        $this->assertEquals('World War II', $firstCampaign['name']);
    }

    public function testCreateNewGameWithInvalidUserId(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create new game');
        
        NewGameService::createNewGame(0);
    }

    public function testCreateNewGameGeneratesUniqueGameStates(): void
    {
        $userId = $this->createTestUser();
        
        $gameState1 = NewGameService::createNewGame($userId);
        $gameState2 = NewGameService::createNewGame($userId);
        
        $this->assertNotEquals($gameState1->getId(), $gameState2->getId());
        $this->assertEquals($gameState1->getUserId(), $gameState2->getUserId());
    }
}
