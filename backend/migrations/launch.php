<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

// Users table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Game states table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS game_states (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Historic people table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS historic_people (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_state_id INT NOT NULL,
        FOREIGN KEY (game_state_id) REFERENCES game_states(id) ON DELETE CASCADE,
        name VARCHAR(255) NOT NULL,
        death_date DATE
    )
");

// Historic events table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS historic_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(255) NOT NULL,
        date DATE
    )
");

// Populate historic events table with initial data
$pdo->exec("
    INSERT INTO historic_events (event_name, date) VALUES
    ('World War II', '1939-09-01')
");

echo "Database launch migration completed successfully.\n";