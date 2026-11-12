<?php
header('Content-Type: application/json; charset=utf-8');

$auth = $_GET['auth'] ?? $_POST['auth'] ?? '';
if ($auth !== 'vlt_9k3mz_gql') {
    http_response_code(403);
    echo json_encode([
        'error' => 'Unauthorized',
        'message' => 'Valid API access token required.',
        'usage' => '?auth=<access_token>',
        'info' => 'NovaBank GraphQL Service v2.1.0'
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'service' => 'NovaBank GraphQL API',
        'version' => '2.1.0',
        'status' => 'active',
        'methods' => ['POST'],
        'content_type' => 'application/json',
        'documentation' => 'Send POST requests with {"query":"..."} body. Include ?auth= in URL.'
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = @file_get_contents('php://input');
$body = @json_decode($input, true);

if (!$body || !isset($body['query'])) {
    if (isset($_POST['query'])) {
        $body = ['query' => $_POST['query']];
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing query parameter', 'usage' => '{"query":"{ __schema { types { name } } }"}']);
        exit;
    }
}

$query = trim($body['query']);

$schema = [
    'types' => [
        ['name' => 'Query', 'kind' => 'OBJECT', 'description' => 'Root query type'],
        ['name' => 'Mutation', 'kind' => 'OBJECT', 'description' => 'Root mutation type'],
        ['name' => 'User', 'kind' => 'OBJECT', 'description' => 'Bank user account'],
        ['name' => 'Transaction', 'kind' => 'OBJECT', 'description' => 'Financial transaction record'],
        ['name' => 'InternalService', 'kind' => 'OBJECT', 'description' => 'Internal microservice registry'],
        ['name' => 'AuditLog', 'kind' => 'OBJECT', 'description' => 'System audit trail'],
        ['name' => 'String', 'kind' => 'SCALAR', 'description' => null],
        ['name' => 'Int', 'kind' => 'SCALAR', 'description' => null],
        ['name' => 'Boolean', 'kind' => 'SCALAR', 'description' => null],
    ],
    'queryType' => ['name' => 'Query'],
    'mutationType' => ['name' => 'Mutation'],
];

$query_fields = [
    [
        'name' => 'users',
        'description' => 'List registered users (sanitized)',
        'args' => [['name' => 'limit', 'type' => 'Int'], ['name' => 'offset', 'type' => 'Int']],
        'type' => ['name' => 'User', 'kind' => 'LIST']
    ],
    [
        'name' => 'transactions',
        'description' => 'Recent transaction records',
        'args' => [['name' => 'account_id', 'type' => 'String']],
        'type' => ['name' => 'Transaction', 'kind' => 'LIST']
    ],
    [
        'name' => 'auditLog',
        'description' => 'System audit entries',
        'args' => [['name' => 'severity', 'type' => 'String']],
        'type' => ['name' => 'AuditLog', 'kind' => 'LIST']
    ],
    [
        'name' => 'systemStatus',
        'description' => 'Current system health status',
        'args' => [],
        'type' => ['name' => 'String', 'kind' => 'SCALAR']
    ]
];

$mutation_fields = [
    [
        'name' => 'verifyAccess',
        'description' => 'Verify elevated access with authorization code',
        'args' => [['name' => 'code', 'type' => 'String', 'description' => 'Format: NB-OVERRIDE-{vault_id}']],
        'type' => ['name' => 'String', 'kind' => 'SCALAR']
    ],
    [
        'name' => 'getInternalPath',
        'description' => 'Resolve internal service path by name',
        'args' => [['name' => 'service', 'type' => 'String']],
        'type' => ['name' => 'InternalService', 'kind' => 'OBJECT']
    ],
    [
        'name' => 'rotateApiKey',
        'description' => 'Rotate API key for a service (admin only)',
        'args' => [['name' => 'service_id', 'type' => 'Int']],
        'type' => ['name' => 'String', 'kind' => 'SCALAR']
    ]
];

if (preg_match('/\{\s*__schema\s*\{/', $query)) {
    $response = ['data' => ['__schema' => []]];

    if (strpos($query, 'types') !== false) {
        $response['data']['__schema']['types'] = $schema['types'];
        $response['data']['__schema']['queryType'] = $schema['queryType'];
        $response['data']['__schema']['mutationType'] = $schema['mutationType'];
    }

    if (strpos($query, 'queryType') !== false && strpos($query, 'fields') !== false) {
        $response['data']['__schema']['queryType'] = [
            'name' => 'Query',
            'fields' => $query_fields
        ];
    }

    if (strpos($query, 'mutationType') !== false && strpos($query, 'fields') !== false) {
        $response['data']['__schema']['mutationType'] = [
            'name' => 'Mutation',
            'fields' => $mutation_fields
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if (preg_match('/\{\s*__type\s*\(\s*name\s*:\s*"(\w+)"\s*\)/', $query, $m)) {
    $type_name = $m[1];
    $type_defs = [
        'User' => [
            'name' => 'User',
            'kind' => 'OBJECT',
            'fields' => [
                ['name' => 'id', 'type' => 'Int'],
                ['name' => 'name', 'type' => 'String'],
                ['name' => 'email', 'type' => 'String'],
                ['name' => 'role', 'type' => 'String'],
                ['name' => 'status', 'type' => 'String']
            ]
        ],
        'InternalService' => [
            'name' => 'InternalService',
            'kind' => 'OBJECT',
            'fields' => [
                ['name' => 'name', 'type' => 'String'],
                ['name' => 'path', 'type' => 'String'],
                ['name' => 'status', 'type' => 'String'],
                ['name' => 'access_level', 'type' => 'String']
            ]
        ],
        'Transaction' => [
            'name' => 'Transaction',
            'kind' => 'OBJECT',
            'fields' => [
                ['name' => 'id', 'type' => 'Int'],
                ['name' => 'amount', 'type' => 'String'],
                ['name' => 'from_account', 'type' => 'String'],
                ['name' => 'to_account', 'type' => 'String'],
                ['name' => 'date', 'type' => 'String'],
                ['name' => 'flag', 'type' => 'String']
            ]
        ]
    ];

    if (isset($type_defs[$type_name])) {
        echo json_encode(['data' => ['__type' => $type_defs[$type_name]]], JSON_PRETTY_PRINT);
    } else {
        echo json_encode(['data' => ['__type' => null], 'errors' => [['message' => "Type '$type_name' not found"]]]);
    }
    exit;
}

if (preg_match('/mutation\s*\{?\s*verifyAccess\s*\(\s*code\s*:\s*"([^"]+)"\s*\)/', $query, $m)) {
    $code = $m[1];
    if ($code === 'NB-OVERRIDE-7f2c9') {
        echo json_encode([
            'data' => [
                'verifyAccess' => [
                    'status' => 'ACCESS_GRANTED',
                    'flag' => 'ADNSU9{graphql_introspection_abuse_88fa}',
                    'message' => 'Elevated access verified. Internal services unlocked.'
                ]
            ]
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'data' => ['verifyAccess' => null],
            'errors' => [['message' => 'Invalid authorization code. Access denied.', 'code' => 'AUTH_FAILED']]
        ], JSON_PRETTY_PRINT);
    }
    exit;
}

if (preg_match('/mutation\s*\{?\s*getInternalPath\s*\(\s*service\s*:\s*"([^"]+)"\s*\)/', $query, $m)) {
    $service = strtolower($m[1]);
    $services = [
        'forum' => [
            'name' => 'Internal Communications Forum',
            'path' => '/nb-internal-comm-8f3a/',
            'status' => 'active',
            'access_level' => 'internal',
            'note' => 'Employee discussion board - requires GraphQL auth session'
        ],
        'monitoring' => [
            'name' => 'System Monitoring',
            'path' => '/monitoring/',
            'status' => 'maintenance',
            'access_level' => 'admin'
        ],
        'logs' => [
            'name' => 'Application Logs',
            'path' => '/logs/',
            'status' => 'active',
            'access_level' => 'public'
        ],
        'vault' => [
            'name' => 'Classified Vault',
            'path' => '/novabank-internal-7f2c9/vault/',
            'status' => 'active',
            'access_level' => 'restricted'
        ]
    ];

    if (isset($services[$service])) {
        echo json_encode([
            'data' => ['getInternalPath' => $services[$service]]
        ], JSON_PRETTY_PRINT);
    } else {
        $available = array_keys($services);
        echo json_encode([
            'data' => ['getInternalPath' => null],
            'errors' => [['message' => "Service '$service' not found. Available: " . implode(', ', $available)]]
        ], JSON_PRETTY_PRINT);
    }
    exit;
}

if (preg_match('/mutation\s*\{?\s*rotateApiKey/', $query)) {
    echo json_encode([
        'data' => ['rotateApiKey' => null],
        'errors' => [['message' => 'Insufficient privileges. Admin role required.', 'code' => 'FORBIDDEN']]
    ], JSON_PRETTY_PRINT);
    exit;
}

if (preg_match('/\{\s*users/', $query)) {
    echo json_encode([
        'data' => [
            'users' => [
                ['id' => 1, 'name' => 'K. Y*****m', 'email' => '[REDACTED]', 'role' => 'admin', 'status' => 'active'],
                ['id' => 2, 'name' => 'E. K***a', 'email' => '[REDACTED]', 'role' => 'user', 'status' => 'active'],
                ['id' => 3, 'name' => 'H. S***r', 'email' => '[REDACTED]', 'role' => 'priority_admin', 'status' => 'active']
            ]
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

if (preg_match('/\{\s*transactions/', $query)) {
    echo json_encode([
        'data' => [
            'transactions' => [
                ['id' => 1, 'amount' => '9,980.00 TRY', 'from_account' => 'NBK-CORP-044', 'to_account' => 'CYP-8827134', 'date' => '2025-09-14', 'flag' => 'STRUCTURING'],
                ['id' => 2, 'amount' => '245,000.00 EUR', 'from_account' => 'NBK-CORP-044', 'to_account' => 'CYP-8827134', 'date' => '2025-10-02', 'flag' => 'OFFSHORE']
            ]
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

if (preg_match('/\{\s*systemStatus/', $query)) {
    echo json_encode([
        'data' => ['systemStatus' => 'All systems operational. GraphQL API v2.1.0']
    ], JSON_PRETTY_PRINT);
    exit;
}

echo json_encode([
    'errors' => [[
        'message' => 'Unrecognized query. Use introspection to discover available types and fields.',
        'supported_operations' => ['query', 'mutation']
    ]]
], JSON_PRETTY_PRINT);
