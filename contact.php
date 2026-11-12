<?php
$page_title = 'Əlaqə';
$current = 'contact';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Səhifə</a> <span>/</span> <span>Əlaqə</span></div>
        <h1 class="page-header-title">Əlaqə</h1>
        <p class="page-header-subtitle">Suallarınız, təklifləriniz və ya görüş tələbləriniz üçün bizə müraciət edin.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div>
                <div class="card" style="padding:2rem;">
                    <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:1.5rem;">Əlaqə Formu</h2>
                    <form>
                        <div class="form-group">
                            <label class="form-label" for="c_name">Ad Soyad</label>
                            <input id="c_name" type="text" class="form-input" placeholder="Adınızı və soyadınızı daxil edin">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="c_email">E-posta Ünvanı</label>
                            <input id="c_email" type="email" class="form-input" placeholder="numunə@email.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="c_phone">Telefon</label>
                            <input id="c_phone" type="tel" class="form-input" placeholder="+994 XX XXX XX XX">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="c_topic">Mövzu</label>
                            <select id="c_topic" class="form-input form-select">
                                <option>Ümumi Məlumat</option>
                                <option>Hesab Əməliyyatları</option>
                                <option>Kredit Müraciəti</option>
                                <option>Şikayət / Təklif</option>
                                <option>Korporativ Bankçılıq</option>
                                <option>Digər</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="c_message">Mesajınız</label>
                            <textarea id="c_message" class="form-input" rows="4" placeholder="Mesajınızı yazın..."></textarea>
                        </div>
                        <button type="button" class="btn btn-primary btn-block btn-lg">Mesaj Göndər</button>
                    </form>
                </div>
            </div>
            <div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Baş Ofis</h3>
                    <div style="font-size:0.88rem;color:var(--gray-600);display:grid;gap:0.5rem;">
                        <p><strong>Ünvan:</strong> Nərimanov rayonu, Heydər Əliyev prospekti 115, Bakı</p>
                        <p><strong>Telefon:</strong> +994 12 444 06 82</p>
                        <p><strong>E-posta:</strong> info@novabank.az</p>
                    </div>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Müştəri Xidmətləri</h3>
                    <div style="font-size:0.88rem;color:var(--gray-600);display:grid;gap:0.5rem;">
                        <p><strong>Çağrı Mərkəzi:</strong> 444 0 682 (7/24)</p>
                        <p><strong>WhatsApp:</strong> +994 50 444 06 82</p>
                        <p><strong>E-posta:</strong> destek@novabank.az</p>
                    </div>
                </div>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">İş Saatları</h3>
                    <div style="font-size:0.88rem;color:var(--gray-600);display:grid;gap:0.3rem;">
                        <p>Bazar ertəsi - Cümə: 09:00 - 17:30</p>
                        <p>Şənbə: 09:30 - 13:00</p>
                        <p>Bazar: Bağlı</p>
                        <p style="margin-top:0.5rem;color:var(--gray-400);font-size:0.82rem;">
                            İnternet və mobil bankçılıq 7/24 xidmətinizdədir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
