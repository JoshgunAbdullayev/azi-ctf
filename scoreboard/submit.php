<?php
require __DIR__ . '/inc/lib.php';
$t = me();
if (!$t) redirect('/scoreboard/login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) redirect('/scoreboard/');
$flags = load_flags();
$n = (int)($_POST['chal'] ?? 0);
$val = trim((string)($_POST['flag'] ?? ''));
$back = '/scoreboard/#c' . $n;
function flash(string $k, string $m): void { $_SESSION['flash'] = [$k, $m]; }
if (!is_open()) { flash('warn', 'Competition is closed.'); redirect($back); }
if (!isset($flags[$n]) || $flags[$n]['hidden']) { flash('err', 'Unknown challenge.'); redirect($back); }
if (strlen($val) > 300) $val = substr($val, 0, 300);
$pdo = db();
$now = time();
// light rate limit on wrong submissions
$s = $pdo->prepare('SELECT COUNT(*) FROM submissions WHERE team_id=? AND correct=0 AND created_at>?');
$s->execute([$t['id'], $now - SB_RATE_WINDOW]);
if ($s->fetchColumn() >= SB_RATE_MAX_WRONG) { flash('err', 'Too many wrong submissions. Wait a minute and try again.'); redirect($back); }

$ok = hash_equals($flags[$n]['flag'], $val);
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$awarded = 0;
if ($ok) {
    $pdo->beginTransaction();
    $ins = $pdo->prepare('INSERT OR IGNORE INTO solves(team_id,chal,points,solved_at) VALUES(?,?,?,?)');
    $ins->execute([$t['id'], $n, $flags[$n]['points'], $now]);
    $awarded = $ins->rowCount() ? 1 : 0;
    $pdo->prepare('INSERT INTO submissions(team_id,chal,value,correct,awarded,ip,created_at) VALUES(?,?,?,?,?,?,?)')->execute([$t['id'], $n, $val, 1, $awarded, $ip, $now]);
    $pdo->commit();
    if ($awarded) {
        $f = $pdo->prepare('SELECT COUNT(*) FROM solves WHERE chal=? AND (solved_at<? OR (solved_at=? AND id<(SELECT id FROM solves WHERE team_id=? AND chal=?)))');
        $f->execute([$n, $now, $now, $t['id'], $n]);
        $first = $f->fetchColumn() == 0;
        flash('ok', 'Correct! +' . $flags[$n]['points'] . ' points' . ($first ? ' - FIRST BLOOD!' : '.'));
    } else {
        flash('warn', 'Correct, but your team already solved this one. No additional points.');
    }
} else {
    $pdo->prepare('INSERT INTO submissions(team_id,chal,value,correct,awarded,ip,created_at) VALUES(?,?,?,?,?,?,?)')->execute([$t['id'], $n, $val, 0, 0, $ip, $now]);
    flash('err', 'Incorrect flag.');
}
redirect($back);
