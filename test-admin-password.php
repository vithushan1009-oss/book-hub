<?php
// Test admin password verification
require_once __DIR__ . '/src/config.php';

$conn = getDbConnection();

// Get admin from database
$sql = "SELECT * FROM admins WHERE email = 'admin@gmail.com'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    echo "Admin found:<br>";
    echo "ID: " . $admin['id'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Full Name: " . $admin['full_name'] . "<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Password Hash: " . $admin['password'] . "<br>";
    echo "Is Active: " . $admin['is_active'] . "<br><br>";
    
    // Test password verification with common passwords
    $test_passwords = ['admin123', 'Admin123', 'password', '12345678', 'Jeya'];
    
    foreach ($test_passwords as $test_pass) {
        $verify = password_verify($test_pass, $admin['password']);
        echo "Password '$test_pass': " . ($verify ? "✓ MATCH" : "✗ NO MATCH") . "<br>";
    }
} else {
    echo "No admin found with email admin@gmail.com";
}

$conn->close();
?>
