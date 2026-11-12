<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Masuk — Gowes.id';
$activePage = '';
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $frontendBase . '/index.php');
    exit;
}

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container">
    <section class="form-card" aria-labelledby="login-title">
        <p class="eyebrow">Selamat datang kembali</p>
        <h1 id="login-title">Masuk akun</h1>
        <p>Lanjutkan perjalananmu bersama Gowes.id.</p>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><?= h($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success" role="status"><i class="bi bi-check-circle" aria-hidden="true"></i><?= h($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <form action="<?= h($backendBase) ?>/auth/login_proses.php" method="post">
            <div class="form-field">
                <label for="username">Username</label>
                <input class="form-control" id="username" name="username" type="text" autocomplete="username" required minlength="4" maxlength="50">
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="button button-primary" type="submit">Masuk <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>
        <p class="form-footer">Belum punya akun? <a href="<?= h($frontendBase) ?>/register.php">Daftar sekarang</a></p>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
