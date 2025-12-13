<?php
session_start();
require_once __DIR__ . '/../config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: /book-hub/public/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($book_id <= 0) {
    $_SESSION['error'] = 'Invalid book ID';
    header('Location: /book-hub/src/views/user.php');
    exit();
}

$conn = getDbConnection();

// Check if user has purchased this book
$purchase_check = "SELECT p.*, b.title, b.pdf_file, b.pdf_file_name 
                  FROM purchases p 
                  JOIN books b ON p.book_id = b.id 
                  WHERE p.user_id = ? AND p.book_id = ? AND p.status = 'completed'";
$stmt = $conn->prepare($purchase_check);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    $_SESSION['error'] = 'You have not purchased this book';
    header('Location: /book-hub/src/views/user.php');
    exit();
}

$purchase = $result->fetch_assoc();

// Check download limit
if($purchase['download_count'] >= $purchase['max_downloads']) {
    $_SESSION['error'] = 'Download limit exceeded for this book';
    header('Location: /book-hub/src/views/user.php');
    exit();
}

// For now, just increment download count and show message
// In production, this would serve the actual PDF file
$update_sql = "UPDATE purchases SET download_count = download_count + 1 WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $purchase['id']);
$update_stmt->execute();

$_SESSION['success'] = 'Download started for "' . $purchase['title'] . '"';
header('Location: /book-hub/src/views/user.php');
exit();
?>