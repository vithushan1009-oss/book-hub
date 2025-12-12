<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/public/register.php');
    exit();
}

try {
    $conn = getDbConnection();
} catch (Exception $e) {
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    header('Location: /book-hub/public/register.php?error=' . urlencode('Database connection failed'));
    exit();
}

// Get and sanitize form data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
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
    try {
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if (!$check_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $errors[] = 'Email already registered';
        }
    } catch (Exception $e) {
        $errors[] = 'Database error occurred during validation';
    }
}

// If there are errors, redirect back with message
if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /book-hub/public/register.php?error=' . urlencode(implode(', ', $errors)));
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Generate verification token
$token = bin2hex(random_bytes(32));
$expiry_seconds = env('EMAIL_VERIFICATION_EXPIRY', 86400);
$expires_at = date('Y-m-d H:i:s', time() + $expiry_seconds);

// Insert user
try {
    $insert_sql = "INSERT INTO users (first_name, last_name, email, password, email_verification_token, email_verification_expires, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
    $insert_stmt = $conn->prepare($insert_sql);

    if (!$insert_stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    $insert_stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $token, $expires_at);

    if ($insert_stmt->execute()) {
        $user_id = $insert_stmt->insert_id;
        
        // Check if email is enabled
        $email_enabled = env('ENABLE_EMAIL', 'false') === 'true';
        
        if ($email_enabled) {
            // Try to send verification email
            try {
                require_once __DIR__ . '/email-functions.php';
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
                header('Location: /book-hub/public/login.php?success=' . urlencode($_SESSION['success']));
            } catch (Exception $e) {
                // Fall back to auto-verification if email fails
                $verify_sql = "UPDATE users SET email_verified = 1 WHERE id = ?";
                $verify_stmt = $conn->prepare($verify_sql);
                $verify_stmt->bind_param("i", $user_id);
                $verify_stmt->execute();
                
                $_SESSION['success'] = 'Registration successful! Your account is ready to use.';
                header('Location: /book-hub/public/login.php?success=' . urlencode($_SESSION['success']));
            }
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
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $log_stmt->bind_param("ss", $email, $ip);
            $log_stmt->execute();
            
            // Update last login
            $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            
            $_SESSION['success'] = 'Welcome to BOOK HUB! Your account has been created successfully.';
            header('Location: /book-hub/src/views/user.php');
        }
    } else {
        throw new Exception("Insert failed: " . $insert_stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: /book-hub/public/register.php?error=' . urlencode('Registration failed. Please try again.'));
}
?>

