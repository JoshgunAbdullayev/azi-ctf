<?php
require __DIR__ . '/_act2_boot.php';
$db = act2_db();

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$msg = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'] ?? '';
    $st = $db->prepare("SELECT * FROM v_reset WHERE token = ? AND used = 0");
    $st->execute([$token]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
        $msg = 'Invalid or already-used reset token.';
    } elseif (strlen($new) < 4) {
        $msg = 'Password too short.';
    } else {
        // Account takeover: whoever holds the token resets the victim's password.
        $db->prepare("UPDATE v_users SET pass_hash = ? WHERE id = ?")
           ->execute([password_hash($new, PASSWORD_BCRYPT), $r['user_id']]);
        $db->prepare("UPDATE v_reset SET used = 1 WHERE token = ?")->execute([$token]);

        // Log the attacker straight in.
        $sid = bin2hex(random_bytes(16));
        $db->prepare("INSERT INTO v_session(sid,user_id,mfa_passed,api_token,created)
                      VALUES(?,?,0,NULL,?)")
           ->execute([$sid, $r['user_id'], gmdate('Y-m-d H:i:s')]);
        setcookie('act2_sid', $sid, ['path' => '/', 'httponly' => true]);
        $done = true;
    }
}
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Set New Password - NovaBank</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
 body{background:#0d1117;color:#e5e7eb;}
 .wrap{max-width:460px;margin:4rem auto;padding:2rem;background:#161b22;border:1px solid #21262d;border-radius:16px;}
 h1{color:#C8102E;font-size:1.4rem;}
 label{display:block;margin:1rem 0 .3rem;font-size:.85rem;color:#9CA3AF;}
 input{width:100%;padding:.7rem;background:#0d1117;border:1px solid #30363d;border-radius:8px;color:#e5e7eb;}
 button{margin-top:1.3rem;width:100%;padding:.75rem;background:#C8102E;color:#fff;border:0;border-radius:8px;font-weight:600;cursor:pointer;}
 .err{margin-top:1rem;color:#ff6b81;font-size:.85rem;}
 .ok{margin-top:1rem;padding:.9rem;background:#132d1c;border:1px solid #1f512f;border-radius:8px;color:#4ade80;font-size:.9rem;}
</style></head><body>
<div class="wrap">
 <h1>Choose a new password</h1>
 <?php if ($done): ?>
   <div class="ok">Password updated. You are now signed in as the account owner.<br>
   Continue to your dashboard: <a style="color:#4ade80" href="/nb-id-recover-a3f9/dash.php">/nb-id-recover-a3f9/dash.php</a></div>
 <?php else: ?>
   <?php if ($msg): ?><div class="err"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
   <form method="post">
     <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
     <label>New password</label>
     <input type="password" name="password" required>
     <button type="submit">Reset password</button>
   </form>
 <?php endif; ?>
</div></body></html>
