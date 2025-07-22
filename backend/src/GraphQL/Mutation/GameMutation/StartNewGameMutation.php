<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation\GameMutation;

use GraphQL\Type\Definition\Type;
use Src\Service\NewGameService;
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
                'campaignId' => [
                    'type' => Type::int(),
                    'defaultValue' => 1,
                    'description' => 'Campaign ID (defaults to 1 for World War II)'
                ],
            ],
            'resolve' => function ($root, $args) {
                // Validate user ID
                if ($args['userId'] <= 0) {
                    throw new ClientSafeException('Invalid user ID.');
                }

                // Validate campaign ID
                $campaignId = $args['campaignId'] ?? 1;
                if ($campaignId <= 0) {
                    throw new ClientSafeException('Invalid campaign ID.');
                }

                // Check if user can create a new game
                if (!NewGameService::canCreateNewGame($args['userId'])) {
                    throw new ClientSafeException('You cannot create a new game at this time.');
                }

                // Check if user already has an incomplete game
                $existingGame = GameState::findIncompleteByUserId($args['userId']);
                if ($existingGame) {
                    throw new ClientSafeException('You already have an ongoing game. Please complete it first.');
                }

                try {
                    $gameState = NewGameService::createNewGame($args['userId'], $campaignId);
                    return $gameState;
                } catch (\Exception $e) {
                    throw new ClientSafeException('Failed to start new game: ' . $e->getMessage());
                }
            },
        ];
    }
}