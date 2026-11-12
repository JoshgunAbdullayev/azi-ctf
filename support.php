<?php
$page_title = 'Kömək & Dəstək';
$current = '';
require_once __DIR__ . '/includes/auth.php';

$debug_mode = false;
if (isset($_COOKIE['debug_mode']) && $_COOKIE['debug_mode'] === 'true') {
    $debug_mode = true;
}

if (isset($_COOKIE['user_role'])) {
    $cookie_role = $_COOKIE['user_role'];
} else {
    $cookie_role = 'guest';
    setcookie('user_role', 'guest', time() + 86400, '/');
}

$ticket_submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_submit'])) {
    $ticket_submitted = true;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Kömək & Dəstək</span></div>
        <h1 class="page-header-title">Kömək & Dəstək</h1>
        <p class="page-header-subtitle">Tez-tez verilən suallar və dəstək kanalları.</p>
    </div>
</div>

<section class="section">
    <div class="container">

        <?php if ($debug_mode): ?>
        <div class="alert alert-warning" style="margin-bottom:1.5rem;border:2px solid #F59E0B;">
            <strong>Debug Mode Active</strong>
            <div style="margin-top:0.5rem;font-size:0.85rem;font-family:monospace;background:#1F2937;color:#E5E7EB;padding:1rem;border-radius:6px;">
                <div>Session Data:</div>
                <pre style="margin-top:0.5rem;"><?= htmlspecialchars(json_encode($_SESSION, JSON_PRETTY_PRINT)) ?></pre>
                <div style="margin-top:0.75rem;">Cookie Data:</div>
                <pre style="margin-top:0.5rem;"><?= htmlspecialchars(json_encode($_COOKIE, JSON_PRETTY_PRINT)) ?></pre>
                <div style="margin-top:0.75rem;">Server Config:</div>
                <pre style="margin-top:0.5rem;">PHP Version: <?= phpversion() ?>
Document Root: <?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? '/var/www/novabank') ?>
Internal API Key: nb_internal_api_8f3k2m9x
JWT Secret: n0v4b4nk_jwt_s3cr3t_k3y_2026
HMAC Secret: adnsu7floor</pre>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($cookie_role === 'support_admin'): ?>
        <div class="alert alert-success" style="margin-bottom:1.5rem;">
            <strong>Dəstək İdarəçisi Paneli</strong>
            <div style="margin-top:0.5rem;font-family:monospace;background:#166534;color:white;padding:0.5rem 0.75rem;border-radius:6px;display:inline-block;">
                ADNSU15{cookie_tampering_role_escalation_5e8d}
            </div>
            <div style="margin-top:0.75rem;font-size:0.85rem;">
                <div style="padding:0.4rem 0;border-bottom:1px solid rgba(255,255,255,0.2);">Bilet #1042 - "Hesabımdan icazəsiz tutulma" - Emre Kaya - <span style="color:#FCA5A5;">Açıq</span></div>
                <div style="padding:0.4rem 0;border-bottom:1px solid rgba(255,255,255,0.2);">Bilet #1043 - "İnvestisiya fondu qaytarılması" - Zeynep Arslan - <span style="color:#FCA5A5;">Açıq</span></div>
                <div style="padding:0.4rem 0;border-bottom:1px solid rgba(255,255,255,0.2);">Bilet #1044 - "Saxta əməliyyat bildirişi" - Ahmet Yılmaz - <span style="color:#FDE68A;">Gözləyir</span></div>
                <div style="padding:0.4rem 0;">Bilet #1045 - "Balans uyğunsuzluğu" - 3 müştəri - <span style="color:#FCA5A5;">Təcili</span></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($ticket_submitted): ?>
        <div class="alert alert-success" style="margin-bottom:1.5rem;">
            Dəstək biletiniz uğurla göndərildi. Ən qısa zamanda sizə geri dönəcəyik.
        </div>
        <?php endif; ?>

        <div class="two-col">
            <div>
                <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:1.5rem;">Tez-tez Verilən Suallar</h2>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">İnternet bankçılığı şifrəmi necə dəyişdirə bilərəm?</h3>
                    <p class="card-text">
                        İnternet bankçılığına daxil olduqdan sonra "Tənzimləmələr" bölməsindən
                        şifrə dəyişdirmə əməliyyatını həyata keçirə bilərsiniz. Alternativ olaraq
                        444 0 682 nömrəli çağrı mərkəzimizə zəng edə bilərsiniz.
                    </p>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">Şifrəmi unutdum, nə etməliyəm?</h3>
                    <p class="card-text">
                        Müştəri xidmətlərimizi 444 0 682 nömrəsindən zəng edərək şəxsiyyət doğrulaması
                        sonrası yeni şifrə təyin edə bilərsiniz. Həmçinin ən yaxın NovaBank
                        filialına şəxsiyyət vəsiqəniz ilə müraciət edə bilərsiniz.
                    </p>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">Gündəlik köçürmə limitim nə qədərdir?</h3>
                    <p class="card-text">
                        Standart gündəlik köçürmə limiti 50.000 AZN-dir. Limit artışı üçün
                        filialınıza müraciət etməniz tələb olunur.
                    </p>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">Banklararası köçürmə nə vaxt qarşı tərəfə çatır?</h3>
                    <p class="card-text">
                        Banklararası köçürmə əməliyyatları iş saatları (08:30 - 17:30) daxilində eyni gün,
                        iş saatlarından kənarda edilmiş əməliyyatlar isə növbəti iş günü
                        qarşı tərəfə çatır. NovaBank müştəriləri arası köçürmə əməliyyatları anlıqdır.
                    </p>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">Kredit kartımı necə ləğv edirəm?</h3>
                    <p class="card-text">
                        Kredit kartı ləğv əməliyyatınızı çağrı mərkəzimizdən və ya filiallarımızdan
                        həyata keçirə bilərsiniz. Kartınızda açıq borc varsa əvvəlcə borcunuzu
                        bağlamalısınız.
                    </p>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <h3 class="card-title">Hesabıma şübhəli bir giriş olduğunu düşünürəm.</h3>
                    <p class="card-text">
                        Dərhal 444 0 682 nömrəli çağrı mərkəzimizə zəng edin. Hesabınız müvəqqəti
                        olaraq bloklanacaq və təhlükəsizlik araşdırması başladilacaqdır.
                    </p>
                </div>

                <div class="card">
                    <h3 class="card-title">Dəstək bileti göndər</h3>
                    <form method="POST">
                        <input type="hidden" name="ticket_submit" value="1">
                        <div class="form-group">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" class="form-input" placeholder="Adınızı daxil edin" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mövzu</label>
                            <select class="form-input">
                                <option>Ümumi Sual</option>
                                <option>Texniki Dəstək</option>
                                <option>Hesab Problemi</option>
                                <option>Şikayət</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mesaj</label>
                            <textarea class="form-input" rows="3" placeholder="Mesajınızı yazın..." required style="resize:vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Göndər</button>
                    </form>
                </div>
            </div>

            <div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Dəstək Kanalları</h3>
                    <ul style="list-style:none;display:grid;gap:0.8rem;font-size:0.88rem;color:var(--gray-600);">
                        <li>
                            <strong style="color:var(--gray-800);">Çağrı Mərkəzi</strong><br>
                            444 0 682 (7/24 xidmət)
                        </li>
                        <li>
                            <strong style="color:var(--gray-800);">WhatsApp Dəstək</strong><br>
                            +994 50 444 06 82
                        </li>
                        <li>
                            <strong style="color:var(--gray-800);">E-posta</strong><br>
                            destek@novabank.az
                        </li>
                        <li>
                            <strong style="color:var(--gray-800);">Filial Ziyarəti</strong><br>
                            <a href="/contact.php" style="color:var(--primary);">Ən yaxın filialı tap</a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Təcili Hallar</h3>
                    <p style="font-size:0.88rem;color:var(--gray-600);">
                        Kartınız oğurlandısa və ya itdisə dərhal <strong>444 0 682</strong>
                        nömrəsinə zəng edərək kartınızı blokladın. Mobil tətbiqiəmizdən
                        də anlıq kart blok əməliyyatı edə bilərsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page rendered at <?= date('Y-m-d H:i:s') ?> | Build: 20260215-nightly-rc3 -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
