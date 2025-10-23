<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_SECRET = 'PRAWIE_SEKRETNY_KLUCZ_123';

function generateJWT($payload, $secret, $exp = 3600) {
    $payload['exp'] = time() + $exp;
    $payload['iat'] = time();
    return JWT::encode($payload, $secret, 'HS256');
}

function verifyJWT($token, $secret) {
    try {
        return (array)JWT::decode($token, new Key($secret, 'HS256'));
    } catch (\Firebase\JWT\ExpiredException $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token expired']);
        exit;
    } catch (\Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}
