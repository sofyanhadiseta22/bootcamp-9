<?php
// ==============================================================================
// FILE: config.php
// DESKRIPSI: Konfigurasi Database PDO & Sesi Global
// ==============================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengaturan Koneksi Database MySQL
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_tokokita';

// Kode Rahasia Admin untuk Registrasi Akun Baru
define('ADMIN_SECRET_KEY', 'ADMIN123');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Mode demo fallback jika MySQL belum dihubungkan
    $pdo = null;
}
?>