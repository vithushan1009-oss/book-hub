<?php
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

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
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">

  <!-- CSS Files -->
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/variables.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/base.css">
  <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/admin.css">
</head>
<body>

<div class="admin-page">
  <?php require_once __DIR__ . '/../components/admin-sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <?php require_once __DIR__ . '/../components/admin-topbar.php'; ?>

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
              <a href="/BOOKHUB/book-hub-central/public/admin-users.php" class="btn-link">View All</a>
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

      <!-- Analytics Section -->
      <section id="analytics-section" class="content-section">
        <div class="section-header">
          <h1>Analytics</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Analytics features coming soon
          </p>
        </div>
      </section>

      <!-- Categories Section -->
      <section id="categories-section" class="content-section">
        <div class="section-header">
          <h1>Categories</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Category management features coming soon
          </p>
        </div>
      </section>

      <!-- Permissions Section -->
      <section id="permissions-section" class="content-section">
        <div class="section-header">
          <h1>Permissions</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Permission management features coming soon
          </p>
        </div>
      </section>

      <!-- Reports Section -->
      <section id="reports-section" class="content-section">
        <div class="section-header">
          <h1>Reports</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Reports features coming soon
          </p>
        </div>
      </section>

      <!-- Backup Section -->
      <section id="backup-section" class="content-section">
        <div class="section-header">
          <h1>Backup</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Backup features coming soon
          </p>
        </div>
      </section>

      <!-- Activity Logs Section -->
      <section id="logs-section" class="content-section">
        <div class="section-header">
          <h1>Activity Logs</h1>
        </div>
        <div class="content-card">
          <p style="text-align: center; padding: 2rem; color: var(--muted-foreground);">
            Activity logs features coming soon
          </p>
        </div>
      </section>
    </div>
  </div>
</div>

<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>

<!-- Force-load Font Awesome -->
<script>
  (function() {
    var fa = document.createElement('link');
    fa.rel = 'stylesheet';
    fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';
    fa.integrity = 'sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==';
    fa.crossOrigin = 'anonymous';
    fa.referrerPolicy = 'no-referrer';
    document.head.appendChild(fa);
  })();
</script>

<!-- Simple Icon Check -->
<script>
console.log('Admin dashboard loaded');
</script>
</body>
</html>
