<?php
// ==============================================================================
// FILE: admin_login.php
// DESKRIPSI: Logika Autentikasi Admin (Login, Register, & Logout)
// ==============================================================================

require_once 'config.php';

$alert_message = '';
$alert_type = '';

// Handle Logout Admin
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nama']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
    session_destroy();
    header("Location: index.php?msg=logout_success");
    exit();
}

// Handle POST Requests (Login / Register)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';

    // ==========================================
    // 1. PROSES LOGIN ADMIN
    // ==========================================
    if ($form_type === 'login') {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['flash_msg'] = "Harap isi email dan password Anda!";
            $_SESSION['flash_type'] = "danger";
        } else {
            if ($pdo) {
                // Autentikasi dengan MySQL Database
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role'] = $admin['role'];

                    $_SESSION['flash_msg'] = "Login berhasil! Selamat datang, " . htmlspecialchars($admin['nama_lengkap']);
                    $_SESSION['flash_type'] = "success";
                } else {
                    $_SESSION['flash_msg'] = "Email atau password admin salah!";
                    $_SESSION['flash_type'] = "danger";
                }
            } else {
                // Fallback Mode Demo (Tanpa Database MySQL)
                if ($email === 'admin@tokokita.com' && $password === 'admin123') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = 1;
                    $_SESSION['admin_nama'] = 'Super Admin (Demo Mode)';
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['admin_role'] = 'Super Admin';

                    $_SESSION['flash_msg'] = "Login Demo Berhasil! (Database MySQL belum terkoneksi)";
                    $_SESSION['flash_type'] = "success";
                } else {
                    $_SESSION['flash_msg'] = "Email atau password demo salah! Gunakan admin@tokokita.com / admin123";
                    $_SESSION['flash_type'] = "danger";
                }
            }
        }
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // 2. PROSES DAFTAR ADMIN BARU
    // ==========================================
    else if ($form_type === 'register') {
        $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $secret_key = trim($_POST['secret_key'] ?? '');

        if (empty($nama_lengkap) || empty($email) || empty($password) || empty($secret_key)) {
            $_SESSION['flash_msg'] = "Semua bidang formulir pendaftaran wajib diisi!";
            $_SESSION['flash_type'] = "danger";
        } elseif ($password !== $confirm_password) {
            $_SESSION['flash_msg'] = "Konfirmasi password tidak cocok!";
            $_SESSION['flash_type'] = "danger";
        } elseif (strlen($password) < 6) {
            $_SESSION['flash_msg'] = "Password minimal terdiri dari 6 karakter!";
            $_SESSION['flash_type'] = "warning";
        } elseif ($secret_key !== ADMIN_SECRET_KEY) {
            $_SESSION['flash_msg'] = "Kode Rahasia Admin (Secret Key) salah! Ketik 'ADMIN123'.";
            $_SESSION['flash_type'] = "danger";
        } else {
            if ($pdo) {
                // Cek ketersediaan email
                $stmtCheck = $pdo->prepare("SELECT id FROM admins WHERE email = :email LIMIT 1");
                $stmtCheck->execute(['email' => $email]);
                
                if ($stmtCheck->fetch()) {
                    $_SESSION['flash_msg'] = "Email ini sudah terdaftar sebagai admin!";
                    $_SESSION['flash_type'] = "warning";
                } else {
                    // Enkripsi Password & Simpan Admin Baru
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $stmtInsert = $pdo->prepare("INSERT INTO admins (nama_lengkap, email, password, role) VALUES (:nama, :email, :pass, 'Admin')");
                    $stmtInsert->execute([
                        'nama' => $nama_lengkap,
                        'email' => $email,
                        'pass' => $hashed_password
                    ]);

                    $_SESSION['flash_msg'] = "Pendaftaran admin baru berhasil! Silakan login.";
                    $_SESSION['flash_type'] = "success";
                }
            } else {
                $_SESSION['flash_msg'] = "Registrasi Demo Berhasil! Sambungkan database MySQL untuk menyimpan secara permanen.";
                $_SESSION['flash_type'] = "success";
            }
        }
        header("Location: index.php");
        exit();
    }
}
?>