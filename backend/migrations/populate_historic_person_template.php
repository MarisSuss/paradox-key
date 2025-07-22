<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Database\Connection;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$pdo = Connection::getInstance();

// Create historic_person_templates table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS historic_person_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        original_death_date DATE NOT NULL,
        alternate_death_dates JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
    )
");

// Clear existing templates
$pdo->exec("DELETE FROM historic_person_templates");

// Insert Winston Churchill template for campaign 1 (World War II)
$alternateDeathDates = json_encode([
    '1965-01-24',  // Actual historical death date
    '1955-04-15',  // Earlier possible death
    '1970-12-31',  // Later possible death
    '1960-06-30'   // Alternative death date
]);

$stmt = $pdo->prepare("
    INSERT INTO historic_person_templates (campaign_id, name, original_death_date, alternate_death_dates) VALUES
    (1, 'Winston Churchill', '1938-01-01', :alternate_death_dates)
");
$stmt->bindParam(':alternate_death_dates', $alternateDeathDates, PDO::PARAM_STR);
$stmt->execute();

echo "Historic person templates populated successfully.\n";
