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
if (!is_int($id) || $id < 1) {
    $_SESSION['flash_error'] = 'ID kategori tidak valid.';
    header('Location: ' . $adminPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $adminPage);
    exit;
}

$kategori = new Kategori($conn);
if ($kategori->hapus($id)) {
    $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
} else {
    $_SESSION['flash_error'] = $kategori->getError() ?? 'Kategori gagal dihapus.';
}

header('Location: ' . $adminPage);
exit;
