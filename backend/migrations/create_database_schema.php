<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

// Drop existing tables to start fresh (order matters for foreign keys)
$pdo->exec("DROP TABLE IF EXISTS dialogue_responses");
$pdo->exec("DROP TABLE IF EXISTS dialogue_prompts");
$pdo->exec("DROP TABLE IF EXISTS historic_people");
$pdo->exec("DROP TABLE IF EXISTS historic_person_templates");
$pdo->exec("DROP TABLE IF EXISTS historic_events");  
$pdo->exec("DROP TABLE IF EXISTS game_states");
$pdo->exec("DROP TABLE IF EXISTS campaigns");
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

// Campaigns table
$pdo->exec("
    CREATE TABLE campaigns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        description TEXT
    )
");

// Game states table
$pdo->exec("
    CREATE TABLE game_states (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        campaign_id INT NOT NULL,
        timeline_accuracy DECIMAL(5,2) DEFAULT 0.00,
        is_completed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
    )
");

// Historic events table (predefined events)
$pdo->exec("
    CREATE TABLE historic_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
    )
");

// Historic person templates table (templates for creating people)
$pdo->exec("
    CREATE TABLE historic_person_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        original_death_date DATE NOT NULL,
        alternate_death_dates JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
    )
");

// Historic people table (per game instance)
$pdo->exec("
    CREATE TABLE historic_people (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_state_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        death_date DATE NOT NULL,
        alternate_death_date DATE NOT NULL,
        FOREIGN KEY (game_state_id) REFERENCES game_states(id) ON DELETE CASCADE
    )
");

// Dialogue prompts table (AI requests from users)
$pdo->exec("
    CREATE TABLE dialogue_prompts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        prompt_text TEXT NOT NULL,
        context TEXT NULL,
        difficulty_level ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
    )
");

// Dialogue responses table (available AI response options)
$pdo->exec("
    CREATE TABLE dialogue_responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prompt_id INT NOT NULL,
        response_text TEXT NOT NULL,
        outcome_type ENUM('helpful', 'neutral', 'harmful') DEFAULT 'neutral',
        timeline_impact DECIMAL(5,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (prompt_id) REFERENCES dialogue_prompts(id) ON DELETE CASCADE
    )
");

// Insert default campaigns
$pdo->exec("
    INSERT INTO campaigns (name, description) VALUES
    ('World War II Campaign', 'Navigate the critical events of World War II')
");

// Insert default historic events
$pdo->exec("
    INSERT INTO historic_events (campaign_id, name, date) VALUES
    (1, 'World War II', '1939-09-01')
");

echo "Clean database schema created successfully.\n";
