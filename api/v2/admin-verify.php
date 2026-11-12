<?php
header('Content-Type: application/json; charset=utf-8');

$secret_hash = '0e462097431906509019562988736854';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'service' => 'Admin Verification Gateway',
        'version' => '1.0.0',
        'usage' => 'POST with JSON: {"admin_code": "your_verification_code"}',
        'note' => 'This endpoint verifies admin maintenance codes using secure hash comparison',
        'hash_algorithm' => 'MD5 (legacy - migration to bcrypt pending)',
        'status' => 'awaiting_verification'
    ], JSON_PRETTY_PRINT);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$code = $input['admin_code'] ?? '';

if (empty($code)) {
    echo json_encode(['error' => 'admin_code is required']);
    exit;
}

$user_hash = md5($code);

if ($user_hash == $secret_hash) {
    echo json_encode([
        'status' => 'verified',
        'message' => 'Admin maintenance code accepted',
        'flag' => 'ADNSU28{php_type_juggling_magic_hash_0e_bypass_d8f1}',
        'access_token' => bin2hex(random_bytes(16)),
        'note' => 'PHP loose comparison (==) with magic hashes starting with 0e are interpreted as 0 == 0'
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'status' => 'denied',
        'message' => 'Invalid verification code',
        'your_hash' => $user_hash,
        'expected_format' => 'Hash comparison: md5(your_code) == stored_hash'
    ], JSON_PRETTY_PRINT);
}
