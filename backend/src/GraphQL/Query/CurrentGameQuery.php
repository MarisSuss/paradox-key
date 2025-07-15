<?php

declare(strict_types=1);

namespace Src\GraphQL\Query;

use Src\GraphQL\Type\GameType\GameStateType;
use Src\Model\GameState;

class CurrentGameQuery
{
    public static function type()
    {
        return GameStateType::type();
    }

    public static function resolve($root, $args, $context)
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userId = $_SESSION['user_id'];
        return GameState::findIncompleteByUserId($userId);
    }
}
