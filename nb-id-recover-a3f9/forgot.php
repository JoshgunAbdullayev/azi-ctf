<?php
require __DIR__ . '/_act2_boot.php';
$db = act2_db();

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    // Host header is trusted to build the reset link (VULN: host header injection).
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $st = $db->prepare("SELECT * FROM v_users WHERE email = ?");
    $st->execute([$email]);
    $user = $st->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $reset_url = 'http://' . $host . '/nb-id-recover-a3f9/reset.php?token=' . $token;
        $poisoned = in_array(strtolower($host), $GLOBALS['ACT2_CANON_HOSTS']) ? 0 : 1;
        $now = gmdate('Y-m-d H:i:s') . ' UTC';

        $db->prepare("INSERT INTO v_reset(token,user_id,host,reset_url,poisoned,used,created)
                      VALUES(?,?,?,?,?,0,?)")
           ->execute([$token, $user['id'], $host, $reset_url, $poisoned, $now]);
        $db->prepare("INSERT INTO v_mail(to_email,subject,host,reset_url,token,poisoned,created)
                      VALUES(?,?,?,?,?,?,?)")
           ->execute([$user['email'], 'NovaBank Password Reset', $host, $reset_url, $token, $poisoned, $now]);
    }
    // Generic response regardless of whether the account exists.
    $sent = true;
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Password Recovery - NovaBank</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
 body{background:#0d1117;color:#e5e7eb;}
 .wrap{max-width:460px;margin:4rem auto;padding:2rem;background:#161b22;border:1px solid #21262d;border-radius:16px;}
 h1{color:#C8102E;font-size:1.4rem;margin-bottom:.4rem;}
 label{display:block;margin:1rem 0 .3rem;font-size:.85rem;color:#9CA3AF;}
 input{width:100%;padding:.7rem;background:#0d1117;border:1px solid #30363d;border-radius:8px;color:#e5e7eb;}
 button{margin-top:1.3rem;width:100%;padding:.75rem;background:#C8102E;color:#fff;border:0;border-radius:8px;font-weight:600;cursor:pointer;}
 .note{margin-top:1rem;font-size:.8rem;color:#6B7280;}
 .ok{margin-top:1rem;padding:.8rem;background:#132d1c;border:1px solid #1f512f;border-radius:8px;color:#4ade80;font-size:.85rem;}
</style></head><body>
<div class="wrap">
 <h1>Forgot your password?</h1>
 <p style="font-size:.85rem;color:#9CA3AF;">Enter your registered e-mail. We'll send a secure reset link via the internal mail relay.</p>
 <?php if ($sent): ?>
   <div class="ok">If an account matches that address, a reset link has been queued to the internal mail relay.</div>
 <?php endif; ?>
 <form method="post">
   <label>E-mail address</label>
   <input type="email" name="email" placeholder="name@novabank.com.tr" required>
   <button type="submit">Send reset link</button>
 </form>
 <p class="note">Dev build: outbound mail is not delivered externally &mdash; it is written to the relay log at
 <code>/nb-id-recover-a3f9/mail-log.php</code> for QA inspection.</p>
</div></body></html>
