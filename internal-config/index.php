<?php
header('Content-Type: application/json; charset=utf-8');

http_response_code(403);
echo json_encode([
    'error' => 'Direct access forbidden',
    'message' => 'This endpoint is only accessible from internal services.',
    'allowed_sources' => ['127.0.0.1', 'report-service', 'auth-service'],
    'status' => 403
], JSON_PRETTY_PRINT);
