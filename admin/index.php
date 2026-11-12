<?php
$page_title = 'İdarəetmə Paneli';
$current = '';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$total_users = $db->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
$total_transactions = $db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_balance = $db->query("SELECT SUM(balance) FROM users WHERE is_admin = 0")->fetchColumn();

$users = $db->query("SELECT * FROM users WHERE is_admin = 0 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$recent_tx = $db->query("
    SELECT t.*, u.name as user_name, u.customer_no
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İdarəetmə Paneli | NovaBank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body style="background:var(--gray-50);">

    <div class="admin-header">
        <div class="container">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <span class="logo-icon" style="width:32px;height:32px;font-size:0.9rem;border-radius:6px;">N</span>
                <span style="font-weight:600;">NovaBank İdarəetmə Paneli</span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;">
                <span style="color:var(--gray-400);">Xoş gəldiniz, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                <a href="/" style="color:var(--gray-400);font-size:0.85rem;">Sayta Qayıt</a>
                <a href="/logout.php" style="color:var(--gray-400);font-size:0.85rem;">Çıxış</a>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top:2rem;padding-bottom:3rem;">

        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Dashboard</h1>
        <p style="font-size:0.9rem;color:var(--gray-500);margin-bottom:2rem;">Ümumi baxış və idarəetmə</p>

        <div class="dashboard-grid">
            <div class="dash-card dash-card-accent">
                <div class="dash-card-label">Ümumi Müştəri</div>
                <div class="dash-card-value"><?= $total_users ?></div>
                <div class="dash-card-sub">Qeydiyyatlı müştəri sayı</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Ümumi Əməliyyat</div>
                <div class="dash-card-value"><?= $total_transactions ?></div>
                <div class="dash-card-sub">Həyata keçirilən əməliyyat sayı</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Ümumi Əmanət</div>
                <div class="dash-card-value">AZN <?= formatMoney($total_balance ?? 0) ?></div>
                <div class="dash-card-sub">Müştəri balans cəmi</div>
            </div>
        </div>

        <div style="margin-bottom:2.5rem;">
            <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Müştəri Siyahısı</h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müştəri No</th>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Telefon</th>
                            <th>IBAN</th>
                            <th style="text-align:right;">Balans</th>
                            <th>Qeydiyyat Tarixi</th>
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
                            <td style="font-size:0.8rem;"><?= htmlspecialchars($u['iban']) ?></td>
                            <td style="text-align:right;font-weight:600;">AZN <?= formatMoney($u['balance']) ?></td>
                            <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="8" style="text-align:center;color:var(--gray-400);">Qeydiyyatlı müştəri yoxdur.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Son Əməliyyatlar</h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müştəri</th>
                            <th>Növ</th>
                            <th>Açıqlama</th>
                            <th>Alıcı</th>
                            <th style="text-align:right;">Məbləğ</th>
                            <th>Tarix</th>
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
                                <?= $tx['amount'] > 0 ? '+' : '' ?>AZN <?= formatMoney($tx['amount']) ?>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
