<?php
/**
 * Contact Form Handler
 * Handles contact form submissions and stores them in the database
 */

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database configuration
require_once __DIR__ . '/../config.php';

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input
$firstName = isset($_POST['firstName']) ? sanitizeInput($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? sanitizeInput($_POST['lastName']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

// Validation
$errors = [];

if (empty($firstName)) {
    $errors[] = 'First name is required';
}

if (empty($lastName)) {
    $errors[] = 'Last name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!isValidEmail($email)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Check message length
if (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters long';
}

if (strlen($message) > 5000) {
    $errors[] = 'Message is too long (maximum 5000 characters)';
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Get user's IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    // Get user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Prepare and execute the insert statement
    $stmt = $conn->prepare("
        INSERT INTO contact_messages (first_name, last_name, email, subject, message, ip_address, user_agent, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'unread', NOW())
    ");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("sssssss", $firstName, $lastName, $email, $subject, $message, $ipAddress, $userAgent);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save message: ' . $stmt->error);
    }
    
    $messageId = $conn->insert_id;
    
    $stmt->close();
    $conn->close();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.',
        'messageId' => $messageId
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while sending your message. Please try again later.',
        'debug' => $e->getMessage() // Remove this line in production
    ]);
}
?>
