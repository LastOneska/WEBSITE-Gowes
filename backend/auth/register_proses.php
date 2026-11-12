<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

$registerPage = '../../frontend/register.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $registerPage);
    exit;
}

$nama = $_POST['nama'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$passwordConfirmation = $_POST['password_confirmation'] ?? '';

$nama = is_string($nama) ? trim($nama) : '';
$username = is_string($username) ? trim($username) : '';
$password = is_string($password) ? $password : '';
$passwordConfirmation = is_string($passwordConfirmation) ? $passwordConfirmation : '';

if ($nama === '' || $username === '' || $password === '' || $passwordConfirmation === '') {
    $_SESSION['flash_error'] = 'Semua field wajib diisi.';
    header('Location: ' . $registerPage);
    exit;
}

if (strlen($username) < 4) {
    $_SESSION['flash_error'] = 'Username harus memiliki minimal 4 karakter.';
    header('Location: ' . $registerPage);
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['flash_error'] = 'Password harus memiliki minimal 6 karakter.';
    header('Location: ' . $registerPage);
    exit;
}

if ($password !== $passwordConfirmation) {
    $_SESSION['flash_error'] = 'Password dan konfirmasi password tidak cocok.';
    header('Location: ' . $registerPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $registerPage);
    exit;
}

$user = new User($conn);
if (!$user->register($username, $password, $nama)) {
    $_SESSION['flash_error'] = $user->getError() ?? 'Registrasi gagal. Silakan coba lagi.';
    header('Location: ' . $registerPage);
    exit;
}

$_SESSION['flash_success'] = 'Pendaftaran berhasil. Silakan masuk dengan akun baru.';
header('Location: ../../frontend/login.php');
exit;
