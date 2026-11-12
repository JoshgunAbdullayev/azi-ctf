<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['moderator_auth']) || $_SESSION['moderator_auth'] !== true) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Moderator authentication required']);
    exit;
}

header('Content-Type: text/html; charset=utf-8');

$report = $_GET['report'] ?? $_POST['report'] ?? '';

if (empty($report)) {
    echo '<!DOCTYPE html><html><head><title>Export Service</title></head><body style="font-family:monospace;background:#0d1117;color:#c9d1d9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="background:#161b22;border:1px solid #30363d;border-radius:8px;padding:2rem;max-width:500px"><h2 style="color:#58a6ff;margin-bottom:1rem">Report Export Service</h2><p style="color:#8b949e;font-size:0.85rem">Usage: <code style="background:#21262d;padding:0.2rem 0.5rem;border-radius:3px">?report=monthly</code></p><p style="color:#484f58;font-size:0.8rem;margin-top:0.75rem">The service fetches report data from internal endpoints.</p></div></body></html>';
    exit;
}

$ssrf_responses = [
    'http://127.0.0.1' => '<html><title>NovaBank Internal</title><body><h1>NovaBank Internal Server</h1><p>Available internal services:</p><ul><li>/internal-config - Configuration service</li><li>/nb-adm-f7d3 - System administration</li><li>/debug - Debug information</li></ul></body></html>',

    'http://127.0.0.1/' => '<html><title>NovaBank Internal</title><body><h1>NovaBank Internal Server</h1><p>Available internal services:</p><ul><li>/internal-config - Configuration service</li><li>/nb-adm-f7d3 - System administration</li><li>/debug - Debug information</li></ul></body></html>',

    'http://localhost' => '<html><title>NovaBank Internal</title><body><h1>NovaBank Internal Server</h1><p>Available internal services:</p><ul><li>/internal-config - Configuration service</li><li>/nb-adm-f7d3 - System administration</li><li>/debug - Debug information</li></ul></body></html>',

    'http://127.0.0.1/internal-config' => json_encode([
        'service' => 'auth-internal',
        'admin_user' => 'root_admin',
        'admin_pass' => 'n0v4_r00t_2026!',
        'database' => 'sqlite:///var/data/novabank.db',
        'hmac_secret' => '[REDACTED]',
        'note' => 'Production credentials - do not expose',
        'admin_endpoint' => '/nb-adm-f7d3/'
    ], JSON_PRETTY_PRINT),

    'http://localhost/internal-config' => json_encode([
        'service' => 'auth-internal',
        'admin_user' => 'root_admin',
        'admin_pass' => 'n0v4_r00t_2026!',
        'database' => 'sqlite:///var/data/novabank.db',
        'hmac_secret' => '[REDACTED]',
        'note' => 'Production credentials - do not expose',
        'admin_endpoint' => '/nb-adm-f7d3/'
    ], JSON_PRETTY_PRINT),

    'http://127.0.0.1/nb-adm-f7d3' => '<html><title>Admin Login</title><body><h1>NovaBank Administration</h1><p>Authentication required. Use internal credentials.</p></body></html>',

    'http://127.0.0.1/debug' => json_encode([
        'server' => 'NovaBank/2.1.0',
        'php_version' => '8.0.30',
        'debug_mode' => true,
        'internal_services' => [
            'report-service' => 'http://report-service:8080',
            'auth-service' => 'http://127.0.0.1/internal-config',
            'admin-panel' => 'http://127.0.0.1/nb-adm-f7d3'
        ],
        'environment' => 'production'
    ], JSON_PRETTY_PRINT),

    'http://report-service' => '{"status":"ok","service":"report-service","version":"1.0.3","endpoints":["/monthly","/quarterly","/annual"]}',
    'http://report-service/' => '{"status":"ok","service":"report-service","version":"1.0.3","endpoints":["/monthly","/quarterly","/annual"]}',
    'http://report-service/monthly' => '{"report":"monthly_compliance","period":"2026-02","total_transactions":12847,"flagged":23,"auto_reported":0,"note":"Auto-reporting to MASAK has been disabled by administrator"}',
];

$is_url = preg_match('#^https?://#i', $report);

if ($is_url) {
    $parsed = parse_url($report);
    $host = strtolower($parsed['host'] ?? '');

    $ssrf_blacklist = ['127.0.0.1', 'localhost', '0.0.0.0', '::1', '0177.0.0.1', '2130706433'];
    $ssrf_blocked = false;
    foreach ($ssrf_blacklist as $blocked_host) {
        if ($host === $blocked_host) {
            $ssrf_blocked = true;
            break;
        }
    }
    if (preg_match('/^10\.|^172\.(1[6-9]|2\d|3[01])\.|^192\.168\./', $host)) {
        $ssrf_blocked = true;
    }

    if ($ssrf_blocked) {
        echo '<!DOCTYPE html><html><head><title>Blocked</title></head><body style="font-family:monospace;background:#0d1117;color:#f85149;padding:2rem"><h2>⛔ SSRF Protection</h2><p style="color:#8b949e;margin-top:1rem">Request to internal/private IP address blocked by security policy.</p><p style="color:#484f58;margin-top:0.5rem;font-size:0.85rem">Host: <code>' . htmlspecialchars($host) . '</code> — Restricted destination. [NB-SSRF-GUARD v2.0]</p></body></html>';
        exit;
    }

    $resolved_host = $host;
    $ip_aliases = [
        '0x7f000001' => '127.0.0.1',
        '017700000001' => '127.0.0.1',
        '127.1' => '127.0.0.1',
        '127.0.1' => '127.0.0.1',
        '0' => '127.0.0.1',
        '[::1]' => '127.0.0.1',
        'localtest.me' => '127.0.0.1',
        'spoofed.burpcollaborator.net' => '127.0.0.1',
        '0x7f.0x0.0x0.0x1' => '127.0.0.1',
        '0177.0.0.01' => '127.0.0.1',
        '2130706433' => '127.0.0.1',
    ];
    if (isset($ip_aliases[$host])) {
        $resolved_host = $ip_aliases[$host];
    } elseif (preg_match('/^0x[0-9a-f]+$/i', $host)) {
        $dec = hexdec($host);
        $resolved_host = long2ip($dec);
    } elseif (is_numeric($host)) {
        $resolved_host = long2ip(intval($host));
    }

    $effective_url = ($resolved_host === '127.0.0.1') ?
        'http://127.0.0.1' . ($parsed['path'] ?? '') :
        'http://' . $host . ($parsed['path'] ?? '');

    $clean_report = rtrim($effective_url, '/');
    $found = false;

    foreach ($ssrf_responses as $url => $response) {
        $clean_url = rtrim($url, '/');
        if (strcasecmp($clean_report, $clean_url) === 0) {
            echo '<!DOCTYPE html><html><head><title>Export Result</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:monospace;background:#0d1117;color:#c9d1d9;min-height:100vh;padding:2rem}.header{background:#161b22;border:1px solid #30363d;border-radius:8px 8px 0 0;padding:1rem;font-size:0.82rem;color:#8b949e}.header strong{color:#58a6ff}.content{background:#0d1117;border:1px solid #30363d;border-top:none;border-radius:0 0 8px 8px;padding:1.5rem;font-size:0.85rem;line-height:1.6;white-space:pre-wrap;word-break:break-all}.back{display:inline-block;margin-top:1rem;color:#58a6ff;text-decoration:none;font-size:0.82rem}</style></head><body>';
            echo '<div class="header">Fetching from: <strong>' . htmlspecialchars($report) . '</strong><br>Resolved: <strong>' . htmlspecialchars($effective_url) . '</strong><br>Status: <span style="color:#3fb950">200 OK</span></div>';
            echo '<div class="content">' . htmlspecialchars($response) . '</div>';
            echo '</body></html>';
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo '<!DOCTYPE html><html><head><title>Export Error</title></head><body style="font-family:monospace;background:#0d1117;color:#f85149;padding:2rem"><h2>Connection Error</h2><p style="color:#8b949e;margin-top:1rem">Could not connect to: <strong style="color:#c9d1d9">' . htmlspecialchars($report) . '</strong></p><p style="color:#484f58;margin-top:0.5rem;font-size:0.85rem">Resolved to: ' . htmlspecialchars($effective_url) . '</p><p style="color:#484f58;font-size:0.8rem;margin-top:0.5rem">Only whitelisted report services can be accessed. Try: http://report-service</p></body></html>';
    }
    exit;
}

$report_data = [
    'monthly' => [
        'title' => 'Aylıq Uyğunluq Hesabatı - Fevral 2026',
        'data' => [
            'Ümumi Əməliyyat: 12,847',
            'Şübhəli Əməliyyat: 23 (avtomatik hesabat DEAKTİV)',
            'MASAK Bildirişi: 0 (admin tərəfindən dayandırıldı)',
            'Yeni Hesab: 342',
            'Bağlanmış Hesab: 18',
            'Offshore Köçürmə: 5 (ümumi 1.2M EUR)',
            'Qeyd: Hesabat servisi bağlantı URL\'si dəyişdirilə bilər'
        ]
    ],
    'quarterly' => [
        'title' => 'Rüblük Hesabat - Q1 2026',
        'data' => ['Hesabat hələ yaradılmayıb.']
    ]
];

$rd = $report_data[$report] ?? null;
if (!$rd) {
    echo '<!DOCTYPE html><html><head><title>Export</title></head><body style="font-family:monospace;background:#0d1117;color:#c9d1d9;padding:2rem"><h2 style="color:#d29922">Report Not Found</h2><p style="color:#8b949e;margin-top:1rem">Report "' . htmlspecialchars($report) . '" not found.</p><p style="color:#484f58;font-size:0.85rem;margin-top:0.5rem">Available: monthly, quarterly. The report parameter also accepts URLs for remote report services (e.g., http://report-service/monthly).</p></body></html>';
    exit;
}

echo '<!DOCTYPE html><html><head><title>Export - ' . htmlspecialchars($rd['title']) . '</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:monospace;background:#0d1117;color:#c9d1d9;min-height:100vh;padding:2rem}.card{background:#161b22;border:1px solid #30363d;border-radius:8px;padding:1.5rem;max-width:700px}h2{color:#58a6ff;font-size:1rem;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid #21262d}li{padding:0.4rem 0;font-size:0.85rem;color:#8b949e;border-bottom:1px solid #161b22}li:last-child{border:none}</style></head><body>';
echo '<div class="card"><h2>' . htmlspecialchars($rd['title']) . '</h2><ul style="list-style:none">';
foreach ($rd['data'] as $line) {
    echo '<li>' . htmlspecialchars($line) . '</li>';
}
echo '</ul></div></body></html>';
