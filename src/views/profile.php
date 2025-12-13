<?php
session_start();
require_once __DIR__ . '/../config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDbConnection();
$success_message = "";
$error_message = "";

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $_SESSION['user_email'] = $email;
        } else {
            $error_message = "Error updating profile. Please try again.";
        }
    } elseif (isset($_POST['delete'])) {
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        session_destroy();
        header('Location: ../../login.html?success=' . urlencode('Account deleted successfully'));
        exit();
    }
}

$user_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$user_initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));

// Fetch user's purchased books
$purchased_books_query = "SELECT b.id, b.title, b.author, b.isbn, b.genre, b.description, b.book_type, p.purchase_price, p.created_at as purchase_date, p.download_count, p.max_downloads 
                         FROM purchases p 
                         JOIN books b ON p.book_id = b.id 
                         WHERE p.user_id = ? AND p.status = 'completed' 
                         ORDER BY p.created_at DESC";
$purchased_books_stmt = $conn->prepare($purchased_books_query);
$purchased_books_stmt->bind_param("i", $user_id);
$purchased_books_stmt->execute();
$purchased_books_result = $purchased_books_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — BOOK HUB</title>
    <link rel="stylesheet" href="/book-hub/public/static/css/variables.css">
    <link rel="stylesheet" href="/book-hub/public/static/css/base.css">
    <link rel="stylesheet" href="/book-hub/public/static/css/components.css">
    <link rel="stylesheet" href="/book-hub/public/static/css/navigation.css">
    <link rel="stylesheet" href="/book-hub/public/static/css/footer.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            min-height: 100vh;
        }

        /* User Navigation Dropdown Styles */
        .user-nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .user-profile-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--background);
            border: 1px solid var(--border);
            padding: 0.4rem 0.75rem 0.4rem 0.4rem;
            border-radius: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            color: var(--foreground);
        }

        .user-profile-btn:hover,
        .user-profile-btn.active {
            background: var(--muted);
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .user-name-display {
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--foreground);
            line-height: 1.2;
        }

        .dropdown-arrow {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
            color: var(--muted-foreground);
        }

        .user-profile-btn.active .dropdown-arrow {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 0.75rem);
            right: 0;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            min-width: 260px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to bottom, var(--muted), transparent);
        }

        .dropdown-header-title {
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--foreground);
            margin: 0 0 0.25rem 0;
        }

        .dropdown-header-subtitle {
            font-size: 0.8125rem;
            color: var(--muted-foreground);
            margin: 0;
        }

        .dropdown-menu {
            padding: 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--foreground);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background: var(--muted);
            color: var(--primary);
        }

        .dropdown-item svg {
            width: 18px;
            height: 18px;
            stroke: var(--muted-foreground);
            transition: stroke 0.2s ease;
            flex-shrink: 0;
        }

        .dropdown-item:hover svg {
            stroke: var(--primary);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 0.5rem 0;
        }

        .dropdown-item.logout {
            color: var(--destructive);
        }

        .dropdown-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--destructive);
        }

        .dropdown-item.logout svg {
            stroke: var(--destructive);
        }

        @media (max-width: 768px) {
            .user-nav-actions {
                gap: 0.5rem;
            }

            .user-info {
                display: none;
            }

            .user-profile-btn {
                padding: 0.5rem;
            }
        }

        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .profile-header-info {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .profile-header-info h1 {
            margin: 0 0 0.5rem 0;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .profile-header-info p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 1.125rem;
        }

        .profile-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .profile-stat {
            text-align: center;
        }

        .profile-stat-value {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .profile-stat-label {
            display: block;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
        }

        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sidebar-card {
            background: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .sidebar-card h3 {
            margin: 0 0 1rem 0;
            color: var(--foreground);
            font-size: 1.125rem;
            font-weight: 600;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            color: var(--foreground);
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            transform: translateX(5px);
        }

        .sidebar-nav a.active {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .sidebar-nav a svg {
            width: 20px;
            height: 20px;
        }

        .profile-main {
            background: white;
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .profile-main h2 {
            margin: 0 0 2rem 0;
            color: var(--foreground);
            font-size: 1.75rem;
            font-weight: 700;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            color: #059669;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--foreground);
            font-size: 0.9375rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
            background: #f9fafb;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-danger {
            background: var(--destructive);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .danger-zone {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #fee2e2;
        }

        .danger-zone h3 {
            color: var(--destructive);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 968px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-stats {
                justify-content: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="nav-content">
                <a href="/book-hub/src/views/user.php" class="nav-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                    <span>BOOK <span class="accent">HUB</span></span>
                </a>

                <ul class="nav-links">
                    <li><a href="/book-hub/src/views/user.php">Home</a></li>
                    <li><a href="/book-hub/public/books.php">Books</a></li>
                    <li><a href="/book-hub/public/about.php">About Us</a></li>
                    <li><a href="/book-hub/public/gallery.php">Gallery</a></li>
                    <li><a href="/book-hub/public/contact.php">Contact</a></li>
                </ul>

                <div class="user-nav-actions">
                    <!-- User Profile Button -->
                    <button class="user-profile-btn" onclick="toggleProfileDropdown()">
                        <div class="user-avatar"><?php echo $user_initials; ?></div>
                        <div class="user-info">
                            <span class="user-name-display"><?php echo htmlspecialchars($user['first_name']); ?></span>
                        </div>
                        <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <p class="dropdown-header-title"><?php echo $user_name; ?></p>
                            <p class="dropdown-header-subtitle"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div class="dropdown-menu">
                            <a href="/book-hub/src/views/profile.php" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="/book-hub/src/views/user.php" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                                </svg>
                                My Books
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                Favorites
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/book-hub/src/handlers/logout-handler.php" class="dropdown-item logout">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <button class="mobile-menu-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="mobile-menu">
                <a href="/book-hub/src/views/user.php">Home</a>
                <a href="/book-hub/public/books.php">Books</a>
                <a href="/book-hub/public/about.php">About Us</a>
                <a href="/book-hub/public/gallery.php">Gallery</a>
                <a href="/book-hub/public/contact.php">Contact</a>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                    <a href="/book-hub/src/views/profile.php">My Profile</a>
                    <a href="/book-hub/src/handlers/logout-handler.php" style="color: var(--destructive);">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-large"><?php echo $user_initials; ?></div>
            <div class="profile-header-info">
                <h1><?php echo $user_name; ?></h1>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <span class="profile-stat-value">12</span>
                        <span class="profile-stat-label">Books Read</span>
                    </div>
                    <div class="profile-stat">
                        <span class="profile-stat-value">5</span>
                        <span class="profile-stat-label">Active Rentals</span>
                    </div>
                    <div class="profile-stat">
                        <span class="profile-stat-value">28</span>
                        <span class="profile-stat-label">Days Member</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="sidebar-card">
                    <h3>Account</h3>
                    <div class="sidebar-nav">
                        <a href="#" class="active">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Profile Settings
                        </a>
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                            </svg>
                            My Books
                        </a>
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            Favorites
                        </a>
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            Reading History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="profile-main">
                <h2>Profile Settings</h2>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                        <a href="user" class="btn btn-outline">Cancel</a>
                    </div>

                    <div class="danger-zone">
                        <h3>⚠️ Danger Zone</h3>
                        <p style="color: var(--muted-foreground); margin-bottom: 1rem;">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/book-hub/public/static/js/common.js"></script>
    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const button = document.querySelector('.user-profile-btn');
            
            dropdown.classList.toggle('active');
            button.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = document.querySelector('.user-profile-btn');
            
            if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
                button.classList.remove('active');
            }
        });
    </script>
</body>
</html>

