<?php
/**
 * NovaBank Identity Recovery - Act II shared bootstrap.
 * Self-contained: owns its OWN SQLite (act2.sqlite) + key material.
 * Does NOT touch the main app's includes/db.php or database.sqlite.
 *
 * NOTE: this file is protected from direct web access by serve.php
 * (see INTEGRATION_NOTE.md). It only runs as an include.
 */

if (!defined('ACT2_BOOT')) {
    define('ACT2_BOOT', 1);

    // --- Flags (only emitted after the real vulnerability is exercised) ---
    define('ACT2_FLAG31', 'ADNSU31{host_header_reset_poisoning_a3f9}');
    define('ACT2_FLAG32', 'ADNSU32{reset_token_account_takeover_b7e4}');
    define('ACT2_FLAG33', 'ADNSU33{mfa_client_trust_header_bypass_7c21}');
    define('ACT2_FLAG34', 'ADNSU34{cors_credentialed_origin_reflection_5b8e}');
    define('ACT2_FLAG35', 'ADNSU35{jwt_kid_path_traversal_key_confusion_9d4c}');

    // Canonical hosts the mailer is allowed to build links for.
    $GLOBALS['ACT2_CANON_HOSTS'] = [
        'localhost', 'localhost:8080', 'localhost:8091',
        '127.0.0.1', '127.0.0.1:8080', '127.0.0.1:8091',
        'novabank.com.tr', 'www.novabank.com.tr',
    ];
}

function act2_dir() {
    // directory of the identity-recovery service (where act2.sqlite lives)
    return __DIR__;
}

function act2_db() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $path = act2_dir() . '/act2.sqlite';
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA journal_mode=WAL');
    act2_init($pdo);
    return $pdo;
}

function act2_init($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS v_users(
        id INTEGER PRIMARY KEY, username TEXT, email TEXT,
        pass_hash TEXT, dashboard_secret TEXT, mfa_ref TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS v_reset(
        token TEXT PRIMARY KEY, user_id INT, host TEXT, reset_url TEXT,
        poisoned INT, used INT DEFAULT 0, created TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS v_mail(
        id INTEGER PRIMARY KEY AUTOINCREMENT, to_email TEXT, subject TEXT,
        host TEXT, reset_url TEXT, token TEXT, poisoned INT, created TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS v_session(
        sid TEXT PRIMARY KEY, user_id INT, mfa_passed INT DEFAULT 0,
        api_token TEXT, created TEXT)");

    $cnt = $pdo->query("SELECT COUNT(*) FROM v_users")->fetchColumn();
    if ($cnt == 0) {
        // Victim account targeted by the takeover chain.
        $st = $pdo->prepare("INSERT INTO v_users
            (id,username,email,pass_hash,dashboard_secret,mfa_ref)
            VALUES(?,?,?,?,?,?)");
        $st->execute([
            42,
            'deniz.aydin',
            'deniz.aydin@novabank.com.tr',
            password_hash('W1nter-2025-Deniz', PASSWORD_BCRYPT),
            ACT2_FLAG32,
            'mfaenr-7c21-deniz',
        ]);
    }
}

/** Shared HS256 secret used to sign user tokens in the CORS step.
 *  Stored in a web-blocked key file; created on first run. */
function act2_hs_secret() {
    $keyfile = act2_dir() . '/../nb-jwks-vault-9d4c/keys/hs_main.key';
    if (!file_exists($keyfile)) {
        @mkdir(dirname($keyfile), 0777, true);
        file_put_contents($keyfile, bin2hex(random_bytes(24)));
    }
    return trim(file_get_contents($keyfile));
}

function act2_session_from_cookie() {
    $sid = $_COOKIE['act2_sid'] ?? '';
    if ($sid === '') return null;
    $st = act2_db()->prepare(
        "SELECT s.*, u.username, u.email, u.mfa_ref, u.dashboard_secret
         FROM v_session s JOIN v_users u ON u.id = s.user_id
         WHERE s.sid = ?");
    $st->execute([$sid]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

// --- base64url + minimal JWT helpers (shared by CORS + JWKS steps) ---
function act2_b64url($d) {
    return rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
}
function act2_b64url_decode($d) {
    $pad = strlen($d) % 4;
    if ($pad) $d .= str_repeat('=', 4 - $pad);
    return base64_decode(strtr($d, '-_', '+/'));
}
function act2_jwt_hs256($header, $payload, $secret) {
    $h = act2_b64url(json_encode($header, JSON_UNESCAPED_SLASHES));
    $p = act2_b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));
    $sig = act2_b64url(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$sig";
}
