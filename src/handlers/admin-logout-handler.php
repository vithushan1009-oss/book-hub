<?php
session_start();

// Unset all admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin']);

// Destroy the session
session_destroy();

// Clear remember me cookie if it exists
if (isset($_COOKIE['admin_remember_token'])) {
    setcookie('admin_remember_token', '', time() - 3600, '/');
}

// Redirect to admin login page
header('Location: /book-hub/public/admin-login.html');
exit();

