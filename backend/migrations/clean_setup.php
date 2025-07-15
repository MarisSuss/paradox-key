<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

// Drop existing tables to start fresh
$pdo->exec("DROP TABLE IF EXISTS historic_people");
$pdo->exec("DROP TABLE IF EXISTS historic_events");  
$pdo->exec("DROP TABLE IF EXISTS game_states");
$pdo->exec("DROP TABLE IF EXISTS users");

// Users table
$pdo->exec("
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Game states table
$pdo->exec("
    CREATE TABLE game_states (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        timeline_accuracy DECIMAL(5,2) DEFAULT 0.00,
        is_completed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

// Historic events table (predefined events)
$pdo->exec("
    CREATE TABLE historic_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Historic people table (per game instance)
$pdo->exec("
    CREATE TABLE historic_people (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_state_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        death_date DATE NOT NULL,
        FOREIGN KEY (game_state_id) REFERENCES game_states(id) ON DELETE CASCADE
    )
");

// Insert default historic events
$pdo->exec("
    INSERT INTO historic_events (name, date) VALUES
    ('World War II', '1939-09-01')
");

echo "Clean database schema created successfully.\n";
