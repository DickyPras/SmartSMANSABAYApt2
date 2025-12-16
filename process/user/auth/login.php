<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../../../error_log.txt');// filepath: /opt/lampp/htdocs/SmartSMANSABAYApt2/process/user/auth/login.php
session_start();

// Include database connection
require_once '../../../config/koneksi.php';

// Function untuk sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function untuk validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Cek apakah request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ../../../views/user/auth/login.php');
    exit;
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid CSRF token';
    header('Location: ../../../views/user/auth/login.php');
    exit;
}

// Ambil dan sanitize input
$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
$errors = [];

// Validate email
if (empty($email)) {
    $errors['email'] = 'Email wajib diisi';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Format email tidak valid';
}

// Validate password
if (empty($password)) {
    $errors['password'] = 'Password wajib diisi';
} elseif (strlen($password) < 6) {
    $errors['password'] = 'Password minimal 6 karakter';
}

// Jika ada error, redirect kembali dengan error message
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_email'] = $email;
    header('Location: ../../../views/user/auth/login.php');
    exit;
}

// Escape input untuk MySQLi
$email = mysqli_real_escape_string($koneksi, $email);

// Query untuk mencari user berdasarkan email
$query = "SELECT id_user, nis, nama, email, password, role, kelas FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    error_log("Login query error: " . mysqli_error($koneksi));
    $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    $_SESSION['old_email'] = $_POST['email'];
    header('Location: ../../../views/user/auth/login.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

// Cek apakah user ditemukan dan password benar
if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Email atau password salah';
    $_SESSION['old_email'] = $_POST['email'];
    header('Location: ../../../views/user/auth/login.php');
    exit;
}

// Login berhasil, simpan data user ke session
$_SESSION['user_id'] = $user['id_user'];
$_SESSION['user_nis'] = $user['nis'];
$_SESSION['user_nama'] = $user['nama'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_kelas'] = $user['kelas'];
$_SESSION['is_logged_in'] = true;
$_SESSION['login_time'] = time();

// Hapus error messages jika ada
unset($_SESSION['errors']);
unset($_SESSION['error']);
unset($_SESSION['old_email']);

// Update last login time (optional)
$updateQuery = "UPDATE users SET last_login = NOW() WHERE id_user = " . $user['id_user'];
mysqli_query($koneksi, $updateQuery);

// Set success message
$_SESSION['success'] = 'Login berhasil! Selamat datang, ' . $user['nama'];

// Redirect berdasarkan role
switch ($user['role']) {
    case 'admin':
        header('Location: ../../../views/admin/dasboard.php');
        break;
    case 'petugas':
        header('Location: ../../../views/petugas/dashboard.php');
        break;
    case 'siswa':
    default:
        header('Location: ../../../views/user/dasboard/home.php');
        break;
}
exit;
?>