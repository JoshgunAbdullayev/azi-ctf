<?php
$page_title = 'Pul Köçürməsi';
$current = '';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = getUser();

$disabled_messages = [
    'Təhlükəsizlik səbəbilə bu əməliyyat müvəqqəti olaraq dayandırılıb. AMB yoxlama prosesi davam edir.',
    'Sistem təmiri səbəbilə köçürmə əməliyyatları hal-hazırda istifadə oluna bilmir. Təxmini açılış: 48 saat.',
    'Anti-fraud sistemi yenilənir. Köçürmə əməliyyatları müvəqqəti olaraq deaktiv edilib.',
];
$msg = $disabled_messages[array_rand($disabled_messages)];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Ana Səhifə</a> <span>/</span> <a href="/dashboard.php">Hesabım</a> <span>/</span> <span>Pul Köçürməsi</span>
        </div>
        <h1 class="page-header-title">Pul Köçürməsi</h1>
        <p class="page-header-subtitle">Daxili və banklararası köçürmə əməliyyatlarınızı buradan həyata keçirə bilərsiniz.</p>
    </div>
</div>

<section class="section" style="padding-top:2rem;">
    <div class="container">
        <div style="max-width:600px;">
            <div class="alert alert-warning" style="margin-bottom:1.5rem;">
                <strong>&#9888;&#65039; Əməliyyat İstifadə Oluna Bilmir</strong>
                <p style="margin-top:0.5rem;font-size:0.88rem;"><?= htmlspecialchars($msg) ?></p>
            </div>

            <div class="card" style="padding:2rem;opacity:0.5;pointer-events:none;user-select:none;">
                <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1.5rem;">Köçürmə Məlumatları</h2>
                <div class="form-group">
                    <label class="form-label">Göndərən Hesab</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($user['iban']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Alıcı Ad Soyad</label>
                    <input type="text" class="form-input" placeholder="Alıcının adını daxil edin" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Alıcı IBAN</label>
                    <input type="text" class="form-input" placeholder="AZ00 0000 0000 0000 0000 0000 00" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Məbləğ</label>
                    <input type="text" class="form-input" placeholder="0,00" disabled>
                </div>
                <button class="btn btn-primary btn-block btn-lg" disabled>Köçürməni Təsdiqlə</button>
            </div>

            <div style="margin-top:1.5rem;">
                <a href="/dashboard.php" class="btn btn-ghost btn-sm">&larr; Hesabıma Qayıt</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
