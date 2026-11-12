<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pageTitle = 'Penyewaan — Gowes.id';
$statusFilter = $_GET['status'] ?? '';
$allowedStatuses = ['berjalan', 'selesai'];
$conditions = [];
$params = [];
if (in_array($statusFilter, $allowedStatuses, true)) {
    $conditions[] = 'penyewaan.status = ?';
    $params[] = $statusFilter;
} else {
    $statusFilter = '';
}
$query = 'SELECT penyewaan.id, penyewaan.jam_mulai, penyewaan.durasi_jam, penyewaan.total_harga,
                 penyewaan.status, users.nama, kendaraan.nama_kendaraan, kategori.nama_kategori,
                 lokasi.nama_lokasi, pembayaran.metode
          FROM penyewaan
          JOIN users ON users.id = penyewaan.user_id
          JOIN kendaraan ON kendaraan.id = penyewaan.kendaraan_id
          JOIN kategori ON kategori.id = kendaraan.kategori_id
          JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
          LEFT JOIN pembayaran ON pembayaran.penyewaan_id = penyewaan.id';
if ($conditions) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}
$query .= ' ORDER BY penyewaan.jam_mulai DESC';
$rentals = db_all($query, $params);

require __DIR__ . '/../includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container admin-shell">
    <aside class="admin-sidebar" aria-label="Navigasi admin">
        <p>Ruang admin</p>
        <a href="<?= h($frontendBase) ?>/admin/dashboard.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i> Ringkasan</a>
        <a href="<?= h($frontendBase) ?>/admin/kategori.php"><i class="bi bi-tags" aria-hidden="true"></i> Kategori</a>
        <a href="<?= h($frontendBase) ?>/admin/kendaraan.php"><i class="bi bi-bicycle" aria-hidden="true"></i> Kendaraan</a>
        <a class="active" href="<?= h($frontendBase) ?>/admin/penyewaan.php"><i class="bi bi-receipt" aria-hidden="true"></i> Penyewaan</a>
    </aside>
    <section class="admin-main">
        <div class="admin-heading">
            <div><p class="eyebrow">Aktivitas pelanggan</p><h1>Semua penyewaan</h1><p>Pantau status sewa, metode pembayaran, dan detail kendaraan.</p></div>
        </div>
        <form class="filter-panel" method="get" action="<?= h($frontendBase) ?>/admin/penyewaan.php" aria-label="Filter status penyewaan">
            <div class="form-field">
                <label for="rental-status">Status</label>
                <select class="form-select" id="rental-status" name="status">
                    <option value="">Semua status</option>
                    <option value="berjalan" <?= $statusFilter === 'berjalan' ? 'selected' : '' ?>>Berjalan</option>
                    <option value="selesai" <?= $statusFilter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <button class="button button-brand" type="submit"><i class="bi bi-filter" aria-hidden="true"></i> Tampilkan</button>
        </form>
        <section class="admin-panel">
            <?php if ($rentals): ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead><tr><th>Penyewa</th><th>Kendaraan</th><th>Lokasi</th><th>Mulai</th><th>Durasi</th><th>Total</th><th>Pembayaran</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($rentals as $rental): ?>
                                <tr>
                                    <td data-label="Penyewa"><strong><?= h($rental['nama']) ?></strong></td>
                                    <td data-label="Kendaraan"><?= h($rental['nama_kendaraan']) ?><br><span class="vehicle-stock"><?= h($rental['nama_kategori']) ?></span></td>
                                    <td data-label="Lokasi"><?= h($rental['nama_lokasi']) ?></td>
                                    <td data-label="Mulai"><?= h(date('d M Y, H:i', strtotime($rental['jam_mulai']))) ?></td>
                                    <td data-label="Durasi"><?= h($rental['durasi_jam']) ?> jam</td>
                                    <td data-label="Total"><?= h(rupiah($rental['total_harga'])) ?></td>
                                    <td data-label="Pembayaran"><?= h($rental['metode'] ?? '—') ?></td>
                                    <td data-label="Status"><span class="status <?= $rental['status'] === 'berjalan' ? 'status-running' : 'status-done' ?>"><?= $rental['status'] === 'berjalan' ? 'Berjalan' : 'Selesai' ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($databaseError === null): ?>
                <div class="empty-state">Tidak ada penyewaan pada filter ini.</div>
            <?php endif; ?>
        </section>
    </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
