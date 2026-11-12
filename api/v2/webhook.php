<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'service' => 'NovaBank Webhook Notification Service',
        'version' => '1.2.0',
        'usage' => 'POST JSON with callback_url and event_type',
        'supported_events' => ['transaction', 'login', 'security_alert', 'balance_update'],
        'example' => [
            'callback_url' => 'https://your-server.com/webhook',
            'event_type' => 'transaction',
            'format' => 'json'
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$callback_url = $input['callback_url'] ?? '';
$event_type = $input['event_type'] ?? '';

if (empty($callback_url)) {
    echo json_encode(['error' => 'callback_url is required']);
    exit;
}

$valid_events = ['transaction', 'login', 'security_alert', 'balance_update'];
if (!in_array($event_type, $valid_events)) {
    echo json_encode(['error' => 'Invalid event_type', 'valid' => $valid_events]);
    exit;
}

$parsed = parse_url($callback_url);
$host = $parsed['host'] ?? '';
$scheme = $parsed['scheme'] ?? '';

$blocked_hosts = ['localhost', '127.0.0.1', '0.0.0.0', '[::1]'];
if (in_array(strtolower($host), $blocked_hosts)) {
    echo json_encode(['error' => 'Internal hosts are not allowed as callback targets']);
    exit;
}

if ($scheme !== 'http' && $scheme !== 'https') {
    echo json_encode(['error' => 'Only http:// and https:// schemes allowed']);
    exit;
}

$internal_responses = [
    '169.254.169.254' => json_encode([
        'instance' => 'novabank-prod-01',
        'region' => 'eu-west-1',
        'iam_role' => 'NovaBank-EC2-Production',
        'credentials' => [
            'access_key' => 'AKIAIOSFODNN7EXAMPLE',
            'secret_key' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
            'token' => 'FwoGZXIvYXdzEBYaDH... (truncated)',
            'expiration' => '2026-03-15T12:00:00Z'
        ],
        'flag' => 'ADNSU27{ssrf_cloud_metadata_credential_theft_4a7b}'
    ], JSON_PRETTY_PRINT),

    '2130706433' => json_encode([
        'status' => 'internal_service_response',
        'services' => ['redis://internal:6379', 'mysql://db.internal:3306', 'http://admin.internal:8080'],
        'note' => 'Internal service discovery via decimal IP bypass'
    ], JSON_PRETTY_PRINT),

    '0x7f000001' => json_encode([
        'status' => 'internal_service_response',
        'services' => ['redis://internal:6379', 'mysql://db.internal:3306'],
        'note' => 'Internal service discovery via hex IP bypass'
    ], JSON_PRETTY_PRINT),

    '017700000001' => json_encode([
        'status' => 'internal_access',
        'note' => 'Octal IP bypass detected'
    ], JSON_PRETTY_PRINT),
];

$resolved_ip = @gethostbyname($host);

foreach ($internal_responses as $check_ip => $response) {
    if ($host === $check_ip || $resolved_ip === $check_ip) {
        echo json_encode([
            'webhook_registered' => true,
            'callback_url' => $callback_url,
            'test_payload_sent' => true,
            'response_from_target' => json_decode($response, true)
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

if ($resolved_ip === '169.254.169.254' || ip2long($resolved_ip) === ip2long('169.254.169.254')) {
    echo json_encode([
        'webhook_registered' => true,
        'callback_url' => $callback_url,
        'test_payload_sent' => true,
        'response_from_target' => json_decode($internal_responses['169.254.169.254'], true)
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($resolved_ip && (
    ip2long($resolved_ip) === ip2long('127.0.0.1') ||
    substr($resolved_ip, 0, 4) === '10.' ||
    substr($resolved_ip, 0, 8) === '192.168.' ||
    substr($resolved_ip, 0, 7) === '172.16.'
)) {
    echo json_encode([
        'webhook_registered' => true,
        'callback_url' => $callback_url,
        'test_payload_sent' => true,
        'internal_access' => true,
        'response_from_target' => [
            'status' => 'internal_service_reached',
            'resolved_ip' => $resolved_ip,
            'services' => ['admin-panel', 'config-service', 'monitoring'],
            'note' => 'DNS rebinding or redirect-based SSRF bypass successful'
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

echo json_encode([
    'webhook_registered' => true,
    'callback_url' => $callback_url,
    'event_type' => $event_type,
    'status' => 'active',
    'next_delivery' => date('Y-m-d H:i:s', time() + 60),
    'test_payload' => [
        'event' => $event_type,
        'timestamp' => date('Y-m-d\TH:i:s\Z'),
        'data' => ['amount' => 150.00, 'currency' => 'TRY']
    ]
], JSON_PRETTY_PRINT);
