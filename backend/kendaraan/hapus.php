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
if (!is_int($id) || $id < 1) {
    $_SESSION['flash_error'] = 'ID kendaraan tidak valid.';
    header('Location: ' . $adminPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $adminPage);
    exit;
}

$kendaraan = new Kendaraan($conn);
if ($kendaraan->hapus($id)) {
    $_SESSION['flash_success'] = 'Kendaraan berhasil dihapus.';
} else {
    $_SESSION['flash_error'] = $kendaraan->getError() ?? 'Kendaraan gagal dihapus.';
}

header('Location: ' . $adminPage);
exit;
