<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/public/login.php');
    exit();
}

$conn = getDbConnection();

$email = trim($_POST['email']);
$password = $_POST['password'];
$remember = isset($_POST['remember']);

// Check login attempts
$max_attempts = env('MAX_LOGIN_ATTEMPTS', 5);
$lockout_time = env('LOCKOUT_TIME', 900); // 15 minutes in seconds

$stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE email = ? AND user_type = 'user' AND success = 0 AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
$stmt->bind_param("si", $email, $lockout_time);
$stmt->execute();
$result = $stmt->get_result();
$attempts = $result->fetch_assoc()['attempts'];

if ($attempts >= $max_attempts) {
    $_SESSION['error'] = "Too many failed attempts. Please try again in 15 minutes.";
    header('Location: /book-hub/public/login.php?error=' . urlencode('Too many failed attempts. Please try again in 15 minutes.'));
    exit();
}

// Check user credentials
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Log failed attempt
    $log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'user', 0)";
    $log_stmt = $conn->prepare($log_sql);
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("ss", $email, $ip);
    $log_stmt->execute();
    
    $_SESSION['error'] = 'Invalid email or password';
    header('Location: /book-hub/public/login.php?error=' . urlencode('Invalid email or password'));
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    // Log failed attempt
    $log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'user', 0)";
    $log_stmt = $conn->prepare($log_sql);
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_stmt->bind_param("ss", $email, $ip);
    $log_stmt->execute();
    
    $_SESSION['error'] = 'Invalid email or password';
    header('Location: /book-hub/public/login.php?error=' . urlencode('Invalid email or password'));
    exit();
}

// Check if email is verified
if (!$user['email_verified']) {
    $_SESSION['error'] = 'Please verify your email before logging in. Check your inbox for the verification link.';
    header('Location: /book-hub/public/login.php?error=' . urlencode('Please verify your email before logging in.'));
    exit();
}

// Update last login
$update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $user['id']);
$update_stmt->execute();

// Log successful attempt
$log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'user', 1)";
$log_stmt = $conn->prepare($log_sql);
$ip = $_SERVER['REMOTE_ADDR'];
$log_stmt->bind_param("ss", $email, $ip);
$log_stmt->execute();

// Create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['user_type'] = 'user';

// Set remember me cookie
if ($remember) {
    $token = bin2hex(random_bytes(32));
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
    
    // Store token in database
    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
    $token_sql = "INSERT INTO user_sessions (user_id, session_token, user_type, expires_at) VALUES (?, ?, 'user', ?)";
    $token_stmt = $conn->prepare($token_sql);
    $token_stmt->bind_param("iss", $user['id'], $token, $expires_at);
    $token_stmt->execute();
}

$conn->close();

// Redirect to user dashboard
header('Location: /book-hub/src/views/user.php');
exit();
?>

