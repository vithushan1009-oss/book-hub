<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/public/admin-login.php');
    exit();
}

$conn = getDbConnection();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate input
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Email and password are required';
    header('Location: /book-hub/public/admin-login.php?error=' . urlencode('Email and password are required'));
    exit();
}

// Check admin credentials
$sql = "SELECT * FROM admins WHERE email = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: /book-hub/public/admin-login.php?error=' . urlencode('Invalid email or password'));
    exit();
}

$admin = $result->fetch_assoc();

if (!password_verify($password, $admin['password'])) {
    $_SESSION['error'] = 'Invalid credentials';
    header('Location: /book-hub/public/admin-login.php?error=' . urlencode('Invalid email or password'));
    exit();
}

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

// Set remember me cookie
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('admin_remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
}

$conn->close();

// Redirect to admin dashboard
header('Location: /book-hub/src/views/admin.php');
exit();
?>