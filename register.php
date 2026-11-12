<?php
$page_title = 'Hesab Aç';
$current = '';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon" style="margin:0 auto;width:48px;height:48px;font-size:1.4rem;">N</span>
        </div>
        <h1 class="auth-title">Yeni Hesab Açma</h1>
        <p class="auth-subtitle">Onlayn hesab açma əməliyyatları</p>

        <div class="alert alert-warning">
            Yeni müştəri qeydiyyatları hal-hazırda müvəqqəti olaraq dayandırılıb. Hesab açmaq üçün zəhmət olmasa
            ən yaxın NovaBank filialını şəxsiyyət vəsiqənizlə birlikdə ziyarət edin.
        </div>

        <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:1.25rem;margin-bottom:1.5rem;">
            <p style="font-size:0.9rem;color:var(--gray-600);margin-bottom:0.75rem;font-weight:600;">Filialda hesab açarkən yanınızda olması lazım olanlar:</p>
            <ul style="font-size:0.85rem;color:var(--gray-500);padding-left:1.25rem;">
                <li>Şəxsiyyət vəsiqəsi və FİN kodu</li>
                <li>Yaşayış yeri sənədi və ya ünvan bəyanı</li>
                <li>Vergi şəxsiyyət nömrəsi</li>
            </ul>
        </div>

        <a href="/login.php" class="btn btn-primary btn-block btn-lg">Daxil Ol</a>

        <div class="auth-footer">
            Köməyə ehtiyacınız var? <a href="/support.php">Dəstək Mərkəzi</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
