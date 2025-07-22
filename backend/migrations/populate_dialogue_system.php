<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

// Clear existing dialogue data
$pdo->exec("DELETE FROM dialogue_responses");
$pdo->exec("DELETE FROM dialogue_prompts");

// Insert sample dialogue prompts for World War II campaign
$prompts = [
    [
        'campaign_id' => 1,
        'prompt_text' => 'Save my world from the coming war!',
        'context' => 'A desperate citizen from 1938 asking for help',
        'difficulty_level' => 'medium'
    ],
    [
        'campaign_id' => 1,
        'prompt_text' => 'Can you help me understand what will happen to my country?',
        'context' => 'A worried person seeking knowledge about the future',
        'difficulty_level' => 'easy'
    ],
    [
        'campaign_id' => 1,
        'prompt_text' => 'I need to know if my family will survive the war',
        'context' => 'A parent concerned about their children\'s safety',
        'difficulty_level' => 'hard'
    ]
];

$promptIds = [];
foreach ($prompts as $prompt) {
    $stmt = $pdo->prepare("
        INSERT INTO dialogue_prompts (campaign_id, prompt_text, context, difficulty_level) 
        VALUES (:campaign_id, :prompt_text, :context, :difficulty_level)
    ");
    $stmt->execute($prompt);
    $promptIds[] = $pdo->lastInsertId();
}

// Insert responses for first prompt: "Save my world from the coming war!"
$responses1 = [
    [
        'prompt_id' => $promptIds[0],
        'response_text' => 'I cannot interfere with historical events',
        'outcome_type' => 'neutral',
        'timeline_impact' => 0.0
    ],
    [
        'prompt_id' => $promptIds[0],
        'response_text' => 'Tell me more about what you think is coming',
        'outcome_type' => 'helpful',
        'timeline_impact' => 5.0
    ],
    [
        'prompt_id' => $promptIds[0],
        'response_text' => 'The world cannot be saved from its destiny',
        'outcome_type' => 'harmful',
        'timeline_impact' => -10.0
    ]
];

// Insert responses for second prompt: "Can you help me understand..."
$responses2 = [
    [
        'prompt_id' => $promptIds[1],
        'response_text' => 'I can share some general guidance about preparing for difficult times',
        'outcome_type' => 'helpful',
        'timeline_impact' => 3.0
    ],
    [
        'prompt_id' => $promptIds[1],
        'response_text' => 'The future is uncertain for everyone',
        'outcome_type' => 'neutral',
        'timeline_impact' => 0.0
    ],
    [
        'prompt_id' => $promptIds[1],
        'response_text' => 'You should focus on the present, not worry about the future',
        'outcome_type' => 'helpful',
        'timeline_impact' => 2.0
    ]
];

// Insert responses for third prompt: "I need to know if my family will survive..."
$responses3 = [
    [
        'prompt_id' => $promptIds[2],
        'response_text' => 'I cannot predict individual fates',
        'outcome_type' => 'neutral',
        'timeline_impact' => 0.0
    ],
    [
        'prompt_id' => $promptIds[2],
        'response_text' => 'Make preparations to keep your family safe',
        'outcome_type' => 'helpful',
        'timeline_impact' => 8.0
    ],
    [
        'prompt_id' => $promptIds[2],
        'response_text' => 'Many families will not survive what\'s coming',
        'outcome_type' => 'harmful',
        'timeline_impact' => -15.0
    ]
];

// Insert all responses
$allResponses = array_merge($responses1, $responses2, $responses3);
foreach ($allResponses as $response) {
    $stmt = $pdo->prepare("
        INSERT INTO dialogue_responses (prompt_id, response_text, outcome_type, timeline_impact) 
        VALUES (:prompt_id, :response_text, :outcome_type, :timeline_impact)
    ");
    $stmt->execute($response);
}

echo "Dialogue prompts and responses populated successfully.\n";
