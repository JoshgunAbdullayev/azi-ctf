<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: text/html; charset=utf-8');

if (isset($_SESSION['root_admin_auth']) && $_SESSION['root_admin_auth'] === true) {
    include __DIR__ . '/dashboard_view.php';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'root_admin' && $password === 'n0v4_r00t_2026!') {
        $_SESSION['root_admin_auth'] = true;
        header('Location: /nb-adm-f7d3/');
        exit;
    } else {
        $error = 'Invalid credentials. Access denied.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaBank Administration</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#000;color:#00ff41;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .login-box{background:#0a0a0a;border:1px solid #1a3a1a;border-radius:4px;padding:2.5rem;max-width:420px;width:90%}
        h1{text-align:center;font-size:1.1rem;letter-spacing:0.2rem;text-transform:uppercase;margin-bottom:0.5rem;text-shadow:0 0 10px #00ff41}
        .sub{text-align:center;color:#006b1a;font-size:0.75rem;margin-bottom:2rem}
        .form-group{margin-bottom:1.25rem}
        label{display:block;font-size:0.8rem;color:#006b1a;margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.1rem}
        input[type="text"],input[type="password"]{width:100%;padding:0.7rem;background:#000;border:1px solid #1a3a1a;color:#00ff41;font-family:inherit;font-size:0.9rem;border-radius:2px;outline:none}
        input:focus{border-color:#00ff41;box-shadow:0 0 5px rgba(0,255,65,0.2)}
        .btn-login{width:100%;padding:0.75rem;background:#003300;color:#00ff41;border:1px solid #00ff41;font-family:inherit;font-size:0.85rem;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.1rem;margin-top:0.5rem}
        .btn-login:hover{background:#004400}
        .error{background:#1a0000;border:1px solid #ff4444;color:#ff4444;padding:0.75rem;border-radius:2px;font-size:0.82rem;margin-bottom:1rem;text-align:center}
        .footer{text-align:center;margin-top:1.5rem;font-size:0.7rem;color:#333}
    </style>
</head>
<body>
<div class="login-box">
    <h1>System Administration</h1>
    <div class="sub">[ NovaBank Internal - Restricted Access ]</div>

    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" autocomplete="off" placeholder="Enter admin username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter admin password">
        </div>
        <button type="submit" class="btn-login">Authenticate</button>
    </form>
    <div class="footer">NovaBank Administration System v2.1.0 | Internal Use Only</div>
</div>
</body>
</html>
