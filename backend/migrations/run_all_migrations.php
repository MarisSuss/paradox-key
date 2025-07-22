<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

echo "Running database migrations...\n\n";

echo "1. Creating database schema...\n";
require_once __DIR__ . '/create_database_schema.php';

echo "\n2. Populating historic person templates...\n";
require_once __DIR__ . '/populate_historic_person_template.php';

echo "\n3. Populating dialogue data...\n";
require_once __DIR__ . '/populate_dialogue.php';

echo "\nAll migrations completed successfully!\n";
echo "Database is ready for use.\n";
