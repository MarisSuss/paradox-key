<?php

declare(strict_types=1);

namespace Src\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Src\GraphQL\Mutation\LoginMutation;
use Src\GraphQL\Mutation\RegisterMutation;
use Src\GraphQL\Query\MeQuery;

class Server
{
    public static function handle(): void
    {   
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Methods: POST, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => array_merge(
                MeQuery::get(),
                [
                    'hello' => [
                        'type' => Type::string(),
                        'resolve' => fn() => 'Hello from Paradox Key!',
                    ]
                ]
            ),
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => array_merge(
                LoginMutation::get(),
                RegisterMutation::get()
            ),
        ]);

        $schema = new Schema([
            'query' => $queryType,
            'mutation' => $mutationType
        ]);

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput ?: '{}', true);
        $query = $input['query'] ?? null;
        $variables = $input['variables'] ?? null;

        if ($query === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing GraphQL query.']);
            return;
        }

        try {
            $result = GraphQL::executeQuery($schema, $query, null, null, $variables);
            $output = $result->toArray();
        } catch (\Throwable $e) {
            http_response_code(500);
            $output = ['error' => $e->getMessage()];
        }

        header('Content-Type: application/json');
        echo json_encode($output);
    }
}