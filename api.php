<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$secretKey = "PRAWIE_SEKRETNY_KLUCZ_123";

$dsn = "mysql:host=localhost;dbname=notes_app;charset=utf8mb4";
$user = "root";
$pass = "";

function authenticate($secretKey) {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Missing Authorization header"]);
        exit;
    }

    $matches = [];
    if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid Authorization header"]);
        exit;
    }

    $jwt = $matches[1];

    try {
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token"]);
        exit;
    }
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);
$id = $_GET["id"] ?? null;

if ($method !== "OPTIONS") { // OPTIONS nie wymaga JWT
    //authenticate($secretKey);         // <<<<<< TUTAJ ODKOMENTUJ !!!
}

switch ($method) {
    case "GET":
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("SELECT * FROM notes");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($data);
        break;

    case "POST":
        if (!isset($input["content"])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing content"]);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO notes (content) VALUES (?)");
        $stmt->execute([$input["content"]]);
        echo json_encode(["id" => $pdo->lastInsertId(), "content" => $input["content"]]);
        break;

    case "PUT":
    case "PATCH":
        if (!$id || !isset($input["content"])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id or content"]);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE notes SET content = ? WHERE id = ?");
        $stmt->execute([$input["content"], $id]);
        echo json_encode(["id" => $id, "content" => $input["content"]]);
        break;

    case "DELETE":
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "deleted", "id" => $id]);
        break;

    case "OPTIONS":
        http_response_code(204);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
