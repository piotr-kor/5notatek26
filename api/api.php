<?php
header("Content-Type: application/json");
require_once 'db.php';
require_once 'jwt.php';

/*
|--------------------------------------------------------------------------
| ROUTING — bez .htaccess, bez rewrite
|--------------------------------------------------------------------------
| Działa dla:
|  - api.php/notes
|  - api.php/notes/2
|  - api.php/tasks/1
|
| PATH_INFO = "/notes/2"
*/

$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$pathParts = array_values(array_filter(explode('/', trim($pathInfo, '/'))));

$table = $pathParts[0] ?? null;
$id = $pathParts[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// ---  Weryfikacja JWT ---
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if ($table !== 'auth') { // endpoint /auth jest publiczny
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Missing or invalid Authorization header']);
        exit;
    }

    $token = $matches[1];
    $user = verifyJWT($token, $JWT_SECRET);
}

// --- Obsługa braku zasobu ---
if (!$table) {
    http_response_code(400);
    echo json_encode(['error' => 'No resource specified']);
    exit;
}

// --- Wczytanie modułu ---
$modulePath = __DIR__ . '/modules/' . $table . '.php';
if (file_exists($modulePath)) {
    require_once $modulePath;
    $functionName = 'handle' . ucfirst($table);
    if (function_exists($functionName)) {
        $functionName($pdo, $method, $id);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Module function not found']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Unknown resource']);
}
