<?php
$servername = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'rental_kendaraan';
$port = 3307;
$conn = null;
$databaseError = null;

try {
    $dsn = "mysql:host={$servername};port={$port};dbname={$database};charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    $databaseError = 'Koneksi database belum tersedia. Pastikan MySQL aktif dan database rental_kendaraan sudah diimpor.';
}