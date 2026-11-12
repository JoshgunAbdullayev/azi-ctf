<?php
$page_title = 'Məxfilik Siyasəti';
$current = '';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Məxfilik Siyasəti</span></div>
        <h1 class="page-header-title">Məxfilik Siyasəti</h1>
        <p class="page-header-subtitle">Şəxsi məlumatlarınızın qorunması və emalına dair məlumatlandırma.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="prose" style="max-width:800px;">
            <h2>1. Məlumat Məsuliyyətçisi</h2>
            <p>
                Şəxsi məlumatlarınız, Azərbaycan Respublikasının "Şəxsi məlumatlar haqqında" Qanunu
                çərçivəsində məlumat məsuliyyətçisi sifətilə NovaBank ASC tərəfindən emal edilir.
            </p>

            <h2>2. Emal Edilən Şəxsi Məlumatlar</h2>
            <ul>
                <li><strong>Şəxsiyyət məlumatları:</strong> Ad, soyad, FİN kodu, doğum tarixi.</li>
                <li><strong>Əlaqə məlumatları:</strong> Telefon nömrəsi, e-posta ünvanı, ünvan.</li>
                <li><strong>Maliyyə məlumatları:</strong> Hesab məlumatları, əməliyyat tarixi, kredit məlumatları.</li>
                <li><strong>Rəqəmsal məlumatlar:</strong> IP ünvanı, çərəz məlumatları, cihaz məlumatları.</li>
            </ul>

            <h2>3. Məlumatların Emal Məqsədi</h2>
            <p>
                Şəxsi məlumatlarınız; bankçılıq xidmətlərinin göstərilməsi, qanuni öhdəliklərin
                yerinə yetirilməsi, müştəri münasibətlərinin idarə edilməsi, risk analizi və dələdəçilik
                qarşısının alınması məqsədilə emal edilir.
            </p>

            <h2>4. Məlumatların Ötürülməsi</h2>
            <p>
                Şəxsi məlumatlarınız, AMB, Maliyyə Monitorinqi Xidməti (MMX) kimi tənzimləyici və nəzarət qurumlarına
                qanuni məcburi olma çərçivəsində ötürülə bilər. Bundan başqa, açıq razılığınız
                olmadan üçüncü tərəflərlə paylaşılmaz.
            </p>

            <h2>5. Çərəz Siyasəti</h2>
            <p>
                Veb saytımızda sessiya idarəetməsi və istifadəçi təcrübəsinin yaxşılaşdırılması məqsədilə
                texniki çərəzlər istifadə olunur. Bu çərəzlər reklam və ya izləmə məqsədilə istifadə olunmur.
            </p>

            <h2>6. Məlumat Təhlükəsizliyi</h2>
            <p>
                NovaBank, şəxsi məlumatlarınızın təhlükəsizliyini təmin etmək üçün lazımi texniki və
                inzibati tədbirlər görür. Məlumatlarınız şifrələnmiş əlaqə kanalları
                üzərindən ötürülür və təhlükəsiz məlumat mərkəzlərində saxlanılır.
            </p>

            <h2>7. Hüquqlarınız</h2>
            <p>
                Azərbaycan Respublikasının qüvvədə olan qanunvericiliyi çərçivəsində; məlumatlarınıza giriş, düzəliş, silmə, ötürmə və emala
                etiraz hüquqlarına sahibsiniz. Bu hüquqlarınızı istifadə etmək üçün
                <a href="/contact.php" style="color:var(--primary);font-weight:500;">əlaqə</a>
                səhifəmizdən bizə müraciət edə bilərsiniz.
            </p>

            <p style="color:var(--gray-400);margin-top:2rem;font-size:0.85rem;">
                Son yeniləmə: 15 Yanvar 2026
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
