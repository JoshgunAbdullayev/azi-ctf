<?php
require __DIR__ . '/../nb-id-recover-a3f9/_act2_boot.php';
act2_hs_secret(); // ensure key material exists
header('Content-Type: application/json; charset=utf-8');

function bearer_token() {
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (stripos($h, 'bearer ') === 0) return trim(substr($h, 7));
    return $_GET['jwt'] ?? $_POST['jwt'] ?? '';
}

$jwt = bearer_token();
if ($jwt === '') {
    echo json_encode([
        'service' => 'NovaBank JWKS Admin Console',
        'version' => '1.4',
        'auth'    => 'Authorization: Bearer <jwt>  (or ?jwt=)',
        'note'    => 'Admin actions require a token with role=admin. Signing key is selected per-token '
                   . 'from the "kid" header (key id = relative key file path).',
    ], JSON_PRETTY_PRINT);
    exit;
}

$parts = explode('.', $jwt);
if (count($parts) !== 3) {
    http_response_code(400);
    echo json_encode(['error' => 'malformed_jwt']);
    exit;
}
list($h64, $p64, $sig64) = $parts;
$header  = json_decode(act2_b64url_decode($h64), true);
$payload = json_decode(act2_b64url_decode($p64), true);
if (!is_array($header) || !is_array($payload)) {
    http_response_code(400);
    echo json_encode(['error' => 'bad_segments']);
    exit;
}

$alg = $header['alg'] ?? '';
$kid = $header['kid'] ?? '';
if (strtoupper($alg) !== 'HS256') {
    http_response_code(400);
    echo json_encode(['error' => 'unsupported_alg', 'message' => 'Only HS256 is accepted.']);
    exit;
}

// --- VULN: the kid header is used as a file path with no sanitisation. ---
// An attacker can point kid at any readable file whose bytes they know and
// then forge the HMAC with those bytes (key confusion via kid path traversal).
$keypath = __DIR__ . '/' . $kid;
$key = @file_get_contents($keypath);
if ($key === false) {
    http_response_code(400);
    echo json_encode(['error' => 'kid_not_found', 'kid' => $kid]);
    exit;
}
$key = trim($key);

$expected = act2_b64url(hash_hmac('sha256', "$h64.$p64", $key, true));
if (!hash_equals($expected, $sig64)) {
    http_response_code(401);
    echo json_encode(['error' => 'bad_signature', 'message' => 'Signature verification failed for selected kid.']);
    exit;
}

$role = $payload['role'] ?? '';
if ($role !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'insufficient_role', 'role' => $role, 'message' => 'Admin role required.']);
    exit;
}

// Admin action authorised.
echo json_encode([
    'status' => 'ok',
    'action' => 'admin_console_unlocked',
    'subject' => $payload['sub'] ?? null,
    'flag' => ACT2_FLAG35,
], JSON_PRETTY_PRINT);
