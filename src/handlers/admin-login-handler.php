<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../../admin-login.html');
    exit();
}

$conn = getDbConnection();

$email = trim($_POST['email']);
$password = $_POST['password'];
$two_factor_code = trim($_POST['two_factor_code'] ?? '');
$remember = isset($_POST['remember']);

// Check login attempts
$max_attempts = env('MAX_LOGIN_ATTEMPTS', 5);
$lockout_time = env('LOCKOUT_TIME', 900);

$stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE email = ? AND user_type = 'admin' AND success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)");
$stmt->bind_param("si", $email, $lockout_time);
$stmt->execute();
$result = $stmt->get_result();
$attempts = $result->fetch_assoc()['attempts'];

if ($attempts >= $max_attempts) {
    $_SESSION['error'] = "Too many failed attempts. Please try again in 15 minutes.";
    header('Location: ../../admin-login.html?error=' . urlencode('Too many failed attempts. Please try again in 15 minutes.'));
    exit();
}

// Check admin credentials
$sql = "SELECT * FROM admins WHERE email = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Log failed attempt
    $log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'admin', 0)";
    $log_stmt = $conn->prepare($log_sql);
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("ss", $email, $ip);
    $log_stmt->execute();
    
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: ../../admin-login.html?error=' . urlencode('Invalid credentials'));
    exit();
}

$admin = $result->fetch_assoc();

if (!password_verify($password, $admin['password'])) {
    // Log failed attempt
    $log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'admin', 0)";
    $log_stmt = $conn->prepare($log_sql);
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("ss", $email, $ip);
    $log_stmt->execute();
    
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: ../../admin-login.html?error=' . urlencode('Invalid credentials'));
    exit();
}

// Update last login
$update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $admin['id']);
$update_stmt->execute();

// Log successful attempt
$log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'admin', 1)";
$log_stmt = $conn->prepare($log_sql);
$ip = $_SERVER['REMOTE_ADDR'];
$log_stmt->bind_param("ss", $email, $ip);
$log_stmt->execute();

// Create session
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_name'] = $admin['full_name'];
$_SESSION['admin_role'] = $admin['role'];
$_SESSION['admin'] = $admin['email'];

// Set remember me cookie
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('admin_remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
}

$conn->close();

// Redirect to admin dashboard
header('Location: ../../admin.php');
exit();
?>
