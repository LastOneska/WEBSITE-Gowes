<?php
$pageTitle = $pageTitle ?? 'Gowes.id — Sewa kendaraan kota';
$activePage = $activePage ?? '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f3ec">
    <title><?= h($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= h($frontendBase) ?>/css/style.css" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#main-content">Lewati ke konten</a>
<header class="site-header">
    <nav class="navbar navbar-expand-lg container" aria-label="Navigasi utama">
        <a class="brand" href="<?= h($frontendBase) ?>/index.php" aria-label="Gowes.id, beranda">
            <span class="brand-mark"><i class="bi bi-bicycle" aria-hidden="true"></i></span>
            <span>gowes<span class="brand-dot">.</span>id</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavigation">
            <div class="navbar-nav nav-links ms-auto me-lg-4">
                <a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="<?= h($frontendBase) ?>/index.php">Beranda</a>
                <a class="nav-link <?= $activePage === 'vehicles' ? 'active' : '' ?>" href="<?= h($frontendBase) ?>/kendaraan.php">Kendaraan</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a class="nav-link <?= $activePage === 'history' ? 'active' : '' ?>" href="<?= h($frontendBase) ?>/riwayat.php">Riwayat</a>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <a class="nav-link" href="<?= h($frontendBase) ?>/admin/dashboard.php">Admin</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="account-nav">
                    <span class="account-name"><i class="bi bi-person-circle" aria-hidden="true"></i> <?= h($_SESSION['nama'] ?? 'Akun') ?></span>
                    <a class="button button-outline button-small" href="<?= h($backendBase) ?>/auth/logout.php">Keluar</a>
                </div>
            <?php else: ?>
                <a class="button button-primary button-small nav-cta" href="<?= h($frontendBase) ?>/login.php">Masuk <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
    </nav>
</header>
