<?php
require __DIR__ . '/inc/lib.php';
$rows = scoreboard();
$flags = load_flags();
$fb = first_bloods();
$t = me();
page_head('Scoreboard', 'Scoreboard', true);
echo '<h1>Scoreboard</h1><div class="meta">Auto-refreshes every ' . (int)(SB_REFRESH_MS / 1000) . 's. Updated ' . date('H:i:s') . '</div><div class="scroll"><table><tr><th>#</th><th>Team</th><th>Points</th><th>Solves</th><th>Last solve</th><th>First bloods</th></tr>';
foreach ($rows as $r) {
    $bl = [];
    foreach ($fb as $n => $name) if ($name === $r['name']) $bl[] = '#' . $n;
    echo '<tr><td>' . $r['rank'] . '</td><td><a style="color:var(--acc)" href="/scoreboard/team.php?id=' . $r['id'] . '">' . e($r['name']) . '</a>' . ($t && $t['id'] == $r['id'] ? ' (you)' : '') . '</td><td>' . $r['pts'] . '</td><td>' . $r['n'] . '</td><td>' . ($r['last'] ? date('H:i:s', $r['last']) : '-') . '</td><td class="fb">' . e(implode(' ', $bl)) . '</td></tr>';
}
if (!$rows) echo '<tr><td colspan="6">No teams yet.</td></tr>';
echo '</table></div>';
echo '<h2>First bloods</h2><div class="scroll"><table><tr><th>Challenge</th><th>Team</th></tr>';
foreach ($fb as $n => $name) echo '<tr><td>#' . $n . ' ' . e($flags[$n]['title'] ?? '') . '</td><td class="fb">' . e($name) . '</td></tr>';
if (!$fb) echo '<tr><td colspan="2">None yet.</td></tr>';
echo '</table></div>';
page_foot();
