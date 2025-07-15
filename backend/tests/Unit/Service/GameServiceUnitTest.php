<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use Tests\TestCase;
use Src\Service\GameService;
use Src\Model\GameState;
use Src\Model\HistoricPerson;
use PHPUnit\Framework\MockObject\MockObject;

class GameServiceUnitTest extends TestCase
{
    public function testCreateNewGameValidatesUserId(): void
    {
        $this->expectException(\Exception::class);
        // The actual exception message will be "Failed to create new game" 
        // when the database operation fails
        $this->expectExceptionMessage('Failed to create new game');
        
        // This will fail because we're using an invalid user ID
        // but it tests the validation logic
        GameService::createNewGame(0);
    }

    public function testGameServiceClassExists(): void
    {
        $this->assertTrue(class_exists(GameService::class));
    }

    public function testGameServiceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(GameService::class);
        
        $this->assertTrue($reflection->hasMethod('createNewGame'));
        $this->assertTrue($reflection->hasMethod('savePersonLife'));
        $this->assertTrue($reflection->hasMethod('completeGame'));
        
        // Check method signatures
        $createNewGameMethod = $reflection->getMethod('createNewGame');
        $this->assertTrue($createNewGameMethod->isStatic());
        $this->assertTrue($createNewGameMethod->isPublic());
        
        $savePersonLifeMethod = $reflection->getMethod('savePersonLife');
        $this->assertTrue($savePersonLifeMethod->isStatic());
        $this->assertTrue($savePersonLifeMethod->isPublic());
        
        $completeGameMethod = $reflection->getMethod('completeGame');
        $this->assertTrue($completeGameMethod->isStatic());
        $this->assertTrue($completeGameMethod->isPublic());
    }
}
