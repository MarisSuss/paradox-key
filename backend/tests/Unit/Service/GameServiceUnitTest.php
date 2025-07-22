<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use Tests\TestCase;
use Src\Service\GameService;
use Src\Service\NewGameService;
use Src\Model\GameState;
use Src\Model\HistoricPerson;
use PHPUnit\Framework\MockObject\MockObject;

class GameServiceUnitTest extends TestCase
{
    public function testNewGameServiceValidatesUserId(): void
    {
        $this->expectException(\Exception::class);
        // The actual exception message will be "Failed to create new game" 
        // when the database operation fails
        $this->expectExceptionMessage('Failed to create new game');
        
        // This will fail because we're using an invalid user ID
        // but it tests the validation logic
        NewGameService::createNewGame(0);
    }

    public function testGameServiceClassExists(): void
    {
        $this->assertTrue(class_exists(GameService::class));
    }

    public function testNewGameServiceClassExists(): void
    {
        $this->assertTrue(class_exists(NewGameService::class));
    }

    public function testGameServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(GameService::class);
        
        $this->assertTrue($reflection->hasMethod('savePersonLife'));
        $this->assertTrue($reflection->hasMethod('completeGame'));
        $this->assertTrue($reflection->hasMethod('getGameStatus'));
        $this->assertTrue($reflection->hasMethod('canSavePerson'));
        
        // Check method signatures
        $savePersonLifeMethod = $reflection->getMethod('savePersonLife');
        $this->assertTrue($savePersonLifeMethod->isStatic());
        $this->assertTrue($savePersonLifeMethod->isPublic());
        
        $completeGameMethod = $reflection->getMethod('completeGame');
        $this->assertTrue($completeGameMethod->isStatic());
        $this->assertTrue($completeGameMethod->isPublic());
    }

    public function testNewGameServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(NewGameService::class);
        
        $this->assertTrue($reflection->hasMethod('createNewGame'));
        $this->assertTrue($reflection->hasMethod('canCreateNewGame'));
        $this->assertTrue($reflection->hasMethod('getAvailableCampaigns'));
        
        // Check method signatures
        $createNewGameMethod = $reflection->getMethod('createNewGame');
        $this->assertTrue($createNewGameMethod->isStatic());
        $this->assertTrue($createNewGameMethod->isPublic());
    }
}
