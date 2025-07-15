<?php

declare(strict_types=1);

namespace Tests\Unit\GraphQL\Mutation;

use Tests\TestCase;
use Src\GraphQL\Mutation\GameMutation\StartNewGameMutation;
use Src\Exception\ClientSafeException;

class StartNewGameMutationTest extends TestCase
{
    public function testGetReturnsCorrectStructure(): void
    {
        $mutation = StartNewGameMutation::get();

        $this->assertIsArray($mutation);
        $this->assertArrayHasKey('type', $mutation);
        $this->assertArrayHasKey('args', $mutation);
        $this->assertArrayHasKey('resolve', $mutation);
        $this->assertIsCallable($mutation['resolve']);
    }

    public function testResolveWithInvalidUserId(): void
    {
        $mutation = StartNewGameMutation::get();
        $resolver = $mutation['resolve'];

        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('Invalid user ID.');

        $resolver(null, ['userId' => 0]);
    }

    public function testResolveWithNegativeUserId(): void
    {
        $mutation = StartNewGameMutation::get();
        $resolver = $mutation['resolve'];

        $this->expectException(ClientSafeException::class);
        $this->expectExceptionMessage('Invalid user ID.');

        $resolver(null, ['userId' => -1]);
    }

    public function testArgsStructure(): void
    {
        $mutation = StartNewGameMutation::get();
        $args = $mutation['args'];

        $this->assertArrayHasKey('userId', $args);
        $this->assertNotNull($args['userId']);
    }
}
