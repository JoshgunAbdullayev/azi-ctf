<?php
require __DIR__ . '/_act2_boot.php';
act2_db();
$s = act2_session_from_cookie();
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Account Dashboard - NovaBank</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
 body{background:#0d1117;color:#e5e7eb;}
 .wrap{max-width:720px;margin:2.5rem auto;padding:2rem;}
 h1{color:#C8102E;font-size:1.3rem;}
 .card{background:#161b22;border:1px solid #21262d;border-radius:14px;padding:1.3rem 1.5rem;margin-top:1.2rem;}
 .k{color:#6B7280;font-size:.8rem;}
 .v{color:#e5e7eb;font-size:.95rem;margin-bottom:.8rem;}
 .flag{padding:.6rem;background:#2a0d12;border:1px dashed #C8102E;border-radius:8px;color:#ff6b81;font-family:monospace;}
 .warn{color:#fbbf24;font-size:.85rem;}
</style></head><body>
<div class="wrap">
<?php if (!$s): ?>
 <div class="card"><span class="warn">Not authenticated.</span> Complete a password reset to obtain a session cookie.</div>
<?php else: ?>
 <h1>Welcome back, <?= htmlspecialchars($s['username']) ?></h1>
 <div class="card">
   <div class="k">Account holder</div><div class="v"><?= htmlspecialchars($s['username']) ?> (<?= htmlspecialchars($s['email']) ?>)</div>
   <div class="k">Account flag</div><div class="v flag"><?= htmlspecialchars($s['dashboard_secret']) ?></div>
 </div>
 <div class="card">
   <div class="k">Security &mdash; Multi-factor authentication</div>
   <div class="v">Your device is enrolled. Enrollment reference:
     <code><?= htmlspecialchars($s['mfa_ref']) ?></code></div>
   <div class="warn">High-value actions require step-up verification at
     <code>/nb-mfa-gw-7c21/verify.php</code> (pass your <code>mfa_ref</code>).</div>
 </div>
<?php endif; ?>
</div></body></html>
