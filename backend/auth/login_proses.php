<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

$loginPage = '../../frontend/login.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $loginPage);
    exit;
}

$usernameInput = $_POST['username'] ?? '';
$passwordInput = $_POST['password'] ?? '';
$usernameInput = is_string($usernameInput) ? trim($usernameInput) : '';
$passwordInput = is_string($passwordInput) ? $passwordInput : '';

if ($usernameInput === '' || $passwordInput === '') {
    $_SESSION['flash_error'] = 'Username dan password wajib diisi.';
    header('Location: ' . $loginPage);
    exit;
}

if (!$conn instanceof PDO) {
    $_SESSION['flash_error'] = $databaseError ?? 'Koneksi database belum tersedia.';
    header('Location: ' . $loginPage);
    exit;
}

$user = new User($conn);
if ($user->login($usernameInput, $passwordInput)) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user->getUserId();
    $_SESSION['role'] = $user->getRole();
    $_SESSION['nama'] = $user->getName();

    $destination = $_SESSION['role'] === 'admin'
        ? '../../frontend/admin/dashboard.php'
        : '../../frontend/index.php';
    header('Location: ' . $destination);
    exit;
}

$_SESSION['flash_error'] = $user->getError() ?? 'Login gagal. Silakan coba lagi.';
header('Location: ' . $loginPage);
exit;