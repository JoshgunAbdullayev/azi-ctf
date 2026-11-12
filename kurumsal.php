<?php
$page_title = 'Korporativ Bankçılıq';
$current = 'kurumsal';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Korporativ Bankçılıq</span></div>
        <h1 class="page-header-title">Korporativ Bankçılıq</h1>
        <p class="page-header-subtitle">Müəssisənizin böyüməsini dəstəkləyən hərtərəfli maliyyə həlləri və məsləhət xidmətləri.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid-3">
            <div class="card">
                <div class="card-icon card-icon-blue">&#127970;</div>
                <h3 class="card-title">Müəssisə Hesabı</h3>
                <p class="card-text">
                    Ticarət fəaliyyətləriniz üçün xüsusi hazırlanmış müəssisə hesabları.
                    Toplu ödəniş, maaş ödənişi və qəbz idarəetməsi xüsusiyyətləri.
                </p>
                <a href="/login.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-blue">&#128200;</div>
                <h3 class="card-title">Ticarət Krediti</h3>
                <p class="card-text">
                    Müəssisə sermayəsi, investisiya və böyümə üçün uyğun şərtlərlə
                    ticarət kredit həlləri. Çevik geri ödəmə planları.
                </p>
                <a href="/login.php" class="card-link">Kredit Müraciəti &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-green">&#128179;</div>
                <h3 class="card-title">POS Həlləri</h3>
                <p class="card-text">
                    Fiziki və virtual POS cihazları ilə müştərilərinizdən asanlıqla
                    ödəniş alın. Taksit və kampaniya idarəetməsi.
                </p>
                <a href="/contact.php" class="card-link">Müraciət Et &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-red">&#127758;</div>
                <h3 class="card-title">Xarici Ticarət</h3>
                <p class="card-text">
                    Akkreditiv, sənəd müqabilində ödəniş və valyuta əməliyyatları ilə
                    beynəlxalq ticarətinizi dəstəkləyirik.
                </p>
                <a href="/contact.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-red">&#128202;</div>
                <h3 class="card-title">Xəzinə Məhsulları</h3>
                <p class="card-text">
                    Valyuta, törəmə məhsullar və risk idarəetmə həlləri ilə
                    korporativ pənəgər axınınızı optimallaşdırın.
                </p>
                <a href="/contact.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-green">&#128179;</div>
                <h3 class="card-title">Korporativ Kart</h3>
                <p class="card-text">
                    Şirəət xərcləriniz üçün korporativ kredit kartları.
                    Xərc limiti idarəetməsi və ətraflı hesabat.
                </p>
                <a href="/login.php" class="card-link">Müraciət Et &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <h2 class="cta-title">Müəssisəniz üçün doğru həlli tapaq</h2>
        <p class="cta-text">Korporativ bankçılıq mütəxəssislərimizlə əlaqə saxlayaraq müəssisənizə xüsusi həlləri kəşf edin.</p>
        <a href="/contact.php" class="btn btn-white btn-lg">Bizə Müraciət Edin</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
