<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Create a custom log file for debugging
$debug_log = __DIR__ . '/../../registration_debug.log';
file_put_contents($debug_log, "\n\n=== NEW REGISTRATION ATTEMPT ===" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents($debug_log, "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/email-functions.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    file_put_contents($debug_log, "Not a POST request, redirecting\n", FILE_APPEND);
    header('Location: /book-hub/public/register.html');
    exit();
}

file_put_contents($debug_log, "POST request received\n", FILE_APPEND);

$conn = getDbConnection();
file_put_contents($debug_log, "Database connected successfully\n", FILE_APPEND);

// Debug: Log incoming data
error_log("Registration attempt - Email: " . ($_POST['email'] ?? 'not set'));

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']);

// Debug: Log parsed data
error_log("Parsed data - Name: $first_name $last_name, Email: $email, Terms: " . ($terms ? 'yes' : 'no'));

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
    file_put_contents($debug_log, "Validation errors: " . implode(', ', $errors) . "\n", FILE_APPEND);
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /book-hub/public/register.html?error=' . urlencode(implode(', ', $errors)));
    exit();
}

file_put_contents($debug_log, "Validation passed, proceeding with insert\n", FILE_APPEND);

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Generate verification token
$token = bin2hex(random_bytes(32));
$expiry_seconds = env('EMAIL_VERIFICATION_EXPIRY', 86400);
$expires_at = date('Y-m-d H:i:s', time() + $expiry_seconds);

// Insert user
$insert_sql = "INSERT INTO users (first_name, last_name, email, password, email_verification_token, email_verification_expires, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
$insert_stmt = $conn->prepare($insert_sql);

if (!$insert_stmt) {
    error_log("Prepare statement failed: " . $conn->error);
    $_SESSION['error'] = 'Database error occurred';
    header('Location: /book-hub/public/register.html?error=' . urlencode('Database error. Please try again.'));
    exit();
}

$insert_stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $token, $expires_at);

error_log("Attempting to insert user: $email");
file_put_contents($debug_log, "Executing SQL INSERT for email: $email\n", FILE_APPEND);

if ($insert_stmt->execute()) {
    $user_id = $insert_stmt->insert_id;
    error_log("User inserted successfully with ID: $user_id");
    file_put_contents($debug_log, "SUCCESS! User inserted with ID: $user_id\n", FILE_APPEND);
    
    // Check if email is enabled
    $email_enabled = env('ENABLE_EMAIL', 'false') === 'true';
    
    if ($email_enabled) {
        // Send verification email
        $emailSent = sendVerificationEmail($email, $first_name, $token);
        
        // Log email attempt
        $log_sql = "INSERT INTO email_logs (recipient_email, subject, email_type, success, error_message) VALUES (?, ?, 'verification', ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $subject = 'Verify Your Email - BOOK HUB';
        $success = $emailSent ? 1 : 0;
        $error_msg = $emailSent ? null : 'Email sending failed';
        $log_stmt->bind_param("ssis", $email, $subject, $success, $error_msg);
        $log_stmt->execute();
        
        $_SESSION['success'] = 'Registration successful! Please check your email to verify your account.';
        header('Location: /book-hub/public/login.html?success=' . urlencode($_SESSION['success']));
    } else {
        // Auto-verify for development and auto-login
        $verify_sql = "UPDATE users SET email_verified = 1 WHERE id = ?";
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param("i", $user_id);
        $verify_stmt->execute();
        
        // Auto-login: Create session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $_SESSION['user_type'] = 'user';
        
        // Log successful auto-login
        $log_sql = "INSERT INTO login_attempts (email, ip_address, user_type, success) VALUES (?, ?, 'user', 1)";
        $log_stmt = $conn->prepare($log_sql);
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->bind_param("ss", $email, $ip);
        $log_stmt->execute();
        
        // Update last login
        $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
        
        $_SESSION['success'] = 'Welcome to BOOK HUB! Your account has been created successfully.';
        header('Location: /book-hub/src/views/user.php?welcome=1');
    }
} else {
    $error_message = 'Registration failed: ' . $insert_stmt->error;
    error_log("Insert failed: " . $insert_stmt->error);
    file_put_contents($debug_log, "INSERT FAILED: " . $insert_stmt->error . "\n", FILE_APPEND);
    $_SESSION['error'] = $error_message;
    header('Location: /book-hub/public/register.html?error=' . urlencode('Registration failed. Please try again.'));
}

file_put_contents($debug_log, "=== END ATTEMPT ===\n", FILE_APPEND);

$conn->close();
exit();
?>

