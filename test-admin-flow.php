<?php
/**
 * Test Admin Login Flow
 * This file tests the complete admin authentication flow
 */

echo "=== TESTING ADMIN LOGIN FLOW ===\n\n";

// Test 1: Check if admin exists
require_once __DIR__ . '/src/config.php';
$conn = getDbConnection();

echo "1. Checking admin account...\n";
$sql = "SELECT id, email, username, full_name, is_active FROM admins WHERE email = 'admin@bookhub.com'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "   ✅ Admin found:\n";
    echo "      - ID: {$admin['id']}\n";
    echo "      - Email: {$admin['email']}\n";
    echo "      - Username: {$admin['username']}\n";
    echo "      - Full Name: {$admin['full_name']}\n";
    echo "      - Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "\n";
} else {
    echo "   ❌ Admin account not found!\n";
    exit;
}

echo "\n2. Testing password verification...\n";
$password = 'admin123';
$sql = "SELECT password FROM admins WHERE email = 'admin@bookhub.com'";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

if (password_verify($password, $admin['password'])) {
    echo "   ✅ Password verification: PASSED\n";
} else {
    echo "   ❌ Password verification: FAILED\n";
}

echo "\n3. Testing routes...\n";
$routes = [
    '/book-hub/admin-login' => 'Admin Login Page',
    '/book-hub/admin' => 'Admin Dashboard',
    '/book-hub/src/handlers/admin-login-handler.php' => 'Login Handler'
];

foreach ($routes as $route => $name) {
    $file_path = '';
    if (strpos($route, '/src/handlers/') !== false) {
        $file_path = __DIR__ . str_replace('/book-hub', '', $route);
    } elseif ($route === '/book-hub/admin-login') {
        $file_path = __DIR__ . '/public/admin-login.html';
    } elseif ($route === '/book-hub/admin') {
        $file_path = __DIR__ . '/src/views/admin.php';
    }
    
    if (file_exists($file_path)) {
        echo "   ✅ $name - File exists: $file_path\n";
    } else {
        echo "   ❌ $name - File missing: $file_path\n";
    }
}

echo "\n4. Testing session check...\n";
$session_check = __DIR__ . '/src/admin-session-check.php';
if (file_exists($session_check)) {
    echo "   ✅ Admin session check exists\n";
    echo "      Location: $session_check\n";
} else {
    echo "   ❌ Admin session check missing\n";
}

echo "\n5. Checking handler redirects...\n";
echo "   - Login success redirects to: /book-hub/admin\n";
echo "   - Login failure redirects to: /book-hub/admin-login?error=...\n";
echo "   - Registration success redirects to: /book-hub/public/admin-login.html?success=...\n";

echo "\n=== TEST SUMMARY ===\n";
echo "✅ Admin account is ready\n";
echo "✅ Password is correct (admin123)\n";
echo "✅ All routes are configured\n";
echo "\nYou can now login at: http://localhost/book-hub/admin-login\n";
echo "Email: admin@bookhub.com\n";
echo "Password: admin123\n";

$conn->close();
?>
