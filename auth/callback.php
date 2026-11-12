<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: text/html; charset=utf-8');

$email = $_GET['email'] ?? $_POST['email'] ?? '';
$code = $_GET['code'] ?? $_POST['code'] ?? '';
$code_verification = $_GET['code_verification'] ?? 'false';

$valid_emails = [
    'moderator_real@novabank.com' => ['role' => 'moderator', 'username' => 'moderator_nb', 'display' => 'Moderator NB', 'code' => '184503'],
    'finans01@novabank.com' => ['role' => 'staff', 'username' => 'finans_01', 'display' => 'Finans 01', 'code' => '294817'],
    'ayse.d@novabank.com' => ['role' => 'staff', 'username' => 'destek_ayse', 'display' => 'Destek Ayse', 'code' => '573920'],
    'mehmet.it@novabank.com' => ['role' => 'staff', 'username' => 'it_mehmet', 'display' => 'IT Mehmet', 'code' => '681245'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($email) && !empty($code)) {
    if (!isset($valid_emails[$email])) {
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body style="font-family:sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="background:#fff;padding:2rem;border-radius:8px;border:1px solid #dadce0;max-width:400px;text-align:center"><h2 style="color:#d93025;margin-bottom:1rem">Account Not Found</h2><p style="color:#5f6368">Email not registered.</p></div></body></html>';
        exit;
    }

    $account = $valid_emails[$email];

    if ($code === $account['code']) {
        if ($account['role'] === 'moderator') {
            $_SESSION['moderator_auth'] = true;
            $_SESSION['moderator_email'] = $email;
            $_SESSION['moderator_username'] = $account['username'];
            header('Location: /nb-mod-c4e2/');
            exit;
        }
        echo '<!DOCTYPE html><html><head><title>Login OK</title></head><body style="font-family:sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="background:#fff;padding:2rem;border-radius:8px;border:1px solid #dadce0;max-width:400px;text-align:center"><h2 style="color:#34a853;margin-bottom:1rem">Login Successful</h2><p style="color:#5f6368">Welcome, <strong>' . htmlspecialchars($account['display']) . '</strong>. Your role (' . htmlspecialchars($account['role']) . ') does not have moderator access.</p></div></body></html>';
        exit;
    } else {
        echo '<!DOCTYPE html><html><head><title>Wrong Code</title></head><body style="font-family:sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="background:#fff;padding:2rem;border-radius:8px;border:1px solid #dadce0;max-width:400px;text-align:center"><h2 style="color:#d93025;margin-bottom:1rem">Verification Failed</h2><p style="color:#5f6368">Invalid verification code.</p></div></body></html>';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($email) && empty($code)) {
    if (!isset($valid_emails[$email])) {
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body style="font-family:sans-serif;background:#f8f9fa;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0"><div style="background:#fff;padding:2rem;border-radius:8px;border:1px solid #dadce0;max-width:400px;text-align:center"><h2 style="color:#d93025;margin-bottom:1rem">Email Not Found</h2><p style="color:#5f6368">The email <strong>' . htmlspecialchars($email) . '</strong> is not registered in the system.</p></div></body></html>';
        exit;
    }

    $account = $valid_emails[$email];
    $response_data = ['status' => 'code_sent', 'message' => 'Verification code sent to ' . $email, 'digits' => 6];

    if ($code_verification === 'true') {
        header('X-Debug-Verification: ' . $account['code']);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaBank Staff Login</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#1a1a2e;display:flex;align-items:center;justify-content:center;min-height:100vh}
        .card{background:#16213e;border:1px solid #2a2a4a;border-radius:8px;max-width:440px;width:90%;padding:2rem}
        h1{text-align:center;font-size:1.2rem;color:#e94560;margin-bottom:0.3rem}
        .sub{text-align:center;font-size:0.82rem;color:#7f8c8d;margin-bottom:2rem}
        .divider{text-align:center;color:#444;font-size:0.78rem;margin:1.5rem 0;position:relative}
        .divider::before,.divider::after{content:'';position:absolute;top:50%;width:35%;height:1px;background:#2a2a4a}
        .divider::before{left:0}
        .divider::after{right:0}
        .form-group{margin-bottom:1rem}
        label{display:block;font-size:0.78rem;color:#7f8c8d;margin-bottom:0.3rem;text-transform:uppercase;letter-spacing:0.05rem}
        input{width:100%;padding:0.65rem;background:#0d1117;border:1px solid #2a2a4a;color:#e0e0e0;font-size:0.9rem;border-radius:4px;outline:none}
        input:focus{border-color:#e94560}
        .btn{width:100%;padding:0.7rem;border:none;border-radius:4px;font-size:0.85rem;font-weight:500;cursor:pointer;margin-top:0.5rem}
        .btn-primary{background:#e94560;color:#fff}
        .btn-primary:hover{background:#c73e54}
        .btn-outline{background:transparent;border:1px solid #2a2a4a;color:#7f8c8d}
        .btn-outline:hover{border-color:#e94560;color:#e94560}
        .section{border:1px solid #2a2a4a;border-radius:6px;padding:1.25rem;margin-top:1.5rem;background:#0d1117}
        .section h3{font-size:0.85rem;color:#e0e0e0;margin-bottom:0.75rem}
        .msg{padding:0.75rem;border-radius:4px;font-size:0.82rem;margin-top:0.75rem;display:none}
        .msg-ok{background:rgba(52,168,83,0.15);border:1px solid #34a853;color:#34a853}
        .msg-err{background:rgba(234,67,53,0.15);border:1px solid #ea4335;color:#ea4335}
        .code-form{display:none;margin-top:1rem}
    </style>
</head>
<body>
<div class="card">
    <h1>Staff Authentication</h1>
    <div class="sub">NovaBank Internal Systems</div>

    <form onsubmit="return false;">
        <div class="form-group">
            <label>Username</label>
            <input type="text" placeholder="Enter username" id="loginUser">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" placeholder="Enter password" id="loginPass">
        </div>
        <button class="btn btn-primary" onclick="attemptLogin()">Sign In</button>
    </form>
    <div id="loginMsg" class="msg msg-err">Invalid credentials. Password-based login is only available for admin accounts.</div>

    <div class="divider">or sign in with email</div>

    <div class="section">
        <h3>Email Verification Login</h3>
        <div class="form-group" style="margin-bottom:0">
            <label>Employee Email</label>
            <input type="email" placeholder="name@novabank.com" id="emailInput">
        </div>
        <button class="btn btn-outline" style="margin-top:0.75rem" onclick="sendCode()">Send Verification Code</button>
        <div id="emailMsg" class="msg msg-ok"></div>

        <div class="code-form" id="codeForm">
            <div class="form-group" style="margin-bottom:0">
                <label>6-Digit Verification Code</label>
                <input type="text" placeholder="000000" maxlength="6" id="codeInput">
            </div>
            <button class="btn btn-primary" style="margin-top:0.75rem" onclick="verifyCode()">Verify & Login</button>
            <div id="codeMsg" class="msg msg-err"></div>
        </div>
    </div>
</div>

<script>
var currentEmail = '';

function attemptLogin() {
    var msg = document.getElementById('loginMsg');
    msg.style.display = 'block';
}

function sendCode() {
    var email = document.getElementById('emailInput').value.trim();
    if (!email) return;
    currentEmail = email;

    var url = new URL(window.location.href);
    var codeVerif = url.searchParams.get('code_verification') || 'false';

    var fd = new FormData();
    fd.append('email', email);

    fetch('/auth/callback.php?code_verification=' + codeVerif, {method:'POST', body:fd})
    .then(function(r){return r.json()})
    .then(function(data){
        var msg = document.getElementById('emailMsg');
        if (data.status === 'code_sent') {
            msg.className = 'msg msg-ok';
            msg.textContent = '6-digit verification code sent to ' + email;
            msg.style.display = 'block';
            document.getElementById('codeForm').style.display = 'block';
        } else {
            msg.className = 'msg msg-err';
            msg.textContent = data.message || 'Email not found.';
            msg.style.display = 'block';
        }
    })
    .catch(function(){
        var msg = document.getElementById('emailMsg');
        msg.className = 'msg msg-err';
        msg.textContent = 'Request failed.';
        msg.style.display = 'block';
    });
}

function verifyCode() {
    var code = document.getElementById('codeInput').value.trim();
    if (!code || !currentEmail) return;

    var fd = new FormData();
    fd.append('email', currentEmail);
    fd.append('code', code);

    fetch('/auth/callback.php', {method:'POST', body:fd})
    .then(function(r){
        if (r.redirected) {
            window.location.href = r.url;
            return;
        }
        return r.text();
    })
    .then(function(html){
        if (html) {
            if (html.indexOf('Verification Failed') !== -1) {
                var msg = document.getElementById('codeMsg');
                msg.textContent = 'Invalid code. Please try again.';
                msg.style.display = 'block';
            } else {
                document.open();
                document.write(html);
                document.close();
            }
        }
    });
}
</script>
</body>
</html>
