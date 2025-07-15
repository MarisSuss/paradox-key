<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\GameMutation\SavePersonMutation;
use GraphQL\Type\Definition\BooleanType;

class SavePersonMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = SavePersonMutation::get();
        
        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('args', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertInstanceOf(BooleanType::class, $mutation['type']);
    }

    public function testArgsStructure(): void
    {
        $mutation = SavePersonMutation::get();
        
        $this->assertArrayHasKey('gameStateId', $mutation['args']);
        $this->assertArrayHasKey('personId', $mutation['args']);
    }

    public function testResolveWithInvalidGameStateId(): void
    {
        $resolver = SavePersonMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Invalid game state ID or person ID.');
        
        $resolver(null, [
            'gameStateId' => 0,
            'personId' => 1
        ], null);
    }

    public function testResolveWithNegativePersonId(): void
    {
        $resolver = SavePersonMutation::get()['resolve'];
        
        $this->expectException(\Src\Exception\ClientSafeException::class);
        $this->expectExceptionMessage('Invalid game state ID or person ID.');
        
        $resolver(null, [
            'gameStateId' => 1,
            'personId' => -1
        ], null);
    }
}
