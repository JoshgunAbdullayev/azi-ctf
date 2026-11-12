<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: text/html; charset=utf-8');

$api_key = $_GET['key'] ?? $_POST['key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
$authenticated = ($api_key === 'nb_internal_api_8f3k2m9x');

if (!$authenticated) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Panel - Authentication Required</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0f0f23;color:#cccccc;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .box{background:#10101a;border:1px solid #333;border-radius:8px;padding:2.5rem;max-width:500px;width:90%}
        h1{color:#00cc00;font-size:1.2rem;margin-bottom:0.5rem}
        .sub{color:#666;font-size:0.82rem;margin-bottom:2rem}
        .form-group{margin-bottom:1rem}
        label{display:block;color:#888;font-size:0.78rem;margin-bottom:0.3rem;text-transform:uppercase}
        input{width:100%;padding:0.6rem;background:#1a1a2e;border:1px solid #333;color:#00cc00;font-family:inherit;font-size:0.85rem;border-radius:4px;outline:none}
        input:focus{border-color:#00cc00}
        .btn{width:100%;padding:0.6rem;background:#003300;color:#00cc00;border:1px solid #00cc00;font-family:inherit;cursor:pointer;border-radius:4px;margin-top:0.5rem}
        .hint{color:#444;font-size:0.75rem;margin-top:1.5rem;text-align:center}
    </style>
</head>
<body>
<div class="box">
    <h1>NovaBank Debug Panel</h1>
    <div class="sub">[ Internal Development Tool - Restricted Access ]</div>
    <form method="GET">
        <div class="form-group">
            <label>API Key</label>
            <input type="text" name="key" placeholder="Enter internal API key" autocomplete="off">
        </div>
        <button type="submit" class="btn">Authenticate</button>
    </form>
    <div class="hint">
        Authentication required. Access restricted to internal operations team.
    </div>
</div>
</body>
</html>
<?php
    exit;
}

$_SESSION['debug_auth'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Panel - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0f0f23;color:#cccccc;min-height:100vh}
        .container{max-width:960px;margin:0 auto;padding:2rem}
        .header{border-bottom:1px solid #333;padding-bottom:1rem;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center}
        .title{color:#00cc00;font-size:1.2rem}
        .card{background:#10101a;border:1px solid #333;border-radius:6px;padding:1.5rem;margin-bottom:1.5rem}
        .card h2{color:#ffcc00;font-size:0.95rem;margin-bottom:1rem;border-bottom:1px solid #222;padding-bottom:0.5rem}
        .flag-box{background:#003300;border:2px solid #00cc00;border-radius:6px;padding:1.25rem;text-align:center;margin-bottom:2rem}
        .flag{color:#00ff00;font-size:1.2rem;font-weight:bold;text-shadow:0 0 10px #00ff00}
        pre{background:#0a0a1a;border:1px solid #222;border-radius:4px;padding:1rem;font-size:0.82rem;overflow-x:auto;line-height:1.6;color:#aaa}
        .key{color:#00cc00}
        .val{color:#ffcc00}
        .comment{color:#555}
        table{width:100%;border-collapse:collapse;font-size:0.82rem}
        th{text-align:left;padding:0.5rem;border-bottom:1px solid #333;color:#00cc00}
        td{padding:0.5rem;border-bottom:1px solid #1a1a2e}
        tr:hover{background:#1a1a2e}
        .critical{color:#ff4444}
        .warn{color:#ffcc00}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <span class="title">NovaBank Debug Panel v3.2.1</span>
        <span style="color:#00cc00;font-size:0.82rem;">Authenticated | API Key: ***8f3k2m9x</span>
    </div>

    <div class="flag-box">
        <div style="color:#006600;font-size:0.8rem;margin-bottom:0.25rem;">DEBUG PANEL ACCESS GRANTED</div>
        <div class="flag">ADNSU17{debug_panel_api_key_exposure_3f7b}</div>
    </div>

    <div class="card">
        <h2>Application Configuration</h2>
        <pre>
<span class="key">app.name</span>          = <span class="val">NovaBank Core Banking Platform</span>
<span class="key">app.version</span>       = <span class="val">3.2.1-beta</span>
<span class="key">app.environment</span>   = <span class="val critical">production</span>  <span class="comment">// WARNING: debug panel active in production!</span>
<span class="key">app.debug</span>         = <span class="val critical">true</span>

<span class="key">db.driver</span>         = <span class="val">sqlite</span>
<span class="key">db.path</span>           = <span class="val">/var/data/novabank.db</span>

<span class="key">auth.jwt_secret</span>   = <span class="val">n0v4b4nk_jwt_s3cr3t_k3y_2026</span>
<span class="key">auth.hmac_secret</span>  = <span class="val">adnsu7floor</span>
<span class="key">auth.session_ttl</span>  = <span class="val">1440</span>

<span class="key">api.master_key</span>    = <span class="val">nb_internal_api_8f3k2m9x</span>
<span class="key">api.rate_limit</span>    = <span class="val">100 req/min</span>

<span class="key">backup.enc_key</span>    = <span class="val">NB_BKP_2026_AES256_K3Y!</span>
<span class="key">backup.path</span>       = <span class="val">/var/backups/novabank/</span>

<span class="key">mail.smtp_host</span>    = <span class="val">smtp.novabank.com.tr</span>
<span class="key">mail.smtp_port</span>    = <span class="val">587</span>
        </pre>
    </div>

    <div class="card">
        <h2>Active Sessions</h2>
        <table>
            <thead><tr><th>Session ID</th><th>User</th><th>Role</th><th>IP</th><th>Last Activity</th></tr></thead>
            <tbody>
                <tr><td>sess_root_a4f8</td><td>root_admin</td><td class="critical">superadmin</td><td>10.0.1.5</td><td><?= date('Y-m-d H:i', time() - 300) ?></td></tr>
                <tr><td>sess_hakan_7b2c</td><td>Hakan Şeker</td><td class="warn">priority_admin</td><td>10.0.1.12</td><td><?= date('Y-m-d H:i', time() - 1800) ?></td></tr>
                <tr><td>sess_kerem_c4e5</td><td>Kerem Yıldırım</td><td>admin</td><td>10.0.1.3</td><td><?= date('Y-m-d H:i', time() - 7200) ?></td></tr>
                <tr><td>sess_debug_<?= substr(md5(session_id()), 0, 4) ?></td><td>debug_user</td><td class="warn">debug</td><td><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?></td><td><?= date('Y-m-d H:i') ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Internal Service Map</h2>
        <table>
            <thead><tr><th>Service</th><th>Endpoint</th><th>Status</th><th>Auth</th></tr></thead>
            <tbody>
                <tr><td>Admin Panel</td><td>/nb-adm-f7d3/</td><td style="color:#00cc00;">Active</td><td>root_admin / n0v4_r00t_2026!</td></tr>
                <tr><td>GraphQL API</td><td>/nb-gql-svc-e8a1/</td><td style="color:#00cc00;">Active</td><td>?auth=vlt_9k3mz_gql</td></tr>
                <tr><td>Moderator Panel</td><td>/nb-mod-c4e2/</td><td style="color:#00cc00;">Active</td><td>OAuth email verification</td></tr>
                <tr><td>REST API v2</td><td>/api/v2/</td><td style="color:#ffcc00;">Beta</td><td>Session / JWT</td></tr>
                <tr><td>Classified Vault</td><td>/novabank-internal-7f2c9/vault/</td><td style="color:#00cc00;">Active</td><td>HMAC-SHA256 token</td></tr>
                <tr><td>Internal Forum</td><td>/nb-internal-comm-8f3a/</td><td style="color:#00cc00;">Active</td><td>None (internal)</td></tr>
                <tr><td>Backup Files</td><td>/backups/</td><td class="critical">EXPOSED</td><td>None (!)</td></tr>
                <tr><td>Debug Panel</td><td>/debug-panel/</td><td class="critical">ACTIVE IN PROD</td><td>API Key</td></tr>
            </tbody>
        </table>
    </div>

    <div class="card" style="border-color:#ff4444;">
        <h2 style="color:#ff4444;">Security Alerts</h2>
        <div style="font-size:0.82rem;line-height:1.8;">
            <div class="critical">[CRITICAL] Debug panel is active in production environment</div>
            <div class="critical">[CRITICAL] Backup directory is publicly accessible without authentication</div>
            <div class="critical">[CRITICAL] JWT algorithm 'none' is accepted by token verifier</div>
            <div class="warn">[WARNING] MASAK auto-reporting has been disabled</div>
            <div class="warn">[WARNING] XML external entity loading is enabled in import API</div>
            <div class="warn">[WARNING] Rate limiting not enforced on admin_panel.php verification endpoint</div>
            <div>[INFO] SSL certificate valid until 2027-03-15</div>
        </div>
    </div>
</div>
</body>
</html>
