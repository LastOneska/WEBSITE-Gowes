<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Penyewaan.php';

$sewaPage = '../../frontend/sewa.php';
$historyPage = '../../frontend/riwayat.php';

function kembaliKeFormSewa(string $message, int $kendaraanId = 0): void
{
    global $sewaPage;

    $_SESSION['flash_error'] = $message;
    $query = $kendaraanId > 0 ? '?id=' . $kendaraanId : '';
    header('Location: ' . $sewaPage . $query);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/kendaraan.php');
    exit;
}

$userId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!is_int($userId) || $userId < 1) {
    $_SESSION['flash_error'] = 'Silakan masuk untuk melakukan penyewaan.';
    header('Location: ../../frontend/login.php');
    exit;
}

$kendaraanId = filter_var($_POST['kendaraan_id'] ?? null, FILTER_VALIDATE_INT);
$durasiJam = filter_var($_POST['durasi_jam'] ?? null, FILTER_VALIDATE_INT);
$metode = $_POST['metode'] ?? null;

if (!is_int($kendaraanId) || $kendaraanId < 1) {
    kembaliKeFormSewa('Kendaraan yang dipilih tidak valid.');
}

if (!is_int($durasiJam) || $durasiJam < 1 || $durasiJam > 12) {
    kembaliKeFormSewa('Durasi sewa harus antara 1 dan 12 jam.', $kendaraanId);
}

if (!is_string($metode) || !in_array($metode, ['tunai', 'transfer', 'e-wallet'], true)) {
    kembaliKeFormSewa('Metode pembayaran tidak valid.', $kendaraanId);
}

if (!$conn instanceof PDO) {
    kembaliKeFormSewa(
        $databaseError ?? 'Koneksi database belum tersedia.',
        $kendaraanId
    );
}

$penyewaan = new Penyewaan($conn);
if (!$penyewaan->sewa($userId, $kendaraanId, $durasiJam, $metode)) {
    kembaliKeFormSewa(
        $penyewaan->getError() ?? 'Penyewaan gagal diproses.',
        $kendaraanId
    );
}

$_SESSION['flash_success'] = 'Penyewaan berhasil dibuat.';
header('Location: ' . $historyPage);
exit;
