<?php
header('Content-Type: application/json; charset=utf-8');

$default_config = [
    'app_name' => 'NovaBank',
    'version' => '3.2.1',
    'debug' => false,
    'admin_access' => false,
    'maintenance_mode' => false,
    'log_level' => 'error',
    'max_transfer' => 100000,
    'currency' => 'TRY'
];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    echo json_encode([
        'service' => 'Configuration API',
        'endpoints' => [
            'GET ?key=<config_key>' => 'Read a configuration value',
            'PATCH' => 'Update configuration (JSON body with key-value pairs)',
        ],
        'available_keys' => array_keys($default_config),
        'note' => 'Some keys are read-only in production'
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['key'])) {
    $key = $_GET['key'];
    if (isset($default_config[$key])) {
        echo json_encode(['key' => $key, 'value' => $default_config[$key]]);
    } else {
        echo json_encode(['error' => 'Unknown key: ' . $key]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        echo json_encode(['error' => 'Invalid JSON body']);
        exit;
    }

    $config = $default_config;

    foreach ($input as $key => $value) {
        if ($key === '__proto__' && is_array($value)) {
            foreach ($value as $pk => $pv) {
                $config[$pk] = $pv;
            }
            continue;
        }
        if ($key === 'constructor' && is_array($value) && isset($value['prototype'])) {
            foreach ($value['prototype'] as $pk => $pv) {
                $config[$pk] = $pv;
            }
            continue;
        }
        $config[$key] = $value;
    }

    $response = [
        'success' => true,
        'updated_config' => $config
    ];

    if (isset($config['admin_access']) && $config['admin_access'] === true &&
        isset($config['debug']) && $config['debug'] === true) {
        $response['_internal'] = [
            'flag' => 'ADNSU29{prototype_pollution_config_override_7c2a}',
            'note' => 'Debug + Admin access combination triggered through object pollution',
            'leaked_secrets' => [
                'jwt_key' => 'n0v4b4nk_jwt_s3cr3t_k3y_2026',
                'hmac_key' => 'adnsu7floor',
                'db_path' => '/var/data/novabank.db'
            ]
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

echo json_encode(['error' => 'Method not allowed'], JSON_PRETTY_PRINT);
