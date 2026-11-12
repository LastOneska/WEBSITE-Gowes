<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Kategori.php';

$adminPage = '../../frontend/admin/kategori.php';

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
$nama = $_POST['nama_kategori'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? null;

if (!is_int($id) || $id < 1 || !is_string($nama) || !is_string($deskripsi)) {
    $_SESSION['flash_error'] = 'Data kategori tidak valid.';
    header('Location: ' . $adminPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $adminPage);
    exit;
}

$kategori = new Kategori($conn);
if ($kategori->ubah($id, $nama, $deskripsi)) {
    $_SESSION['flash_success'] = 'Kategori berhasil diperbarui.';
} else {
    $_SESSION['flash_error'] = $kategori->getError() ?? 'Kategori gagal diperbarui.';
}

header('Location: ' . $adminPage);
exit;
