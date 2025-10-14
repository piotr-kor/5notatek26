<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$secretKey = "PRAWIE_SEKRETNY_KLUCZ_123";

$validUsername = "admin";                   // <<<<<<<<<<<<<<<<<<< login
$validPasswordHash = hash("sha256", "zsz"); // <<<<<<<<<<<<<<<<<<< hasło

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$username = $input["username"] ?? null;
$password = $input["password"] ?? null;

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Missing username or password"]);
    exit;
}

// Sprawdzenie po SHA256
if ($username !== $validUsername || hash("sha256", $password) !== $validPasswordHash) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
}

// Generowanie tokena JWT
$payload = [
    "sub" => $username,
    "iat" => time(),
    "exp" => time() + 3600 // ważny 1 godzinę
];

$jwt = JWT::encode($payload, $secretKey, 'HS256');

echo json_encode(["token" => $jwt]);
