<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Gowes.id — Keliling Bandung dengan cara yang lebih ringan';
$activePage = 'home';
$statistics = db_one(
    'SELECT
        (SELECT COUNT(*) FROM kendaraan) AS total_kendaraan,
        (SELECT COUNT(*) FROM lokasi) AS total_lokasi,
        (SELECT MIN(harga_per_jam) FROM kendaraan WHERE stok > 0) AS tarif_minimum'
) ?? [];
$featuredVehicles = db_all(
    'SELECT kendaraan.id, kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
            kategori.nama_kategori, lokasi.nama_lokasi
     FROM kendaraan
     JOIN kategori ON kategori.id = kendaraan.kategori_id
     JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
     WHERE kendaraan.stok > 0
     ORDER BY kendaraan.id DESC
     LIMIT 3'
);
$locations = db_all('SELECT nama_lokasi, alamat, kota FROM lokasi ORDER BY id LIMIT 5');

require __DIR__ . '/includes/header.php';
render_db_alert();
?>
<main id="main-content">
    <section class="hero">
        <div class="container hero-layout">
            <div class="hero-copy">
                <p class="eyebrow">Jelajahi Bandung, lebih dekat</p>
                <h1>Perjalanan dekat, <span>lebih terasa.</span></h1>
                <p>Sewa sepeda dan skuter listrik per jam. Pilih kendaraan, ambil di titik terdekat, lalu lanjutkan harimu.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="<?= h($frontendBase) ?>/kendaraan.php">Lihat kendaraan <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    <a class="text-link" href="#cara-sewa">Cara sewa <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="hero-image-wrap">
                <img class="hero-image" src="<?= h($frontendBase) ?>/img/city-bicycle.jpg" alt="Sepeda siap digunakan untuk menjelajahi jalan kota">
                <div class="hero-note">
                    <strong>Bandung, dari dekat.</strong>
                    <span>Ambil kendaraan di lokasi pilihanmu.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-strip" aria-label="Ringkasan layanan">
        <div class="container stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?= h($statistics['total_kendaraan'] ?? '—') ?></span>
                <span class="stat-label">Kendaraan tersedia</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= h($statistics['total_lokasi'] ?? '—') ?></span>
                <span class="stat-label">Titik pengambilan</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= isset($statistics['tarif_minimum']) && $statistics['tarif_minimum'] !== null ? h(rupiah($statistics['tarif_minimum'])) : '—' ?></span>
                <span class="stat-label">Tarif mulai per jam</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Pilih teman perjalananmu</p>
                    <h2>Siap dipakai hari ini</h2>
                </div>
                <a class="text-link" href="<?= h($frontendBase) ?>/kendaraan.php">Lihat semua <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <?php if ($featuredVehicles): ?>
                <div class="vehicle-grid">
                    <?php foreach ($featuredVehicles as $vehicle): ?>
                        <article class="vehicle-card">
                            <div class="vehicle-photo">
                                <img src="<?= h(vehicle_image($vehicle['nama_kategori'], $vehicle['nama_kendaraan'])) ?>" alt="<?= h($vehicle['nama_kendaraan']) ?>" loading="lazy">
                                <span class="vehicle-tag"><?= h($vehicle['nama_kategori']) ?></span>
                            </div>
                            <div class="vehicle-body">
                                <div class="vehicle-title-row">
                                    <h3><?= h($vehicle['nama_kendaraan']) ?></h3>
                                </div>
                                <p class="vehicle-location"><i class="bi bi-geo-alt" aria-hidden="true"></i><?= h($vehicle['nama_lokasi']) ?></p>
                                <div class="vehicle-bottom">
                                    <div>
                                        <div class="vehicle-price"><?= h(rupiah($vehicle['harga_per_jam'])) ?> <small>/ jam</small></div>
                                        <span class="vehicle-stock"><?= h($vehicle['stok']) ?> unit tersedia</span>
                                    </div>
                                    <a class="button button-outline button-small" href="<?= h($frontendBase) ?>/sewa.php?id=<?= h($vehicle['id']) ?>">Pilih</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($databaseError === null): ?>
                <div class="empty-state">Belum ada kendaraan yang tersedia saat ini.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section" id="cara-sewa">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Mudah dari awal</p>
                    <h2>Tiga langkah, lalu berangkat</h2>
                </div>
            </div>
            <div class="steps">
                <article class="step">
                    <span class="step-number">01</span>
                    <div><h3>Pilih kendaraan</h3><p>Bandingkan jenis, tarif, dan titik pengambilan yang paling dekat.</p></div>
                </article>
                <article class="step">
                    <span class="step-number">02</span>
                    <div><h3>Tentukan durasi</h3><p>Atur lama sewa dan pilih metode pembayaran yang sesuai.</p></div>
                </article>
                <article class="step">
                    <span class="step-number">03</span>
                    <div><h3>Nikmati perjalanan</h3><p>Ambil kendaraan di lokasi, lalu kembalikan setelah selesai.</p></div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Titik pengambilan</p>
                    <h2>Berangkat dari sekitar kamu</h2>
                </div>
                <span class="text-link"><i class="bi bi-geo-alt" aria-hidden="true"></i> Bandung</span>
            </div>
            <?php if ($locations): ?>
                <div class="location-list">
                    <?php foreach ($locations as $location): ?>
                        <div class="location-item">
                            <strong><?= h($location['nama_lokasi']) ?></strong>
                            <span><?= h($location['alamat']) ?>, <?= h($location['kota']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($databaseError === null): ?>
                <div class="empty-state">Informasi lokasi belum tersedia.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container closing-banner">
            <div>
                <h2>Jarak dekat tak harus terasa jauh.</h2>
                <p>Temukan kendaraan dan titik pengambilan yang cocok untukmu.</p>
            </div>
            <a class="button button-primary" href="<?= h($frontendBase) ?>/kendaraan.php">Cari kendaraan <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
