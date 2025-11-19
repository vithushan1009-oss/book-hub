<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header('Location: /BOOKHUB/book-hub-central/public/admin-login.html');
    exit();
}
?>
