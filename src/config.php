<?php
// Load .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die('.env file not found. Please copy .env.example to .env and configure it.');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Load environment variables
loadEnv(__DIR__ . '/.env');

// Helper function to get env variable
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Database connection using .env
function getDbConnection() {
    $host = env('DB_HOST', 'localhost');
    $dbname = env('DB_NAME', 'bookhub_db');
    $username = env('DB_USER', 'root');
    $password = env('DB_PASS', '');
    
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Set timezone
date_default_timezone_set(env('TIMEZONE', 'Asia/Colombo'));
?>
