<?php
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Get book ID from query parameter
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($book_id <= 0) {
    // Return default placeholder image
    header('Content-Type: image/svg+xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>
    <svg xmlns="http://www.w3.org/2000/svg" width="300" height="400">
        <rect fill="#e0e0e0" width="300" height="400"/>
        <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-size="16" font-family="Arial">No Image</text>
    </svg>';
    exit;
}

// Fetch book image from database
$sql = "SELECT cover_image, cover_image_type FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if($row['cover_image'] && $row['cover_image_type']) {
        // Set appropriate headers
        header('Content-Type: ' . $row['cover_image_type']);
        header('Content-Length: ' . strlen($row['cover_image']));
        header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
        
        // Output the image
        echo $row['cover_image'];
        exit;
    }
}

// If no image found, return placeholder
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="400">
    <rect fill="#e0e0e0" width="300" height="400"/>
    <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-size="16" font-family="Arial">No Image</text>
</svg>';
?>


