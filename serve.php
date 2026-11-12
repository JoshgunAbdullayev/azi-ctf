<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

$blocked = [
    '#\.sqlite3?$#i',
    '#\.db$#i',
    '#^(?!/backups/).*\.sql$#i',
    '#^/private/#i',
    '#/\.(?!htaccess|well-known)#',
    '#^/router\.php$#i',
    '#^/nb-adm-f7d3/data/#i',
    '#^/nb-adm-f7d3/dashboard_view\.php$#i',
    // Scoreboard internals (flag answers + includes must stay secret)
    '#^/scoreboard/(flags\.json|inc/)#i',
    // ACT II (Account Takeover chain) state + signing key
    '#^/nb-id-recover-a3f9/_act2_boot\.php$#i',
    '#^/nb-id-recover-a3f9/act2\.sqlite#i',
    '#^/nb-jwks-vault-9d4c/keys/#i',
];

foreach ($blocked as $pattern) {
    if (preg_match($pattern, $path)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head>';
        echo '<body><h1>Forbidden</h1><p>You don\'t have permission to access this resource.</p></body></html>';
        return true;
    }
}

if (preg_match('#\.log$#i', $path)) {
    $file = __DIR__ . $path;
    if (file_exists($file) && is_file($file)) {
        header('Content-Type: text/plain; charset=utf-8');
        readfile($file);
        return true;
    }
}

return false;
