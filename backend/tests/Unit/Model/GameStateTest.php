<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Tests\TestCase;
use Src\Model\GameState;

class GameStateTest extends TestCase
{
    public function testGameStateCreation(): void
    {
        $gameState = new GameState(
            id: 1,
            userId: 123,
            campaignId: 1,
            timelineAccuracy: 75.5,
            isCompleted: false,
            createdAt: '2025-01-01 12:00:00'
        );

        $this->assertEquals(1, $gameState->getId());
        $this->assertEquals(123, $gameState->getUserId());
        $this->assertEquals(1, $gameState->getCampaignId());
        $this->assertEquals(75.5, $gameState->getTimelineAccuracy());
        $this->assertFalse($gameState->isCompleted());
        $this->assertEquals('2025-01-01 12:00:00', $gameState->getCreatedAt());
        $this->assertNull($gameState->getCompletedAt());
    }

    public function testGameStateDefaultValues(): void
    {
        $gameState = new GameState(id: 0, userId: 123, campaignId: 1);

        $this->assertEquals(0, $gameState->getId());
        $this->assertEquals(123, $gameState->getUserId());
        $this->assertEquals(1, $gameState->getCampaignId());
        $this->assertEquals(0.0, $gameState->getTimelineAccuracy());
        $this->assertFalse($gameState->isCompleted());
        $this->assertNotEmpty($gameState->getCreatedAt());
        $this->assertNull($gameState->getCompletedAt());
    }

    public function testTimelineAccuracyReturnsFloat(): void
    {
        $gameState = new GameState(id: 1, userId: 123, campaignId: 1, timelineAccuracy: 85.0);
        
        $this->assertIsFloat($gameState->getTimelineAccuracy());
        $this->assertEquals(85.0, $gameState->getTimelineAccuracy());
    }
}
