<?php
require __DIR__ . '/inc/lib.php';
$flags = load_flags();
uasort($flags, fn($a, $b) => [$a['points'], $a['number']] <=> [$b['points'], $b['number']]);
$t = me();
$fb = first_bloods();
$cnt = solve_counts();
$mine = [];
if ($t) {
    $s = db()->prepare('SELECT chal FROM solves WHERE team_id=?');
    $s->execute([$t['id']]);
    $mine = array_flip($s->fetchAll(PDO::FETCH_COLUMN));
}
$msg = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
page_head('Challenges', 'Challenges');
echo '<h1>Challenges</h1>';
if ($msg) echo '<div class="alert ' . e($msg[0]) . '">' . e($msg[1]) . '</div>';
if (!$t) echo '<div class="alert warn">Login or register a team to submit flags.</div>';
echo '<div class="grid">';
foreach ($flags as $n => $c) {
    if ($c['hidden']) continue;
    $solved = isset($mine[$n]);
    echo '<div class="card' . ($solved ? ' solved' : '') . '" id="c' . $n . '"><h3>#' . $n . ' ' . e($c['title']) . ($solved ? ' &#10003;' : '') . '</h3>';
    echo '<div class="meta"><span class="tag">' . e($c['category']) . '</span><span class="tag ' . e($c['tier']) . '">' . e($c['tier']) . '</span><b>' . $c['points'] . ' pts</b></div>';
    echo '<div class="meta">Solves: ' . ($cnt[$n] ?? 0) . (isset($fb[$n]) ? ' &middot; <span class="fb">First blood: ' . e($fb[$n]) . '</span>' : '') . '</div>';
    if ($t && is_open()) {
        echo '<form class="inline" method="post" action="/scoreboard/submit.php"><input type="hidden" name="csrf" value="' . csrf() . '"><input type="hidden" name="chal" value="' . $n . '"><input type="text" name="flag" placeholder="ADNSU' . $n . '{...}" autocomplete="off" required><button>Submit</button></form>';
    }
    echo '</div>';
}
echo '</div>';
page_foot();
