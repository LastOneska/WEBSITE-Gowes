<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Daftar — Gowes.id';
$activePage = '';
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . $frontendBase . '/index.php');
    exit;
}

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container">
    <section class="form-card" aria-labelledby="register-title">
        <p class="eyebrow">Mulai perjalananmu</p>
        <h1 id="register-title">Buat akun</h1>
        <p>Daftar untuk menyewa kendaraan dan melihat riwayat perjalananmu.</p>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><?= h($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <form action="<?= h($backendBase) ?>/auth/register_proses.php" method="post">
            <div class="form-field">
                <label for="nama">Nama lengkap</label>
                <input class="form-control" id="nama" name="nama" type="text" autocomplete="name" required maxlength="100">
            </div>
            <div class="form-field">
                <label for="username">Username</label>
                <input class="form-control" id="username" name="username" type="text" autocomplete="username" required minlength="4" maxlength="50" aria-describedby="username-help">
                <span class="form-hint" id="username-help">Minimal 4 karakter.</span>
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" required minlength="6" aria-describedby="password-help">
                <span class="form-hint" id="password-help">Minimal 6 karakter.</span>
            </div>
            <div class="form-field">
                <label for="password_confirmation">Konfirmasi password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="6">
            </div>
            <button class="button button-primary" type="submit">Buat akun <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>
        <p class="form-footer">Sudah punya akun? <a href="<?= h($frontendBase) ?>/login.php">Masuk</a></p>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
