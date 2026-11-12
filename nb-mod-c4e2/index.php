<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['moderator_auth']) || $_SESSION['moderator_auth'] !== true) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body style="font-family:monospace;background:#111;color:#ff4444;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="text-align:center"><h1>403 Forbidden</h1><p>Moderator authentication required.</p><p style="color:#666;font-size:0.85rem;margin-top:0.5rem">Complete the OAuth login process with a moderator account.</p></div></body></html>';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Panel - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0d1117;color:#c9d1d9;min-height:100vh}
        .header{background:#161b22;border-bottom:1px solid #30363d;padding:1rem 0}
        .container{max-width:960px;margin:0 auto;padding:0 1.5rem}
        .header .container{display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:1rem;font-weight:600;color:#58a6ff}
        .user-info{font-size:0.82rem;color:#8b949e}
        .user-info strong{color:#00cc44}
        .main{padding:2rem 0}
        .flag-banner{background:linear-gradient(135deg,#0d2818,#1a4731);border:1px solid #238636;border-radius:8px;padding:1.5rem;text-align:center;margin-bottom:2rem}
        .flag-banner h3{color:#3fb950;font-size:0.9rem;margin-bottom:0.5rem}
        .flag-banner .flag{font-size:1.3rem;color:#58a6ff;font-weight:700;letter-spacing:0.1rem;font-family:monospace}
        .flag-banner .sub{font-size:0.78rem;color:#3fb950;margin-top:0.5rem}
        .card{background:#161b22;border:1px solid #30363d;border-radius:6px;padding:1.25rem;margin-bottom:1rem}
        .card h3{font-size:0.95rem;font-weight:600;color:#f0f6fc;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid #21262d}
        .log-entry{font-size:0.8rem;font-family:'Courier New',monospace;padding:0.4rem 0;border-bottom:1px solid #161b22;color:#8b949e}
        .log-entry:hover{background:#21262d}
        .log-time{color:#484f58}
        .log-level{font-weight:600;display:inline-block;width:60px}
        .log-warn{color:#d29922}
        .log-err{color:#f85149}
        .log-info{color:#58a6ff}
        .btn{display:inline-block;padding:0.6rem 1.2rem;border-radius:6px;font-size:0.85rem;text-decoration:none;font-weight:500;cursor:pointer;border:none}
        .btn-primary{background:#238636;color:#fff}
        .btn-primary:hover{background:#2ea043}
        .btn-outline{background:transparent;border:1px solid #30363d;color:#c9d1d9}
        .btn-outline:hover{background:#21262d}
        .tools-grid{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem}
        .tool-card{background:#0d1117;border:1px solid #21262d;border-radius:6px;padding:1rem;text-align:center}
        .tool-card h4{font-size:0.85rem;color:#c9d1d9;margin-bottom:0.25rem}
        .tool-card p{font-size:0.75rem;color:#484f58}
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <span class="logo">&#9881; NovaBank Moderator Panel</span>
        <span class="user-info">Giriş: <strong><?= htmlspecialchars($_SESSION['moderator_email'] ?? 'moderator') ?></strong> | Rol: Moderator</span>
    </div>
</div>

<div class="main">
    <div class="container">
        <div class="flag-banner">
            <h3>MODERATOR ACCESS GRANTED - OAUTH BYPASS DETECTED</h3>
            <div class="flag">ADNSU10{oauth_email_verification_bypass_2aa7}</div>
            <div class="sub">Flag 10 | OAuth Authentication Logic Vulnerability</div>
        </div>

        <div class="card">
            <h3>&#128203; Sistem Logları (Son 24 Saat)</h3>
            <div class="log-entry"><span class="log-time">[2026-03-05 23:45:12]</span> <span class="log-level log-warn">WARN</span> Unusual login pattern detected for account NBK-CORP-044</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 22:18:33]</span> <span class="log-level log-err">ERROR</span> MASAK reporting threshold exceeded - auto-report FAILED (disabled by admin)</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 21:02:15]</span> <span class="log-level log-info">INFO</span> Scheduled batch transfer executed: 3 corporate accounts, total 2.4M TRY</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 19:30:44]</span> <span class="log-level log-warn">WARN</span> Offshore transfer route CYP-8827134 connection timeout - retrying</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 18:15:08]</span> <span class="log-level log-info">INFO</span> User moderator_nb logged in via OAuth bypass</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 16:42:51]</span> <span class="log-level log-err">ERROR</span> Certificate verification failed for report-service endpoint</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 14:20:33]</span> <span class="log-level log-info">INFO</span> Compliance report generation queued (monthly)</div>
            <div class="log-entry"><span class="log-time">[2026-03-05 03:12:00]</span> <span class="log-level log-warn">WARN</span> Automated fee deductions processed: 847 accounts, total 38,450 TRY</div>
        </div>

        <div class="card">
            <h3>&#128295; Moderator Alətləri</h3>
            <div class="tools-grid">
                <a href="/nb-mod-c4e2/export.php?report=monthly" class="tool-card" style="text-decoration:none;color:inherit">
                    <h4>&#128196; Hesabat İxracı (Export)</h4>
                    <p>Aylıq uyğunluq hesabatını yüklə</p>
                </a>
                <div class="tool-card">
                    <h4>&#128100; İstifadəçi İdarəetməsi</h4>
                    <p>Giriş icazələri (deaktiv)</p>
                </div>
                <div class="tool-card">
                    <h4>&#128202; Statistikalar</h4>
                    <p>Sistem metrikləri (deaktiv)</p>
                </div>
                <div class="tool-card">
                    <h4>&#128274; Təhlükəsizlik Taraması</h4>
                    <p>Zəiflik hesabatı (deaktiv)</p>
                </div>
            </div>
        </div>

        <div class="card" style="border-color:#30363d">
            <h3>&#128172; Moderator Qeydləri</h3>
            <div style="font-size:0.85rem;color:#8b949e;line-height:1.7">
                <p><strong style="color:#d29922">[Önəmli]</strong> Hesabat ixracı əməliyyatı üçün <code style="background:#21262d;padding:0.15rem 0.4rem;border-radius:3px;font-size:0.8rem">export.php?report=</code> endpointi istifadə edilə bilər. Sistem, müvafiq hesabat servisindən məlumatları avtomatik olaraq çəkir.</p>
                <p style="margin-top:0.75rem"><strong style="color:#f85149">[Xəbərdarlıq]</strong> Son zamanlarda report-service bağlantı xətası alır. Alternativ olaraq <code style="background:#21262d;padding:0.15rem 0.4rem;border-radius:3px;font-size:0.8rem">report</code> parametrinə fərqli URL'lər sınana bilər.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
