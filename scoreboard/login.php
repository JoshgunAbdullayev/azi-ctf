<?php
require __DIR__ . '/inc/lib.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $pw = (string)($_POST['password'] ?? '');
    if (!csrf_ok()) $err = 'Bad session, reload and retry.';
    else {
        $s = db()->prepare('SELECT id,pass_hash FROM teams WHERE name=?');
        $s->execute([$name]);
        $r = $s->fetch();
        // verify against a dummy hash when team is missing to keep timing similar
        $hash = $r['pass_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuJ5Lz0r8bW0kPq1m7f7v9x0Zc1d2e3f4G';
        if (password_verify($pw, $hash) && $r) {
            session_regenerate_id(true);
            $_SESSION['team'] = (int)$r['id'];
            redirect('/scoreboard/');
        }
        $err = 'Invalid team name or password.';
        usleep(300000);
    }
}
page_head('Login', 'Login');
?>
<div class="box"><h1>Team login</h1>
<?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<label>Team name</label><input type="text" name="name" required>
<label>Password</label><input type="password" name="password" required>
<p><button>Login</button></p></form></div>
<?php page_foot();
