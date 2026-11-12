<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$users = [
    ['username' => 'root_admin', 'display' => 'root_admin', 'role' => 'Sistem İdarəçisi', 'color' => '#ff4444', 'badge' => 'ADMIN', 'email' => 'Gizli', 'joined' => '2023-01-15', 'posts' => 142, 'avatar' => 'RA'],
    ['username' => 'moderator_nb', 'display' => 'moderator_nb', 'role' => 'Moderator', 'color' => '#00cc44', 'badge' => 'MOD', 'email' => 'moderator_real@novabank.com', 'joined' => '2023-06-20', 'posts' => 87, 'avatar' => 'MN'],
    ['username' => 'finans_01', 'display' => 'finans_01', 'role' => 'Maliyyə Mütəxəssisi', 'color' => '#888', 'badge' => 'STAFF', 'email' => 'finans01@novabank.com', 'joined' => '2024-02-10', 'posts' => 34, 'avatar' => 'F1'],
    ['username' => 'destek_ayse', 'display' => 'destek_ayse', 'role' => 'Müştəri Dəstəyi', 'color' => '#888', 'badge' => 'STAFF', 'email' => 'ayse.d@novabank.com', 'joined' => '2024-05-03', 'posts' => 56, 'avatar' => 'DA'],
    ['username' => 'it_mehmet', 'display' => 'it_mehmet', 'role' => 'IT Departamenti', 'color' => '#888', 'badge' => 'STAFF', 'email' => 'mehmet.it@novabank.com', 'joined' => '2024-03-18', 'posts' => 21, 'avatar' => 'IM'],
];

$show_profile = isset($_GET['user']) ? $_GET['user'] : null;
$profile_user = null;
if ($show_profile) {
    foreach ($users as $u) {
        if ($u['username'] === $show_profile) {
            $profile_user = $u;
            break;
        }
    }
}

$posts = [
    ['id' => 1, 'user' => 'root_admin', 'title' => 'Sistem Yeniləməsi - Mart 2026', 'time' => '2026-02-28 09:15', 'content' => 'Bütün şöbələr diqqət: Mart ayında planlaşdırılan sistem yeniləməsi səbəbilə 2-3 Mart tarixlərində texniki xidmət işləri aparılacaq. Əməliyyatlarınızı buna uyğun planlaşdırın.', 'replies' => 3, 'pinned' => true],
    ['id' => 2, 'user' => 'finans_01', 'title' => 'Yeni müştərilərin pulları hara gedir?', 'time' => '2026-02-27 14:30', 'content' => 'Son 3 ayda açılan yeni hesablardan edilən investisiya fondlarının aqibətini maraqlanıram. Hesabat sistemində bu köçürmələrin təfərrüatları görünmür. Normalda hər əməliyyat MASAK hesabatına düşməli idi, amma "Xüsusi İnvestisiya Fondu" etiketli əməliyyatlar görünmür. Bunu araşdırmağımız lazımdır.', 'replies' => 5, 'pinned' => false],
    ['id' => 3, 'user' => 'destek_ayse', 'title' => 'Müştəri Şikayətləri Artır', 'time' => '2026-02-26 11:45', 'content' => 'Son həftələrdə "Hesabımdan xəbərsiz tutulma edildi" şəklində şikayətlər ciddi artış göstərdi. "İdarəetmə Fondu Tutulması" və "Rəqəmsal İnfrastruktur Töhfə Payı" adında tutulmalar görürlər. Bu tutulmalar haqqında məlumatı olan var mı? Müştəri müqavilələrində bu maddələr yoxdur.', 'replies' => 8, 'pinned' => false],
    ['id' => 4, 'user' => 'moderator_nb', 'title' => 'Forum Qaydaları Xatırlatma', 'time' => '2026-02-25 16:00', 'content' => 'Xatırlatma: Bu forum şirkətdaxili ünsiyyət üçündür. Həssas məlumatları burada paylaşmayın. Hər hansı bir təhlükəsizlik pozuntusu şübhəniz varsa birbaşa IT departamentinə bildirin. Giriş üçün e-poçt doğrulama sistemini istifadə edin, şirkət e-poçtunuza gələn kodu daxil edin.', 'replies' => 1, 'pinned' => true],
    ['id' => 5, 'user' => 'it_mehmet', 'title' => 'Server Loglarında Qəribəlik', 'time' => '2026-02-24 08:20', 'content' => 'Keçən həftə server loglarını araşdırarkən bəzi qəribə pattern\'lər fərq etdim. Gecə 02:00-04:00 arası avtomatik köçürmələr edilir və bunlar normal batch əməliyyatlara bənzəmir. Mənbə hesablar həmişə eyni 3 korporativ hesab. root_admin\'in bu barədə məlumatı var mı görəsən?', 'replies' => 2, 'pinned' => false],
    ['id' => 6, 'user' => 'root_admin', 'title' => 'RE: Server Logları', 'time' => '2026-02-24 10:05', 'content' => 'Bu əməliyyatlar planlı texniki xidmət çərçivəsindədir. Ətraflı məlumat idarə heyəti qərarı ilə gizlilik altındadır. Mövzu haqqında daha çox araşdırma aparılmasına ehtiyac yoxdur.', 'replies' => 0, 'pinned' => false],
];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NB Internal Communications</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#1a1a2e;color:#e0e0e0;min-height:100vh}
        .container{max-width:960px;margin:0 auto;padding:1.5rem}
        .header{background:#16213e;border-bottom:2px solid #0f3460;padding:1.25rem 0}
        .header .container{display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:1.1rem;font-weight:700;color:#e94560;letter-spacing:0.05rem}
        .logo span{color:#0f3460;font-weight:400;font-size:0.85rem;margin-left:0.5rem}
        .nav{display:flex;gap:1rem;font-size:0.82rem}
        .nav a{color:#7f8c8d;text-decoration:none;padding:0.3rem 0.6rem;border-radius:4px}
        .nav a:hover,.nav a.active{color:#e94560;background:rgba(233,69,96,0.1)}
        .badge-role{display:inline-block;font-size:0.65rem;padding:0.15rem 0.4rem;border-radius:3px;font-weight:700;letter-spacing:0.05rem}
        .badge-admin{background:rgba(255,68,68,0.2);color:#ff4444;border:1px solid rgba(255,68,68,0.3)}
        .badge-mod{background:rgba(0,204,68,0.2);color:#00cc44;border:1px solid rgba(0,204,68,0.3)}
        .badge-staff{background:rgba(136,136,136,0.2);color:#888;border:1px solid rgba(136,136,136,0.3)}
        .section-title{font-size:1rem;font-weight:600;color:#e94560;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid #2a2a4a}
        .post{background:#16213e;border:1px solid #2a2a4a;border-radius:6px;padding:1.25rem;margin-bottom:0.75rem;transition:border-color 0.2s}
        .post:hover{border-color:#0f3460}
        .post.pinned{border-left:3px solid #e94560}
        .post-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem}
        .post-title{font-size:0.95rem;font-weight:600;color:#fff}
        .post-meta{font-size:0.75rem;color:#555}
        .post-user{font-weight:600;cursor:pointer;text-decoration:none}
        .post-user:hover{text-decoration:underline}
        .post-content{font-size:0.85rem;color:#aaa;line-height:1.6;margin-top:0.5rem}
        .post-footer{display:flex;gap:1rem;margin-top:0.75rem;font-size:0.75rem;color:#555}
        .sidebar{background:#16213e;border:1px solid #2a2a4a;border-radius:6px;padding:1.25rem;margin-bottom:1rem}
        .user-list{list-style:none;display:grid;gap:0.5rem}
        .user-item{display:flex;align-items:center;gap:0.75rem;padding:0.5rem;border-radius:4px;cursor:pointer;text-decoration:none;color:inherit}
        .user-item:hover{background:rgba(255,255,255,0.05)}
        .avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;flex-shrink:0}
        .user-info{flex:1;min-width:0}
        .user-name{font-size:0.82rem;font-weight:600}
        .user-role-text{font-size:0.7rem;color:#666}
        .grid{display:grid;grid-template-columns:1fr 300px;gap:1.5rem}
        @media(max-width:768px){.grid{grid-template-columns:1fr}}
        .profile-modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;z-index:999}
        .profile-card{background:#16213e;border:1px solid #2a2a4a;border-radius:8px;padding:2rem;max-width:400px;width:90%;position:relative}
        .profile-card .close{position:absolute;top:12px;right:16px;cursor:pointer;color:#666;font-size:1.3rem}
        .profile-card .close:hover{color:#fff}
        .profile-avatar{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;margin:0 auto 1rem}
        .profile-field{display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid #2a2a4a;font-size:0.85rem}
        .profile-field:last-child{border-bottom:none}
        .profile-label{color:#666}
        .profile-value{color:#e0e0e0;font-weight:500}
        .pin-icon{color:#e94560;font-size:0.7rem;margin-right:0.3rem}
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <div class="logo">NB Internal Comm <span>| Şirkətdaxili Ünsiyyət</span></div>
        <div class="nav">
            <a href="#" class="active">Forum</a>
            <a href="#">Elanlar</a>
            <a href="#">Fayllar</a>
            <a href="/auth/callback.php" style="color:#4285f4">Staff Login</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="grid">
        <div>
            <h2 class="section-title">Son Mövzular</h2>
            <?php foreach ($posts as $post):
                $post_user = null;
                foreach ($users as $u) {
                    if ($u['username'] === $post['user']) { $post_user = $u; break; }
                }
                $badge_class = $post_user['badge'] === 'ADMIN' ? 'badge-admin' : ($post_user['badge'] === 'MOD' ? 'badge-mod' : 'badge-staff');
            ?>
            <div class="post<?= $post['pinned'] ? ' pinned' : '' ?>">
                <div class="post-header">
                    <div>
                        <?php if ($post['pinned']): ?><span class="pin-icon">&#128204;</span><?php endif; ?>
                        <span class="post-title"><?= htmlspecialchars($post['title']) ?></span>
                    </div>
                    <div class="post-meta"><?= $post['time'] ?></div>
                </div>
                <div style="margin-bottom:0.5rem">
                    <a class="post-user" style="color:<?= $post_user['color'] ?>" onclick="showProfile('<?= $post_user['username'] ?>')"><?= htmlspecialchars($post_user['display']) ?></a>
                    <span class="badge-role <?= $badge_class ?>"><?= $post_user['badge'] ?></span>
                </div>
                <div class="post-content"><?= htmlspecialchars($post['content']) ?></div>
                <div class="post-footer">
                    <span>&#128172; <?= $post['replies'] ?> cavab</span>
                    <span>&#128065; <?= rand(20, 150) ?> baxış</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div>
            <div class="sidebar">
                <h3 class="section-title" style="font-size:0.9rem">Aktiv İstifadəçilər</h3>
                <ul class="user-list">
                    <?php foreach ($users as $u):
                        $badge_class = $u['badge'] === 'ADMIN' ? 'badge-admin' : ($u['badge'] === 'MOD' ? 'badge-mod' : 'badge-staff');
                    ?>
                    <a class="user-item" onclick="showProfile('<?= $u['username'] ?>')">
                        <div class="avatar" style="background:<?= $u['color'] ?>22;color:<?= $u['color'] ?>"><?= $u['avatar'] ?></div>
                        <div class="user-info">
                            <div class="user-name" style="color:<?= $u['color'] ?>"><?= htmlspecialchars($u['display']) ?></div>
                            <div class="user-role-text"><?= htmlspecialchars($u['role']) ?></div>
                        </div>
                        <span class="badge-role <?= $badge_class ?>"><?= $u['badge'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="sidebar">
                <h3 class="section-title" style="font-size:0.9rem">Forum Statistikaları</h3>
                <div style="font-size:0.82rem;color:#888;display:grid;gap:0.4rem">
                    <div>Ümumi Mövzu: <span style="color:#e0e0e0">47</span></div>
                    <div>Ümumi Cavab: <span style="color:#e0e0e0">213</span></div>
                    <div>Aktiv İstifadəçi: <span style="color:#e0e0e0">5</span></div>
                    <div>Son Giriş: <span style="color:#e0e0e0"><?= date('d.m.Y H:i') ?></span></div>
                </div>
            </div>

            <div class="sidebar" style="border-color:#333;background:#1a1a2e">
                <h3 class="section-title" style="font-size:0.9rem;color:#4285f4">Sürətli Giriş</h3>
                <p style="font-size:0.8rem;color:#666;line-height:1.6">
                    Moderator və ya admin hesabına giriş üçün e-poçt doğrulama ilə daxil olun.
                    <a href="/auth/callback.php" style="color:#4285f4">Staff Login &rarr;</a>
                </p>
            </div>
        </div>
    </div>
</div>

<div id="profileModal" style="display:none" class="profile-modal" onclick="if(event.target===this)this.style.display='none'">
    <div class="profile-card">
        <span class="close" onclick="document.getElementById('profileModal').style.display='none'">&times;</span>
        <div class="profile-avatar" id="profileAvatar"></div>
        <h3 id="profileName" style="text-align:center;margin-bottom:1rem;font-size:1.1rem"></h3>
        <div id="profileFields"></div>
    </div>
</div>

<script>
var users = <?= json_encode($users, JSON_UNESCAPED_UNICODE) ?>;

function showProfile(username) {
    var user = null;
    for (var i = 0; i < users.length; i++) {
        if (users[i].username === username) { user = users[i]; break; }
    }
    if (!user) return;

    var modal = document.getElementById('profileModal');
    var avatar = document.getElementById('profileAvatar');
    var name = document.getElementById('profileName');
    var fields = document.getElementById('profileFields');

    avatar.textContent = user.avatar;
    avatar.style.background = user.color + '22';
    avatar.style.color = user.color;
    name.textContent = user.display;
    name.style.color = user.color;

    var emailDisplay = user.email === 'Gizli'
        ? '<span style="color:#ff4444;font-style:italic">Məxfi</span>'
        : '<span style="color:#00cc44">' + user.email + '</span>';

    fields.innerHTML =
        '<div class="profile-field"><span class="profile-label">İstifadəçi Adı:</span><span class="profile-value">' + user.username + '</span></div>' +
        '<div class="profile-field"><span class="profile-label">Rol:</span><span class="profile-value">' + user.role + '</span></div>' +
        '<div class="profile-field"><span class="profile-label">E-poçt:</span><span class="profile-value">' + emailDisplay + '</span></div>' +
        '<div class="profile-field"><span class="profile-label">Qoşulma:</span><span class="profile-value">' + user.joined + '</span></div>' +
        '<div class="profile-field"><span class="profile-label">Paylaşım:</span><span class="profile-value">' + user.posts + '</span></div>';

    modal.style.display = 'flex';
}
</script>
</body>
</html>
