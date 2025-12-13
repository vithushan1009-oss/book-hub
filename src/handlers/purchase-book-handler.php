<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session-check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/public/books.php');
    exit();
}

// Check if user is logged in
if (!$is_logged_in) {
    $_SESSION['error'] = 'Please login to purchase books';
    header('Location: /book-hub/public/login.html?error=' . urlencode('Please login to purchase books'));
    exit();
}

$conn = getDbConnection();

$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

// Validate inputs
$errors = [];

if ($book_id <= 0) {
    $errors[] = 'Invalid book selected';
}

// Check if book exists and is available for purchase
if (empty($errors)) {
    $book_query = "SELECT id, title, book_type, purchase_price FROM books WHERE id = ? AND book_type = 'online' AND is_active = 1";
    $book_stmt = $conn->prepare($book_query);
    $book_stmt->bind_param("i", $book_id);
    $book_stmt->execute();
    $book_result = $book_stmt->get_result();
    
    if ($book_result->num_rows == 0) {
        $errors[] = 'Book not found or not available for purchase';
    } else {
        $book = $book_result->fetch_assoc();
        
        // Check if user already purchased this book
        $purchase_check = "SELECT id FROM purchases 
                          WHERE user_id = ? AND book_id = ? 
                          AND status = 'completed'";
        $purchase_stmt = $conn->prepare($purchase_check);
        $purchase_stmt->bind_param("ii", $_SESSION['user_id'], $book_id);
        $purchase_stmt->execute();
        $purchase_result = $purchase_stmt->get_result();
        
        if ($purchase_result->num_rows > 0) {
            $errors[] = 'You have already purchased this book';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /book-hub/public/books.php?error=' . urlencode(implode(', ', $errors)));
    exit();
}

// Create purchase record (for now, auto-complete payment - in production, integrate with payment gateway)
$insert_sql = "INSERT INTO purchases (user_id, book_id, purchase_price, status, payment_method) 
               VALUES (?, ?, ?, 'completed', 'manual')";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("iid", $_SESSION['user_id'], $book_id, $book['purchase_price']);

if ($insert_stmt->execute()) {
    $_SESSION['success'] = 'Book purchased successfully! You can now download it from your account.';
    header('Location: /book-hub/src/views/user.php?success=' . urlencode($_SESSION['success']));
} else {
    $_SESSION['error'] = 'Failed to complete purchase. Please try again.';
    header('Location: /book-hub/public/books.php?error=' . urlencode('Failed to complete purchase'));
}

$conn->close();
exit();
?>


