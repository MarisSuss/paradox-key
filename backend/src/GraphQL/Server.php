<?php

declare(strict_types=1);

namespace Src\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Src\GraphQL\Query\MeQuery;
use Src\GraphQL\Query\CurrentGameQuery;
use Src\GraphQL\Mutation\LoginMutation;
use Src\GraphQL\Mutation\RegisterMutation;
use Src\GraphQL\Mutation\LogoutMutation;
use GraphQL\Error\DebugFlag;
use Src\GraphQL\Mutation\GameMutation\StartNewGameMutation;
use Src\GraphQL\Mutation\GameMutation\SavePersonMutation;
use Src\GraphQL\Mutation\GameMutation\EndGameMutation;

class Server
{
    public function handle(): void
    {
        header('Content-Type: application/json');
        
        try {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON input');
            }

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'me' => [
                        'type' => MeQuery::type(),
                        'resolve' => [MeQuery::class, 'resolve'],
                    ],
                    'currentGame' => [
                        'type' => CurrentGameQuery::type(),
                        'resolve' => [CurrentGameQuery::class, 'resolve'],
                    ],
                ],
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'login' => LoginMutation::get(),
                    'register' => RegisterMutation::get(),
                    'logout' => LogoutMutation::get(),
                    'startNewGame' => StartNewGameMutation::get(),
                    'savePerson' => SavePersonMutation::get(),
                    'endGame' => EndGameMutation::get(),
                ],
            ]);

            $schema = new Schema([
                'query' => $queryType,
                'mutation' => $mutationType,
            ]);

            $result = GraphQL::executeQuery(
                $schema,
                $input['query'] ?? '',
                null,
                null,
                $input['variables'] ?? null
            );

            $debugFlag = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;

            echo json_encode($result->toArray($debugFlag), JSON_THROW_ON_ERROR);
            
        } catch (\Exception $e) {
            http_response_code(500);
            error_log("GraphQL Server Error: " . $e->getMessage());
            
            $response = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]
                ]
            ];
            
            echo json_encode($response);
        }
    }
}