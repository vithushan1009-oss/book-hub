<?php
require_once __DIR__ . '/../config.php';
session_start();

// Admin-only access
if (!isset($_SESSION['admin_id'])) {
  // Not an admin, redirect to admin login
  header('Location: /BOOKHUB/book-hub-central/public/admin-login.html');
  exit();
}

$conn = getDbConnection();

// Fetch statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
$verified_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE email_verified = 1")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — BOOK HUB</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/variables.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/base.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/admin.css">
</head>
<body>

<div class="admin-container">
    <aside class="sidebar">
    <div class="profile-section">
      <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
      <div class="profile-info">
        <h3><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h3>
        <p>ID: <?php echo htmlspecialchars($_SESSION['admin_id'] ?? 'admin'); ?></p>
      </div>
    </div>
    
    <nav class="menu">
      <ul>
        <li><a href="/BOOKHUB/book-hub-central/src/views/admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="/BOOKHUB/book-hub-central/src/views/manage-users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="/BOOKHUB/book-hub-central/src/views/manage-books.php"><i class="fas fa-book"></i> Books</a></li>
        <li><a href="/BOOKHUB/book-hub-central/src/views/manage-rentals.php"><i class="fas fa-shopping-cart"></i> Rentals</a></li>
        <li><a href="/BOOKHUB/book-hub-central/src/views/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="/BOOKHUB/book-hub-central/src/views/admin-profile.php"><i class="fas fa-user"></i> Profile</a></li>
      </ul>
    </nav>

    <div style="margin-top: 1rem;">
      <a href="/BOOKHUB/book-hub-central/src/handlers/logout-handler.php" class="logout-btn" style="display:inline-block; padding:0.6rem 1rem; background:#ef4444; color:white; border-radius:6px; text-decoration:none;">
        <i class="fas fa-sign-out-alt"></i> Log Out
      </a>
    </div>
  </aside>

  <main class="main-content">
    <h1>Dashboard</h1>
    <div class="grid-container">
      <a href="manage-users" style="text-decoration: none;">
        <div class="card">
          <div class="card-icon"><i class="fas fa-users"></i></div>
          <div class="card-content">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
          </div>
        </div>
      </a>
      
      <div class="card">
        <div class="card-icon"><i class="fas fa-user-check"></i></div>
        <div class="card-content">
          <h3><?php echo $verified_users; ?></h3>
          <p>Verified Users</p>
        </div>
      </div>
      
      <div class="card">
        <div class="card-icon"><i class="fas fa-user-shield"></i></div>
        <div class="card-content">
          <h3><?php echo $total_admins; ?></h3>
          <p>Administrators</p>
        </div>
      </div>
      
      <div class="card">
        <div class="card-icon"><i class="fas fa-book"></i></div>
        <div class="card-content">
          <h3>0</h3>
          <p>Total Books</p>
        </div>
      </div>
    </div>

    <div class="recent-activity">
      <h2>Recent Activity</h2>
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Action</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>No activity yet</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script src="/BOOKHUB/book-hub-central/public/static/js/common.js"></script>
<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
</body>
</html>
