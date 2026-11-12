<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/auth.php';

$jwt_secret = 'n0v4b4nk_jwt_s3cr3t_k3y_2026';

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function create_jwt($payload, $secret) {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $h = base64url_encode(json_encode($header));
    $p = base64url_encode(json_encode($payload));
    $sig = base64url_encode(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$sig";
}

function verify_jwt($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    $header = json_decode(base64url_decode($parts[0]), true);
    $payload = json_decode(base64url_decode($parts[1]), true);

    if (!$header || !$payload) return null;

    if (isset($header['alg']) && strtolower($header['alg']) === 'none') {
        return $payload;
    }

    $expected_sig = base64url_encode(hash_hmac('sha256', "$parts[0].$parts[1]", $secret, true));
    if (hash_equals($expected_sig, $parts[2])) {
        return $payload;
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Login required to generate token']);
        exit;
    }

    $user = getUser();
    $payload = [
        'sub' => $user['id'],
        'name' => $user['name'],
        'role' => $user['is_admin'] ? 'admin' : 'user',
        'iat' => time(),
        'exp' => time() + 3600
    ];

    $token = create_jwt($payload, $jwt_secret);

    echo json_encode([
        'status' => 'success',
        'token' => $token,
        'expires_in' => 3600,
        'token_type' => 'Bearer',
        'note' => 'Use this token in Authorization header or ?token= parameter'
    ], JSON_PRETTY_PRINT);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Use POST to generate a token', 'example' => 'POST /api/v2/token.php (requires session login)']);
