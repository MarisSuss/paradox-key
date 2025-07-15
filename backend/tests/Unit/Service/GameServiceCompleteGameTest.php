<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use Tests\TestCase;
use Src\Service\GameService;
use Src\Model\HistoricEvent;

class GameServiceCompleteGameTest extends TestCase
{
    public function testCalculateEventAccuracyMethod(): void
    {
        $reflection = new \ReflectionClass(GameService::class);
        
        // Test that the calculateEventAccuracy method exists
        $this->assertTrue($reflection->hasMethod('calculateEventAccuracy'));
        
        $method = $reflection->getMethod('calculateEventAccuracy');
        $this->assertTrue($method->isPrivate());
        $this->assertTrue($method->isStatic());
    }

    public function testCompleteGameMethodExists(): void
    {
        $reflection = new \ReflectionClass(GameService::class);
        
        $this->assertTrue($reflection->hasMethod('completeGame'));
        
        $method = $reflection->getMethod('completeGame');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    public function testCompleteGameWithInvalidId(): void
    {
        $this->expectException(\Exception::class);
        
        // This should fail because game ID -1 doesn't exist
        GameService::completeGame(-1);
    }

    public function testCompleteGameReturnsCorrectStructure(): void
    {
        // This test would need a real database to work fully
        // For now, we test that the method signature is correct
        $reflection = new \ReflectionClass(GameService::class);
        $method = $reflection->getMethod('completeGame');
        
        $this->assertEquals('array', $method->getReturnType()->getName());
    }
}
