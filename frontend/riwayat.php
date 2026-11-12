<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$pageTitle = 'Riwayat sewa — Gowes.id';
$activePage = 'history';
$rentals = db_all(
    'SELECT penyewaan.id, penyewaan.jam_mulai, penyewaan.durasi_jam, penyewaan.total_harga,
            penyewaan.status, kendaraan.nama_kendaraan, kategori.nama_kategori, lokasi.nama_lokasi
     FROM penyewaan
     JOIN kendaraan ON kendaraan.id = penyewaan.kendaraan_id
     JOIN kategori ON kategori.id = kendaraan.kategori_id
     JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
     WHERE penyewaan.user_id = ?
     ORDER BY penyewaan.jam_mulai DESC',
    [$_SESSION['user_id']]
);

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container">
    <section class="page-intro">
        <p class="eyebrow">Perjalananmu</p>
        <h1>Riwayat penyewaan</h1>
        <p>Lihat detail penyewaan dan kembalikan kendaraan yang masih kamu gunakan.</p>
    </section>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><?= h($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" role="status"><i class="bi bi-check-circle" aria-hidden="true"></i><?= h($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if ($rentals): ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Kendaraan</th><th>Mulai</th><th>Durasi</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rental): ?>
                        <tr>
                            <td data-label="Kendaraan"><strong><?= h($rental['nama_kendaraan']) ?></strong><br><span class="vehicle-stock"><?= h($rental['nama_kategori']) ?> · <?= h($rental['nama_lokasi']) ?></span></td>
                            <td data-label="Mulai"><?= h(date('d M Y, H:i', strtotime($rental['jam_mulai']))) ?></td>
                            <td data-label="Durasi"><?= h($rental['durasi_jam']) ?> jam</td>
                            <td data-label="Total"><?= h(rupiah($rental['total_harga'])) ?></td>
                            <td data-label="Status"><span class="status <?= $rental['status'] === 'berjalan' ? 'status-running' : 'status-done' ?>"><?= $rental['status'] === 'berjalan' ? 'Berjalan' : 'Selesai' ?></span></td>
                            <td data-label="Aksi">
                                <?php if ($rental['status'] === 'berjalan'): ?>
                                    <form class="table-actions" action="<?= h($backendBase) ?>/penyewaan/kembali_proses.php" method="post" data-confirm="Kembalikan kendaraan ini sekarang?">
                                        <input type="hidden" name="penyewaan_id" value="<?= h($rental['id']) ?>">
                                        <button class="button button-brand button-small" type="submit">Kembalikan</button>
                                    </form>
                                <?php else: ?>
                                    <span class="vehicle-stock">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($databaseError === null): ?>
        <div class="content-panel history-empty">
            <i class="bi bi-bicycle" aria-hidden="true"></i>
            <h2>Belum ada perjalanan</h2>
            <p>Perjalanan sewa yang kamu buat akan muncul di sini.</p>
            <a class="button button-primary" href="<?= h($frontendBase) ?>/kendaraan.php">Jelajahi kendaraan</a>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
