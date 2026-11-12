<?php
require __DIR__ . '/inc/lib.php';
$me = me();
$id = isset($_GET['id']) ? (int)$_GET['id'] : ($me['id'] ?? 0);
if (!$id) redirect('/scoreboard/login.php');
$tm = db()->prepare('SELECT id,name FROM teams WHERE id=?');
$tm->execute([$id]);
$team = $tm->fetch();
if (!$team) { http_response_code(404); page_head('Not found'); echo '<h1>Team not found</h1>'; page_foot(); exit; }
$rank = 0; $pts = 0;
foreach (scoreboard() as $r) if ($r['id'] == $id) { $rank = $r['rank']; $pts = $r['pts']; }
$flags = load_flags();
$fb = first_bloods();
$s = db()->prepare('SELECT chal,points,solved_at FROM solves WHERE team_id=? ORDER BY solved_at');
$s->execute([$id]);
$solves = $s->fetchAll();
page_head('Team ' . $team['name'], 'My Team');
echo '<h1>' . e($team['name']) . '</h1><p>Rank <b>#' . $rank . '</b> &middot; <b>' . $pts . '</b> points &middot; ' . count($solves) . ' of ' . count(array_filter($flags, fn($f) => !$f['hidden'])) . ' flags solved</p>';
echo '<div class="scroll"><table><tr><th>Challenge</th><th>Category</th><th>Tier</th><th>Points</th><th>Solved at</th></tr>';
foreach ($solves as $v) {
    $c = $flags[$v['chal']] ?? ['title' => '(removed)', 'category' => '-', 'tier' => '-'];
    echo '<tr><td>#' . $v['chal'] . ' ' . e($c['title']) . (($fb[$v['chal']] ?? '') === $team['name'] ? ' <span class="fb">FIRST BLOOD</span>' : '') . '</td><td>' . e($c['category']) . '</td><td class="' . e($c['tier']) . '">' . e($c['tier']) . '</td><td>' . $v['points'] . '</td><td>' . date('Y-m-d H:i:s', $v['solved_at']) . '</td></tr>';
}
if (!$solves) echo '<tr><td colspan="5">No solves yet.</td></tr>';
echo '</table></div>';
page_foot();
