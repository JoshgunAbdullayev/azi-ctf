<?php
require __DIR__ . '/inc/lib.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok()) { $msg = 'Bad CSRF token.'; }
    elseif (($_POST['do'] ?? '') === 'login') {
        if (hash_equals(SB_ADMIN_PASSWORD, (string)($_POST['password'] ?? ''))) { session_regenerate_id(true); $_SESSION['sb_admin'] = 1; redirect('/scoreboard/admin.php'); }
        $msg = 'Wrong admin password.'; usleep(500000);
    } elseif (is_admin()) {
        $pdo = db();
        switch ($_POST['do'] ?? '') {
            case 'logout': unset($_SESSION['sb_admin']); redirect('/scoreboard/admin.php');
            case 'toggle': $pdo->prepare("UPDATE settings SET v=? WHERE k='open'")->execute([is_open() ? '0' : '1']); $msg = 'Competition ' . (is_open() ? 'OPEN' : 'CLOSED') . '.'; break;
            case 'reset':
                $id = (int)($_POST['team'] ?? 0);
                $pdo->prepare('DELETE FROM solves WHERE team_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM submissions WHERE team_id=?')->execute([$id]);
                $msg = 'Team #' . $id . ' reset (solves + submissions cleared).'; break;
            case 'delete':
                $id = (int)($_POST['team'] ?? 0);
                foreach (['solves', 'submissions'] as $tb) $pdo->prepare("DELETE FROM $tb WHERE team_id=?")->execute([$id]);
                $pdo->prepare('DELETE FROM teams WHERE id=?')->execute([$id]);
                $msg = 'Team #' . $id . ' deleted.'; break;
        }
    }
}
page_head('Admin');
echo '<h1>Admin</h1>';
if ($msg) echo '<div class="alert warn">' . e($msg) . '</div>';
if (!is_admin()) {
    echo '<div class="box"><form method="post"><input type="hidden" name="csrf" value="' . csrf() . '"><input type="hidden" name="do" value="login"><label>Admin password</label><input type="password" name="password" required><p><button>Login</button></p></form></div>';
    page_foot(); exit;
}
$tok = '<input type="hidden" name="csrf" value="' . csrf() . '">';
echo '<form method="post" style="display:inline">' . $tok . '<input type="hidden" name="do" value="toggle"><button>' . (is_open() ? 'Close / freeze competition' : 'Re-open competition') . '</button></form> ';
echo '<form method="post" style="display:inline">' . $tok . '<input type="hidden" name="do" value="logout"><button class="sec">Admin logout</button></form>';
echo '<h2>Teams</h2><div class="scroll"><table><tr><th>ID</th><th>Name</th><th>Points</th><th>Solves</th><th>Registered</th><th></th></tr>';
$created = [];
foreach (db()->query('SELECT id,created_at FROM teams') as $r) $created[$r['id']] = $r['created_at'];
foreach (scoreboard() as $r) {
    echo '<tr><td>' . $r['id'] . '</td><td>' . e($r['name']) . '</td><td>' . $r['pts'] . '</td><td>' . $r['n'] . '</td><td>' . date('m-d H:i', $created[$r['id']]) . '</td><td><form method="post" style="display:inline" onsubmit="return confirm(\'Proceed?\')">' . $tok . '<input type="hidden" name="team" value="' . $r['id'] . '"><button class="sec" name="do" value="reset">Reset</button> <button class="sec" name="do" value="delete">Delete</button></form></td></tr>';
}
echo '</table></div><h2>Submissions (latest 300)</h2><div class="scroll"><table><tr><th>Time</th><th>Team</th><th>Chal</th><th>Value</th><th>Result</th><th>IP</th></tr>';
$q = db()->query('SELECT s.*,t.name FROM submissions s LEFT JOIN teams t ON t.id=s.team_id ORDER BY s.id DESC LIMIT 300');
foreach ($q as $r) {
    echo '<tr><td>' . date('m-d H:i:s', $r['created_at']) . '</td><td>' . e($r['name']) . '</td><td>#' . $r['chal'] . '</td><td>' . e($r['value']) . '</td><td class="' . ($r['correct'] ? ($r['awarded'] ? 'Easy' : 'Medium') : 'Expert') . '">' . ($r['correct'] ? ($r['awarded'] ? 'CORRECT (awarded)' : 'correct (dup)') : 'wrong') . '</td><td>' . e($r['ip']) . '</td></tr>';
}
echo '</table></div>';
page_foot();
