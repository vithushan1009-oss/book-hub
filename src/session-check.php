<?php
// Session Check - Include this at the top of every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_data = null;

if ($is_logged_in) {
    require_once __DIR__ . '/config.php';
    $conn = getDbConnection();
    
    // Fetch user details
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    if (!$user_data) {
        // User not found, destroy session
        session_destroy();
        $is_logged_in = false;
    } else {
        // Update user data in session
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
        $_SESSION['user_type'] = 'user';
    }
    
    $conn->close();
}
?>
