<?php

declare(strict_types=1);

namespace Src\GraphQL\Mutation;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class LogoutMutation
{
    public static function get(): array
    {
        return [
            'type' => new ObjectType([
                'name' => 'LogoutResult',
                'fields' => [
                    'success' => Type::nonNull(Type::boolean()),
                    'message' => Type::nonNull(Type::string()),
                ],
            ]),
            'resolve' => function () {
                // Destroy the session
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_destroy();
                }
                
                // Clear session variables
                $_SESSION = [];
                
                // Clear session cookie
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                return [
                    'success' => true,
                    'message' => 'Logout successful',
                ];
            },
        ];
    }
}
