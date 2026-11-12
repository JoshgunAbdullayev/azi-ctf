<?php
$page_title = 'Daxil Ol';
$current = '';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_no = trim($_POST['customer_no'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE customer_no = ?");
    $stmt->execute([$customer_no]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['user_name'] = $user['name'];

        $redirect = $_SESSION['redirect_after_login'] ?? ($user['is_admin'] ? '/admin/' : '/dashboard.php');
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Müştəri nömrəsi və ya şifrə səhvdir.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon" style="margin:0 auto;width:48px;height:48px;font-size:1.4rem;">N</span>
        </div>
        <h1 class="auth-title">İnternet Bankçılığı</h1>
        <p class="auth-subtitle">NovaBank hesabınıza daxil olun</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login.php">
            <div class="form-group">
                <label class="form-label" for="customer_no">Müştəri Nömrəsi</label>
                <input id="customer_no" name="customer_no" type="text" class="form-input"
                       placeholder="Müştəri nömrənizi daxil edin"
                       value="<?= htmlspecialchars($_POST['customer_no'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Şifrə</label>
                <input id="password" name="password" type="password" class="form-input"
                       placeholder="Şifrənizi daxil edin" required>
            </div>

            <div class="form-group" style="display:flex;justify-content:space-between;align-items:center;">
                <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;color:var(--gray-600);cursor:pointer;">
                    <input type="checkbox" name="remember"> Məni xatırla
                </label>
                <a href="/support.php" style="font-size:0.85rem;color:var(--primary);font-weight:500;">Şifrəmi unutdum</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">Daxil Ol</button>
        </form>

        <div class="auth-footer">
            Hesabınız yoxdur? <a href="/contact.php">Filialımızı ziyarət edin</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
