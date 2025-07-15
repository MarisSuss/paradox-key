<?php

declare(strict_types=1);

namespace Tests\Integration\GraphQL;

use Tests\TestCase;
use Src\GraphQL\Server;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use Src\GraphQL\Query\MeQuery;
use Src\GraphQL\Query\CurrentGameQuery;
use Src\GraphQL\Mutation\LoginMutation;
use Src\GraphQL\Mutation\RegisterMutation;
use Src\GraphQL\Mutation\LogoutMutation;
use Src\GraphQL\Mutation\GameMutation\StartNewGameMutation;
use Src\GraphQL\Mutation\GameMutation\SavePersonMutation;
use Src\GraphQL\Mutation\GameMutation\EndGameMutation;

class GraphQLServerTest extends TestCase
{
    private Schema $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = $this->buildTestSchema();
    }

    private function buildTestSchema(): Schema
    {
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

        return new Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
        ]);
    }

    public function testSchemaCreation(): void
    {
        $this->assertInstanceOf(Schema::class, $this->schema);
    }

    public function testQueryTypeExists(): void
    {
        $queryType = $this->schema->getQueryType();
        $this->assertNotNull($queryType);
        $this->assertEquals('Query', $queryType->name);
    }

    public function testMutationTypeExists(): void
    {
        $mutationType = $this->schema->getMutationType();
        $this->assertNotNull($mutationType);
        $this->assertEquals('Mutation', $mutationType->name);
    }

    public function testQueryFields(): void
    {
        $queryType = $this->schema->getQueryType();
        $fields = $queryType->getFields();
        
        $this->assertArrayHasKey('me', $fields);
        $this->assertArrayHasKey('currentGame', $fields);
    }

    public function testMutationFields(): void
    {
        $mutationType = $this->schema->getMutationType();
        $fields = $mutationType->getFields();
        
        $this->assertArrayHasKey('login', $fields);
        $this->assertArrayHasKey('register', $fields);
        $this->assertArrayHasKey('logout', $fields);
        $this->assertArrayHasKey('startNewGame', $fields);
        $this->assertArrayHasKey('savePerson', $fields);
        $this->assertArrayHasKey('endGame', $fields);
    }

    public function testInvalidQuery(): void
    {
        $query = '{ invalidField }';
        
        $result = GraphQL::executeQuery($this->schema, $query);
        $this->assertNotEmpty($result->errors);
    }

    public function testValidQueryStructure(): void
    {
        $query = '{ me { id username } }';
        
        $result = GraphQL::executeQuery($this->schema, $query);
        $this->assertNotNull($result->data);
        $this->assertArrayHasKey('me', $result->data);
    }

    public function testInvalidMutation(): void
    {
        $mutation = 'mutation { invalidMutation }';
        
        $result = GraphQL::executeQuery($this->schema, $mutation);
        $this->assertNotEmpty($result->errors);
    }

    public function testValidMutationStructure(): void
    {
        // Start a session for the logout mutation to work
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set some session data to make the logout meaningful
        $_SESSION['user_id'] = 1;
        
        $mutation = 'mutation { logout { success message } }';
        
        $result = GraphQL::executeQuery($this->schema, $mutation);
        $this->assertNotNull($result->data);
        $this->assertArrayHasKey('logout', $result->data);
        $this->assertArrayHasKey('success', $result->data['logout']);
        $this->assertTrue($result->data['logout']['success']);
    }

    public function testSchemaValidation(): void
    {
        $errors = $this->schema->validate();
        $this->assertEmpty($errors, 'Schema should be valid');
    }

    public function testIntrospectionQuery(): void
    {
        $query = '{ __schema { types { name } } }';
        
        $result = GraphQL::executeQuery($this->schema, $query);
        $this->assertNotNull($result->data);
        $this->assertArrayHasKey('__schema', $result->data);
        $this->assertArrayHasKey('types', $result->data['__schema']);
        $this->assertNotEmpty($result->data['__schema']['types']);
    }

    public function testServerClassExists(): void
    {
        $this->assertTrue(class_exists(Server::class));
        $this->assertTrue(method_exists(Server::class, 'handle'));
    }
}
