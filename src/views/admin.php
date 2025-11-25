<?php
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Fetch statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
$verified_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE email_verified = 1")->fetch_assoc()['count'];
$pending_users = $total_users - $verified_users;

// Get database info
$db_name = $conn->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name'];
$db_size_query = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?";
$db_size_stmt = $conn->prepare($db_size_query);
$db_size_stmt->bind_param("s", $db_name);
$db_size_stmt->execute();
$db_size = $db_size_stmt->get_result()->fetch_assoc()['size_mb'] ?? 0;

// Fetch recent users
$recent_users_query = "SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, created_at, email_verified FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users = $conn->query($recent_users_query);

// Fetch rental statistics
$total_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals")->fetch_assoc()['count'];
$pending_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'approved'")->fetch_assoc()['count'];
$active_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'approved' AND start_date <= CURDATE() AND end_date >= CURDATE()")->fetch_assoc()['count'];
$completed_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'completed'")->fetch_assoc()['count'];

// Get filter parameters for rentals
$rental_status_filter = isset($_GET['rental_status']) ? $_GET['rental_status'] : 'all';
$rental_search = isset($_GET['rental_search']) ? trim($_GET['rental_search']) : '';

// Build rental query
$rental_where = "1=1";
$rental_params = [];
$rental_types = "";

if ($rental_status_filter !== 'all') {
    $rental_where .= " AND r.status = ?";
    $rental_params[] = $rental_status_filter;
    $rental_types .= "s";
}

if ($rental_search) {
    $rental_where .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR b.title LIKE ? OR b.author LIKE ?)";
    $search_param = "%$rental_search%";
    for ($i = 0; $i < 5; $i++) {
        $rental_params[] = $search_param;
    }
    $rental_types .= "sssss";
}

// Fetch rentals with user and book details
$rentals_query = "SELECT r.id, r.user_id, r.book_id, r.start_date, r.end_date, r.phone_number, r.status,
                         CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email,
                         b.title as book_title, b.author as book_author, b.rental_price_per_day
                  FROM rentals r
                  INNER JOIN users u ON r.user_id = u.id
                  INNER JOIN books b ON r.book_id = b.id
                  WHERE $rental_where
                  ORDER BY r.id DESC
                  LIMIT 100";

$rentals_result = null;
if (!empty($rental_params) && !empty($rental_types)) {
    $rental_stmt = $conn->prepare($rentals_query);
    if ($rental_stmt) {
        $rental_stmt->bind_param($rental_types, ...$rental_params);
        $rental_stmt->execute();
        $rentals_result = $rental_stmt->get_result();
    }
} else {
    $rentals_result = $conn->query($rentals_query);
}
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
              <div class="stat-change neutral">
                <i class="fas fa-minus"></i> Needs attention
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon warning">
              <i class="fas fa-handshake"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Pending Rentals</div>
              <div class="stat-value"><?php echo $pending_rentals; ?></div>
              <div class="stat-change <?php echo $pending_rentals > 0 ? 'negative' : 'neutral'; ?>">
                <i class="fas fa-<?php echo $pending_rentals > 0 ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo $pending_rentals > 0 ? 'Action required' : 'All clear'; ?>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Active Rentals</div>
              <div class="stat-value"><?php echo $active_rentals; ?></div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> Currently active
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon primary">
              <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Total Rentals</div>
              <div class="stat-value"><?php echo $total_rentals; ?></div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> All time
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
              <div class="stat-change positive">
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
              <a href="/BOOKHUB/book-hub-central/admin-users" class="btn-link">View All</a>
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

        <!-- Database Information Card -->
        <div class="dashboard-card" style="margin-top: 2rem;">
          <div class="card-header">
            <h3><i class="fas fa-database"></i> Database Information</h3>
          </div>
          <div class="card-content">
            <div class="db-info-grid">
              <div class="db-info-item">
                <div class="db-info-label">
                  <i class="fas fa-database"></i> Database Name
                </div>
                <div class="db-info-value"><?php echo htmlspecialchars($db_name); ?></div>
              </div>
              <div class="db-info-item">
                <div class="db-info-label">
                  <i class="fas fa-hdd"></i> Database Size
                </div>
                <div class="db-info-value"><?php echo number_format($db_size, 2); ?> MB</div>
              </div>
              <div class="db-info-item">
                <div class="db-info-label">
                  <i class="fas fa-server"></i> Server Version
                </div>
                <div class="db-info-value"><?php echo $conn->server_info; ?></div>
              </div>
              <div class="db-info-item">
                <div class="db-info-label">
                  <i class="fas fa-plug"></i> Connection Status
                </div>
                <div class="db-info-value">
                  <span class="status-badge success">
                    <i class="fas fa-check-circle"></i> Connected
                  </span>
                </div>
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

      <!-- Rentals Section -->
      <section id="rentals-section" class="content-section">
        <div class="section-header">
          <h1>Rental Management</h1>
          <div class="header-actions">
            <button class="btn btn-secondary" onclick="exportRentals()">
              <i class="fas fa-download"></i> Export
            </button>
          </div>
        </div>

        <!-- Rental Statistics Cards -->
        <div class="stats-grid" style="margin-bottom: 2rem;">
          <div class="stat-card">
            <div class="stat-icon warning">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Pending</div>
              <div class="stat-value"><?php echo $pending_rentals; ?></div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Approved</div>
              <div class="stat-value"><?php echo $approved_rentals; ?></div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon primary">
              <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Active</div>
              <div class="stat-value"><?php echo $active_rentals; ?></div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon accent">
              <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
              <div class="stat-label">Completed</div>
              <div class="stat-value"><?php echo $completed_rentals; ?></div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="content-card">
          <form method="GET" action="/BOOKHUB/book-hub-central/admin#rentals" class="card-filters">
            <input type="text" name="rental_search" class="filter-input" placeholder="Search by user name, email, or book title..." value="<?php echo htmlspecialchars($rental_search); ?>">
            <select name="rental_status" class="filter-select">
              <option value="all" <?php echo $rental_status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
              <option value="pending" <?php echo $rental_status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="approved" <?php echo $rental_status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
              <option value="rejected" <?php echo $rental_status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
              <option value="completed" <?php echo $rental_status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
              <option value="cancelled" <?php echo $rental_status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search"></i> Filter
            </button>
            <?php if ($rental_search || $rental_status_filter !== 'all'): ?>
              <a href="/BOOKHUB/book-hub-central/admin#rentals" class="btn btn-outline">
                <i class="fas fa-times"></i> Clear
              </a>
            <?php endif; ?>
          </form>

          <!-- Rentals Table -->
          <div style="overflow-x: auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Book</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Days</th>
                  <th>Total Cost</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Requested</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($rentals_result && $rentals_result->num_rows > 0): ?>
                  <?php while ($rental = $rentals_result->fetch_assoc()): 
                    $start = new DateTime($rental['start_date']);
                    $end = new DateTime($rental['end_date']);
                    $days = $start->diff($end)->days + 1;
                    $total_cost = $days * (float)$rental['rental_price_per_day'];
                    $status_class = [
                      'pending' => 'warning',
                      'approved' => 'success',
                      'rejected' => 'danger',
                      'completed' => 'primary',
                      'cancelled' => 'muted'
                    ][$rental['status']] ?? 'muted';
                  ?>
                    <tr>
                      <td>#<?php echo $rental['id']; ?></td>
                      <td>
                        <div style="display: flex; flex-direction: column;">
                          <strong><?php echo htmlspecialchars($rental['user_name']); ?></strong>
                          <small style="color: var(--muted-foreground);"><?php echo htmlspecialchars($rental['user_email']); ?></small>
                        </div>
                      </td>
                      <td>
                        <div style="display: flex; flex-direction: column;">
                          <strong><?php echo htmlspecialchars($rental['book_title']); ?></strong>
                          <small style="color: var(--muted-foreground);">by <?php echo htmlspecialchars($rental['book_author']); ?></small>
                        </div>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($rental['start_date'])); ?></td>
                      <td><?php echo date('M d, Y', strtotime($rental['end_date'])); ?></td>
                      <td><?php echo $days; ?> day<?php echo $days !== 1 ? 's' : ''; ?></td>
                      <td><strong>LKR <?php echo number_format($total_cost, 2); ?></strong></td>
                      <td><?php echo htmlspecialchars($rental['phone_number']); ?></td>
                      <td>
                        <span class="status-badge <?php echo $status_class; ?>">
                          <i class="fas fa-<?php 
                            echo $rental['status'] === 'pending' ? 'clock' : 
                                ($rental['status'] === 'approved' ? 'check-circle' : 
                                ($rental['status'] === 'rejected' ? 'times-circle' : 
                                ($rental['status'] === 'completed' ? 'check-double' : 'ban'))); 
                          ?>"></i>
                          <?php echo ucfirst($rental['status']); ?>
                        </span>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($rental['start_date'])); ?></td>
                      <td>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                          <?php if ($rental['status'] === 'pending'): ?>
                            <button onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'approved')" class="btn btn-sm btn-success" title="Approve">
                              <i class="fas fa-check"></i>
                            </button>
                            <button onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'rejected')" class="btn btn-sm btn-danger" title="Reject">
                              <i class="fas fa-times"></i>
                            </button>
                          <?php elseif ($rental['status'] === 'approved'): ?>
                            <button onclick="updateRentalStatus(<?php echo $rental['id']; ?>, 'completed')" class="btn btn-sm btn-primary" title="Mark as Completed">
                              <i class="fas fa-check-double"></i>
                            </button>
                          <?php endif; ?>
                          <button onclick="viewRentalDetails(<?php echo $rental['id']; ?>)" class="btn btn-sm btn-outline" title="View Details">
                            <i class="fas fa-eye"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="11" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
                      <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                      <p>No rentals found</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
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

// Rental Management Functions
function updateRentalStatus(rentalId, newStatus) {
  const statusNames = {
    'approved': 'approve',
    'rejected': 'reject',
    'completed': 'complete'
  };
  const actionName = statusNames[newStatus] || newStatus;
  
  if (confirm(`Are you sure you want to ${actionName} this rental request?`)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/BOOKHUB/book-hub-central/src/handlers/update-rental-status-handler.php';
    
    const rentalIdInput = document.createElement('input');
    rentalIdInput.type = 'hidden';
    rentalIdInput.name = 'rental_id';
    rentalIdInput.value = rentalId;
    form.appendChild(rentalIdInput);
    
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = newStatus;
    form.appendChild(statusInput);
    
    document.body.appendChild(form);
    form.submit();
  }
}

function viewRentalDetails(rentalId) {
  // Open modal or redirect to details page
  alert('Rental Details for ID: ' + rentalId + '\n\nThis feature can be expanded to show full rental information.');
}

function exportRentals() {
  const params = new URLSearchParams(window.location.search);
  params.set('export', 'rentals');
  window.location.href = `/BOOKHUB/book-hub-central/admin?${params.toString()}`;
}

// Display success/error messages
document.addEventListener('DOMContentLoaded', function() {
  const params = new URLSearchParams(window.location.search);
  const success = params.get('success');
  const error = params.get('error');
  
  if (success) {
    // You can add a toast notification here
    console.log('Success:', decodeURIComponent(success));
  }
  
  if (error) {
    // You can add a toast notification here
    console.error('Error:', decodeURIComponent(error));
  }
});
</script>
</body>
</html>
