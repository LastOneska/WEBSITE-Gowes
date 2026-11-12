<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pageTitle = 'Kelola kendaraan — Gowes.id';
$vehicles = db_all(
    'SELECT kendaraan.id, kendaraan.nama_kendaraan, kendaraan.harga_per_jam, kendaraan.stok,
            kendaraan.kategori_id, kendaraan.lokasi_id, kategori.nama_kategori, lokasi.nama_lokasi
     FROM kendaraan
     JOIN kategori ON kategori.id = kendaraan.kategori_id
     JOIN lokasi ON lokasi.id = kendaraan.lokasi_id
     ORDER BY kendaraan.nama_kendaraan'
);
$categories = db_all('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori');
$locations = db_all('SELECT id, nama_lokasi FROM lokasi ORDER BY nama_lokasi');

require __DIR__ . '/../includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container admin-shell">
    <aside class="admin-sidebar" aria-label="Navigasi admin">
        <p>Ruang admin</p>
        <a href="<?= h($frontendBase) ?>/admin/dashboard.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i> Ringkasan</a>
        <a href="<?= h($frontendBase) ?>/admin/kategori.php"><i class="bi bi-tags" aria-hidden="true"></i> Kategori</a>
        <a class="active" href="<?= h($frontendBase) ?>/admin/kendaraan.php"><i class="bi bi-bicycle" aria-hidden="true"></i> Kendaraan</a>
        <a href="<?= h($frontendBase) ?>/admin/penyewaan.php"><i class="bi bi-receipt" aria-hidden="true"></i> Penyewaan</a>
    </aside>
    <section class="admin-main">
        <div class="admin-heading">
            <div><p class="eyebrow">Pengaturan katalog</p><h1>Kendaraan</h1><p>Atur jenis, titik pengambilan, tarif, dan ketersediaan unit.</p></div>
            <button class="button button-primary" type="button" data-bs-toggle="modal" data-bs-target="#addVehicle" <?= (!$categories || !$locations) ? 'disabled' : '' ?>><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah kendaraan</button>
        </div>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><?= h($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success" role="status"><i class="bi bi-check-circle" aria-hidden="true"></i><?= h($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <section class="admin-panel">
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Kendaraan</th><th>Kategori</th><th>Lokasi</th><th>Tarif / jam</th><th>Stok</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td data-label="Kendaraan">
                                    <div class="inventory-vehicle">
                                        <img class="inventory-image" src="<?= h(vehicle_image($vehicle['nama_kategori'], $vehicle['nama_kendaraan'])) ?>" alt="">
                                        <strong><?= h($vehicle['nama_kendaraan']) ?></strong>
                                    </div>
                                </td>
                                <td data-label="Kategori"><?= h($vehicle['nama_kategori']) ?></td>
                                <td data-label="Lokasi"><?= h($vehicle['nama_lokasi']) ?></td>
                                <td data-label="Tarif / jam"><?= h(rupiah($vehicle['harga_per_jam'])) ?></td>
                                <td data-label="Stok"><?= h($vehicle['stok']) ?> unit</td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <button class="button button-quiet" type="button" data-bs-toggle="modal" data-bs-target="#editVehicle<?= h($vehicle['id']) ?>">Ubah</button>
                                        <form action="<?= h($backendBase) ?>/kendaraan/hapus.php" method="post" data-confirm="Hapus kendaraan ini?">
                                            <input type="hidden" name="id" value="<?= h($vehicle['id']) ?>">
                                            <button class="button button-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                    <div class="modal fade" id="editVehicle<?= h($vehicle['id']) ?>" tabindex="-1" aria-labelledby="editVehicleTitle<?= h($vehicle['id']) ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="<?= h($backendBase) ?>/kendaraan/edit.php" method="post">
                                                    <div class="modal-header"><h2 class="modal-title fs-5" id="editVehicleTitle<?= h($vehicle['id']) ?>">Ubah kendaraan</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= h($vehicle['id']) ?>">
                                                        <div class="form-field"><label for="vehicle-name-<?= h($vehicle['id']) ?>">Nama kendaraan</label><input class="form-control" id="vehicle-name-<?= h($vehicle['id']) ?>" name="nama_kendaraan" required maxlength="100" value="<?= h($vehicle['nama_kendaraan']) ?>"></div>
                                                        <div class="form-field"><label for="vehicle-category-<?= h($vehicle['id']) ?>">Kategori</label><select class="form-select" id="vehicle-category-<?= h($vehicle['id']) ?>" name="kategori_id" required><?php foreach ($categories as $category): ?><option value="<?= h($category['id']) ?>" <?= (int) $vehicle['kategori_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= h($category['nama_kategori']) ?></option><?php endforeach; ?></select></div>
                                                        <div class="form-field"><label for="vehicle-location-<?= h($vehicle['id']) ?>">Lokasi</label><select class="form-select" id="vehicle-location-<?= h($vehicle['id']) ?>" name="lokasi_id" required><?php foreach ($locations as $location): ?><option value="<?= h($location['id']) ?>" <?= (int) $vehicle['lokasi_id'] === (int) $location['id'] ? 'selected' : '' ?>><?= h($location['nama_lokasi']) ?></option><?php endforeach; ?></select></div>
                                                        <div class="form-field"><label for="vehicle-rate-<?= h($vehicle['id']) ?>">Harga per jam (Rp)</label><input class="form-control" id="vehicle-rate-<?= h($vehicle['id']) ?>" name="harga_per_jam" type="number" min="1" step="1000" required value="<?= h($vehicle['harga_per_jam']) ?>"></div>
                                                        <div class="form-field"><label for="vehicle-stock-<?= h($vehicle['id']) ?>">Stok</label><input class="form-control" id="vehicle-stock-<?= h($vehicle['id']) ?>" name="stok" type="number" min="0" required value="<?= h($vehicle['stok']) ?>"></div>
                                                    </div>
                                                    <div class="modal-footer"><button class="button button-outline" type="button" data-bs-dismiss="modal">Batal</button><button class="button button-brand" type="submit">Simpan perubahan</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$vehicles && $databaseError === null): ?>
                            <tr><td colspan="6">Belum ada kendaraan terdaftar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
<div class="modal fade" id="addVehicle" tabindex="-1" aria-labelledby="addVehicleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= h($backendBase) ?>/kendaraan/tambah.php" method="post">
                <div class="modal-header"><h2 class="modal-title fs-5" id="addVehicleTitle">Tambah kendaraan</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <div class="form-field"><label for="new-vehicle-name">Nama kendaraan</label><input class="form-control" id="new-vehicle-name" name="nama_kendaraan" required maxlength="100"></div>
                    <div class="form-field"><label for="new-vehicle-category">Kategori</label><select class="form-select" id="new-vehicle-category" name="kategori_id" required><option value="">Pilih kategori</option><?php foreach ($categories as $category): ?><option value="<?= h($category['id']) ?>"><?= h($category['nama_kategori']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-field"><label for="new-vehicle-location">Lokasi</label><select class="form-select" id="new-vehicle-location" name="lokasi_id" required><option value="">Pilih lokasi</option><?php foreach ($locations as $location): ?><option value="<?= h($location['id']) ?>"><?= h($location['nama_lokasi']) ?></option><?php endforeach; ?></select></div>
                    <div class="form-field"><label for="new-vehicle-rate">Harga per jam (Rp)</label><input class="form-control" id="new-vehicle-rate" name="harga_per_jam" type="number" min="1" step="1000" required></div>
                    <div class="form-field"><label for="new-vehicle-stock">Stok</label><input class="form-control" id="new-vehicle-stock" name="stok" type="number" min="0" required value="1"></div>
                </div>
                <div class="modal-footer"><button class="button button-outline" type="button" data-bs-dismiss="modal">Batal</button><button class="button button-primary" type="submit">Simpan kendaraan</button></div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
