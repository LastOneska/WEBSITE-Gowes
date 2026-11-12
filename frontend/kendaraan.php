<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Kendaraan — Gowes.id';
$activePage = 'vehicles';
$categories = db_all('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori');
$locations = db_all('SELECT id, nama_lokasi FROM lokasi ORDER BY nama_lokasi');
$categoryId = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT) ?: 0;
$locationId = filter_input(INPUT_GET, 'lokasi', FILTER_VALIDATE_INT) ?: 0;
$conditions = [];
$params = [];
if ($categoryId > 0) {
    $conditions[] = 'kendaraan.kategori_id = ?';
    $params[] = $categoryId;
}
if ($locationId > 0) {
    $conditions[] = 'kendaraan.lokasi_id = ?';
    $params[] = $locationId;
}
$query = 'SELECT kendaraan.id, kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
                 kategori.nama_kategori, lokasi.nama_lokasi
          FROM kendaraan
          JOIN kategori ON kategori.id = kendaraan.kategori_id
          JOIN lokasi ON lokasi.id = kendaraan.lokasi_id';
if ($conditions) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}
$query .= ' ORDER BY kendaraan.stok DESC, kendaraan.nama_kendaraan';
$vehicles = db_all($query, $params);

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container">
    <section class="page-intro">
        <p class="eyebrow">Kendaraan Gowes.id</p>
        <h1>Temukan cara bergerakmu.</h1>
        <p>Sepeda dan skuter listrik untuk perjalanan singkat di sekitar Bandung. Pilih kendaraan dan titik ambil yang paling pas.</p>
    </section>

    <form class="filter-panel" method="get" action="<?= h($frontendBase) ?>/kendaraan.php" aria-label="Filter kendaraan">
        <div class="form-field">
            <label for="filter-category">Jenis kendaraan</label>
            <select class="form-select" id="filter-category" name="kategori">
                <option value="">Semua jenis</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= h($category['id']) ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= h($category['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="filter-location">Titik pengambilan</label>
            <select class="form-select" id="filter-location" name="lokasi">
                <option value="">Semua lokasi</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= h($location['id']) ?>" <?= $locationId === (int) $location['id'] ? 'selected' : '' ?>><?= h($location['nama_lokasi']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button button-brand" type="submit"><i class="bi bi-sliders" aria-hidden="true"></i> Terapkan filter</button>
    </form>

    <p class="result-count"><?= count($vehicles) ?> kendaraan ditemukan</p>
    <?php if ($vehicles): ?>
        <div class="vehicle-grid">
            <?php foreach ($vehicles as $vehicle): ?>
                <article class="vehicle-card">
                    <div class="vehicle-photo">
                        <img src="<?= h(vehicle_image($vehicle['nama_kategori'], $vehicle['nama_kendaraan'])) ?>" alt="<?= h($vehicle['nama_kendaraan']) ?>" loading="lazy">
                        <span class="vehicle-tag"><?= h($vehicle['nama_kategori']) ?></span>
                    </div>
                    <div class="vehicle-body">
                        <div class="vehicle-title-row"><h3><?= h($vehicle['nama_kendaraan']) ?></h3></div>
                        <p class="vehicle-location"><i class="bi bi-geo-alt" aria-hidden="true"></i><?= h($vehicle['nama_lokasi']) ?></p>
                        <div class="vehicle-bottom">
                            <div>
                                <div class="vehicle-price"><?= h(rupiah($vehicle['harga_per_jam'])) ?> <small>/ jam</small></div>
                                <span class="vehicle-stock"><?= $vehicle['stok'] > 0 ? h($vehicle['stok']) . ' unit tersedia' : 'Stok sedang habis' ?></span>
                            </div>
                            <?php if ((int) $vehicle['stok'] > 0): ?>
                                <a class="button button-primary button-small" href="<?= h($frontendBase) ?>/sewa.php?id=<?= h($vehicle['id']) ?>">Sewa</a>
                            <?php else: ?>
                                <button class="button button-outline button-small" type="button" disabled>Habis</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php elseif ($databaseError === null): ?>
        <div class="empty-state">Tidak ada kendaraan yang sesuai dengan filter ini. Coba pilih kategori atau lokasi lain.</div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
