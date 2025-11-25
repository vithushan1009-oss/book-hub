<?php
session_start();

// Clear session
session_destroy();

// Clear cookies
setcookie('remember_token', '', time() - 3600, '/');
setcookie('admin_remember_token', '', time() - 3600, '/');

// Redirect to home page
header('Location: /BOOKHUB/book-hub-central/public/index.php');
exit();
?>
