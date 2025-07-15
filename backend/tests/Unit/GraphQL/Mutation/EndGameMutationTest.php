<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\GameMutation\EndGameMutation;
use Src\Exception\ClientSafeException;

class EndGameMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = EndGameMutation::get();

        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('args', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertIsCallable($mutation['resolve']);
    }

    public function testResolveWithInvalidGameStateId(): void
    {
        $mutation = EndGameMutation::get();
        $resolver = $mutation['resolve'];

        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('Invalid game state ID.');

        $resolver(null, ['gameStateId' => 0]);
    }

    public function testResolveWithNegativeGameStateId(): void
    {
        $mutation = EndGameMutation::get();
        $resolver = $mutation['resolve'];

        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('Invalid game state ID.');

        $resolver(null, ['gameStateId' => -1]);
    }

    public function testArgsStructure(): void
    {
        $mutation = EndGameMutation::get();
        $args = $mutation['args'];

        $this->assertArrayHasKey('gameStateId', $args);
        $this->assertNotNull($args['gameStateId']);
    }
}
