<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['vault_auth']) || $_SESSION['vault_auth'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed', 'allowed' => 'POST']);
    exit;
}

$query = $_POST['q'] ?? '';

$results = [];
if (strlen($query) > 0) {
    $docs = [
        ['id' => 'DOC-001', 'title' => 'MASAK Alert #047 - Transaction Pattern Analysis', 'relevance' => 0.2],
        ['id' => 'DOC-002', 'title' => 'Internal Communications Intercept Log', 'relevance' => 0.15],
        ['id' => 'DOC-003', 'title' => 'Offshore Transfer Route Mapping', 'relevance' => 0.1],
    ];
    foreach ($docs as $d) {
        if (stripos($d['title'], $query) !== false) {
            $results[] = $d;
        }
    }
}

echo json_encode([
    'query' => $query,
    'results' => $results,
    'total' => count($results),
    'message' => count($results) > 0 ? count($results) . ' result(s) found.' : 'No results found.',
    'engine' => [
        'name' => 'NovaBank DocSearch',
        'version' => '1.2',
        'backend' => 'GraphQL-powered search index',
        'api_endpoint' => '/nb-gql-svc-e8a1/',
        'index_status' => 'synchronized',
        'debug' => false
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
