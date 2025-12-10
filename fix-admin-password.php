<?php
/**
 * Fix Admin Password - Reset to 'admin123'
 * Run this file once to reset the admin password
 */

require_once __DIR__ . '/src/config.php';

$conn = getDbConnection();

// New password
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin password
$sql = "UPDATE admins SET password = ? WHERE email = 'admin@bookhub.com'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "✅ Admin password has been reset successfully!\n";
    echo "Email: admin@bookhub.com\n";
    echo "Password: admin123\n";
    echo "\nPassword hash: " . $hashed_password . "\n";
    
    // Verify the password works
    $verify_sql = "SELECT password FROM admins WHERE email = 'admin@bookhub.com'";
    $verify_result = $conn->query($verify_sql);
    $admin = $verify_result->fetch_assoc();
    
    if (password_verify($new_password, $admin['password'])) {
        echo "\n✅ Password verification test: PASSED\n";
    } else {
        echo "\n❌ Password verification test: FAILED\n";
    }
} else {
    echo "❌ Error updating password: " . $stmt->error . "\n";
}

$conn->close();
?>
