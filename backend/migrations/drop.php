<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

$pdo->exec("DROP TABLE IF EXISTS historic_people");
$pdo->exec("DROP TABLE IF EXISTS historic_events");
$pdo->exec("DROP TABLE IF EXISTS game_states");
$pdo->exec("DROP TABLE IF EXISTS users");

echo "All tables dropped successfully.\n";