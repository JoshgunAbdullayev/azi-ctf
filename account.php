<?php
$page_title = 'Hesab Detallari';
$current = '';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (isAdmin()) {
    header('Location: /admin/');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Ana Səhifə</a> <span>/</span> <a href="/dashboard.php">Hesabım</a> <span>/</span> <span>Hesab Detallari</span>
        </div>
        <h1 class="page-header-title">Hesab Detallari</h1>
        <p class="page-header-subtitle">Muştəri hesab məlumatlarını görüntuləyin</p>
    </div>
</div>

<section class="section" style="padding-top:2rem;">
    <div class="container">
        <?php if (!$account): ?>
            <div class="alert alert-error">Göstərilən hesab nömrəsinə aid qeyd tapılmadı.</div>
            <a href="/dashboard.php" class="btn btn-primary">Hesabıma Qayıt</a>
        <?php elseif ($account['is_admin']): ?>
            <div class="card" style="max-width:600px;">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                    <div style="width:56px;height:56px;background:var(--primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">
                        <?= mb_substr($account['name'], 0, 1, 'UTF-8') ?>
                    </div>
                    <div>
                        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:0.15rem;"><?= htmlspecialchars($account['name']) ?></h2>
                        <span class="badge badge-red">Sistem İdarəçisi</span>
                    </div>
                </div>
                <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:1rem;">
                    <div style="font-size:0.88rem;color:var(--gray-500);display:grid;gap:0.6rem;">
                        <div><strong style="color:var(--gray-700);">Hesab No:</strong> <?= $account['id'] ?></div>
                        <div><strong style="color:var(--gray-700);">Ad Soyad:</strong> <?= htmlspecialchars($account['name']) ?></div>
                        <div><strong style="color:var(--gray-700);">Rol:</strong> Sistem İdarəçisi</div>
                        <div><strong style="color:var(--gray-700);">Hesab Vəziyyəti:</strong> <span style="color:var(--green-600);">Aktiv</span></div>
                        <div><strong style="color:var(--gray-700);">Qeydiyyat Tarixi:</strong> <?= date('d.m.Y', strtotime($account['created_at'])) ?></div>
                        <div style="color:var(--gray-400);font-size:0.82rem;margin-top:0.5rem;font-style:italic;">Digər məlumatlar səlahiyyət səviyyəniz üçün məhdudlaşdırılmışdır.</div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card" style="max-width:600px;">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
                    <div style="width:56px;height:56px;background:var(--blue-500);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">
                        <?= mb_substr($account['name'], 0, 1, 'UTF-8') ?>
                    </div>
                    <div>
                        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:0.15rem;"><?= htmlspecialchars($account['name']) ?></h2>
                        <span class="badge badge-blue">Fərdi Muştəri</span>
                    </div>
                </div>
                <div style="font-size:0.88rem;color:var(--gray-500);display:grid;gap:0.75rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:1rem;">
                            <div style="font-size:0.78rem;color:var(--gray-400);margin-bottom:0.25rem;">Hesab No</div>
                            <div style="font-weight:600;color:var(--gray-800);"><?= $account['id'] ?></div>
                        </div>
                        <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:1rem;">
                            <div style="font-size:0.78rem;color:var(--gray-400);margin-bottom:0.25rem;">Muştəri No</div>
                            <div style="font-weight:600;color:var(--gray-800);"><?= htmlspecialchars($account['customer_no']) ?></div>
                        </div>
                    </div>
                    <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:1rem;display:grid;gap:0.5rem;">
                        <div><strong style="color:var(--gray-700);">Ad Soyad:</strong> <?= htmlspecialchars($account['name']) ?></div>
                        <div><strong style="color:var(--gray-700);">E-posta:</strong> <?= htmlspecialchars($account['email']) ?></div>
                        <div><strong style="color:var(--gray-700);">Telefon:</strong> <?= htmlspecialchars($account['phone']) ?></div>
                        <div><strong style="color:var(--gray-700);">IBAN:</strong> <?= htmlspecialchars($account['iban']) ?></div>
                    </div>
                    <div style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--radius-sm);padding:1.25rem;color:white;">
                        <div style="font-size:0.8rem;opacity:0.8;margin-bottom:0.25rem;">Cari Balans</div>
                        <div style="font-size:1.5rem;font-weight:700;">AZN <?= formatMoney($account['balance']) ?></div>
                    </div>
                    <div style="font-size:0.82rem;color:var(--gray-400);">
                        Hesab açılış tarixi: <?= date('d.m.Y', strtotime($account['created_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($account && !$account['is_admin']):
            $txStmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
            $txStmt->execute([$id]);
            $txList = $txStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (!empty($txList)): ?>
        <div class="card" style="max-width:600px;margin-top:1.5rem;">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Hesab Hərəkətləri</h3>
            <div style="overflow-x:auto;">
                <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--gray-200);text-align:left;">
                            <th style="padding:0.5rem;">Tarix</th>
                            <th style="padding:0.5rem;">Açıqlama</th>
                            <th style="padding:0.5rem;">Məbləğ</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($txList as $tx): ?>
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:0.5rem;white-space:nowrap;"><?= date('d.m.Y H:i', strtotime($tx['created_at'])) ?></td>
                            <td style="padding:0.5rem;"><?= htmlspecialchars($tx['description']) ?></td>
                            <td style="padding:0.5rem;font-weight:600;color:<?= $tx['amount'] >= 0 ? 'var(--green-600)' : 'var(--primary)' ?>;">
                                <?= $tx['amount'] >= 0 ? '+' : '' ?>AZN <?= formatMoney($tx['amount']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top:1.5rem;">
            <a href="/dashboard.php" class="btn btn-ghost btn-sm">&larr; Hesabıma Qayıt</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
