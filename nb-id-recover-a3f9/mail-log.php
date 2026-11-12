<?php
require __DIR__ . '/_act2_boot.php';
$db = act2_db();
header('Content-Type: text/html; charset=utf-8');

$rows = $db->query("SELECT * FROM v_mail ORDER BY id DESC LIMIT 25")->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Internal Mail Relay Log - NovaBank QA</title>
<link rel="stylesheet" href="/assets/style.css">
<style>
 body{background:#0d1117;color:#e5e7eb;font-family:'Inter',monospace;}
 .wrap{max-width:900px;margin:2.5rem auto;padding:1.5rem;}
 h1{color:#C8102E;font-size:1.2rem;}
 .sub{color:#6B7280;font-size:.8rem;margin-bottom:1.5rem;}
 .mail{background:#161b22;border:1px solid #21262d;border-radius:12px;padding:1rem 1.2rem;margin-bottom:1rem;font-size:.85rem;}
 .mail.poison{border-color:#9B0C23;background:#1b1113;}
 .k{color:#6B7280;display:inline-block;width:120px;}
 .v{color:#e5e7eb;word-break:break-all;}
 .flag{margin-top:.7rem;padding:.6rem;background:#2a0d12;border:1px dashed #C8102E;border-radius:8px;color:#ff6b81;}
 .badge{font-size:.7rem;padding:.1rem .5rem;border-radius:6px;background:#9B0C23;color:#fff;}
</style></head><body>
<div class="wrap">
 <h1>Internal Mail Relay &mdash; Outbound Queue</h1>
 <div class="sub">QA-only view. Shows raw messages handed to the relay (not delivered in dev builds).</div>
 <?php if (!$rows): ?>
   <div class="mail">No queued messages. Trigger a reset via <code>/nb-id-recover-a3f9/forgot.php</code>.</div>
 <?php endif; ?>
 <?php foreach ($rows as $m): ?>
   <div class="mail <?= $m['poisoned'] ? 'poison' : '' ?>">
     <div><span class="k">To:</span><span class="v"><?= htmlspecialchars($m['to_email']) ?></span></div>
     <div><span class="k">Subject:</span><span class="v"><?= htmlspecialchars($m['subject']) ?></span></div>
     <div><span class="k">Build-Host:</span><span class="v"><?= htmlspecialchars($m['host']) ?>
        <?php if ($m['poisoned']): ?><span class="badge">NON-CANONICAL</span><?php endif; ?></span></div>
     <div><span class="k">Reset-Link:</span><span class="v"><?= htmlspecialchars($m['reset_url']) ?></span></div>
     <div><span class="k">Queued:</span><span class="v"><?= htmlspecialchars($m['created']) ?></span></div>
     <?php if ($m['poisoned']): ?>
       <div class="flag">
         ! Reset link was built from a non-canonical Host header (host-header injection).<br>
         A poisoned link leaks the victim's valid reset token to the attacker-controlled host.<br>
         <b><?= htmlspecialchars(ACT2_FLAG31) ?></b><br>
         reset_token = <code><?= htmlspecialchars($m['token']) ?></code>
       </div>
     <?php endif; ?>
   </div>
 <?php endforeach; ?>
</div></body></html>
