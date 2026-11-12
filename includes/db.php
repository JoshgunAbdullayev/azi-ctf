<?php

$db_path = __DIR__ . '/../database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('PRAGMA journal_mode = WAL');
    $db->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    die('Database connection error.');
}

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_no TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT DEFAULT '',
    password TEXT NOT NULL,
    balance REAL DEFAULT 0,
    iban TEXT DEFAULT '',
    is_admin INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    type TEXT NOT NULL,
    amount REAL NOT NULL,
    description TEXT DEFAULT '',
    recipient_name TEXT DEFAULT '',
    recipient_iban TEXT DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS admin_accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'admin',
    last_login DATETIME DEFAULT NULL
)");

$stmt = $db->prepare("SELECT id FROM users WHERE id = 1");
$stmt->execute();
if (!$stmt->fetch()) {
    $hash = password_hash('KYr_2026!_X9mQ#zP', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (id, customer_no, name, email, phone, password, balance, iban, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([1, 'NBK-ADMIN-001', "Kerem Y\xc4\xb1ld\xc4\xb1r\xc4\xb1m", 'kerem.yildirim@novabank.com.tr', '+90 533 100 00 01', $hash, 0, '', 1, '2024-03-15 09:00:00']);
}

$stmt = $db->prepare("SELECT id FROM users WHERE id = 37");
$stmt->execute();
if (!$stmt->fetch()) {
    $hash = password_hash('AhmetY2025!', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (id, customer_no, name, email, phone, password, balance, iban, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([37, '10080045', "Ahmet Y\xc4\xb1lmaz", 'ahmet.yilmaz@email.com', '+90 532 845 67 89', $hash, 24850.75, 'TR33 0001 0000 1008 0045 0000 01', 0, '2025-06-20 14:30:00']);
    $uid = 37;
    $txns = [
        [$uid, 'incoming', 8500.00, 'Maaş Ödənişi - Fevral 2026', '', '', '2026-02-25 09:00:00'],
        [$uid, 'outgoing', -234.50, 'Market Alış-verişi - Migros', '', '', '2026-02-24 14:32:00'],
        [$uid, 'outgoing', -187.30, 'Elektrik Qəbzi', '', '', '2026-02-23 10:15:00'],
        [$uid, 'outgoing', -3500.00, 'Kirayə Ödənişi', 'Mehmet Demir', 'TR50 0001 0000 2345 6789 5001', '2026-02-22 08:00:00'],
        [$uid, 'incoming', 1250.00, 'Köçürmə - Ali Kaya', '', '', '2026-02-20 16:45:00'],
    ];
    $ins = $db->prepare("INSERT INTO transactions (user_id, type, amount, description, recipient_name, recipient_iban, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($txns as $t) { $ins->execute($t); }
}

$stmt = $db->prepare("SELECT id FROM users WHERE id = 89");
$stmt->execute();
if (!$stmt->fetch()) {
    $hash = password_hash('ZeynepA_2025!', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (id, customer_no, name, email, phone, password, balance, iban, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([89, '10063217', 'Zeynep Arslan', 'zeynep.arslan@email.com', '+90 505 312 44 78', $hash, 42100.00, 'TR41 0001 0000 1006 3217 0000 01', 0, '2025-08-12 11:20:00']);
    $uid = 89;
    $txns = [
        [$uid, 'incoming', 12000.00, 'Maaş Ödənişi - Fevral 2026', '', '', '2026-02-25 09:00:00'],
        [$uid, 'outgoing', -1200.00, 'Kredit Kartı Ödənişi', '', '', '2026-02-24 10:00:00'],
    ];
    $ins = $db->prepare("INSERT INTO transactions (user_id, type, amount, description, recipient_name, recipient_iban, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($txns as $t) { $ins->execute($t); }
}

$stmt = $db->prepare("SELECT id FROM users WHERE id = 125");
$stmt->execute();
if (!$stmt->fetch()) {
    $hash = password_hash('EmreKaya2025!', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (id, customer_no, name, email, phone, password, balance, iban, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([125, '10052089', 'Emre Kaya', 'emre.kaya@email.com', '+90 541 789 23 45', $hash, 18340.50, 'TR76 0001 0000 1005 2089 0000 01', 0, '2025-11-08 10:15:00']);
    $uid = 125;
    $txns = [
        [$uid, 'incoming', 6500.00, 'Maaş Ödənişi - Fevral 2026', '', '', '2026-02-25 09:00:00'],
        [$uid, 'outgoing', -450.00, 'Kirayə Ödənişi', 'Ayse Korkmaz', 'TR88 0001 0000 3456 7890 1234', '2026-02-23 08:00:00'],
        [$uid, 'outgoing', -125.80, 'Təbii Qaz Qəbzi', '', '', '2026-02-22 11:30:00'],
        [$uid, 'incoming', 2000.00, 'Freelance Ödəniş - Web Layihəsi', '', '', '2026-02-20 14:00:00'],
        [$uid, 'outgoing', -89.90, 'Spotify Premium', '', '', '2026-02-19 00:00:00'],
        [$uid, 'outgoing', -340.00, 'Market Alış-verişi - A101', '', '', '2026-02-18 16:45:00'],
        [$uid, 'incoming', 750.00, 'Köçürmə - Mehmet Kaya', '', '', '2026-02-15 10:20:00'],
        [$uid, 'outgoing', -47.50, 'İdarəetmə Fondu Tutulması', '', '', '2026-02-14 03:12:00'],
        [$uid, 'outgoing', -23.90, 'Xüsusi Hesab Əməliyyat Haqqı', '', '', '2026-02-10 02:48:00'],
        [$uid, 'outgoing', -15.00, 'Rəqəmsal İnfrastruktur Töhfəsi', '', '', '2026-02-05 04:30:00'],
        [$uid, 'outgoing', -31.75, 'Hesab Təhlükəsizlik Sığortası', '', '', '2026-01-28 03:05:00'],
        [$uid, 'outgoing', -19.99, 'Müştəri Sadiqlik Fondu Tutulması', '', '', '2026-01-20 02:15:00'],
        [$uid, 'outgoing', -9999.00, 'Internal Ref: ADNSU2{idor_account_data_leak_a7c4}', 'NB-INTERNAL-RESERVE', 'TR00 0000 0000 0000 0000 00', '2026-01-15 03:44:00'],
    ];
    $ins = $db->prepare("INSERT INTO transactions (user_id, type, amount, description, recipient_name, recipient_iban, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($txns as $t) { $ins->execute($t); }
}

$stmt = $db->prepare("SELECT id FROM admin_accounts LIMIT 1");
$stmt->execute();
if (!$stmt->fetch()) {
    $stmt = $db->prepare("INSERT INTO admin_accounts (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(["Kerem Y\xc4\xb1ld\xc4\xb1r\xc4\xb1m", 'Kyr_N0v4_2026!_Sec', 'superadmin']);
    $stmt->execute(["Hakan \xc5\x9eeker", 'HkS_Pr1v_2026!_Adm', 'priority_admin']);
}
