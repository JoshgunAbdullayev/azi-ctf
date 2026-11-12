<?php
header('Content-Type: application/json; charset=utf-8');

$jwt_secret = 'n0v4b4nk_jwt_s3cr3t_k3y_2026';

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function verify_jwt($token, $secret) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return ['valid' => false, 'error' => 'Invalid token format'];

    $header = json_decode(base64url_decode($parts[0]), true);
    $payload = json_decode(base64url_decode($parts[1]), true);

    if (!$header || !$payload) return ['valid' => false, 'error' => 'Malformed token'];

    if (isset($header['alg']) && strtolower($header['alg']) === 'none') {
        return ['valid' => true, 'payload' => $payload, 'warning' => 'Algorithm: none (no signature verification)'];
    }

    $expected_sig = base64url_encode(hash_hmac('sha256', "$parts[0].$parts[1]", $secret, true));
    if (hash_equals($expected_sig, $parts[2])) {
        return ['valid' => true, 'payload' => $payload];
    }

    return ['valid' => false, 'error' => 'Invalid signature'];
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    echo json_encode([
        'error' => 'Missing token parameter',
        'usage' => 'GET /api/v2/verify.php?token=<JWT>'
    ], JSON_PRETTY_PRINT);
    exit;
}

$parts = explode('.', $token);
if (count($parts) === 3) {
    $raw_header = base64url_decode($parts[0]);
    if (strpos($raw_header, '"none"') !== false) {
        http_response_code(403);
        echo json_encode([
            'error' => 'JWT Security Policy Violation',
            'message' => 'Algorithm "none" is explicitly blocked. [NB-JWT-GUARD v1.4]',
            'details' => 'Unsigned tokens are not accepted by this endpoint.'
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

$result = verify_jwt($token, $jwt_secret);

if ($result['valid']) {
    $response = [
        'status' => 'valid',
        'payload' => $result['payload']
    ];

    if (isset($result['warning'])) {
        $response['warning'] = $result['warning'];
    }

    if (isset($result['payload']['role']) && $result['payload']['role'] === 'admin') {
        $response['admin_access'] = true;
        $response['flag'] = 'ADNSU13{jwt_algorithm_none_bypass_4d2e}';
        $response['internal_endpoints'] = [
            '/api/v2/accounts.php' => 'Full account management',
            '/nb-adm-f7d3/' => 'System administration panel',
            '/novabank-internal-7f2c9/vault/' => 'Classified vault access'
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    http_response_code(401);
    echo json_encode([
        'status' => 'invalid',
        'error' => $result['error'],
        'details' => 'Signature verification failed'
    ], JSON_PRETTY_PRINT);
}
