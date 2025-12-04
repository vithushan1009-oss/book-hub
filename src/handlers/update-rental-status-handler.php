<?php
session_start();
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/admin#rentals');
    exit;
}

$conn = getDbConnection();

$rental_id = isset($_POST['rental_id']) ? (int)$_POST['rental_id'] : 0;
$new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

// Validate inputs
$errors = [];

if ($rental_id <= 0) {
    $errors[] = 'Invalid rental ID';
}

$allowed_statuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
if (!in_array($new_status, $allowed_statuses)) {
    $errors[] = 'Invalid status';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /book-hub/admin#rentals?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Check if rental exists
$check_query = "SELECT id, status, user_id, book_id FROM rentals WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $rental_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = 'Rental not found';
    header('Location: /book-hub/admin#rentals?error=' . urlencode('Rental not found'));
    exit;
}

$rental = $check_result->fetch_assoc();
$old_status = $rental['status'];

// Update rental status
$update_query = "UPDATE rentals SET status = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $new_status, $rental_id);

if ($update_stmt->execute()) {
    // Log the status change (optional - you can add an activity log table)
    $admin_id = $_SESSION['admin_id'] ?? null;
    
    $_SESSION['success'] = "Rental status updated from " . ucfirst($old_status) . " to " . ucfirst($new_status);
    header('Location: /book-hub/admin#rentals?success=' . urlencode($_SESSION['success']));
} else {
    $_SESSION['error'] = 'Failed to update rental status';
    header('Location: /book-hub/admin#rentals?error=' . urlencode('Failed to update rental status'));
}

$conn->close();
exit;
?>


