<?php
// Create test admin account
require_once __DIR__ . '/src/config.php';

$conn = getDbConnection();

// Admin details
$full_name = "Admin User";
$username = "admin";
$email = "admin@bookhub.com";
$password = "admin123"; // Simple password for testing
$role = "super_admin";
$is_active = 1;

echo "<h2>Setting up admin account...</h2>";

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Generated password hash: " . substr($hashed_password, 0, 30) . "...<br><br>";

// Check if admin already exists
$check_sql = "SELECT id, email, password FROM admins WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $existing = $result->fetch_assoc();
    echo "Admin with email $email already exists (ID: {$existing['id']})<br>";
    echo "Updating password...<br>";
    
    // Update existing admin password
    $update_sql = "UPDATE admins SET password = ?, full_name = ?, username = ?, role = ?, is_active = 1 WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $hashed_password, $full_name, $username, $role, $email);
    
    if ($update_stmt->execute()) {
        echo "✓ Admin account updated successfully!<br>";
    } else {
        echo "✗ Failed to update: " . $conn->error . "<br>";
    }
} else {
    echo "Creating new admin account...<br>";
    
    // Create new admin
    $insert_sql = "INSERT INTO admins (full_name, username, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssssi", $full_name, $username, $email, $hashed_password, $role, $is_active);
    
    if ($insert_stmt->execute()) {
        echo "✓ Admin account created successfully!<br>";
    } else {
        echo "✗ Failed to create admin: " . $conn->error . "<br>";
    }
}

// Verify the account
echo "<br><h3>Verification:</h3>";
$verify_sql = "SELECT id, username, email, full_name, role, is_active, password FROM admins WHERE email = ?";
$verify_stmt = $conn->prepare($verify_sql);
$verify_stmt->bind_param("s", $email);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows > 0) {
    $admin = $verify_result->fetch_assoc();
    echo "ID: {$admin['id']}<br>";
    echo "Username: {$admin['username']}<br>";
    echo "Email: {$admin['email']}<br>";
    echo "Full Name: {$admin['full_name']}<br>";
    echo "Role: {$admin['role']}<br>";
    echo "Is Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
    
    // Test password verification
    echo "<br><h3>Password Test:</h3>";
    if (password_verify($password, $admin['password'])) {
        echo "✓ Password verification: <strong style='color: green;'>SUCCESS</strong><br>";
    } else {
        echo "✗ Password verification: <strong style='color: red;'>FAILED</strong><br>";
    }
}

echo "<br><hr><br>";
echo "<h3>Login Credentials:</h3>";
echo "Email: <strong style='color: blue;'>$email</strong><br>";
echo "Password: <strong style='color: blue;'>$password</strong><br>";
echo "<br>";
echo "<a href='/BOOKHUB/book-hub-central/admin-login' style='display: inline-block; padding: 10px 20px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a>";

$conn->close();
?>
