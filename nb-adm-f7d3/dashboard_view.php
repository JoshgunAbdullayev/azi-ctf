<?php
if (!isset($_SESSION['root_admin_auth']) || $_SESSION['root_admin_auth'] !== true) {
    header('Location: /nb-adm-f7d3/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0a0a0a;color:#00ff41;min-height:100vh}
        .header{background:#050505;border-bottom:1px solid #1a3a1a;padding:1rem 0}
        .container{max-width:960px;margin:0 auto;padding:0 1.5rem}
        .header .container{display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:1rem;letter-spacing:0.15rem;text-shadow:0 0 8px #00ff41}
        .user{font-size:0.78rem;color:#006b1a}
        .main{padding:2rem 0}
        .card{background:#0d0d0d;border:1px solid #1a3a1a;border-radius:4px;padding:1.5rem;margin-bottom:1.25rem}
        .card h2{color:#00cc33;font-size:0.95rem;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid #1a3a1a;text-transform:uppercase;letter-spacing:0.1rem}
        .stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
        .stat{background:#050505;border:1px solid #1a3a1a;border-radius:4px;padding:1rem;text-align:center}
        .stat-val{font-size:1.3rem;color:#00ff41;text-shadow:0 0 10px #00ff41}
        .stat-label{font-size:0.7rem;color:#006b1a;margin-top:0.25rem;text-transform:uppercase}
        .maint-link{display:inline-block;background:#330000;color:#ff4444;border:1px solid #ff4444;padding:0.6rem 1.5rem;text-decoration:none;font-family:inherit;font-size:0.85rem;border-radius:2px;margin-top:0.75rem}
        .maint-link:hover{background:#440000}
        .doc{background:#1a0000;border:1px solid #ff3333;border-radius:4px;padding:1.25rem;margin-top:1rem;font-size:0.82rem;color:#ff6666;line-height:1.7}
        .doc h3{color:#ff3333;margin-bottom:0.75rem;font-size:0.9rem}
        .table{width:100%;border-collapse:collapse;font-size:0.8rem}
        .table th{text-align:left;padding:0.5rem;border-bottom:1px solid #333;color:#00cc33}
        .table td{padding:0.5rem;border-bottom:1px solid #1a1a1a;color:#00ff41}
        .table tr:hover{background:#001a00}
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <span class="logo">&#9881; NovaBank Root Administration</span>
        <span class="user">root_admin | <?= date('Y-m-d H:i:s') ?></span>
    </div>
</div>

<div class="main">
    <div class="container">
        <div class="card">
            <h2>System Overview</h2>
            <div class="stat-grid">
                <div class="stat">
                    <div class="stat-val">12,847</div>
                    <div class="stat-label">Total Transactions</div>
                </div>
                <div class="stat">
                    <div class="stat-val">3,421</div>
                    <div class="stat-label">Active Accounts</div>
                </div>
                <div class="stat">
                    <div class="stat-val" style="color:#ff4444;text-shadow:0 0 10px #ff4444">45.2M TRY</div>
                    <div class="stat-label" style="color:#ff4444">Laundered Funds</div>
                </div>
            </div>
        </div>

        <div class="card" style="border-color:#333">
            <h2>User Management</h2>
            <table class="table">
                <thead><tr><th>Username</th><th>Role</th><th>Last Login</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>root_admin</td><td>System Administrator</td><td><?= date('Y-m-d H:i') ?></td><td style="color:#00ff41">ACTIVE</td></tr>
                    <tr><td>moderator_nb</td><td>Moderator</td><td>2026-03-05 18:15</td><td style="color:#00ff41">ACTIVE</td></tr>
                    <tr><td>finans_01</td><td>Finance Staff</td><td>2026-03-05 14:30</td><td style="color:#00ff41">ACTIVE</td></tr>
                    <tr><td>kerem_y</td><td>Bank Admin</td><td>2026-03-04 09:20</td><td style="color:#ffaa00">IDLE</td></tr>
                    <tr><td>hakan_s</td><td>Priority Admin</td><td>2026-03-03 11:45</td><td style="color:#ffaa00">IDLE</td></tr>
                </tbody>
            </table>
        </div>

        <div class="card" style="border-color:#ff3333">
            <h2 style="color:#ff3333">&#9888; Maintenance Tools</h2>
            <p style="font-size:0.82rem;color:#ff6666;margin-bottom:0.75rem">System backup and maintenance operations. Handle with care.</p>
            <a href="/nb-adm-f7d3/maintenance.php" class="maint-link">&#128736; Open Maintenance Console</a>
        </div>

        <div class="doc">
            <h3>CONFIDENTIAL - OWNERSHIP RECORDS</h3>
            <p>NovaBank A.S. is registered under a complex corporate structure involving multiple offshore entities.
            The true beneficial owner has been carefully concealed through layers of shell companies.</p>
            <p style="margin-top:0.5rem">Detailed ownership records are stored in the maintenance system backup.
            Access the maintenance console for detailed records.</p>
        </div>

        <div class="doc" style="border-color:#ffaa00;background:#1a1a00">
            <h3 style="color:#ffaa00">FRAUD SCHEME DOCUMENTATION</h3>
            <p style="color:#ccaa00">Evidence gathered during the investigation reveals a systematic fraud operation:</p>
            <ul style="margin-top:0.5rem;margin-left:1.5rem;color:#ccaa00">
                <li>Fake BDDK license (izin no: 48291 - non-existent)</li>
                <li>Fraudulent TMSF coverage claims (unlimited insurance - impossible)</li>
                <li>Ponzi-scheme "investment" programs (guaranteed %15-25 monthly returns)</li>
                <li>Systematic customer fee deductions ("Yonetim Fonu Kesintisi")</li>
                <li>Money laundering through 3+ shell companies (45M+ TRY)</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
