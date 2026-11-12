<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pageTitle = 'Dashboard admin — Gowes.id';
$activePage = '';
$stats = db_one(
    'SELECT
        (SELECT COUNT(*) FROM kendaraan) AS total_kendaraan,
        (SELECT COUNT(*) FROM users WHERE role = "user") AS total_user,
        (SELECT COUNT(*) FROM penyewaan WHERE status = "berjalan") AS sewa_aktif,
        (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayaran) AS total_pendapatan'
) ?? [];
$recentRentals = db_all(
    'SELECT penyewaan.id, penyewaan.jam_mulai, penyewaan.durasi_jam, penyewaan.total_harga,
            penyewaan.status, users.nama, kendaraan.nama_kendaraan
     FROM penyewaan
     JOIN users ON users.id = penyewaan.user_id
     JOIN kendaraan ON kendaraan.id = penyewaan.kendaraan_id
     ORDER BY penyewaan.jam_mulai DESC
     LIMIT 8'
);

require __DIR__ . '/../includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container admin-shell">
    <aside class="admin-sidebar" aria-label="Navigasi admin">
        <p>Ruang admin</p>
        <a class="active" href="<?= h($frontendBase) ?>/admin/dashboard.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i> Ringkasan</a>
        <a href="<?= h($frontendBase) ?>/admin/kategori.php"><i class="bi bi-tags" aria-hidden="true"></i> Kategori</a>
        <a href="<?= h($frontendBase) ?>/admin/kendaraan.php"><i class="bi bi-bicycle" aria-hidden="true"></i> Kendaraan</a>
        <a href="<?= h($frontendBase) ?>/admin/penyewaan.php"><i class="bi bi-receipt" aria-hidden="true"></i> Penyewaan</a>
    </aside>
    <section class="admin-main">
        <div class="admin-heading">
            <div>
                <p class="eyebrow">Gowes.id · Admin</p>
                <h1>Ringkasan</h1>
                <p>Pantau layanan dan aktivitas penyewaan terbaru.</p>
            </div>
            <a class="button button-outline" href="<?= h($frontendBase) ?>/kendaraan.php" target="_blank" rel="noopener">Lihat katalog <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
        </div>
        <div class="admin-stats">
            <div class="admin-stat"><span>Total kendaraan</span><strong><?= h($stats['total_kendaraan'] ?? '—') ?></strong></div>
            <div class="admin-stat"><span>Pengguna</span><strong><?= h($stats['total_user'] ?? '—') ?></strong></div>
            <div class="admin-stat"><span>Sewa aktif</span><strong><?= h($stats['sewa_aktif'] ?? '—') ?></strong></div>
            <div class="admin-stat"><span>Total pendapatan</span><strong><?= isset($stats['total_pendapatan']) ? h(rupiah($stats['total_pendapatan'])) : '—' ?></strong></div>
        </div>
        <section class="admin-panel">
            <div class="admin-panel-head">
                <h2>Penyewaan terbaru</h2>
                <a class="text-link" href="<?= h($frontendBase) ?>/admin/penyewaan.php">Lihat semua <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if ($recentRentals): ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead><tr><th>Penyewa</th><th>Kendaraan</th><th>Mulai</th><th>Durasi</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentRentals as $rental): ?>
                                <tr>
                                    <td data-label="Penyewa"><strong><?= h($rental['nama']) ?></strong></td>
                                    <td data-label="Kendaraan"><?= h($rental['nama_kendaraan']) ?></td>
                                    <td data-label="Mulai"><?= h(date('d M Y, H:i', strtotime($rental['jam_mulai']))) ?></td>
                                    <td data-label="Durasi"><?= h($rental['durasi_jam']) ?> jam</td>
                                    <td data-label="Total"><?= h(rupiah($rental['total_harga'])) ?></td>
                                    <td data-label="Status"><span class="status <?= $rental['status'] === 'berjalan' ? 'status-running' : 'status-done' ?>"><?= $rental['status'] === 'berjalan' ? 'Berjalan' : 'Selesai' ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($databaseError === null): ?>
                <div class="empty-state">Belum ada data penyewaan.</div>
            <?php endif; ?>
        </section>
    </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
