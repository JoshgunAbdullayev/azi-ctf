<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'service' => 'NovaBank REST API',
    'version' => '2.0.0-beta',
    'status' => 'active',
    'endpoints' => [
        'GET /api/v2/accounts.php' => 'List all accounts',
        'GET /api/v2/accounts.php?id={id}' => 'Get account details',
        'PUT /api/v2/accounts.php' => 'Update account (JSON body)',
        'POST /api/v2/token.php' => 'Generate JWT auth token',
        'GET /api/v2/verify.php?token={jwt}' => 'Verify JWT token',
        'POST /api/v2/import.php' => 'Import data (XML format)',
    ],
    'authentication' => 'Session-based or JWT Bearer token',
    'note' => 'API v2 is in beta. Some endpoints may have relaxed validation.'
], JSON_PRETTY_PRINT);
