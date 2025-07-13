<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Src\GraphQL\Server;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configure session for production
if (($_ENV['APP_DEBUG'] ?? 'false') === 'false') {
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
}

ini_set('session.gc_maxlifetime', $_ENV['SESSION_LIFETIME'] ?? '3600');
ini_set('session.cookie_lifetime', $_ENV['SESSION_LIFETIME'] ?? '3600');

session_start();

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('POST', '/graphql', [Server::class, 'handle']);
    $r->addRoute('OPTIONS', '/graphql', function () {
        http_response_code(204);
        exit;
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?');

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$origin = $_ENV['FRONTEND_ORIGIN'];

header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        if (is_callable($handler)) {
            call_user_func_array($handler, $vars);
        } elseif (is_array($handler) && method_exists($handler[0], $handler[1])) {
            call_user_func([new $handler[0], $handler[1]]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Invalid route handler']);
        }
        break;
}