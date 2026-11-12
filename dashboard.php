<?php
$page_title = 'Hesabım';
$current = '';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: /admin/');
    exit;
}

$user = getUser();

$stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user['id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as income, SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) as expense FROM transactions WHERE user_id = ?");
$stmt->execute([$user['id']]);
$summary = $stmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Ana Səhifə</a> <span>/</span> <span>Hesabım</span>
        </div>
        <h1 class="page-header-title">Xoş gəldiniz, <?= htmlspecialchars($user['name']) ?></h1>
        <p class="page-header-subtitle">Hesab xülasəsi və son əməliyyatlarınız</p>
    </div>
</div>

<section class="section" style="padding-top:2rem;">
    <div class="container">

        <div class="alert alert-success" style="display:flex;align-items:center;gap:0.75rem;">
            <span style="font-size:1.2rem;">&#9888;&#65039;</span>
            <div>
                <strong>Təhlükəsizlik Bildirişi:</strong> Hesabınız uğurla doğrulandı.
                <span style="font-size:0.82rem;color:#166534;display:block;margin-top:0.25rem;">Son giriş: <?= date('d.m.Y H:i') ?></span>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dash-card dash-card-accent">
                <div class="dash-card-label">Ümumi Balans</div>
                <div class="dash-card-value">AZN <?= formatMoney($user['balance']) ?></div>
                <div class="dash-card-sub">IBAN: <?= htmlspecialchars($user['iban']) ?></div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Bu Ay Gəlir</div>
                <div class="dash-card-value" style="color:var(--green-600);">AZN <?= formatMoney($summary['income'] ?? 0) ?></div>
                <div class="dash-card-sub">Maaş və köçürmələr</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">Bu Ay Xərc</div>
                <div class="dash-card-value">AZN <?= formatMoney(abs($summary['expense'] ?? 0)) ?></div>
                <div class="dash-card-sub">Qəbz, kirayə və xərclər</div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-size:1.25rem;font-weight:700;">Son Əməliyyatlar</h2>
            <a href="/transfer.php" class="btn btn-primary btn-sm">Pul Köçürməsi</a>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tarix</th>
                        <th>Açıqlama</th>
                        <th>Növ</th>
                        <th style="text-align:right;">Məbləğ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                        <td><?= htmlspecialchars($tx['description']) ?></td>
                        <td>
                            <?php if ($tx['type'] === 'incoming'): ?>
                                <span class="badge badge-green">Gələn</span>
                            <?php else: ?>
                                <span class="badge badge-red">Gedən</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;" class="<?= $tx['amount'] > 0 ? 'amount-positive' : 'amount-negative' ?>">
                            <?= $tx['amount'] > 0 ? '+' : '' ?>AZN <?= formatMoney($tx['amount']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--gray-400);">Hələlik əməliyyat yoxdur.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:2rem;">
            <div class="grid-3">
                <div class="card">
                    <h3 class="card-title">Hesab Məlumatları</h3>
                    <div style="font-size:0.88rem;color:var(--gray-500);display:grid;gap:0.5rem;margin-top:0.5rem;">
                        <div><strong style="color:var(--gray-700);">Hesab No:</strong> <?= $user['id'] ?></div>
                        <div><strong style="color:var(--gray-700);">Müştəri No:</strong> <?= htmlspecialchars($user['customer_no']) ?></div>
                        <div><strong style="color:var(--gray-700);">Ad Soyad:</strong> <?= htmlspecialchars($user['name']) ?></div>
                        <div><strong style="color:var(--gray-700);">E-posta:</strong> <?= htmlspecialchars($user['email']) ?></div>
                        <div><strong style="color:var(--gray-700);">Telefon:</strong> <?= htmlspecialchars($user['phone']) ?></div>
                    </div>
                    <a href="/account.php?id=<?= $user['id'] ?>" class="card-link" style="margin-top:1rem;">Hesab Detalları &rarr;</a>
                </div>
                <div class="card">
                    <h3 class="card-title">Sürətli Əməliyyatlar</h3>
                    <div style="display:grid;gap:0.5rem;margin-top:0.75rem;">
                        <a href="/transfer.php" class="btn btn-outline btn-block btn-sm">Pul Köçürməsi</a>
                        <a href="/account.php?id=<?= $user['id'] ?>" class="btn btn-ghost btn-block btn-sm">Hesab Detalları</a>
                        <a href="/support.php" class="btn btn-ghost btn-block btn-sm">Dəstək Tələbi</a>
                        <a href="/chatbot.php" class="btn btn-ghost btn-block btn-sm">🤖 AI Köməkçi</a>
                    </div>
                </div>
                <div class="card">
                    <h3 class="card-title">Elanlar</h3>
                    <p class="card-text" style="margin-top:0.5rem;">
                        NovaBank mobil tətbiqimiz yeniləndi! App Store və Google Play-dən
                        son versiyanı yükləməyi unutmayın.
                    </p>
                </div>
            </div>
        </div>

        <div style="margin-top:2rem;">
            <div class="card" style="border:1px solid var(--primary);background:var(--red-50);">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h3 style="font-size:1rem;font-weight:700;color:var(--primary);margin-bottom:0.25rem;">&#128274; İdarəçi Bank Hesabı</h3>
                        <p style="font-size:0.85rem;color:var(--gray-500);">İdarəçi bank hesabına giriş paneli. Yalnız səlahiyyətli personal üçün.</p>
                    </div>
                    <a href="/admin_login.php" class="btn btn-primary btn-sm">İdarəçi Girişi &rarr;</a>
                </div>
            </div>
        </div>

    </div>
</section>

<div id="investPopup" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;max-width:480px;width:90%;padding:2rem;position:relative;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;">
        <div style="position:absolute;top:12px;right:16px;cursor:pointer;font-size:1.5rem;color:var(--gray-400);" onclick="document.getElementById('investPopup').style.display='none';">&times;</div>
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">&#128176;&#127775;</div>
        <h3 style="font-size:1.2rem;font-weight:800;color:var(--primary);margin-bottom:0.5rem;">XÜSUSI INVESTISIYA FÜRSATI!</h3>
        <p style="font-size:0.95rem;line-height:1.7;color:var(--gray-600);margin-bottom:1rem;">
            İndi <strong>50.000 AZN</strong> yatırın, cəmi <strong>3 ayda %200 qazanın!</strong><br>
            AMB təsdiqli zəmanətli gəlir proqramı ilə pulunuz təhlükəsizdir.
        </p>
        <div style="background:var(--green-50);border:1px solid var(--green-200);border-radius:8px;padding:1rem;margin-bottom:1.25rem;">
            <div style="font-size:0.8rem;color:var(--green-700);font-weight:600;">ƏSF ZƏMANƏTI ILƏ LIMITSIZ SIGORTA</div>
            <div style="font-size:0.78rem;color:var(--green-600);margin-top:0.25rem;">Risk dərəcəsi: %0 | Zəmanətli mənfəət: Aylıq %15-25</div>
        </div>
        <button onclick="document.getElementById('investPopup').style.display='none';" class="btn btn-primary" style="width:100%;">Indi Investisiya Edin</button>
        <p style="font-size:0.7rem;color:var(--gray-400);margin-top:0.75rem;">* AMB lisenziya no: 48291 | NovaBank ASC Xüsusi Investisiya Proqramı</p>
    </div>
</div>
<script>setTimeout(function(){document.getElementById('investPopup').style.display='flex';},1500);</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
