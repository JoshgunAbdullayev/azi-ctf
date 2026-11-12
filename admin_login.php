<?php
$page_title = 'İdarəçi Girişi';
$current = '';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: /admin/');
    exit;
}

if (isset($_SESSION['admin_bank_auth']) && $_SESSION['admin_bank_auth'] === true) {
    header('Location: /admin_panel.php');
    exit;
}
if (isset($_SESSION['hakan_auth']) && $_SESSION['hakan_auth'] === true) {
    header('Location: /hakan_panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['admin_username'] ?? '');
    $password = $_POST['admin_password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Zəhmət olmasa bütün sahələri doldurun.';
    } else {
        // WAF: blocks data-exfiltration style injection (UNION/stacked queries,
        // time-based blind, comment-based dumping). A simple boolean auth
        // bypass (e.g. ' OR '1'='1) is intentionally still possible.
        $waf_patterns = [
            '/\bunion\b/i',
            '/\bselect\b/i',
            '/\bdrop\b/i',
            '/\binsert\b/i',
            '/\bdelete\b/i',
            '/\bupdate\b/i',
            '/\bexec\b/i',
            '/\bsleep\b/i',
            '/\bbenchmark\b/i',
            '/\bwaitfor\b/i',
            '/\bchar\(/i',
            '/0x[0-9a-f]/i',
        ];

        $waf_blocked = false;
        foreach ($waf_patterns as $pattern) {
            if (preg_match($pattern, $password)) {
                $waf_blocked = true;
                break;
            }
        }

        if ($waf_blocked) {
            http_response_code(403);
            $error = '⛔ WAF: Şübhəli giriş aşkarlandı. Sorğunuz bloklandı. [NB-WAF-7.2]';
        } else {
            $password = urldecode($password);

            $stmt = $db->prepare("SELECT * FROM admin_accounts WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                $error = 'İdarəçi adı səhvdir!';
            } else {
                try {
                    $query = "SELECT * FROM admin_accounts WHERE username = '" . $admin['username'] . "' AND password = '" . $password . "'";
                    $result = $db->query($query);
                    $row = $result->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        if ($admin['role'] === 'priority_admin') {
                            $_SESSION['hakan_auth'] = true;
                            header('Location: /hakan_panel.php');
                        } else {
                            $_SESSION['admin_bank_auth'] = true;
                            header('Location: /admin_panel.php');
                        }
                        exit;
                    } else {
                        $error = 'Şifrə səhvdir!';
                    }
                } catch (PDOException $e) {
                    $error = 'Sistem xətası baş verdi. [Ref: DB-' . substr(md5($e->getMessage()), 0, 6) . ']';
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card" style="max-width:460px;">
        <div class="auth-logo">
            <span class="logo-icon" style="margin:0 auto;width:48px;height:48px;font-size:1.4rem;background:var(--gray-900);">&#128274;</span>
        </div>
        <h1 class="auth-title">İdarəçi Bank Hesabı</h1>
        <p class="auth-subtitle">Bu sahəyə yalnız səlahiyyətli idarəçilər daxil ola bilər</p>

        <?php if ($error): ?>
            <div class="alert alert-error" style="font-size:0.85rem;"><?= $error ?></div>
        <?php endif; ?>

        <div class="alert alert-warning" style="font-size:0.82rem;">
            <strong>Təhlükəsizlik Xəbərdarlığı:</strong> Bu giriş paneli NovaBank idarəetmə həyəti üçün ayrılmışdır.
            İcazasiz giriş cəhdləri qeydə alınır və hesabat verilir.
        </div>

        <form method="POST" action="/admin_login.php">
            <div class="form-group">
                <label class="form-label" for="admin_username">İdarəçi Adı</label>
                <input id="admin_username" name="admin_username" type="text" class="form-input"
                       placeholder="İdarəçi adını daxil edin"
                       value="<?= htmlspecialchars($_POST['admin_username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="admin_password">İdarəçi Şifrəsi</label>
                <input id="admin_password" name="admin_password" type="password" class="form-input"
                       placeholder="Şifrənizi daxil edin" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="background:var(--gray-900);">İdarəçi Girişi</button>
        </form>

        <div class="auth-footer">
            <a href="/dashboard.php">&larr; Hesabıma Qayıt</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
