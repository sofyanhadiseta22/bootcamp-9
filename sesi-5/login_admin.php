<?php
// ==============================================================================
// FILE: admin_login.php
// DESKRIPSI: Sistem Autentikasi Admin Terpisah (Login, Register & Logout)
// ==============================================================================
/* 
SKEMA TABEL MYSQL (Jalankan query ini di phpMyAdmin):

CREATE DATABASE IF NOT EXISTS db_tokokita;
USE db_tokokita;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun Default Demo (Email: admin@tokokita.com | Password: admin123)
INSERT INTO admins (nama_lengkap, email, password, role) 
VALUES ('Super Admin', 'admin@tokokita.com', '$2y$10$4.z4z1TfO3O8x8cZ7G8J4eN1oO6K3uG7.6XyZ8pQ1.eO8w9jK1X2G', 'Super Admin')
ON DUPLICATE KEY UPDATE id=id;
*/

session_start();

// Pengaturan Koneksi Database MySQL
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_tokokita';

// Kode Rahasia Admin untuk Registrasi Akun Baru
define('ADMIN_SECRET_KEY', 'ADMIN123');

$alert_message = '';
$alert_type = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Mode demo fallback jika MySQL belum dihubungkan
    $pdo = null;
}

// Handle Logout Admin
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nama']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
    session_destroy();
    header("Location: admin_login.php?msg=logout_success");
    exit();
}

// Handle Pesan Notifikasi GET
if (isset($_GET['msg']) && $_GET['msg'] === 'logout_success') {
    $alert_message = "Anda telah berhasil logout dari Portal Admin.";
    $alert_type = "info";
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
            $alert_message = "Harap isi email dan password Anda!";
            $alert_type = "danger";
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

                    $alert_message = "Login berhasil! Selamat datang, " . htmlspecialchars($admin['nama_lengkap']);
                    $alert_type = "success";
                } else {
                    $alert_message = "Email atau password admin salah!";
                    $alert_type = "danger";
                }
            } else {
                // Fallback Mode Demo (Tanpa Database MySQL)
                if ($email === 'admin@tokokita.com' && $password === 'admin123') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = 1;
                    $_SESSION['admin_nama'] = 'Super Admin (Demo Mode)';
                    $_SESSION['admin_email'] = $email;
                    $_SESSION['admin_role'] = 'Super Admin';

                    $alert_message = "Login Demo Berhasil! (Database MySQL belum terkoneksi)";
                    $alert_type = "success";
                } else {
                    $alert_message = "Email atau password demo salah! Gunakan admin@tokokita.com / admin123";
                    $alert_type = "danger";
                }
            }
        }
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
            $alert_message = "Semua bidang formulir pendaftaran wajib diisi!";
            $alert_type = "danger";
        } elseif ($password !== $confirm_password) {
            $alert_message = "Konfirmasi password tidak cocok!";
            $alert_type = "danger";
        } elseif (strlen($password) < 6) {
            $alert_message = "Password minimal terdiri dari 6 karakter!";
            $alert_type = "warning";
        } elseif ($secret_key !== ADMIN_SECRET_KEY) {
            $alert_message = "Kode Rahasia Admin (Secret Key) salah! Ketik 'ADMIN123'.";
            $alert_type = "danger";
        } else {
            if ($pdo) {
                // Cek ketersediaan email
                $stmtCheck = $pdo->prepare("SELECT id FROM admins WHERE email = :email LIMIT 1");
                $stmtCheck->execute(['email' => $email]);
                
                if ($stmtCheck->fetch()) {
                    $alert_message = "Email ini sudah terdaftar sebagai admin!";
                    $alert_type = "warning";
                } else {
                    // Enkripsi Password & Simpan Admin Baru
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $stmtInsert = $pdo->prepare("INSERT INTO admins (nama_lengkap, email, password, role) VALUES (:nama, :email, :pass, 'Admin')");
                    $stmtInsert->execute([
                        'nama' => $nama_lengkap,
                        'email' => $email,
                        'pass' => $hashed_password
                    ]);

                    $alert_message = "Pendaftaran admin baru berhasil! Silakan login.";
                    $alert_type = "success";
                }
            } else {
                $alert_message = "Registrasi Demo Berhasil! Sambungkan database MySQL untuk menyimpan secara permanen.";
                $alert_type = "success";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Login Admin Toko - PHP Standalone</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .auth-container {
            width: 100%;
            max-width: 920px;
            margin: auto;
        }

        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: #ffffff;
        }

        .auth-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .nav-pills-auth .nav-link {
            color: #64748b;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.65rem 1.2rem;
            transition: all 0.2s ease;
        }

        .nav-pills-auth .nav-link.active {
            background-color: #0d6efd;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="auth-container">
        
        <?php if (!empty($alert_message)): ?>
            <div class="alert alert-<?= $alert_type; ?> alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> <?= $alert_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- KONDISI 1: JIKA ADMIN SUDAH LOGIN -->
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <div class="card auth-card p-4 p-md-5 text-center">
                <div class="mb-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold fs-6">
                        <i class="bi bi-shield-check me-1"></i> Sesi Login Aktif
                    </span>
                </div>
                <h2 class="fw-bold mb-2">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_nama']); ?>!</h2>
                <p class="text-muted mb-4">
                    Email: <code><?= htmlspecialchars($_SESSION['admin_email']); ?></code> | Role: <strong><?= htmlspecialchars($_SESSION['admin_role']); ?></strong>
                </p>

                <div class="p-4 bg-light rounded-4 text-start mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-speedometer2 text-primary me-2"></i>Status Sistem PHP Admin</h5>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span>Status Database MySQL</span>
                            <span class="badge <?= $pdo ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                <?= $pdo ? 'Terkoneksi (PDO)' : 'Mode Demo (Tanpa DB)'; ?>
                            </span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span>Enkripsi Password</span>
                            <span class="badge bg-primary">PASSWORD_BCRYPT</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                            <span>Session ID PHP</span>
                            <span class="text-muted small"><code><?= session_id(); ?></code></span>
                        </li>
                    </ul>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="index.html" class="btn btn-outline-primary btn-lg rounded-pill px-4 fw-semibold">
                        <i class="bi bi-shop me-2"></i> Ke Katalog Toko
                    </a>
                    <a href="admin_login.php?action=logout" class="btn btn-danger btn-lg rounded-pill px-4 fw-semibold">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout Admin
                    </a>
                </div>
            </div>

        <!-- KONDISI 2: TAMPILAN FORM LOGIN / REGISTER JIKA BELUM LOGIN -->
        <?php else: ?>
            <div class="auth-card">
                <div class="row g-0">
                    <!-- Left Side Info Banner -->
                    <div class="col-lg-5 auth-banner">
                        <div class="mb-4">
                            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Portal PHP Administrator</span>
                            <h2 class="fw-bold mb-3">Kelola Toko Online Anda</h2>
                            <p class="text-white-50">Sistem keamanan login terpisah berbasis PHP Native, PDO Database, dan Password Hashing.</p>
                        </div>
                        <div class="pt-3 border-top border-secondary">
                            <div class="d-flex align-items-center gap-2 mb-2 text-white-50 small">
                                <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                                <span>Terproteksi Admin Secret Key.</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-white-50 small">
                                <i class="bi bi-key-fill text-warning fs-5"></i>
                                <span>Demo Login: <strong>admin@tokokita.com</strong> / <strong>admin123</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Login & Register Forms -->
                    <div class="col-lg-7 p-4 p-md-5">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills nav-pills-auth mb-4 justify-content-center bg-light p-1 rounded-3" id="authTabs" role="tablist">
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100 active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login Admin
                                </button>
                            </li>
                            <li class="nav-item flex-fill text-center" role="presentation">
                                <button class="nav-link w-100" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab">
                                    <i class="bi bi-person-plus me-1"></i> Daftar Admin
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="authTabsContent">
                            
                            <!-- FORM 1: LOGIN ADMIN -->
                            <div class="tab-pane fade show active" id="pills-login" role="tabpanel">
                                <h4 class="fw-bold text-dark mb-1">Masuk Akun Admin</h4>
                                <p class="text-muted small mb-4">Masukkan kredensial admin Anda untuk melanjutkan.</p>

                                <form action="admin_login.php" method="POST">
                                    <input type="hidden" name="form_type" value="login">

                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label fw-semibold small">Email Admin</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="admin@tokokita.com" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label fw-semibold small">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="••••••••" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label small" for="rememberMe">Ingat Saya</label>
                                        </div>
                                        <span class="small text-muted">Demo: <code>admin123</code></span>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold shadow-sm">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                                    </button>
                                </form>
                            </div>

                            <!-- FORM 2: REGISTRASI ADMIN -->
                            <div class="tab-pane fade" id="pills-register" role="tabpanel">
                                <h4 class="fw-bold text-dark mb-1">Pendaftaran Admin Baru</h4>
                                <p class="text-muted small mb-3">Buat pengelola baru menggunakan Admin Secret Key.</p>

                                <form action="admin_login.php" method="POST">
                                    <input type="hidden" name="form_type" value="register">

                                    <div class="mb-3">
                                        <label for="regNama" class="form-label fw-semibold small">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                                            <input type="text" class="form-control" id="regNama" name="nama_lengkap" placeholder="Budi Santoso" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="regEmail" class="form-label fw-semibold small">Email Admin</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                                            <input type="email" class="form-control" id="regEmail" name="email" placeholder="nama@tokokita.com" required>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label for="regPass" class="form-label fw-semibold small">Password</label>
                                            <input type="password" class="form-control" id="regPass" name="password" placeholder="Min. 6 karakter" minlength="6" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="regConfirm" class="form-label fw-semibold small">Konfirmasi Password</label>
                                            <input type="password" class="form-control" id="regConfirm" name="confirm_password" placeholder="Ulangi password" minlength="6" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="regSecret" class="form-label fw-semibold small">
                                            Kode Rahasia Admin (Admin Secret Key)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-warning-subtle text-warning-emphasis"><i class="bi bi-key-fill"></i></span>
                                            <input type="password" class="form-control" id="regSecret" name="secret_key" placeholder="Ketik: ADMIN123" required>
                                        </div>
                                        <div class="form-text small">Diperlukan verifikasi untuk membuat akun admin. (Default demo: <code>ADMIN123</code>)</div>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-semibold shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i> Daftar Sebagai Admin
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>
</body>
</html>