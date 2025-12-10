<?php
/**
 * BOOK HUB - Front Controller
 * Routes all requests to appropriate handlers
 */

// Include configuration
require_once __DIR__ . '/src/config.php';

// Start session for all requests (check if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/book-hub';

// Remove base path and query string
$route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));

// Remove trailing slash
$route = rtrim($route, '/');

// Remove .html extension if present
$route = preg_replace('/\.html$/', '', $route);

// Default route
if ($route === '' || $route === '/') {
    $route = '/index';
}

// Handle direct file requests for static assets
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $route)) {
    $file = __DIR__ . '/public' . $route;
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $content_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        if (isset($content_types[$ext])) {
            header('Content-Type: ' . $content_types[$ext]);
        }
        
        readfile($file);
        exit;
    }
}

// Route to public pages (prefer PHP over HTML for dynamic content)
$public_pages = [
    '/index', '/login', '/register', '/admin-login', '/admin-register',
    '/books', '/about', '/contact', '/gallery'
];

if (in_array($route, $public_pages)) {
    // Try PHP file first (dynamic content)
    $php_file = __DIR__ . '/public' . $route . '.php';
    if (file_exists($php_file)) {
        require_once $php_file;
        exit;
    }
    
    // Fallback to HTML file (static content)
    $html_file = __DIR__ . '/public' . $route . '.html';
    if (file_exists($html_file)) {
        readfile($html_file);
        exit;
    }
}

// Route to protected views (requires authentication)
$protected_views = [
    '/user' => 'user.php',
    '/admin' => 'admin.php',
    '/admin-users' => 'manage-users.php',
    '/admin-books' => 'manage-books.php',
    '/admin-rentals' => 'manage-rentals.php',
    '/admin-analytics' => 'admin-analytics.php',
    '/admin-profile' => 'admin-profile.php',
    '/admin-settings' => 'admin-settings.php',
    '/profile' => 'profile.php',
    '/manage-users' => 'manage-users.php'
];

if (isset($protected_views[$route])) {
    $file = __DIR__ . '/src/views/' . $protected_views[$route];
    if (file_exists($file)) {
        require_once $file;
        exit;
    }
}

// Route to handlers
if (strpos($route, '/handler/') === 0) {
    $handler = str_replace('/handler/', '', $route) . '.php';
    $file = __DIR__ . '/src/handlers/' . $handler;
    if (file_exists($file)) {
        require_once $file;
        exit;
    }
}

// Serve static files from public directory
if (strpos($route, '/static/') === 0 || strpos($route, '/assets/') === 0) {
    $file = __DIR__ . '/public' . $route;
    if (file_exists($file)) {
        // Set appropriate content type
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $content_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml'
        ];
        
        if (isset($content_types[$ext])) {
            header('Content-Type: ' . $content_types[$ext]);
        }
        
        readfile($file);
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { font-size: 48px; color: #e74c3c; }
        p { font-size: 18px; color: #555; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Page Not Found</p>
    <a href="' . $base_path . '">Go Back to Homepage</a>
</body>
</html>';
