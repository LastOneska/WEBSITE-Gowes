<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$pageTitle = 'Kelola kategori — Gowes.id';
$categories = db_all(
    'SELECT kategori.id, kategori.nama_kategori, kategori.deskripsi, COUNT(kendaraan.id) AS jumlah_kendaraan
     FROM kategori
     LEFT JOIN kendaraan ON kendaraan.kategori_id = kategori.id
     GROUP BY kategori.id, kategori.nama_kategori, kategori.deskripsi
     ORDER BY kategori.nama_kategori'
);

require __DIR__ . '/../includes/header.php';
render_db_alert();
?>
<main id="main-content" class="container admin-shell">
    <aside class="admin-sidebar" aria-label="Navigasi admin">
        <p>Ruang admin</p>
        <a href="<?= h($frontendBase) ?>/admin/dashboard.php"><i class="bi bi-grid-1x2" aria-hidden="true"></i> Ringkasan</a>
        <a class="active" href="<?= h($frontendBase) ?>/admin/kategori.php"><i class="bi bi-tags" aria-hidden="true"></i> Kategori</a>
        <a href="<?= h($frontendBase) ?>/admin/kendaraan.php"><i class="bi bi-bicycle" aria-hidden="true"></i> Kendaraan</a>
        <a href="<?= h($frontendBase) ?>/admin/penyewaan.php"><i class="bi bi-receipt" aria-hidden="true"></i> Penyewaan</a>
    </aside>
    <section class="admin-main">
        <div class="admin-heading">
            <div><p class="eyebrow">Pengaturan katalog</p><h1>Kategori</h1><p>Kelola jenis kendaraan yang tampil di katalog.</p></div>
            <button class="button button-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCategory"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah kategori</button>
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
                    <thead><tr><th>Nama kategori</th><th>Deskripsi</th><th>Kendaraan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td data-label="Nama kategori"><strong><?= h($category['nama_kategori']) ?></strong></td>
                                <td data-label="Deskripsi"><?= h($category['deskripsi'] ?? '—') ?></td>
                                <td data-label="Kendaraan"><?= h($category['jumlah_kendaraan']) ?> unit</td>
                                <td data-label="Aksi">
                                    <div class="table-actions">
                                        <button class="button button-quiet" type="button" data-bs-toggle="modal" data-bs-target="#editCategory<?= h($category['id']) ?>">Ubah</button>
                                        <form action="<?= h($backendBase) ?>/kategori/hapus.php" method="post" data-confirm="Hapus kategori ini? Kategori yang masih digunakan kendaraan tidak dapat dihapus.">
                                            <input type="hidden" name="id" value="<?= h($category['id']) ?>">
                                            <button class="button button-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                    <div class="modal fade" id="editCategory<?= h($category['id']) ?>" tabindex="-1" aria-labelledby="editCategoryTitle<?= h($category['id']) ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="<?= h($backendBase) ?>/kategori/edit.php" method="post">
                                                    <div class="modal-header"><h2 class="modal-title fs-5" id="editCategoryTitle<?= h($category['id']) ?>">Ubah kategori</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= h($category['id']) ?>">
                                                        <div class="form-field"><label for="category-name-<?= h($category['id']) ?>">Nama kategori</label><input class="form-control" id="category-name-<?= h($category['id']) ?>" name="nama_kategori" required maxlength="50" value="<?= h($category['nama_kategori']) ?>"></div>
                                                        <div class="form-field"><label for="category-description-<?= h($category['id']) ?>">Deskripsi</label><textarea class="form-control" id="category-description-<?= h($category['id']) ?>" name="deskripsi" rows="3"><?= h($category['deskripsi']) ?></textarea></div>
                                                    </div>
                                                    <div class="modal-footer"><button class="button button-outline" type="button" data-bs-dismiss="modal">Batal</button><button class="button button-brand" type="submit">Simpan perubahan</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$categories && $databaseError === null): ?>
                            <tr><td colspan="4">Belum ada kategori.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
<div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="addCategoryTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= h($backendBase) ?>/kategori/tambah.php" method="post">
                <div class="modal-header"><h2 class="modal-title fs-5" id="addCategoryTitle">Tambah kategori</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <div class="form-field"><label for="new-category-name">Nama kategori</label><input class="form-control" id="new-category-name" name="nama_kategori" required maxlength="50"></div>
                    <div class="form-field"><label for="new-category-description">Deskripsi</label><textarea class="form-control" id="new-category-description" name="deskripsi" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button class="button button-outline" type="button" data-bs-dismiss="modal">Batal</button><button class="button button-primary" type="submit">Simpan kategori</button></div>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
