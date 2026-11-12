<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Kendaraan.php';

$adminPage = '../../frontend/admin/kendaraan.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['flash_error'] = 'Akses hanya untuk admin.';
    header('Location: ../../frontend/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $adminPage);
    exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$nama = $_POST['nama_kendaraan'] ?? null;
$kategoriId = filter_var($_POST['kategori_id'] ?? null, FILTER_VALIDATE_INT);
$lokasiId = filter_var($_POST['lokasi_id'] ?? null, FILTER_VALIDATE_INT);
$hargaPerJam = filter_var($_POST['harga_per_jam'] ?? null, FILTER_VALIDATE_INT);
$stok = filter_var($_POST['stok'] ?? null, FILTER_VALIDATE_INT);

if (
    !is_int($id) || $id < 1
    || !is_string($nama)
    || !is_int($kategoriId) || $kategoriId < 1
    || !is_int($lokasiId) || $lokasiId < 1
    || !is_int($hargaPerJam) || $hargaPerJam < 1
    || !is_int($stok) || $stok < 0
) {
    $_SESSION['flash_error'] = 'Data kendaraan tidak valid.';
    header('Location: ' . $adminPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $adminPage);
    exit;
}

$kendaraan = new Kendaraan($conn);
if ($kendaraan->ubah($id, $nama, $kategoriId, $lokasiId, $hargaPerJam, $stok)) {
    $_SESSION['flash_success'] = 'Kendaraan berhasil diperbarui.';
} else {
    $_SESSION['flash_error'] = $kendaraan->getError() ?? 'Kendaraan gagal diperbarui.';
}

header('Location: ' . $adminPage);
exit;
