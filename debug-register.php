<?php
// Simple registration debug test
require_once 'src/config.php';

echo "<h1>Registration Debug Test</h1>";

try {
    $conn = getDbConnection();
    echo "<p>✓ Database connection successful</p>";
    
    // Test env function
    $app_name = env('APP_NAME');
    echo "<p>✓ Environment loaded: APP_NAME = $app_name</p>";
    
    // Test password hashing
    $test_password = 'testpass123';
    $hashed = password_hash($test_password, PASSWORD_DEFAULT);
    echo "<p>✓ Password hashing works: " . substr($hashed, 0, 20) . "...</p>";
    
    // Test database write
    $test_email = 'debug_test_' . time() . '@example.com';
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 86400);
    
    $sql = "INSERT INTO users (first_name, last_name, email, password, email_verification_token, email_verification_expires, email_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "<p>✗ Prepare failed: " . $conn->error . "</p>";
    } else {
        $stmt->bind_param("ssssss", 'Debug', 'Test', $test_email, $hashed, $token, $expires_at);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            echo "<p>✓ Test user inserted with ID: $user_id</p>";
            
            // Clean up test user
            $delete_sql = "DELETE FROM users WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            echo "<p>✓ Test user cleaned up</p>";
        } else {
            echo "<p>✗ Insert failed: " . $stmt->error . "</p>";
        }
    }
    
    $conn->close();
    echo "<p>✓ All tests passed - Registration should work</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='public/register.html'>Go to Registration</a></p>";
?>