<?php
require __DIR__ . '/inc/lib.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $pw = (string)($_POST['password'] ?? '');
    if (!csrf_ok()) $err = 'Bad session, reload and retry.';
    elseif (!preg_match('/^[A-Za-z0-9 _.\-]{3,32}$/', $name)) $err = 'Team name: 3-32 chars, letters/digits/space/_ . - only.';
    elseif (strlen($pw) < 6 || strlen($pw) > 72) $err = 'Password must be 6-72 characters.';
    else {
        try {
            $s = db()->prepare('INSERT INTO teams(name,pass_hash,created_at) VALUES(?,?,?)');
            $s->execute([$name, password_hash($pw, PASSWORD_BCRYPT), time()]);
            session_regenerate_id(true);
            $_SESSION['team'] = (int)db()->lastInsertId();
            redirect('/scoreboard/');
        } catch (PDOException $e) {
            $err = 'That team name is already taken.';
        }
    }
}
page_head('Register', 'Register');
?>
<div class="box"><h1>Register team</h1>
<?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<label>Team name</label><input type="text" name="name" required maxlength="32">
<label>Password</label><input type="password" name="password" required>
<p><button>Create team</button></p></form></div>
<?php page_foot();
