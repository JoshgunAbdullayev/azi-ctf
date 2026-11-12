<?php
$page_title = 'Ana Səhifə';
$current = 'home';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">NovaBank &mdash; Etibarlı Ünvan</div>
            <h1 class="hero-title">Gələcəyin bankçılığı,<br>bu gün başlayır.</h1>
            <p class="hero-subtitle">
                NovaBank ilə təhlükəsiz, sürətli və müasir bankçılıq təcrübəsini yaşayın.
                7/24 rəqəmsal bankçılıq xidmətləri ilə hər zaman yanınızdayıq.
            </p>
            <div class="hero-actions">
                <a href="/login.php" class="btn btn-white btn-lg">İnternet Bankçılığı</a>
                <a href="/bireysel.php" class="btn btn-lg" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25);">Məhsullarımızı Kəşf Edin</a>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">2M+</div>
                <div class="stat-label">Aktiv Müştəri</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">450+</div>
                <div class="stat-label">Filial Şəbəkəsi</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">7/24</div>
                <div class="stat-label">Rəqəmsal Xidmət</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">15+</div>
                <div class="stat-label">İllik Təcrübə</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Xidmətlərimiz</div>
            <h2 class="section-title">Ehtiyacınıza uyğun bankçılıq həlləri</h2>
            <p class="section-subtitle">
                Fərdi və korporativ müştərilərimiz üçün əhatəli maliyyə xidmətləri təqdim edirik.
            </p>
        </div>
        <div class="grid-3">
            <div class="card">
                <div class="card-icon card-icon-red">&#127974;</div>
                <h3 class="card-title">Fərdi Bankçılıq</h3>
                <p class="card-text">
                    Cari hesablar, kredit kartları, fərdi kreditlər və əmanət
                    məhsulları ilə maliyyə hədəflərinizə çatın.
                </p>
                <a href="/bireysel.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-blue">&#127970;</div>
                <h3 class="card-title">Korporativ Bankçılıq</h3>
                <p class="card-text">
                    Müəssisə hesabları, ticarət kreditləri, POS həlləri və xarici ticarət
                    xidmətləri ilə işinizi böyüdün.
                </p>
                <a href="/kurumsal.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-green">&#128241;</div>
                <h3 class="card-title">Rəqəmsal Bankçılıq</h3>
                <p class="card-text">
                    Mobil tətbiqimiz və internet bankçılığı ilə istədiyiniz yerdən
                    əməliyyatlarınızı asanlıqla həyata keçirin.
                </p>
                <a href="/login.php" class="card-link">Daxil Ol &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div class="section-label">Niyə NovaBank?</div>
            <h2 class="section-title">Sizə xüsusi üstünlüklər</h2>
            <p class="section-subtitle">
                Müştəri məmnuniyyətini ön planda tutaraq, innovativ məhsul və xidmətlərlə fərq yaradırıq.
            </p>
        </div>
        <div class="grid-4">
            <div class="card">
                <h3 class="card-title">Xərcsiz Hesab</h3>
                <p class="card-text">Hesab idarəetmə haqqı olmadan bankçılıq xidmətlərindən faydalanın.</p>
            </div>
            <div class="card">
                <h3 class="card-title">Ani Köçürmə</h3>
                <p class="card-text">7/24 ani pul köçürməsi ilə ödənişlərinizi saniyələr içində göndərin.</p>
            </div>
            <div class="card">
                <h3 class="card-title">Təhlükəsiz Altyapı</h3>
                <p class="card-text">Ən müasir şifrələmə texnologiyaları və çoxfaktorlu doğrulama ilə məlumatlarınız təhlükəsizdir.</p>
            </div>
            <div class="card">
                <h3 class="card-title">7/24 Dəstək</h3>
                <p class="card-text">Müştəri xidmətlərimiz günün hər saatı sizə kömək etməyə hazırdır.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <h2 class="cta-title">NovaBank ailəsinə qoşulun</h2>
        <p class="cta-text">
            Ən yaxın NovaBank filialını ziyarət edərək dərhal hesab aça bilər və ya
            mövcud hesabınızla internet bankçılığına daxil ola bilərsiniz.
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="/login.php" class="btn btn-white btn-lg">Daxil Ol</a>
            <a href="/contact.php" class="btn btn-lg" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.25);">Bizimlə Əlaqə</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
