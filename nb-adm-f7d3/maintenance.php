<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['root_admin_auth']) || $_SESSION['root_admin_auth'] !== true) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>Forbidden</title></head><body style="font-family:monospace;background:#000;color:#ff4444;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="text-align:center"><h1>403 ACCESS DENIED</h1><p>Root administrator authentication required.</p></div></body></html>';
    exit;
}

$output = '';
$executed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup_path'])) {
    $backup_path = $_POST['backup_path'];

    $blocked_cmds = ['rm ', 'del ', 'format', 'shutdown', 'reboot', 'mkfs', 'dd ', 'wget ', 'curl ', 'nc ', 'ncat', 'python', 'perl', 'ruby', 'bash', 'sh ', 'powershell', 'cmd.exe'];
    $blocked_chars = ['|', '`', '$', '(', ')', '{', '}', '<', '>', '!', '\\'];
    $is_blocked = false;
    $block_reason = '';

    foreach ($blocked_cmds as $b) {
        if (stripos($backup_path, $b) !== false) {
            $is_blocked = true;
            $block_reason = 'Restricted command detected';
            break;
        }
    }
    if (!$is_blocked) {
        foreach ($blocked_chars as $c) {
            if (strpos($backup_path, $c) !== false) {
                $is_blocked = true;
                $block_reason = 'Restricted character: ' . $c;
                break;
            }
        }
    }

    if ($is_blocked) {
        $output = "[SECURITY] Operation blocked by NovaBank WAF v3.1. Reason: " . $block_reason . "\n[INFO] Only alphanumeric paths with / . - _ ; and spaces are permitted.";
    } elseif (empty(trim($backup_path))) {
        $output = "[ERROR] Backup path cannot be empty.";
    } else {
        // Backup worker runs from the admin service directory, so relative
        // paths like ./data/ resolve here.
        @chdir(__DIR__);
        $cmd = "echo Backup started for: " . $backup_path;
        // Executed via the system shell (command injection vulnerability).
        $result = @shell_exec("sh -c \"" . $cmd . "\" 2>&1");
        if ($result === null || $result === '') {
            $result = @shell_exec($cmd);
        }
        $output = $result ?: "[INFO] Command executed but produced no output.";
        $executed = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Console - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0a0a0a;color:#00ff41;min-height:100vh}
        .header{background:#050505;border-bottom:1px solid #333;padding:1rem 0}
        .container{max-width:800px;margin:0 auto;padding:0 1.5rem}
        .header .container{display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:0.95rem;color:#ff4444;letter-spacing:0.1rem}
        .back{color:#006b1a;text-decoration:none;font-size:0.8rem}
        .back:hover{color:#00ff41}
        .main{padding:2rem 0}
        .card{background:#0d0d0d;border:1px solid #1a3a1a;border-radius:4px;padding:1.5rem;margin-bottom:1.25rem}
        .card h2{color:#00cc33;font-size:0.95rem;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid #1a3a1a}
        .warn{background:#1a1a00;border:1px solid #ffaa00;border-radius:4px;padding:1rem;margin-bottom:1.25rem;font-size:0.82rem;color:#ffaa00}
        label{display:block;font-size:0.8rem;color:#006b1a;margin-bottom:0.4rem;text-transform:uppercase}
        input[type="text"]{width:100%;padding:0.7rem;background:#000;border:1px solid #1a3a1a;color:#00ff41;font-family:inherit;font-size:0.85rem;border-radius:2px;outline:none}
        input:focus{border-color:#00ff41}
        .btn{padding:0.6rem 1.5rem;background:#003300;color:#00ff41;border:1px solid #00ff41;font-family:inherit;font-size:0.85rem;cursor:pointer;border-radius:2px;margin-top:0.75rem}
        .btn:hover{background:#004400}
        .output{background:#000;border:1px solid #333;border-radius:4px;padding:1.25rem;margin-top:1rem;font-size:0.82rem;color:#aaa;white-space:pre-wrap;line-height:1.6;max-height:400px;overflow-y:auto}
        .output .prompt{color:#00ff41}
        .recent{font-size:0.8rem;color:#333}
        .recent li{padding:0.3rem 0;border-bottom:1px solid #111}
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <span class="logo">&#128736; Maintenance Console</span>
            <a href="/nb-adm-f7d3/" class="back">&larr; Back to Dashboard</a>
    </div>
</div>

<div class="main">
    <div class="container">
        <div class="warn">
            &#9888; WARNING: This console executes system-level backup operations.
            Incorrect paths may cause data loss. Use with caution.
        </div>

        <div class="card">
            <h2>Backup Tool</h2>
            <p style="font-size:0.82rem;color:#006b1a;margin-bottom:1rem">
                Specify a directory path to initiate backup process.
            </p>
            <form method="POST">
                <label>Backup Path</label>
                <input type="text" name="backup_path" placeholder="/var/backups/novabank/" autocomplete="off" value="<?= htmlspecialchars($_POST['backup_path'] ?? '') ?>">
                <button type="submit" class="btn">Execute Backup</button>
            </form>

            <?php if ($output): ?>
            <div class="output">
                <span class="prompt">root@novabank:~#</span> echo Backup started for: <?= htmlspecialchars($_POST['backup_path'] ?? '') ?>

<?= htmlspecialchars($output) ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="card" style="border-color:#333">
            <h2 style="color:#666">Recent Operations</h2>
            <ul class="recent" style="list-style:none">
                <li>[2026-03-05 03:00] Auto-backup: /var/backups/novabank/daily_20260305.tar.gz (SUCCESS)</li>
                <li>[2026-03-04 03:00] Auto-backup: /var/backups/novabank/daily_20260304.tar.gz (SUCCESS)</li>
                <li>[2026-03-03 15:42] Manual backup: ./data/ (root_admin)</li>
                <li>[2026-03-01 03:00] Auto-backup: /var/backups/novabank/monthly_202602.tar.gz (SUCCESS)</li>
            </ul>
        </div>

        <div class="card" style="border-color:#222">
            <h2 style="color:#444">System Info</h2>
            <div style="font-size:0.8rem;color:#444;line-height:1.8">
                <div>Application Root: /var/www/novabank/</div>
                <div>Backup Storage: /var/backups/novabank/</div>
                <div>Database: /var/data/novabank.db</div>
                <div>Local data directory: ./data/</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
