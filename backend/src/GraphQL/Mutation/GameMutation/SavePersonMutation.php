<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation\GameMutation;

use GraphQL\Type\Definition\Type;
use Src\Service\GameService;
use Src\GraphQL\Type\GameType\GameStateType;
use Src\Exception\ClientSafeException;

class SavePersonMutation
{
    public static function get()
    {
        return [
            'type' => Type::boolean(),
            'args' => [
                'gameStateId' => Type::nonNull(Type::int()),
                'personId' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($root, $args) {
                // Validate arguments
                if ($args['gameStateId'] <= 0 || $args['personId'] <= 0) {
                    throw new ClientSafeException('Invalid game state ID or person ID.');
                }

                try {
                    $success = GameService::savePersonLife($args['gameStateId'], $args['personId']);
                    
                    if (!$success) {
                        throw new ClientSafeException('Failed to save person. Game may be completed or person not found.');
                    }
                    
                    return true;
                } catch (\Exception $e) {
                    throw new ClientSafeException('Failed to save person: ' . $e->getMessage());
                }
            },
        ];
    }
}
