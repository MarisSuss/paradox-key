<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Database\Connection;

$pdo = Connection::getInstance();

$pdo->exec("DROP TABLE IF EXISTS users, game_states");


echo "All tables droped successfully.\n";