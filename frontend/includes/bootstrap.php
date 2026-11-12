<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$frontendPosition = strpos($scriptPath, '/frontend/');
$projectBase = $frontendPosition === false ? '' : substr($scriptPath, 0, $frontendPosition);
$frontendBase = $projectBase . '/frontend';
$backendBase = $projectBase . '/backend';
$databaseError = null;

require_once __DIR__ . '/../../backend/config/database.php';

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function db_all(string $sql, array $params = []): array
{
    global $conn, $databaseError;

    if (!$conn instanceof PDO) {
        return [];
    }

    try {
        $statement = $conn->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $databaseError = 'Data belum dapat dimuat. Pastikan database rental_kendaraan sudah diimpor dan konfigurasi koneksi sudah benar.';
        return [];
    }
}

function db_one(string $sql, array $params = []): ?array
{
    $rows = db_all($sql, $params);
    return $rows[0] ?? null;
}

function render_db_alert(): void
{
    global $databaseError;
    if ($databaseError !== null) {
        echo '<div class="container page-alert"><div class="alert alert-error" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> ' . h($databaseError) . '</div></div>';
    }
}

function require_login(): void
{
    global $frontendBase;
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $frontendBase . '/login.php');
        exit;
    }
}

function require_admin(): void
{
    global $frontendBase;
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ' . $frontendBase . '/login.php');
        exit;
    }
}

function vehicle_image(string $category, string $name = ''): string
{
    global $frontendBase;
    $normalized = strtolower($category . ' ' . $name);
    if (strpos($normalized, 'skuter') !== false) {
        $image = 'electric-scooter.jpg';
    } elseif (strpos($normalized, 'sepeda listrik') !== false) {
        $image = 'electric-bicycle.jpg';
    } elseif (strpos($normalized, 'lipat') !== false) {
        $image = 'folding-bicycle.jpg';
    } elseif (strpos($normalized, 'gunung') !== false) {
        $image = 'mountain-bicycle.jpg';
    } elseif (strpos($normalized, 'fixie') !== false) {
        $image = 'fixie-bicycle.jpg';
    } elseif (strpos($normalized, 'city bike') !== false) {
        $image = 'city-bicycle.jpg';
    } else {
        $image = 'city-bicycle.jpg';
    }

    return $frontendBase . '/img/' . $image;
}
