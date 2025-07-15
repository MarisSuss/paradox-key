<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation\GameMutation;

use GraphQL\Type\Definition\Type;
use Src\Service\GameService;
use Src\Model\GameState;
use Src\GraphQL\Type\GameType\GameStateType;
use Src\Exception\ClientSafeException;

class StartNewGameMutation
{
    public static function get()
    {
        return [
            'type' => GameStateType::type(),
            'args' => [
                'userId' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($root, $args) {
                // Validate user ID
                if ($args['userId'] <= 0) {
                    throw new ClientSafeException('Invalid user ID.');
                }

                // Check if user already has an incomplete game
                $existingGame = GameState::findIncompleteByUserId($args['userId']);
                if ($existingGame) {
                    throw new ClientSafeException('You already have an ongoing game. Please complete it first.');
                }

                try {
                    $gameState = GameService::createNewGame($args['userId']);
                    return $gameState;
                } catch (\Exception $e) {
                    throw new ClientSafeException('Failed to start new game: ' . $e->getMessage());
                }
            },
        ];
    }
}