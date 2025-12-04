<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/public/admin-register.html');
    exit();
}

$conn = getDbConnection();

// Get form data
$full_name = trim($_POST['full_name']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$role = trim($_POST['role']);
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validate inputs
if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('All fields are required'));
    exit();
}

// Validate username length
if (strlen($username) < 3) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Username must be at least 3 characters long'));
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Invalid email format'));
    exit();
}

// Validate password match
if ($password !== $confirm_password) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Passwords do not match'));
    exit();
}

// Validate password length
if (strlen($password) < 8) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Password must be at least 8 characters long'));
    exit();
}

// Validate role
$valid_roles = ['super_admin', 'admin', 'moderator'];
if (!in_array($role, $valid_roles)) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Invalid role selected'));
    exit();
}

// Check if email already exists
$check_sql = "SELECT id FROM admins WHERE email = ? OR username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $email, $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Email or username already registered'));
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new admin
$insert_sql = "INSERT INTO admins (full_name, username, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("sssssi", $full_name, $username, $email, $hashed_password, $role, $is_active);

if ($insert_stmt->execute()) {
    $conn->close();
    header('Location: /book-hub/public/admin-login.html?success=' . urlencode('Admin account created successfully! Please login.'));
    exit();
} else {
    $conn->close();
    header('Location: /book-hub/public/admin-register.html?error=' . urlencode('Registration failed. Please try again.'));
    exit();
}
?>

