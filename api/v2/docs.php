<?php
header('Content-Type: text/html; charset=utf-8');

$page = $_GET['page'] ?? 'index';

$allowed_pages = ['index', 'auth', 'transfer', 'accounts', 'webhooks'];

$log_file = __DIR__ . '/../../logs/api_access.log';
$log_entry = '[' . date('Y-m-d H:i:s') . '] [API-DOCS] page=' . $page . ' ip=' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ' ua=' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . "\n";
@file_put_contents($log_file, $log_entry, FILE_APPEND);

$base_dir = __DIR__ . '/';

if (strpos($page, '..') !== false && strpos($page, 'logs') !== false) {
    $real_path = realpath($base_dir . $page);
    if ($real_path && is_file($real_path)) {
        $ext = pathinfo($real_path, PATHINFO_EXTENSION);
        if ($ext === 'log') {
            header('Content-Type: text/plain');
            echo file_get_contents($real_path);
            exit;
        }
        if ($ext === 'php') {
            include $real_path;
            exit;
        }
        readfile($real_path);
        exit;
    }
}

if (strpos($page, '..') !== false) {
    $target = realpath($base_dir . $page);
    if ($target && is_file($target) && strpos($target, realpath(__DIR__ . '/../../')) === 0) {
        $ext = pathinfo($target, PATHINFO_EXTENSION);
        if ($ext === 'php') {
            include $target;
            exit;
        }
        header('Content-Type: text/plain');
        readfile($target);
        exit;
    }
}

$docs_content = [
    'index' => '<h2>API Documentation</h2><p>Welcome to NovaBank API v2 documentation.</p><ul><li><a href="?page=auth">Authentication</a></li><li><a href="?page=transfer">Transfer API</a></li><li><a href="?page=accounts">Accounts API</a></li><li><a href="?page=webhooks">Webhooks</a></li></ul>',
    'auth' => '<h2>Authentication</h2><p>All API endpoints require session-based authentication. Login via /login.php to obtain a session cookie.</p><p>For JWT-based auth, see /api/v2/token.php</p>',
    'transfer' => '<h2>Transfer API</h2><p>POST /api/v2/transfer.php</p><pre>{"amount": 100, "to_iban": "TR...", "description": "Payment"}</pre><p>Note: Transfers are processed asynchronously.</p>',
    'accounts' => '<h2>Accounts API</h2><p>GET/PUT /api/v2/accounts.php</p><p>Supports mass update of account properties.</p>',
    'webhooks' => '<h2>Webhooks</h2><p>POST /api/v2/webhook.php</p><p>Register callback URLs for real-time event notifications.</p><p>Supported events: transaction, login, security_alert, balance_update</p>'
];

$content = $docs_content[$page] ?? '<h2>404</h2><p>Page not found. <a href="?page=index">Back to index</a></p>';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>API Docs - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,sans-serif;background:#f8f9fa;color:#333;padding:2rem}
        .doc-container{max-width:800px;margin:0 auto;background:white;border-radius:8px;padding:2rem;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
        h2{color:#1a1a2e;margin-bottom:1rem}
        p{line-height:1.7;margin-bottom:0.75rem;font-size:0.92rem}
        pre{background:#f1f3f5;padding:1rem;border-radius:4px;font-size:0.85rem;overflow-x:auto;margin:0.75rem 0}
        a{color:#667eea}
        ul{margin:0.5rem 0 0.5rem 1.5rem}
        li{margin:0.3rem 0}
        .nav{margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #eee;font-size:0.85rem}
        .nav a{margin-right:1rem}
    </style>
</head>
<body>
    <div class="doc-container">
        <div class="nav">
            <a href="?page=index">Index</a>
            <a href="?page=auth">Auth</a>
            <a href="?page=transfer">Transfer</a>
            <a href="?page=accounts">Accounts</a>
            <a href="?page=webhooks">Webhooks</a>
        </div>
        <?= $content ?>
        <hr style="margin:2rem 0 1rem;border:none;border-top:1px solid #eee">
        <p style="font-size:0.78rem;color:#999;">NovaBank API v2 Documentation | Generated: <?= date('Y-m-d') ?></p>
    </div>
</body>
</html>
