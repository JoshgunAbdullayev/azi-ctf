<?php
$page_title = 'Bloq';
$current = 'blog';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Bloq</span></div>
        <h1 class="page-header-title">NovaBank Bloq</h1>
        <p class="page-header-subtitle">Maliyyə savadlılığı, bankçılıq trendləri və NovaBank-dan xəbərlər.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div style="display:grid;gap:1.25rem;">
                <a href="/blog/guvenlik.php" class="blog-card" style="border-left:3px solid var(--primary);">
                    <div class="blog-card-date">28 Fevral 2026 &middot; 3 dəq oxuma &middot; <span style="color:var(--primary);font-weight:600;">SEÇİLMİŞ</span></div>
                    <h2 class="blog-card-title">Niyə Tam Etibarlıyıq?</h2>
                    <p class="blog-card-excerpt">
                        NovaBank təhlükəsizlik infrastrukturu, beynəlxalq sertifkatlar və %300 daha təhlükəsiz
                        bnakaçılıq xidmətlərimiz haqqında ətraflı məlumatlandırma.
                    </p>
                    <div style="display:flex;gap:0.5rem;">
                        <span class="blog-tag">Təhlükəsizlik</span>
                        <span class="blog-tag">Sertifikat</span>
                        <span class="blog-tag">AMB</span>
                    </div>
                </a>

                <a href="/blog/dijital-bankacilik.php" class="blog-card">
                    <div class="blog-card-date">27 Fevral 2026 &middot; 5 dəq oxuma</div>
                    <h2 class="blog-card-title">Rəqəmsal Bankçılığın Gələcəyi: 2026 Trendləri</h2>
                    <p class="blog-card-excerpt">
                        Süni intellekt dəstəkli müştəri xidmətlərindən açıq bankçılıq tətbiqətlərinə,
                        2026-cı ildə bankçılıq sektoruna forma verəcək önəmli trendləri nəzərdən keçiririk.
                    </p>
                    <div style="display:flex;gap:0.5rem;">
                        <span class="blog-tag">Rəqəmsal Bankçılıq</span>
                        <span class="blog-tag">Trendlər</span>
                    </div>
                </a>

                <a href="/blog/tasarruf-yonetimi.php" class="blog-card">
                    <div class="blog-card-date">20 Fevral 2026 &middot; 4 dəq oxuma</div>
                    <h2 class="blog-card-title">Əmanət Hesabınızı Ən Səmərəli Şəkildə İdarə Etməyin 5 Yolu</h2>
                    <p class="blog-card-excerpt">
                        Yığımlarınızı dəyərləndirmək və maliyyə hədəflərinizə çatmaq üçün
                        tətbiq edə biləcəyiniz praktik əmanət strategiyaları.
                    </p>
                    <div style="display:flex;gap:0.5rem;">
                        <span class="blog-tag">Əmanət</span>
                        <span class="blog-tag">Maliyyə</span>
                    </div>
                </a>

                <a href="/blog/konut-kredisi.php" class="blog-card">
                    <div class="blog-card-date">12 Fevral 2026 &middot; 6 dəq oxuma</div>
                    <h2 class="blog-card-title">Mənzil Krediti Müraciət Prosesində Bilməli Olduqlarınız</h2>
                    <p class="blog-card-excerpt">
                        İlk dəfə mənzil krediti müraciəti edəcəklər üçün hərtərəfli bələdçi.
                        Lazımi sənədlər, faiz dərəcələri və diqqət edilməli məqamlar.
                    </p>
                    <div style="display:flex;gap:0.5rem;">
                        <span class="blog-tag">Mənzil Krediti</span>
                        <span class="blog-tag">Bələdçi</span>
                    </div>
                </a>
            </div>

            <div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Kateqoriyalar</h3>
                    <ul class="sidebar-list">
                        <li><a href="#">Rəqəmsal Bankçılıq (3)</a></li>
                        <li><a href="#">Fərdi Maliyyə (5)</a></li>
                        <li><a href="#">Kredit & İpoteka (2)</a></li>
                        <li><a href="#">İnvestisiya (4)</a></li>
                        <li><a href="#">NovaBank Xəbərləri (6)</a></li>
                    </ul>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Populyar Yazılar</h3>
                    <ul class="sidebar-list">
                        <li><a href="/blog/dijital-bankacilik.php">Rəqəmsal Bankçılığın Gələcəyi</a></li>
                        <li><a href="/blog/tasarruf-yonetimi.php">Əmanət İdarəetmə Bələdçisi</a></li>
                        <li><a href="/blog/konut-kredisi.php">Mənzil Krediti Bələdçisi</a></li>
                    </ul>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Bülletenimizə Abonə Olun</h3>
                    <p style="font-size:0.85rem;color:var(--gray-500);margin-bottom:0.75rem;">
                        Maliyyə dünyasındakı inkişafları və NovaBank-dan xəbərləri e-posta ilə izləyin.
                    </p>
                    <form style="display:flex;gap:0.5rem;">
                        <input type="email" class="form-input" placeholder="E-posta ünvanınız" style="flex:1;">
                        <button type="button" class="btn btn-primary btn-sm">Abonə Ol</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
