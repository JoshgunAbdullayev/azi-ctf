<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        $stmt = $db->prepare("SELECT id, customer_no, name, email, phone, balance, iban, created_at FROM users WHERE id = ? AND is_admin = 0");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(['status' => 'success', 'data' => $user], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Account not found']);
        }
    } else {
        $stmt = $db->query("SELECT id, customer_no, name, email, balance, created_at FROM users WHERE is_admin = 0 ORDER BY id ASC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'total' => count($users), 'data' => $users], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($method === 'PUT' || $method === 'PATCH') {
    $input = @file_get_contents('php://input');
    $data = @json_decode($input, true);

    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing account ID']);
        exit;
    }

    $id = intval($data['id']);
    $user = getUser();

    if ($user['is_admin'] != 1 && $user['id'] != $id) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied', 'message' => 'You can only modify your own account']);
        exit;
    }

    $allowed_fields = ['name', 'email', 'phone'];
    $updates = [];
    $params = [];

    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }

    if (isset($data['role'])) {
        $updates[] = "is_admin = ?";
        $params[] = ($data['role'] === 'admin') ? 1 : 0;
    }

    if (isset($data['balance'])) {
        $updates[] = "balance = ?";
        $params[] = floatval($data['balance']);
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }

    $params[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);

    $response = [
        'status' => 'success',
        'message' => 'Account updated',
        'data' => $updated
    ];

    if (isset($data['role']) && $data['role'] === 'admin' && $updated['is_admin'] == 1) {
        $response['admin_token'] = base64_encode('ADNSU12{mass_assignment_privilege_escalation_7f3a}');
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed', 'allowed' => ['GET', 'PUT', 'PATCH']]);
