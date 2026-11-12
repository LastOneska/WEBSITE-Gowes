<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Penyewaan.php';

$historyPage = '../../frontend/riwayat.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $historyPage);
    exit;
}

$userId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!is_int($userId) || $userId < 1) {
    $_SESSION['flash_error'] = 'Silakan masuk untuk mengembalikan kendaraan.';
    header('Location: ../../frontend/login.php');
    exit;
}

$penyewaanId = filter_var($_POST['penyewaan_id'] ?? null, FILTER_VALIDATE_INT);
if (!is_int($penyewaanId) || $penyewaanId < 1) {
    $_SESSION['flash_error'] = 'Data penyewaan tidak valid.';
    header('Location: ' . $historyPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $historyPage);
    exit;
}

$penyewaan = new Penyewaan($conn);
if (!$penyewaan->kembalikan($penyewaanId, $userId)) {
    $_SESSION['flash_error'] = $penyewaan->getError() ?? 'Pengembalian gagal diproses.';
    header('Location: ' . $historyPage);
    exit;
}

$_SESSION['flash_success'] = 'Kendaraan berhasil dikembalikan.';
header('Location: ' . $historyPage);
exit;
