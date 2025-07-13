<?php

declare(strict_types=1);

namespace Src\GraphQL\Query;

use Src\Database\Connection;
use Src\GraphQL\Type\UserType;
use Src\Model\User;

class MeQuery
{
    public static function resolve()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user = User::findById($_SESSION['user_id']);
        return $user ? $user->toArray() : null;
    }

    public static function type()
    {
        return UserType::get();
    }
}