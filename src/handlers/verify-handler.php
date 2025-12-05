<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_GET['token'])) {
    $_SESSION['error'] = 'Invalid verification link';
    header('Location: /book-hub/public/login.html?error=' . urlencode('Invalid verification link'));
    exit();
}

$token = $_GET['token'];
$conn = getDbConnection();

// Find user with this token
$sql = "SELECT * FROM users WHERE email_verification_token = ? AND email_verification_expires > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = 'Invalid or expired verification link';
    header('Location: /book-hub/public/login.html?error=' . urlencode('Invalid or expired verification link'));
    exit();
}

$user = $result->fetch_assoc();

// Update user as verified
$update_sql = "UPDATE users SET email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $user['id']);

if ($update_stmt->execute()) {
    $_SESSION['success'] = 'Email verified successfully! You can now log in.';
    header('Location: /book-hub/public/login.html?success=' . urlencode('Email verified successfully! You can now log in.'));
} else {
    $_SESSION['error'] = 'Verification failed. Please try again.';
    header('Location: /book-hub/public/login.html?error=' . urlencode('Verification failed. Please try again.'));
}

$conn->close();
exit();
?>

