<?php
$page_title = 'Haqqımızda';
$current = 'about';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Haqqımızda</span></div>
        <h1 class="page-header-title">Haqqımızda</h1>
        <p class="page-header-subtitle">NovaBank ASC, 2008-ci ildən bəri Azərbaycanın etibarlı rəqəmsal bankçılıq həll ortağıdır.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="prose">
                <h2>Hekayəmiz</h2>
                <p>
                    NovaBank, 2008-ci ildə Bakıda kiçik bir fintech təşəbbüsü olaraq qurulmuş,
                    qısa müddətdə Azərbaycanın ən sürətlə böyüyən rəqəmsal banklarından biri halına
                    gəlmişdir. Qurulduğumuz gündən bəri müştəri yönümlü yanaşmamız və innovativ
                    texnologiyamızla sektorda fərq yaratmağı davam etdiririk.
                </p>

                <h2>Missiyamız</h2>
                <p>
                    Bankçılıq xidmətlərini hər kəs üçün əlçatan, şəffaf və asan halə gətirmək.
                    Müştərilərimizin maliyyə hədəflərinə çatmasına kömək etmək üçün
                    daima innovativ həllər hazırlayırıq.
                </p>

                <h2>Viziyamız</h2>
                <p>
                    Azərbaycanın ən çox üstünlük verilən rəqəmsal bankı olmaq və qlobal arenada
                    Azərbaycan bankçılıq sektorunu təmsil edən lider qurumlardan birinə çevrilmək.
                </p>

                <h2>Dəyərlərimiz</h2>
                <ul>
                    <li><strong>Etibar:</strong> Müştərilərimizin maliyyə təhlükəsizliyini hər şeyin üstündə tuturuq.</li>
                    <li><strong>İnnovasiya:</strong> Texnologiyanı bankçılığın mərkəzinə qoyaraq sektora istiqamət veririk.</li>
                    <li><strong>Şəffaflıq:</strong> Tariflərimiz, şərtlərimiz və proseslərimiz hər zaman açıq və anlaşılandır.</li>
                    <li><strong>Müştəri Yönümlülük:</strong> Hər qərarımızı müştəri təcrübəsini yaxşılaşdırmaq üçün alırıq.</li>
                </ul>

                <h2>İdarə Heyəti</h2>
                <ul>
                    <li><strong>Mehmet Kaya</strong> — İdarə Heyətinin Sədri</li>
                    <li><strong>Ayşə Demir</strong> — Baş Direktor (CEO)</li>
                    <li><strong>Can Öztürk</strong> — Maliyyə Direktoru (CFO)</li>
                    <li><strong>Elif Arslan</strong> — Texnologiya Direktoru (CTO)</li>
                    <li><strong>Burak Çəlik</strong> — Əməliyyat Direktoru (COO)</li>
                </ul>
            </div>
            <div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Rəqəmlərlə NovaBank</h3>
                    <ul style="list-style:none;display:grid;gap:1rem;font-size:0.9rem;">
                        <li>
                            <div style="font-size:1.5rem;font-weight:700;color:var(--primary);">2M+</div>
                            <div style="color:var(--gray-500);">Aktiv Müştəri</div>
                        </li>
                        <li>
                            <div style="font-size:1.5rem;font-weight:700;color:var(--primary);">450+</div>
                            <div style="color:var(--gray-500);">Filial</div>
                        </li>
                        <li>
                            <div style="font-size:1.5rem;font-weight:700;color:var(--primary);">5.200+</div>
                            <div style="color:var(--gray-500);">İşçi</div>
                        </li>
                        <li>
                            <div style="font-size:1.5rem;font-weight:700;color:var(--primary);">42 Milyard AZN</div>
                            <div style="color:var(--gray-500);">Aktiv Böyüklük</div>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Mükafatlar & Nailəyyatlar</h3>
                    <ul style="list-style:none;display:grid;gap:0.6rem;font-size:0.88rem;color:var(--gray-600);">
                        <li>&#127942; 2025 — Ən Yaxşı Rəqəmsal Bank (Maliyyə Jurnalı)</li>
                        <li>&#127942; 2024 — İlin Mobil Bankçılıq Tətbiqi</li>
                        <li>&#127942; 2023 — Müştəri Məmnuniyyəti Birincilikləri</li>
                        <li>&#127942; 2022 — Ən İnnovativ Bank Mükafatı</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
