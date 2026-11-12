<?php
require __DIR__ . '/../nb-id-recover-a3f9/_act2_boot.php';
$db = act2_db();

// --- VULN: reflect ANY Origin and allow credentials. ---
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Headers: X-Api-Token, Content-Type');
    http_response_code(204);
    exit;
}

$s = act2_session_from_cookie();
$api_token = $_SERVER['HTTP_X_API_TOKEN'] ?? $_GET['api_token'] ?? '';

if (!$s) {
    http_response_code(401);
    echo json_encode(['error' => 'no_session', 'message' => 'Credentialed session required.'], JSON_PRETTY_PRINT);
    exit;
}
if (!$s['mfa_passed']) {
    http_response_code(403);
    echo json_encode(['error' => 'mfa_required', 'message' => 'Session has not cleared MFA step-up.'], JSON_PRETTY_PRINT);
    exit;
}
if (!$s['api_token'] || !hash_equals($s['api_token'], (string)$api_token)) {
    http_response_code(403);
    echo json_encode(['error' => 'bad_api_token', 'message' => 'Valid internal api_token required.'], JSON_PRETTY_PRINT);
    exit;
}

// Build a signed per-user token (HS256) whose header carries a kid file path.
$secret = act2_hs_secret();
$header = ['alg' => 'HS256', 'typ' => 'JWT', 'kid' => 'keys/hs_main.key'];
$payload = [
    'iss'  => 'novabank-edge',
    'sub'  => $s['username'],
    'role' => 'user',
    'iat'  => time(),
];
$jwt = act2_jwt_hs256($header, $payload, $secret);

$resp = [
    'service' => 'NovaBank Internal Edge API',
    'account' => ['username' => $s['username'], 'email' => $s['email']],
    'session_token' => $jwt,
    'admin_console' => '/nb-jwks-vault-9d4c/admin.php (presents this token in Authorization: Bearer)',
];

// Cross-origin exfiltration proof: the flag is only returned when the request
// carries an attacker-controlled Origin that the server blindly reflects with
// Access-Control-Allow-Credentials: true.
$same_origin_hosts = ['', 'http://localhost', 'http://localhost:8080', 'http://localhost:8091',
                      'http://127.0.0.1', 'http://127.0.0.1:8080', 'http://127.0.0.1:8091',
                      'http://internal-api.novabank.local'];
if ($origin !== '' && !in_array(strtolower($origin), $same_origin_hosts, true)) {
    $resp['cross_origin_exfil'] = true;
    $resp['flag'] = ACT2_FLAG34;
} else {
    $resp['hint'] = 'Protected data is readable cross-origin: replay with an attacker Origin header to prove credentialed exfiltration.';
}

echo json_encode($resp, JSON_PRETTY_PRINT);
