<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'service' => 'NovaBank Transfer API v2',
        'usage' => 'POST with JSON body: {amount, to_iban, description}',
        'note' => 'Transfers are processed asynchronously for performance'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$amount = floatval($input['amount'] ?? 0);
$to_iban = $input['to_iban'] ?? '';
$desc = $input['description'] ?? 'Transfer';

if ($amount <= 0 || $amount > 100000) {
    echo json_encode(['error' => 'Invalid amount. Must be between 0.01 and 100,000']);
    exit;
}
if (empty($to_iban)) {
    echo json_encode(['error' => 'Recipient IBAN required']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT balance, name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

// TOCTOU: the balance validation runs against a short-lived snapshot taken at
// the start of a burst of requests rather than the live row. Under a flood of
// concurrent/rapid transfers this stale snapshot lets multiple withdrawals pass
// the "sufficient funds" check before the row is updated (classic double-spend
// race). A single, paced transfer always validates against a fresh balance.
$snap_file = sys_get_temp_dir() . '/nb_xfer_' . $user_id . '.json';
$now = microtime(true);
$snap = @json_decode(@file_get_contents($snap_file), true);
if (is_array($snap) && isset($snap['balance'], $snap['ts']) && ($now - $snap['ts']) < 3.0) {
    $check_balance = (float)$snap['balance'];
} else {
    $check_balance = (float)$user['balance'];
    @file_put_contents($snap_file, json_encode(['balance' => $check_balance, 'ts' => $now]));
}

if ($check_balance < $amount) {
    echo json_encode([
        'error' => 'Insufficient funds',
        'balance' => $user['balance'],
        'requested' => $amount
    ]);
    exit;
}

usleep(100000);

$stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
$stmt->execute([$amount, $user_id]);

$stmt = $db->prepare("INSERT INTO transactions (user_id, type, amount, description, recipient_name, recipient_iban, created_at) VALUES (?, 'outgoing', ?, ?, 'External', ?, datetime('now'))");
$stmt->execute([$user_id, -$amount, $desc, $to_iban]);

$stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$new_balance = $stmt->fetch(PDO::FETCH_ASSOC)['balance'];

$response = [
    'success' => true,
    'transaction_id' => 'TX-' . strtoupper(bin2hex(random_bytes(6))),
    'amount' => $amount,
    'from' => $user['name'],
    'to_iban' => $to_iban,
    'new_balance' => $new_balance,
    'timestamp' => date('Y-m-d H:i:s')
];

if ($new_balance < 0) {
    $response['_debug'] = 'RACE_CONDITION_DETECTED';
    $response['flag'] = 'ADNSU25{race_condition_double_spend_b4nk_8f2e}';
    $response['note'] = 'Negative balance achieved through concurrent requests. In production, this would require distributed locking.';
}

echo json_encode($response, JSON_PRETTY_PRINT);
