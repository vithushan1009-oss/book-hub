<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session-check.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /BOOKHUB/book-hub-central/public/books.php');
    exit();
}

// Check if user is logged in
if (!$is_logged_in) {
    $_SESSION['error'] = 'Please login to rent books';
    header('Location: /BOOKHUB/book-hub-central/public/login.html?error=' . urlencode('Please login to rent books'));
    exit();
}

$conn = getDbConnection();

$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
$end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';

// Validate inputs
$errors = [];

if ($book_id <= 0) {
    $errors[] = 'Invalid book selected';
}

$start_date_obj = null;
$end_date_obj = null;

if (empty($start_date)) {
    $errors[] = 'Start date is required';
} else {
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
    if (!$start_date_obj || $start_date_obj->format('Y-m-d') !== $start_date) {
        $errors[] = 'Invalid start date format';
    } else {
        // Ensure start date is not in the past
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if ($start_date_obj < $today) {
            $errors[] = 'Start date cannot be in the past';
        }
    }
}

if (empty($end_date)) {
    $errors[] = 'End date is required';
} else {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
    if (!$end_date_obj || $end_date_obj->format('Y-m-d') !== $end_date) {
        $errors[] = 'Invalid end date format';
    }
}

if ($start_date_obj && $end_date_obj) {
    if ($end_date_obj < $start_date_obj) {
        $errors[] = 'End date must be after start date';
    }
    // Ensure rental period is reasonable (e.g., max 30 days)
    $diff = $start_date_obj->diff($end_date_obj);
    if ($diff->days > 30) {
        $errors[] = 'Rental period cannot exceed 30 days';
    }
}

// Get user's phone number from database if not provided
if (empty($phone_number) && $is_logged_in && $user_data) {
    $phone_number = $user_data['phone_number'] ?? '';
}

if (empty($phone_number)) {
    $errors[] = 'Phone number is required';
} elseif (!preg_match('/^[0-9+\-\s()]+$/', $phone_number)) {
    $errors[] = 'Invalid phone number format';
}

// Check if book exists and is available for rent
if (empty($errors)) {
    $book_query = "SELECT id, title, book_type, total_quantity, rental_price_per_day FROM books WHERE id = ? AND book_type = 'physical' AND is_active = 1";
    $book_stmt = $conn->prepare($book_query);
    $book_stmt->bind_param("i", $book_id);
    $book_stmt->execute();
    $book_result = $book_stmt->get_result();
    
    if ($book_result->num_rows == 0) {
        $errors[] = 'Book not found or not available for rent';
    } else {
        $book = $book_result->fetch_assoc();
        
        // Check if book is available (check active rentals)
        $rental_check = "SELECT COUNT(*) as active_rentals FROM rentals 
                        WHERE book_id = ? AND status IN ('pending', 'approved') 
                        AND (start_date <= ? AND end_date >= ?)";
        $rental_stmt = $conn->prepare($rental_check);
        $rental_stmt->bind_param("iss", $book_id, $end_date, $start_date);
        $rental_stmt->execute();
        $rental_result = $rental_stmt->get_result();
        $rental_data = $rental_result->fetch_assoc();
        
        if ($rental_data['active_rentals'] >= $book['total_quantity']) {
            $errors[] = 'This book is not available for the selected dates';
        }
        
        // Check if user already has an active rental for this book
        $user_rental_check = "SELECT id FROM rentals 
                             WHERE user_id = ? AND book_id = ? 
                             AND status IN ('pending', 'approved') 
                             AND (start_date <= ? AND end_date >= ?)";
        $user_rental_stmt = $conn->prepare($user_rental_check);
        $user_rental_stmt->bind_param("iiss", $_SESSION['user_id'], $book_id, $end_date, $start_date);
        $user_rental_stmt->execute();
        $user_rental_result = $user_rental_stmt->get_result();
        
        if ($user_rental_result->num_rows > 0) {
            $errors[] = 'You already have an active rental for this book';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /BOOKHUB/book-hub-central/public/books.php?error=' . urlencode(implode(', ', $errors)));
    exit();
}

// Create rental request
$insert_sql = "INSERT INTO rentals (user_id, book_id, start_date, end_date, phone_number, status) 
               VALUES (?, ?, ?, ?, ?, 'pending')";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("iisss", $_SESSION['user_id'], $book_id, $start_date, $end_date, $phone_number);

if ($insert_stmt->execute()) {
    $_SESSION['success'] = 'Rental request submitted successfully! We will review your request and notify you soon.';
    header('Location: /BOOKHUB/book-hub-central/public/books.php?success=' . urlencode($_SESSION['success']));
} else {
    $_SESSION['error'] = 'Failed to submit rental request. Please try again.';
    header('Location: /BOOKHUB/book-hub-central/public/books.php?error=' . urlencode('Failed to submit rental request'));
}

$conn->close();
exit();
?>

