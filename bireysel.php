<?php
$page_title = 'Fərdi Bankçılıq';
$current = 'bireysel';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Fərdi Bankçılıq</span></div>
        <h1 class="page-header-title">Fərdi Bankçılıq</h1>
        <p class="page-header-subtitle">Gündəlik bankçılıq ehtiyaclarınızdan investisiya hədəflərinizə qədər bütün maliyyə həlləri bir yerdə.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid-3">
            <div class="card">
                <div class="card-icon card-icon-red">&#128179;</div>
                <h3 class="card-title">Müddətsiz Əmanət Hesabı</h3>
                <p class="card-text">
                    Hesab idarəetmə haqqı olmadan AZN, USD və EUR ilə müddətsiz hesab açın.
                    Ani köçürmə, avtomatik ödəniş və kart bağlama imkanı.
                </p>
                <a href="/login.php" class="card-link">Hesab Aç &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-red">&#128179;</div>
                <h3 class="card-title">Kredit Kartları</h3>
                <p class="card-text">
                    NovaCard ilə alış-verişlərinizdə xal qazanın, taksit üstünlüklərindən yararlanın.
                    Kontaktsız ödəniş və mobil cüzdan dəstəyi.
                </p>
                <a href="/login.php" class="card-link">Müraciət Et &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-blue">&#128200;</div>
                <h3 class="card-title">Fərdi Ehtiyac Krediti</h3>
                <p class="card-text">
                    Uyğun faiz dərəcələri və çevik müddət seçimləri ilə ehtiyaclarınızı
                    təxirə salmayın. Onlayn müraciət ilə ani nəticə.
                </p>
                <a href="/login.php" class="card-link">Kredit Hesabla &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-blue">&#127968;</div>
                <h3 class="card-title">Mənzil Krediti</h3>
                <p class="card-text">
                    Xəyal etdiyiniz evə NovaBank mənzil krediti ilə qovuşun. 120 aya qədər
                    müddət və uyğun faiz dərəcələri.
                </p>
                <a href="/login.php" class="card-link">Ətraflı Məlumat &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-green">&#128176;</div>
                <h3 class="card-title">Müddətli Əmanət</h3>
                <p class="card-text">
                    Yığımlarınızı dəyərləndirin. 1 aydan 12 aya qədər müddətli əmanət
                    seçimləri ilə qazancınızı artırın.
                </p>
                <a href="/login.php" class="card-link">Faiz Dərəcələri &rarr;</a>
            </div>
            <div class="card">
                <div class="card-icon card-icon-green">&#128737;</div>
                <h3 class="card-title">Sığorta Məhsulları</h3>
                <p class="card-text">
                    Həyat sığortası, mənzil sığortası və səyahət sığortası məhsullarımızla
                    özünüzü və yaxınlarınızı təminat altına alın.
                </p>
                <a href="/contact.php" class="card-link">Məlumat Al &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <h2 class="cta-title">NovaBank ilə bankçılıq çox asandır</h2>
        <p class="cta-text">İndi internet bankçılığına daxil olaraq məhsullarımızdan yararlanın.</p>
        <a href="/login.php" class="btn btn-white btn-lg">İnternet Bankçılığına Giriş</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
