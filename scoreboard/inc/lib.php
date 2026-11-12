<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name('sbsess');
    session_set_cookie_params(['path' => '/scoreboard/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO('sqlite:' . __DIR__ . '/../scoreboard.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA busy_timeout=5000;');
    $pdo->exec("CREATE TABLE IF NOT EXISTS teams(id INTEGER PRIMARY KEY, name TEXT UNIQUE COLLATE NOCASE NOT NULL, pass_hash TEXT NOT NULL, created_at INTEGER NOT NULL);
      CREATE TABLE IF NOT EXISTS solves(id INTEGER PRIMARY KEY, team_id INTEGER NOT NULL, chal INTEGER NOT NULL, points INTEGER NOT NULL, solved_at INTEGER NOT NULL, UNIQUE(team_id, chal));
      CREATE TABLE IF NOT EXISTS submissions(id INTEGER PRIMARY KEY, team_id INTEGER NOT NULL, chal INTEGER NOT NULL, value TEXT, correct INTEGER NOT NULL, awarded INTEGER NOT NULL DEFAULT 0, ip TEXT, created_at INTEGER NOT NULL);
      CREATE TABLE IF NOT EXISTS settings(k TEXT PRIMARY KEY, v TEXT);
      INSERT OR IGNORE INTO settings(k,v) VALUES('open','1');");
    return $pdo;
}

function load_flags(): array {
    $raw = @file_get_contents(__DIR__ . '/../flags.json');
    $j = json_decode((string)$raw, true);
    $out = [];
    foreach (($j['challenges'] ?? []) as $c) {
        if (!isset($c['number'], $c['flag'], $c['tier'])) continue;
        $c['number'] = (int)$c['number'];
        $c['title'] = $c['title'] ?? ('Challenge ' . $c['number']);
        $c['category'] = $c['category'] ?? 'Misc';
        $c['points'] = (int)($c['points'] ?? (SB_TIER_POINTS[$c['tier']] ?? 0));
        $c['hidden'] = !empty($c['hidden']);
        $out[$c['number']] = $c;
    }
    return $out;
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_open(): bool { return db()->query("SELECT v FROM settings WHERE k='open'")->fetchColumn() === '1'; }
function me(): ?array {
    if (empty($_SESSION['team'])) return null;
    $s = db()->prepare('SELECT id,name FROM teams WHERE id=?');
    $s->execute([$_SESSION['team']]);
    return $s->fetch() ?: null;
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_ok(): bool { return hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? '')); }
function redirect(string $u): never { header('Location: ' . $u); exit; }
function is_admin(): bool { return !empty($_SESSION['sb_admin']); }

function scoreboard(): array {
    $rows = db()->query("SELECT t.id,t.name,COALESCE(SUM(s.points),0) pts,COUNT(s.id) n,COALESCE(MAX(s.solved_at),0) last FROM teams t LEFT JOIN solves s ON s.team_id=t.id GROUP BY t.id ORDER BY pts DESC, last ASC, t.id ASC")->fetchAll();
    $r = 1;
    foreach ($rows as &$x) { $x['rank'] = $r++; }
    return $rows;
}
function first_bloods(): array { // chal => team name
    $q = db()->query("SELECT s.chal,t.name FROM solves s JOIN teams t ON t.id=s.team_id WHERE s.id=(SELECT s2.id FROM solves s2 WHERE s2.chal=s.chal ORDER BY s2.solved_at ASC, s2.id ASC LIMIT 1)");
    $o = [];
    foreach ($q as $r) $o[$r['chal']] = $r['name'];
    return $o;
}
function solve_counts(): array {
    $o = [];
    foreach (db()->query('SELECT chal,COUNT(*) n FROM solves GROUP BY chal') as $r) $o[$r['chal']] = $r['n'];
    return $o;
}

function page_head(string $title, string $active = '', bool $refresh = false): void {
    $t = me();
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . ' | ADNSU CTF</title><link rel="stylesheet" href="/scoreboard/style.css">';
    if ($refresh) echo '<meta http-equiv="refresh" content="' . (int)(SB_REFRESH_MS / 1000) . '">';
    echo '</head><body><header><div class="brand">ADNSU<span>//</span>CTF</div><nav>';
    $items = ['/scoreboard/' => 'Challenges', '/scoreboard/scoreboard.php' => 'Scoreboard'];
    if ($t) { $items['/scoreboard/team.php'] = 'My Team'; } else { $items['/scoreboard/login.php'] = 'Login'; $items['/scoreboard/register.php'] = 'Register'; }
    foreach ($items as $u => $l) echo '<a href="' . $u . '"' . ($active === $l ? ' class="on"' : '') . '>' . $l . '</a>';
    if ($t) echo '<a href="/scoreboard/logout.php">Logout (' . e($t['name']) . ')</a>';
    echo '</nav></header><main>';
    if (!is_open()) echo '<div class="alert warn">The competition is currently CLOSED / frozen. Submissions are disabled.</div>';
}
function page_foot(): void { echo '</main></body></html>'; }
