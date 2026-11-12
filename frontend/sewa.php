<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$pageTitle = 'Sewa kendaraan — Gowes.id';
$activePage = 'vehicles';
$vehicleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$vehicle = $vehicleId ? db_one(
    'SELECT kendaraan.id, kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
            kategori.nama_kategori, lokasi.nama_lokasi, lokasi.alamat
     FROM kendaraan
     JOIN kategori ON kategori.id = kendaraan.kategori_id
     JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
     WHERE kendaraan.id = ?',
    [$vehicleId]
) : null;
$duration = filter_input(INPUT_POST, 'durasi_jam', FILTER_VALIDATE_INT) ?: 1;
$duration = max(1, min(24, $duration));

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container">
    <section class="page-intro">
        <p class="eyebrow">Atur perjalananmu</p>
        <h1>Konfirmasi sewa</h1>
        <p>Pilih durasi dan metode pembayaran. Total biaya diperbarui otomatis.</p>
    </section>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><?= h($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!$vehicle): ?>
        <?php if ($databaseError === null): ?>
            <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>Kendaraan tidak ditemukan. Silakan pilih kendaraan yang tersedia.</div>
        <?php endif; ?>
        <a class="button button-brand" href="<?= h($frontendBase) ?>/kendaraan.php">Kembali ke kendaraan</a>
    <?php elseif ((int) $vehicle['stok'] < 1): ?>
        <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>Kendaraan ini sedang habis. Pilih kendaraan lain yang masih tersedia.</div>
        <a class="button button-brand" href="<?= h($frontendBase) ?>/kendaraan.php">Pilih kendaraan lain</a>
    <?php else: ?>
        <div class="rental-layout">
            <section class="content-panel">
                <p class="detail-breadcrumb"><a href="<?= h($frontendBase) ?>/kendaraan.php">Kendaraan</a> / Konfirmasi sewa</p>
                <h2>Detail penyewaan</h2>
                <form action="<?= h($backendBase) ?>/penyewaan/sewa_proses.php" method="post">
                    <input type="hidden" name="kendaraan_id" value="<?= h($vehicle['id']) ?>">
                    <div class="form-field">
                        <label for="durasi_jam">Durasi sewa</label>
                        <select class="form-select" id="durasi_jam" name="durasi_jam" data-rental-duration data-hourly-rate="<?= h($vehicle['harga_per_jam']) ?>">
                            <?php for ($hour = 1; $hour <= 12; $hour++): ?>
                                <option value="<?= $hour ?>" <?= $duration === $hour ? 'selected' : '' ?>><?= $hour ?> jam</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <fieldset class="form-field">
                        <legend class="form-field-label">Metode pembayaran</legend>
                        <div class="form-check">
                            <input class="form-check-input" id="payment-ewallet" type="radio" name="metode" value="e-wallet" checked required>
                            <label class="form-check-label" for="payment-ewallet">E-wallet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="payment-transfer" type="radio" name="metode" value="transfer">
                            <label class="form-check-label" for="payment-transfer">Transfer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="payment-cash" type="radio" name="metode" value="tunai">
                            <label class="form-check-label" for="payment-cash">Tunai</label>
                        </div>
                    </fieldset>
                    <div class="alert alert-success"><i class="bi bi-info-circle" aria-hidden="true"></i>Pembayaran dicatat saat penyewaan dikonfirmasi.</div>
                    <button class="button button-primary" type="submit">Konfirmasi penyewaan <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                </form>
            </section>
            <aside class="summary-panel" aria-label="Ringkasan kendaraan dan biaya">
                <img class="summary-image" src="<?= h(vehicle_image($vehicle['nama_kategori'], $vehicle['nama_kendaraan'])) ?>" alt="<?= h($vehicle['nama_kendaraan']) ?>">
                <div class="summary-content">
                    <p class="eyebrow"><?= h($vehicle['nama_kategori']) ?></p>
                    <h2><?= h($vehicle['nama_kendaraan']) ?></h2>
                    <div class="summary-line"><span><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= h($vehicle['nama_lokasi']) ?></span><span><?= h($vehicle['alamat']) ?></span></div>
                    <div class="summary-line"><span>Tarif per jam</span><strong><?= h(rupiah($vehicle['harga_per_jam'])) ?></strong></div>
                    <div class="summary-line"><span>Durasi</span><strong data-duration-output><?= h($duration) ?> jam</strong></div>
                    <div class="summary-total"><span>Total</span><strong data-rental-total><?= h(rupiah($vehicle['harga_per_jam'] * $duration)) ?></strong></div>
                    <span class="vehicle-stock"><?= h($vehicle['stok']) ?> unit tersedia</span>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
