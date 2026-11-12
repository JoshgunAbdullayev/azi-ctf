<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['hakan_auth']) || $_SESSION['hakan_auth'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden', 'message' => 'Priority admin authentication required.']);
    exit;
}

$id_raw = $_GET['id'] ?? '0';
$key = $_GET['key'] ?? '';

if (strpos($id_raw, '?') !== false) {
    $parts = explode('?', $id_raw, 2);
    $id_raw = $parts[0];
    if (!empty($parts[1])) {
        parse_str($parts[1], $extra);
        if (isset($extra['key']) && $key === '') $key = $extra['key'];
    }
}

$id = intval($id_raw);

$services = [
    1 => ['name' => 'Account Management Service', 'status' => 'active', 'version' => '2.4.1', 'uptime' => '99.98%',
          'description' => 'Handles customer account lifecycle: creation, modification, closure, and balance management.',
          'endpoints' => ['/accounts', '/accounts/{id}', '/accounts/{id}/balance'], 'visibility' => 'public', 'rate_limit' => '500 req/min'],
    2 => ['name' => 'Transaction Processing Engine', 'status' => 'active', 'version' => '3.1.0', 'uptime' => '99.99%',
          'description' => 'Core transaction pipeline: validation, AML screening, authorization, settlement, and reconciliation.',
          'endpoints' => ['/transactions', '/transactions/{id}', '/transactions/batch'], 'visibility' => 'public', 'rate_limit' => '200 req/min'],
    3 => ['name' => 'Notification Gateway', 'status' => 'active', 'version' => '1.7.2', 'uptime' => '99.95%',
          'description' => 'Multi-channel notification delivery: SMS via Turkcell API, email via SES, push via FCM/APNs.',
          'endpoints' => ['/notifications/send', '/notifications/templates'], 'visibility' => 'public', 'rate_limit' => '100 req/min'],
    4 => ['name' => 'Authentication Service', 'status' => 'active', 'version' => '2.1.0', 'uptime' => '99.99%',
          'description' => 'Session-based authentication with bcrypt hashing, TOTP MFA, and device fingerprinting.',
          'endpoints' => ['/auth/login', '/auth/logout', '/auth/mfa/verify'], 'visibility' => 'public', 'rate_limit' => '50 req/min'],
    5 => ['name' => 'Logging & Audit Trail', 'status' => 'active', 'version' => '1.3.1', 'uptime' => '99.97%',
          'description' => 'Immutable event logging with structured JSON output for compliance reporting.',
          'endpoints' => ['/logs/query', '/logs/export', '/logs/retention'], 'visibility' => 'public', 'rate_limit' => '300 req/min'],
    6 => ['name' => 'Report Generation Engine', 'status' => 'active', 'version' => '1.5.0', 'uptime' => '99.90%',
          'description' => 'Generates PDF/CSV reports for account statements and regulatory filings.',
          'endpoints' => ['/reports/generate', '/reports/{id}', '/reports/schedule'], 'visibility' => 'public', 'rate_limit' => '20 req/min'],
    7 => ['name' => 'Customer Support API', 'status' => 'active', 'version' => '1.2.4', 'uptime' => '99.92%',
          'description' => 'Ticket lifecycle management, SLA tracking, and agent assignment routing.',
          'endpoints' => ['/support/tickets', '/support/tickets/{id}'], 'visibility' => 'public', 'rate_limit' => '150 req/min'],
    8 => ['name' => 'Mobile Banking Bridge', 'status' => 'active', 'version' => '2.0.3', 'uptime' => '99.96%',
          'description' => 'REST API bridge for mobile applications with biometric auth support.',
          'endpoints' => ['/mobile/auth', '/mobile/transactions'], 'visibility' => 'public', 'rate_limit' => '400 req/min'],
    9 => ['name' => 'Payment Gateway Integration', 'status' => 'active', 'version' => '1.8.3', 'uptime' => '99.98%',
          'description' => 'Unified interface for SWIFT gpi, EFT/RTGS, and instant payment systems.',
          'endpoints' => ['/payments/initiate', '/payments/{id}/status'], 'visibility' => 'public', 'rate_limit' => '100 req/min'],
    10 => ['name' => 'KYC/AML Verification', 'status' => 'active', 'version' => '1.4.2', 'uptime' => '99.94%',
           'description' => 'Customer identity verification, sanctions screening, and risk scoring.',
           'endpoints' => ['/kyc/verify', '/kyc/screening', '/kyc/risk-score'], 'visibility' => 'public', 'rate_limit' => '50 req/min'],
    11 => ['name' => 'Internal Admin Override', 'status' => 'active', 'version' => '0.9.1-internal', 'uptime' => '100%',
           'description' => 'Administrative override service for emergency system configuration.',
           'endpoints' => ['/admin/override', '/admin/config', '/admin/emergency-stop'], 'visibility' => 'restricted', 'rate_limit' => '10 req/min']
];

if ($id < 1 || $id > 11) {
    echo json_encode(['error' => 'Invalid service ID', 'message' => 'Valid range: 1-11', 'total_services' => 11]);
    exit;
}

$svc = $services[$id];

if ($svc['visibility'] === 'restricted' && $key !== 'priority_override') {
    echo json_encode([
        'service_id' => $id,
        'name' => $svc['name'],
        'visibility' => 'restricted',
        'message' => 'Restricted service. Authorized access key required.',
        'status' => 'access_denied'
    ]);
    exit;
}

$response = [
    'service_id' => $id,
    'name' => $svc['name'],
    'status' => $svc['status'],
    'version' => $svc['version'],
    'uptime' => $svc['uptime'],
    'description' => $svc['description'],
    'endpoints' => $svc['endpoints'],
    'visibility' => $svc['visibility'] === 'public' ? 'Visible to normal users' : 'Restricted - authorized access only',
    'rate_limit' => $svc['rate_limit']
];

if ($id === 11 && $key === 'priority_override') {
    $response['access_granted'] = true;
    $response['internal_ref'] = 'ADNSU6{api_enumeration_access_bypass_6e2d}';
    $response['linked_services'] = [
        'vault' => '/novabank-internal-7f2c9/vault/',
        'graphql' => '/nb-gql-svc-e8a1/'
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
