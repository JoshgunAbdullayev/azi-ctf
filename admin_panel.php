<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn() || !isset($_SESSION['admin_bank_auth']) || $_SESSION['admin_bank_auth'] !== true) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    header('Content-Type: application/json; charset=utf-8');

    $code = $_POST['code'] ?? '';

    $rate_key = 'verify_' . session_id();
    $last_attempt = $_SESSION[$rate_key] ?? 0;
    $now = microtime(true);

    if ($now - $last_attempt < 3.0) {
        echo json_encode(['success' => false, 'error' => 'Çox tez cəhd edirsiniz. Gözləyin.', 'retry_after' => 3], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $_SESSION[$rate_key] = $now;

    if ($code === '1046') {
        $_SESSION['priority_verified'] = true;
        echo json_encode([
            'success' => true,
            'flag' => 'ADNSU5{rate_limit_bypass_bruteforce_c9f1}',
            'chat' => [
                ['role' => 'ADMIN', 'user' => 'Sistem İdarəçisi', 'date' => '15.02.2026 14:30', 'msg' => "Hakan \xc5\x9eeker bəyə fərqli xüsusiyyətləri olan bir idarəçi profili təqdim edirik, zəhmət olmasa daimi olaraq nəzarətləri oradan təmin etsin."],
                ['role' => 'MOD', 'user' => 'Mehmet K.', 'date' => '15.02.2026 14:35', 'msg' => 'Tamam, qeyd aldim. Hakan Bəyə məlumat veriləcək.'],
                ['role' => 'MOD', 'user' => 'Ayse D.', 'date' => '15.02.2026 14:38', 'msg' => 'Anlaşildı, lazımi yönləndirmələri edəcəyik.'],
                ['role' => 'ADMIN', 'user' => 'Sistem İdarəçisi', 'date' => '16.02.2026 09:15', 'msg' => "Hakan Bəyin hesabı aktivləşdirildi. İdarəçi girişindən daxil ola bilər."],
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'error' => 'Doğrulama kodu səhvdir.'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if (isset($_GET['view_photo'])) {
    $file = $_GET['view_photo'];
    $base_path = __DIR__ . '/uploads/photos/';
    $full_path = $base_path . $file;
    $real = @realpath($full_path);
    $root_dir = realpath(__DIR__);
    $private_dir = realpath(__DIR__ . '/private');
    $photos_dir = realpath(__DIR__ . '/uploads/photos');
    $uploads_dir = realpath(__DIR__ . '/uploads');

    if ($real === false) {
        $basename = basename($file);
        $try_private = @realpath(__DIR__ . '/private/' . $basename);
        if ($try_private !== false) {
            $real = $try_private;
        } else {
            header("Content-Type: text/plain; charset=utf-8");
            http_response_code(404);
            echo "File not found: /uploads/photos/" . $file;
            exit;
        }
    }

    if ($real === $uploads_dir || $real === $root_dir) {
        $real = $private_dir;
    }

    $in_photos = $photos_dir && strpos($real, $photos_dir) === 0;
    $in_private = $private_dir && strpos($real, $private_dir) === 0;

    if (!$in_photos && !$in_private) {
        header("Content-Type: text/plain; charset=utf-8");
        http_response_code(403);
        echo "Access denied: Path outside permitted scope.";
        exit;
    }

    if (is_dir($real)) {
        header("Content-Type: text/plain; charset=utf-8");
        $items = @scandir($real);
        if ($items !== false) {
            echo "Index of /storage/" . basename($real) . "/\n";
            echo str_repeat('-', 50) . "\n\n";
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $fp = $real . DIRECTORY_SEPARATOR . $item;
                $type = is_dir($fp) ? '[DIR]' : '[FILE]';
                $size = is_file($fp) ? ' (' . filesize($fp) . ' bytes)' : '';
                echo "  $type  $item$size\n";
            }
        }
    } elseif (is_file($real)) {
        $ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $mime = @mime_content_type($real) ?: 'image/jpeg';
            header("Content-Type: $mime");
            readfile($real);
        } else {
            header("Content-Type: text/plain; charset=utf-8");
            readfile($real);
        }
    }
    exit;
}

$users = $db->query("SELECT * FROM users WHERE is_admin = 0 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$total_users = count($users);
$total_balance = array_sum(array_column($users, 'balance'));
$total_tx = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();

$recent_tx = $db->query("
    SELECT t.*, u.name as user_name, u.customer_no
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İdarəçi Paneli | NovaBank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .priority-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; }
        .priority-modal.active { display:flex; }
        .priority-box { background:white; border-radius:16px; padding:2rem; max-width:550px; width:90%; max-height:80vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.25); }
        .chat-msg { padding:0.75rem; margin-bottom:0.5rem; border-radius:8px; font-size:0.85rem; }
        .chat-admin { background:#FEF2F2; border-left:3px solid var(--primary); }
        .chat-mod { background:var(--gray-50); border-left:3px solid var(--gray-300); }
        .chat-role { font-weight:700; font-size:0.75rem; text-transform:uppercase; }
        .chat-date { font-size:0.72rem; color:var(--gray-400); }
    </style>
</head>
<body style="background:var(--gray-50);">

    <div class="admin-header">
        <div class="container">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <span class="logo-icon" style="width:32px;height:32px;font-size:0.9rem;border-radius:6px;background:var(--primary);">N</span>
                <span style="font-weight:600;">NovaBank İdarəçi Paneli</span>
                <span class="badge badge-red" style="margin-left:0.5rem;">İDARƏÇİ</span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;">
                <span style="color:var(--gray-400);font-size:0.85rem;">Səlahiyyət: Super İdarəçi</span>
                <a href="/dashboard.php" style="color:var(--gray-400);font-size:0.85rem;">Hesabıma Qayıt</a>
                <a href="/logout.php" style="color:var(--gray-400);font-size:0.85rem;">Çıxış</a>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top:2rem;padding-bottom:3rem;">

        <!-- session_integrity: <?= hash('sha256', session_id()) ?> -->
        <!-- admin_ref: ADNSU3{sqli_auth_bypass_3d8e} -->

        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">İdarəçi Dashboard</h1>
        <p style="font-size:0.9rem;color:var(--gray-500);margin-bottom:2rem;">Bank ümumi idarəetmə paneli</p>

        <div class="dashboard-grid">
            <div class="dash-card dash-card-accent">
                <div class="dash-card-label">Umumi Müştəri</div>
                <div class="dash-card-value"><?= $total_users ?></div>
                <div class="dash-card-sub">Qeydiyyatlı müştəri sayı</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Umumi Əməliyyat</div>
                <div class="dash-card-value"><?= $total_tx ?></div>
                <div class="dash-card-sub">Həyata keçirilən əməliyyat sayı</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Umumi Əmanət</div>
                <div class="dash-card-value">&#8378;<?= formatMoney($total_balance) ?></div>
                <div class="dash-card-sub">Müştəri balans cəmi</div>
            </div>
        </div>

        <div style="margin-bottom:2.5rem;">
            <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Müştəri Siyahısı &amp; Profil Şəkilləri</h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Muştəri No</th>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th style="text-align:right;">Balans</th>
                            <th>Profil Şəkli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['customer_no']) ?></strong></td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td style="text-align:right;font-weight:600;">&#8378;<?= formatMoney($u['balance']) ?></td>
                            <td>
                                <a href="/admin_panel.php?view_photo=profil_<?= $u['id'] ?>.jpg"
                                   class="btn btn-outline btn-sm" style="font-size:0.75rem;padding:0.25rem 0.5rem;"
                                   target="_blank">
                                   &#128247; Görüntulə
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-bottom:2.5rem;">
            <div class="card" style="border:2px solid #F59E0B;background:#FFFBEB;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h3 style="font-size:1.05rem;font-weight:700;color:#92400E;margin-bottom:0.25rem;">&#128272; Öncəlikli İdarəetməyə Keçid</h3>
                        <p style="font-size:0.85rem;color:#92400E;">Öncəlikli idarəetmə panelinə giriş üçün e-posta doğrulaması tələb olunur.</p>
                    </div>
                    <button onclick="document.getElementById('priorityModal').classList.add('active')" class="btn btn-sm" style="background:#F59E0B;color:white;border:none;">Doğrulamanı Başlat</button>
                </div>
            </div>
        </div>

        <div>
            <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Son Əməliyyatlar</h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Müştəri</th><th>Növ</th><th>Açıqlama</th><th>Alıcı</th><th style="text-align:right;">Məbləğ</th><th>Tarix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_tx as $tx): ?>
                        <tr>
                            <td><?= $tx['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($tx['user_name']) ?></strong>
                                <div style="font-size:0.75rem;color:var(--gray-400);"><?= htmlspecialchars($tx['customer_no']) ?></div>
                            </td>
                            <td>
                                <?php if ($tx['type'] === 'incoming'): ?>
                                    <span class="badge badge-green">Gələn</span>
                                <?php else: ?>
                                    <span class="badge badge-red">Gedən</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($tx['description']) ?></td>
                            <td><?= htmlspecialchars($tx['recipient_name'] ?: '-') ?></td>
                            <td style="text-align:right;" class="<?= $tx['amount'] > 0 ? 'amount-positive' : 'amount-negative' ?>">
                                <?= $tx['amount'] > 0 ? '+' : '' ?>&#8378;<?= formatMoney($tx['amount']) ?>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="priorityModal" class="priority-modal">
        <div class="priority-box">
            <div id="verifySection">
                <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:0.5rem;">&#128272; E-posta Doğrulaması</h2>
                <p style="font-size:0.88rem;color:var(--gray-500);margin-bottom:1.5rem;">
                    Qeydiyyatlı e-posta ünvanınıza 4 rəqəmli doğrulama kodu göndərildi. Zəhmət olmasa kodu daxil edin.
                </p>
                <div id="verifyError" class="alert alert-error" style="display:none;font-size:0.85rem;"></div>
                <form id="verifyForm" onsubmit="return submitVerify(event)">
                    <input type="hidden" name="verify_code" value="1">
                    <div class="form-group">
                        <label class="form-label">Doğrulama Kodu</label>
                        <input type="text" name="code" id="codeInput" class="form-input" placeholder="4 rəqəmli kodu daxil edin"
                               maxlength="4" pattern="[0-9]{4}" style="font-size:1.5rem;text-align:center;letter-spacing:0.5rem;" required>
                    </div>
                    <button type="submit" id="verifyBtn" class="btn btn-primary btn-block btn-lg">Doğrula</button>
                    <p style="font-size:0.78rem;color:var(--gray-400);text-align:center;margin-top:0.75rem;">
                        Kod gəlməyibsə spam qovluğunuzu yoxlayın.
                    </p>
                </form>
                <button onclick="document.getElementById('priorityModal').classList.remove('active')"
                        style="display:block;margin:1rem auto 0;font-size:0.85rem;color:var(--gray-400);background:none;border:none;cursor:pointer;">Bağla</button>
            </div>
            <div id="successSection" style="display:none;">
                <div style="text-align:center;margin-bottom:1.5rem;">
                    <span style="font-size:3rem;">&#127942;</span>
                    <h2 style="font-size:1.2rem;font-weight:700;margin-top:0.5rem;">Giriş Təmin Edildi!</h2>
                    <div style="margin-top:0.75rem;padding:0.75rem;background:#166534;color:white;border-radius:8px;font-family:monospace;font-size:1.1rem;" id="flagDisplay"></div>
                </div>
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--gray-200);">Öncəlikli İdarəetmə Söhbəti</h3>
                <div id="chatMessages"></div>
                <button onclick="document.getElementById('priorityModal').classList.remove('active')"
                        class="btn btn-ghost btn-block btn-sm" style="margin-top:1rem;">Kapat</button>
            </div>
        </div>
    </div>

    <script>
    function submitVerify(e) {
        e.preventDefault();
        var form = document.getElementById('verifyForm');
        var btn = document.getElementById('verifyBtn');
        var errDiv = document.getElementById('verifyError');
        btn.disabled = true;
        btn.textContent = 'Doğrulanır...';
        errDiv.style.display = 'none';

        var formData = new FormData(form);

        fetch('/admin_panel.php', { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('verifySection').style.display = 'none';
                document.getElementById('successSection').style.display = 'block';
                document.getElementById('flagDisplay').textContent = data.flag;
                var chatDiv = document.getElementById('chatMessages');
                chatDiv.innerHTML = '';
                data.chat.forEach(function(m) {
                    var cls = m.role === 'ADMIN' ? 'chat-admin' : 'chat-mod';
                    var color = m.role === 'ADMIN' ? 'var(--primary)' : 'var(--gray-500)';
                    chatDiv.innerHTML += '<div class="chat-msg ' + cls + '">' +
                        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">' +
                        '<span class="chat-role" style="color:' + color + ';">[' + m.role + '] ' + m.user + '</span>' +
                        '<span class="chat-date">' + m.date + '</span></div>' +
                        '<div>' + m.msg + '</div></div>';
                });
            } else {
                errDiv.textContent = data.error;
                errDiv.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Doğrula';
            }
        })
        .catch(function() {
            errDiv.textContent = 'Bağlantı xətası. Yenidən cəhd edin.';
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Doğrula';
        });
        return false;
    }
    </script>

</body>
</html>
