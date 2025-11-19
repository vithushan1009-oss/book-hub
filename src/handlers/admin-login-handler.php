<?php
session_start();
require_once __DIR__ . '/../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file for debugging
$log_file = __DIR__ . '/../../admin-login-debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Login attempt started\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Not POST request, redirecting\n", FILE_APPEND);
    header('Location: /BOOKHUB/book-hub-central/admin-login');
    exit();
}

$conn = getDbConnection();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Email: $email\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Password length: " . strlen($password) . "\n", FILE_APPEND);

// Validate input
if (empty($email) || empty($password)) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Empty email or password\n", FILE_APPEND);
    $_SESSION['error'] = 'Email and password are required';
    header('Location: /BOOKHUB/book-hub-central/admin-login?error=' . urlencode('Email and password are required'));
    exit();
}

// Check admin credentials
$sql = "SELECT * FROM admins WHERE email = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - No admin found with email: $email\n", FILE_APPEND);
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: /BOOKHUB/book-hub-central/admin-login?error=' . urlencode('Invalid email or password'));
    exit();
}

$admin = $result->fetch_assoc();

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Admin found: ID={$admin['id']}, Email={$admin['email']}, Active={$admin['is_active']}\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Password hash starts with: " . substr($admin['password'], 0, 20) . "\n", FILE_APPEND);

if (!password_verify($password, $admin['password'])) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Password verification FAILED\n", FILE_APPEND);
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: /BOOKHUB/book-hub-central/admin-login?error=' . urlencode('Invalid email or password'));
    exit();
}

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Password verification SUCCESS\n", FILE_APPEND);

// Update last login
$update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $admin['id']);
$update_stmt->execute();

// Create session
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_name'] = $admin['full_name'];
$_SESSION['admin_role'] = $admin['role'];
$_SESSION['admin'] = $admin['email'];

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Session created: admin_id={$_SESSION['admin_id']}, admin_email={$_SESSION['admin_email']}\n", FILE_APPEND);

// Set remember me cookie
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('admin_remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
}

$conn->close();

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Redirecting to admin dashboard\n", FILE_APPEND);

// Redirect to admin dashboard
header('Location: /BOOKHUB/book-hub-central/admin');
exit();

