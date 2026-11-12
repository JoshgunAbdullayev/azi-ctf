<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: /login.php');
        exit;
    }
}

function getUser(): ?array {
    if (!isLoggedIn()) return null;
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function formatMoney(float $amount): string {
    return number_format($amount, 2, ',', '.');
}
