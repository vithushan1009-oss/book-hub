<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

// Debug logging
$log_file = __DIR__ . '/../../admin-dashboard-debug.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Admin page accessed\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Session ID: " . session_id() . "\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Session data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Admin-only access
if (!isset($_SESSION['admin_id'])) {
  file_put_contents($log_file, date('Y-m-d H:i:s') . " - No admin_id in session, redirecting to login\n", FILE_APPEND);
  header('Location: /BOOKHUB/book-hub-central/admin-login');
  exit();
}

file_put_contents($log_file, date('Y-m-d H:i:s') . " - Admin authenticated: ID={$_SESSION['admin_id']}\n", FILE_APPEND);

$conn = getDbConnection();

// Fetch statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
$verified_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE email_verified = 1")->fetch_assoc()['count'];
$pending_users = $total_users - $verified_users;

// Fetch recent users
$recent_users_query = "SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, created_at, email_verified FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users = $conn->query($recent_users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — BOOK HUB</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/variables.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/base.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/admin.css">
</head>
<body>

<div class="admin-page">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="fas fa-book-reader"></i>
        <span>BOOK <span class="accent">HUB</span></span>
      </div>
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">
        <h4 class="nav-section-title">Main</h4>
        <a href="#dashboard" class="nav-item active" data-section="dashboard">
          <i class="fas fa-th-large"></i>
          <span>Dashboard</span>
        </a>
        <a href="#users" class="nav-item" data-section="users">
          <i class="fas fa-users"></i>
          <span>Users</span>
        </a>
        <a href="#books" class="nav-item" data-section="books">
          <i class="fas fa-book"></i>
          <span>Books</span>
        </a>
        <a href="#rentals" class="nav-item" data-section="rentals">
          <i class="fas fa-shopping-cart"></i>
          <span>Rentals</span>
        </a>
      </div>

      <div class="nav-section">
        <h4 class="nav-section-title">Management</h4>
        <a href="#admins" class="nav-item" data-section="admins">
          <i class="fas fa-user-shield"></i>
          <span>Administrators</span>
        </a>
        <a href="#settings" class="nav-item" data-section="settings">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
        </div>
        <div class="user-info">
          <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></div>
          <div class="user-role"><?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Administrator'); ?></div>
        </div>
      </div>
      <button class="btn-logout" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Top Bar -->
    <header class="top-bar">
      <button class="mobile-menu-btn" id="mobileSidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      
      <div class="top-bar-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search...">
      </div>

      <div class="top-bar-actions">
        <button class="icon-btn" id="themeToggle" title="Toggle Theme">
          <i class="fas fa-moon"></i>
        </button>
        <button class="icon-btn" title="Notifications">
          <i class="fas fa-bell"></i>
          <span class="badge">3</span>
        </button>
        <button class="icon-btn" title="Messages">
          <i class="fas fa-envelope"></i>
          <span class="badge">5</span>
        </button>
      </div>
    </header>

    <!-- Content Area -->
    <div class="content-area">
      <!-- Dashboard Section -->
      <section id="dashboard-section" class="content-section active">
        <div class="section-header">
          <h1>Dashboard Overview</h1>
          <div class="header-actions">
            <button class="btn btn-secondary">
              <i class="fas fa-download"></i> Export Report
            </button>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon primary">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Total Users</div>
              <div class="stat-value"><?php echo $total_users; ?></div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> 12% this month
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Verified Users</div>
              <div class="stat-value"><?php echo $verified_users; ?></div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <?php echo $total_users > 0 ? round(($verified_users/$total_users)*100) : 0; ?>%
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon accent">
              <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Pending Verification</div>
              <div class="stat-value"><?php echo $pending_users; ?></div>
              <div class="stat-change">
                <i class="fas fa-minus"></i> Awaiting action
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon secondary">
              <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Administrators</div>
              <div class="stat-value"><?php echo $total_admins; ?></div>
              <div class="stat-change">
                <i class="fas fa-check"></i> Active
              </div>
            </div>
          </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
          <!-- Recent Users -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Recent Users</h3>
              <a href="#users" class="btn-link" data-section="users">View All</a>
            </div>
            <div class="card-content">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($recent_users->num_rows > 0): ?>
                    <?php while($user = $recent_users->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                          <?php if ($user['email_verified']): ?>
                            <span class="status-badge success">Verified</span>
                          <?php else: ?>
                            <span class="status-badge pending">Pending</span>
                          <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" style="text-align: center;">No users found</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="dashboard-card">
            <div class="card-header">
              <h3>Quick Actions</h3>
            </div>
            <div class="card-content">
              <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button class="btn btn-primary" style="width: 100%;">
                  <i class="fas fa-user-plus"></i> Add New User
                </button>
                <button class="btn btn-primary" style="width: 100%;">
                  <i class="fas fa-book-medical"></i> Add New Book
                </button>
                <button class="btn btn-secondary" style="width: 100%;">
                  <i class="fas fa-user-shield"></i> Manage Admins
                </button>
                <button class="btn btn-secondary" style="width: 100%;">
                  <i class="fas fa-chart-bar"></i> View Reports
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Users Section -->
      <section id="users-section" class="content-section">
        <div class="section-header">
          <h1>User Management</h1>
          <div class="header-actions">
            <button class="btn btn-primary">
              <i class="fas fa-user-plus"></i> Add New User
            </button>
          </div>
        </div>

        <div class="content-card">
          <div class="card-filters">
            <input type="text" class="filter-input" placeholder="Search users...">
            <select class="filter-select">
              <option value="">All Status</option>
              <option value="verified">Verified</option>
              <option value="pending">Pending</option>
            </select>
          </div>

          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="6" style="text-align: center; padding: 2rem;">User management features coming soon</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Books Section -->
      <section id="books-section" class="content-section">
        <div class="section-header">
          <h1>Book Management</h1>
          <div class="header-actions">
            <button class="btn btn-primary">
              <i class="fas fa-book-medical"></i> Add New Book
            </button>
          </div>
        </div>

        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Book management features coming soon
          </p>
        </div>
      </section>

      <!-- Other sections placeholders -->
      <section id="rentals-section" class="content-section">
        <div class="section-header">
          <h1>Rental Management</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Rental management features coming soon
          </p>
        </div>
      </section>

      <section id="admins-section" class="content-section">
        <div class="section-header">
          <h1>Administrator Management</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Administrator management features coming soon
          </p>
        </div>
      </section>

      <section id="settings-section" class="content-section">
        <div class="section-header">
          <h1>Settings</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Settings features coming soon
          </p>
        </div>
      </section>
    </div>
  </div>
</div>

<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
</body>
</html>
