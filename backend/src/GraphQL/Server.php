<?php

declare(strict_types=1);

namespace Src\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Src\GraphQL\Query\MeQuery;
use Src\GraphQL\Mutation\LoginMutation;
use Src\GraphQL\Mutation\RegisterMutation;
use GraphQL\Error\DebugFlag;

class Server
{
    public function handle(): void
    {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'me' => [
                    'type' => MeQuery::type(),
                    'resolve' => [MeQuery::class, 'resolve'],
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'login' => LoginMutation::get(),
                'register' => RegisterMutation::get(),
            ],
        ]);

        $schema = new Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
        ]);

        try {
            $result = GraphQL::executeQuery(
                $schema,
                $input['query'] ?? '',
                null,
                null,
                $input['variables'] ?? null
            );
       
            echo json_encode(
                $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE),
                JSON_THROW_ON_ERROR
            );
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }
}