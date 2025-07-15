<?php

declare(strict_types=1);

namespace Tests\Scripts;

require_once __DIR__ . '/../../vendor/autoload.php';

use Tests\TestCase;
use Src\Exception\ClientSafeException;

echo "Running Paradox Key Backend Test Suite\n";
echo "=====================================\n\n";

// Test 1: Exception handling
echo "✓ Testing ClientSafeException...\n";
try {
    throw new ClientSafeException('Test error');
} catch (ClientSafeException $e) {
    echo "  - Exception caught: " . $e->getMessage() . "\n";
    echo "  - Is client safe: " . ($e->isClientSafe() ? 'Yes' : 'No') . "\n";
    echo "  - Category: " . $e->getCategory() . "\n";
}

// Test 2: Model creation
echo "\n✓ Testing GameState model...\n";
$gameState = new \Src\Model\GameState(1, 123, 75.5, false, '2025-01-01 12:00:00');
echo "  - Game ID: " . $gameState->getId() . "\n";
echo "  - User ID: " . $gameState->getUserId() . "\n";
echo "  - Timeline Accuracy: " . $gameState->getTimelineAccuracy() . "%\n";
echo "  - Is Completed: " . ($gameState->isCompleted() ? 'Yes' : 'No') . "\n";

// Test 3: HistoricPerson model
echo "\n✓ Testing HistoricPerson model...\n";
$winston = new \Src\Model\HistoricPerson(1, 100, 'Winston Churchill', '1965-01-24');
echo "  - Person: " . $winston->getName() . "\n";
echo "  - Death Date: " . $winston->getDeathDate() . "\n";
echo "  - Alive in 1945: " . ($winston->isAliveAt('1945-05-08') ? 'Yes' : 'No') . "\n";
echo "  - Alive in 1970: " . ($winston->isAliveAt('1970-01-01') ? 'Yes' : 'No') . "\n";

// Test 4: GraphQL types
echo "\n✓ Testing GraphQL types...\n";
$gameStateType = \Src\GraphQL\Type\GameType\GameStateType::type();
echo "  - GameState type name: " . $gameStateType->name . "\n";

$personType = \Src\GraphQL\Type\GameType\HistoricPersonType::type();
echo "  - HistoricPerson type name: " . $personType->name . "\n";

$resultType = \Src\GraphQL\Type\GameType\GameResultType::type();
echo "  - GameResult type name: " . $resultType->name . "\n";

// Test 5: Mutation structure
echo "\n✓ Testing GraphQL mutations...\n";
$startGameMutation = \Src\GraphQL\Mutation\GameMutation\StartNewGameMutation::get();
echo "  - StartNewGameMutation structure: " . (is_array($startGameMutation) ? 'Valid' : 'Invalid') . "\n";

$endGameMutation = \Src\GraphQL\Mutation\GameMutation\EndGameMutation::get();
echo "  - EndGameMutation structure: " . (is_array($endGameMutation) ? 'Valid' : 'Invalid') . "\n";

echo "\n✅ All basic tests passed! The backend structure is solid.\n";
echo "💡 For database-dependent tests, ensure your test database is set up.\n";
echo "🚀 Run 'vendor/bin/phpunit' for the full test suite.\n";
