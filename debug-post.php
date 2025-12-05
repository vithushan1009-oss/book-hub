<?php
// Debug register handler to see what's being received
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Registration Debug Handler</h1>";
echo "<p>Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p>Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . "</p>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h2>POST Data Received:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Form Fields:</h2>";
    echo "<p>First Name: " . ($_POST['first_name'] ?? 'NOT SET') . "</p>";
    echo "<p>Last Name: " . ($_POST['last_name'] ?? 'NOT SET') . "</p>";
    echo "<p>Email: " . ($_POST['email'] ?? 'NOT SET') . "</p>";
    echo "<p>Password Length: " . (isset($_POST['password']) ? strlen($_POST['password']) : 'NOT SET') . "</p>";
    echo "<p>Confirm Password Length: " . (isset($_POST['confirm_password']) ? strlen($_POST['confirm_password']) : 'NOT SET') . "</p>";
    echo "<p>Terms: " . (isset($_POST['terms']) ? 'CHECKED' : 'NOT CHECKED') . "</p>";
    
    if (count($_POST) == 0) {
        echo "<p><strong>ERROR: No POST data received!</strong></p>";
        echo "<p>This suggests the form is not submitting properly.</p>";
    }
} else {
    echo "<p>Not a POST request</p>";
}

echo "<p><a href='register.html'>Back to Registration</a></p>";
?>