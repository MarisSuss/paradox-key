<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Type;

use Tests\TestCase;
use Src\GraphQL\Type\GameType\GameStateType;
use Src\GraphQL\Type\GameType\HistoricPersonType;
use Src\GraphQL\Type\GameType\GameResultType;
use GraphQL\Type\Definition\ObjectType;

class GraphQLTypesTest extends TestCase
{
    public function testGameStateTypeReturnsObjectType(): void
    {
        $type = GameStateType::type();
        
        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertEquals('GameState', $type->name);
    }

    public function testGameStateTypeHasRequiredFields(): void
    {
        $type = GameStateType::type();
        $fields = $type->getFields();
        
        $expectedFields = ['id', 'userId', 'timelineAccuracy', 'isCompleted', 'createdAt', 'completedAt', 'people'];
        
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $fields);
        }
    }

    public function testHistoricPersonTypeReturnsObjectType(): void
    {
        $type = HistoricPersonType::type();
        
        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertEquals('HistoricPerson', $type->name);
    }

    public function testHistoricPersonTypeHasRequiredFields(): void
    {
        $type = HistoricPersonType::type();
        $fields = $type->getFields();
        
        $expectedFields = ['id', 'gameStateId', 'name', 'deathDate'];
        
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $fields);
        }
    }

    public function testGameResultTypeReturnsObjectType(): void
    {
        $type = GameResultType::type();
        
        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertEquals('GameResult', $type->name);
    }

    public function testGameResultTypeHasRequiredFields(): void
    {
        $type = GameResultType::type();
        $fields = $type->getFields();
        
        $expectedFields = ['gameStateId', 'timelineAccuracy', 'eventResults', 'peopleSaved', 'totalPeople', 'message'];
        
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $fields);
        }
    }
}
