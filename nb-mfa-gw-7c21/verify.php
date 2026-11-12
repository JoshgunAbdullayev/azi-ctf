<?php
require __DIR__ . '/../nb-id-recover-a3f9/_act2_boot.php';
$db = act2_db();
header('Content-Type: application/json; charset=utf-8');

$s = act2_session_from_cookie();
if (!$s) {
    http_response_code(401);
    echo json_encode([
        'error' => 'no_session',
        'message' => 'Authenticated session cookie (act2_sid) required. Take over an account first.',
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'service'  => 'NovaBank MFA Step-up Gateway',
        'version'  => '3.2',
        'method'   => 'POST',
        'required' => ['mfa_ref', 'otp'],
        'gateway'  => [
            // Internal reverse proxy appends this header AFTER a successful
            // hardware-OTP challenge. Downstream app trusts it. Clients MUST NOT set it.
            'trusted_verification_header' => 'X-MFA-Verified',
        ],
        'note' => 'Submit the 6-digit code from your enrolled authenticator.',
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

// Accept JSON or form body.
$raw  = @file_get_contents('php://input');
$body = @json_decode($raw, true);
if (!is_array($body)) $body = $_POST;

$mfa_ref = $body['mfa_ref'] ?? '';
$otp     = (string)($body['otp'] ?? '');

if ($mfa_ref !== $s['mfa_ref']) {
    http_response_code(403);
    echo json_encode(['error' => 'bad_mfa_ref', 'message' => 'Enrollment reference does not match this account.'], JSON_PRETTY_PRINT);
    exit;
}

// --- VULN: downstream app trusts the proxy-only verification header from the client. ---
$header_verified = isset($_SERVER['HTTP_X_MFA_VERIFIED']) &&
                   in_array(strtolower((string)$_SERVER['HTTP_X_MFA_VERIFIED']), ['1', 'true', 'yes'], true);

// The real OTP is a server-side secret the attacker does not have.
$real_otp = substr(strval(crc32('otp:' . $s['user_id'])), 0, 6);
$otp_ok = hash_equals($real_otp, $otp);

if (!$header_verified && !$otp_ok) {
    http_response_code(401);
    echo json_encode(['error' => 'otp_invalid', 'message' => 'Incorrect verification code.'], JSON_PRETTY_PRINT);
    exit;
}

// Passed step-up. Mint an internal API token and mark the session.
$api_token = 'nbint_' . bin2hex(random_bytes(10));
$db->prepare("UPDATE v_session SET mfa_passed = 1, api_token = ? WHERE sid = ?")
   ->execute([$api_token, $s['sid']]);

echo json_encode([
    'status'   => 'verified',
    'bypass'   => $header_verified && !$otp_ok,
    'flag'     => ACT2_FLAG33,
    'internal_api' => [
        'origin'    => 'http://internal-api.novabank.local',
        'endpoint'  => '/nb-cors-edge-5b8e/data.php',
        'api_token' => $api_token,
        'auth'      => 'Send api_token via X-Api-Token header (or ?api_token=) with your session cookie.',
    ],
], JSON_PRETTY_PRINT);
