<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/email-functions.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../../register.html');
    exit();
}

$conn = getDbConnection();

$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$terms = isset($_POST['terms']);

$errors = [];

// Validation
if (strlen($first_name) < 2) {
    $errors[] = 'First name must be at least 2 characters';
}

if (strlen($last_name) < 2) {
    $errors[] = 'Last name must be at least 2 characters';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}

$min_password_length = env('PASSWORD_MIN_LENGTH', 8);
if (strlen($password) < $min_password_length) {
    $errors[] = "Password must be at least $min_password_length characters";
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

if (!$terms) {
    $errors[] = 'You must accept the terms and conditions';
}

// Check if email exists
if (empty($errors)) {
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = 'Email already registered';
    }
}

// If there are errors, redirect back with message
if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: ../../register.html?error=' . urlencode(implode(', ', $errors)));
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Generate verification token
$token = bin2hex(random_bytes(32));
$expiry_seconds = env('EMAIL_VERIFICATION_EXPIRY', 86400);
$expires_at = date('Y-m-d H:i:s', time() + $expiry_seconds);

// Insert user
$insert_sql = "INSERT INTO users (first_name, last_name, email, password, email_verification_token, email_verification_expires, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $token, $expires_at);

if ($insert_stmt->execute()) {
    // Send verification email
    $emailSent = sendVerificationEmail($email, $first_name, $token);
    
    // Log email attempt
    $user_id = $insert_stmt->insert_id;
    $log_sql = "INSERT INTO email_logs (user_id, email_type, recipient, status, sent_at) VALUES (?, 'verification', ?, ?, NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $email_status = $emailSent ? 'sent' : 'failed';
    $log_stmt->bind_param("iss", $user_id, $email, $email_status);
    $log_stmt->execute();
    
    $_SESSION['success'] = 'Registration successful! Please check your email to verify your account.';
    header('Location: ../../login.html?success=' . urlencode('Registration successful! Please check your email to verify your account.'));
} else {
    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: ../../register.html?error=' . urlencode('Registration failed. Please try again.'));
}

$conn->close();
exit();
?>
