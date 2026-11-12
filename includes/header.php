<?php if (!isset($page_title)) $page_title = 'NovaBank'; ?>
<?php $current_page = $current ?? ''; ?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | NovaBank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-inner">
            <a href="/" class="logo">
                <span class="logo-icon">N</span>
                <span class="logo-text">Nova<strong>Bank</strong></span>
            </a>
            <div class="nav-menu">
                <a href="/" class="<?= $current_page === 'home' ? 'active' : '' ?>">Ana Səhifə</a>
                <a href="/about.php" class="<?= $current_page === 'about' ? 'active' : '' ?>">Haqqımızda</a>
                <a href="/bireysel.php" class="<?= $current_page === 'bireysel' ? 'active' : '' ?>">Fərdi</a>
                <a href="/kurumsal.php" class="<?= $current_page === 'kurumsal' ? 'active' : '' ?>">Korporativ</a>
                <a href="/blog/" class="<?= $current_page === 'blog' ? 'active' : '' ?>">Bloq</a>
                <a href="/contact.php" class="<?= $current_page === 'contact' ? 'active' : '' ?>">Əlaqə</a>
            </div>
            <div class="nav-auth">
                <?php if (isLoggedIn()): ?>
                    <a href="/dashboard.php" class="btn btn-ghost btn-sm">Hesabım</a>
                    <a href="/logout.php" class="btn btn-sm" style="color:var(--gray-500);">Çıxış</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-primary btn-sm">Daxil Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main>
