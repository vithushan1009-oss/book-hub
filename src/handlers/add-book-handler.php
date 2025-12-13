<?php
session_start();
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /book-hub/src/views/manage-books.php');
    exit;
}

$conn = getDbConnection();

// Get form data
$title = trim($_POST['title']);
$author = trim($_POST['author']);
$isbn = trim($_POST['isbn'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$description = trim($_POST['description'] ?? '');
$book_type = $_POST['book_type'];
$publisher = trim($_POST['publisher'] ?? '');
$publication_date = !empty($_POST['publication_date']) ? $_POST['publication_date'] : null;

// Physical book fields
$total_quantity = $book_type === 'physical' ? (int)$_POST['total_quantity'] : 1;
$rental_price_per_day = $book_type === 'physical' && !empty($_POST['rental_price_per_day']) ? (float)$_POST['rental_price_per_day'] : null;

// Online book fields
$purchase_price = $book_type === 'online' && !empty($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : null;

// Validate required fields
$errors = [];

if(empty($title)) {
    $errors[] = 'Title is required';
}

if(empty($author)) {
    $errors[] = 'Author is required';
}

if($book_type === 'physical') {
    if(empty($rental_price_per_day) || $rental_price_per_day < 0) {
        $errors[] = 'Valid rental price per day is required for physical books';
    }
    if($total_quantity < 1) {
        $errors[] = 'Total quantity must be at least 1';
    }
} elseif($book_type === 'online') {
    if(empty($purchase_price) || $purchase_price < 0) {
        $errors[] = 'Valid purchase price is required for online books';
    }
}

if(!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Handle cover image upload
$cover_image = null;
$cover_image_type = null;
if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['cover_image'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if(in_array($file['type'], $allowed_types)) {
        // Check file size (max 5MB)
        if($file['size'] <= 5 * 1024 * 1024) {
            $cover_image = file_get_contents($file['tmp_name']);
            $cover_image_type = $file['type'];
        } else {
            $_SESSION['error'] = 'Cover image size must be less than 5MB';
            header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode('Cover image size must be less than 5MB'));
            exit;
        }
    } else {
        $_SESSION['error'] = 'Invalid image format. Allowed: JPG, PNG, GIF, WebP';
        header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode('Invalid image format'));
        exit;
    }
}

// Handle PDF file upload for online books
$pdf_file = null;
$pdf_file_name = null;
$pdf_file_size = null;
$pdf_file_type = null;
if($book_type === 'online' && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['pdf_file'];
    
    if($file['type'] === 'application/pdf') {
        // Check file size (max 50MB)
        if($file['size'] <= 50 * 1024 * 1024) {
            $pdf_file = file_get_contents($file['tmp_name']);
            $pdf_file_name = $file['name'];
            $pdf_file_size = $file['size'];
            $pdf_file_type = $file['type'];
        } else {
            $_SESSION['error'] = 'PDF file size must be less than 50MB';
            header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode('PDF file size must be less than 50MB'));
            exit;
        }
    } else {
        $_SESSION['error'] = 'Invalid file format. Only PDF files are allowed';
        header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode('Invalid file format'));
        exit;
    }
}

// Get admin ID
$admin_id = $_SESSION['admin_id'] ?? null;

// Insert book into database
if($cover_image && $pdf_file) {
    $sql = "INSERT INTO books (title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, cover_image, cover_image_type, pdf_file, pdf_file_name, pdf_file_size, pdf_file_type, publisher, publication_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiddbsssssssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $cover_image, $cover_image_type, $pdf_file, $pdf_file_name, $pdf_file_size, $pdf_file_type, $publisher, $publication_date, $admin_id);
} elseif($cover_image) {
    $sql = "INSERT INTO books (title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, cover_image, cover_image_type, publisher, publication_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiddbsssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $cover_image, $cover_image_type, $publisher, $publication_date, $admin_id);
} elseif($pdf_file) {
    $sql = "INSERT INTO books (title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, pdf_file, pdf_file_name, pdf_file_size, pdf_file_type, publisher, publication_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiddbssssssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $pdf_file, $pdf_file_name, $pdf_file_size, $pdf_file_type, $publisher, $publication_date, $admin_id);
} else {
    $sql = "INSERT INTO books (title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, publisher, publication_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiddsssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $publisher, $publication_date, $admin_id);
}

if($stmt->execute()) {
    $conn->close();
    $_SESSION['success'] = 'Book added successfully!';
    header('Location: /book-hub/src/views/manage-books.php?success=' . urlencode('Book added successfully!'));
    exit;
} else {
    $conn->close();
    $_SESSION['error'] = 'Failed to add book: ' . $conn->error;
    header('Location: /book-hub/src/views/manage-books.php?error=' . urlencode('Failed to add book'));
    exit;
}
?>


