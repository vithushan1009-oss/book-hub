<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);
// Get the current route from the URL
$current_route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_route = rtrim($current_route, '/');
// Remove base path if present
$current_route = str_replace('/book-hub', '', $current_route);
// Handle empty route (root)
if (empty($current_route) || $current_route === '/') {
    $current_route = '/admin';
}
// Also check the script name for direct file access
if ($current === 'admin.php') {
    $current_route = '/admin';
} elseif ($current === 'manage-users.php') {
    $current_route = '/admin-users';
} elseif ($current === 'manage-books.php') {
    $current_route = '/admin-books';
} elseif ($current === 'manage-rentals.php') {
    $current_route = '/admin-rentals';
} elseif ($current === 'admin-analytics.php') {
    $current_route = '/admin-analytics';
} elseif ($current === 'admin-profile.php') {
    $current_route = '/admin-profile';
} elseif ($current === 'admin-settings.php') {
    $current_route = '/admin-settings';
} elseif ($current === 'manage-contacts.php') {
    $current_route = '/admin-contacts';
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';
$adminInitial = strtoupper(substr($adminName, 0, 1));

// Get pending rentals count for badge (only if config is available)
$pending_rentals = 0;
$unread_contacts = 0;
if (file_exists(__DIR__ . '/../config.php')) {
    try {
        require_once __DIR__ . '/../config.php';
        if (function_exists('getDbConnection')) {
            $temp_conn = getDbConnection();
            if ($temp_conn) {
                $pending_result = $temp_conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'pending'");
                if ($pending_result) {
                    $pending_rentals = (int)$pending_result->fetch_assoc()['count'];
                }
                // Get unread contact messages count
                $contact_result = $temp_conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
                if ($contact_result) {
                    $unread_contacts = (int)$contact_result->fetch_assoc()['count'];
                }
            }
        }
    } catch (Exception $e) {
        // Silently fail if database not available
        $pending_rentals = 0;
        $unread_contacts = 0;
    }
}
?>

<!-- SIDEBAR START -->
<aside class="sidebar" id="sidebar">

  <!-- Header Logo Section -->
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <i class="fas fa-book-open"></i>
      <span>BOOK <span class="accent">HUB</span></span>
      <div class="logo-subtitle">Admin Panel</div>
    </div>

    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <i class="fas fa-angle-double-left"></i>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="sidebar-nav">

    <!-- Overview Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-tachometer-alt"></i> Overview
      </h4>

      <a href="/book-hub/admin"
         class="nav-item <?= ($current_route === '/admin' || $current_route === '/admin/') ? 'active' : '' ?>"
         data-section="dashboard">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard</span>
        <div class="nav-badge">Live</div>
      </a>

      <a href="/book-hub/admin-analytics"
         class="nav-item <?= ($current_route === '/admin-analytics') ? 'active' : '' ?>">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
    </div>

    <!-- Content Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-database"></i> Content
      </h4>

      <a href="/book-hub/admin-users"
         class="nav-item <?= ($current_route === '/admin-users') ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>User Management</span>
        <div class="nav-count">4</div>
      </a>

      <a href="/book-hub/admin-books"
         class="nav-item <?= ($current_route === '/admin-books') ? 'active' : '' ?>">
        <i class="fas fa-book"></i>
        <span>Book Management</span>
      </a>

      <a href="/book-hub/admin-rentals"
         class="nav-item <?= ($current_route === '/admin-rentals') ? 'active' : '' ?>">
        <i class="fas fa-handshake"></i>
        <span>Rental Management</span>
        <?php if($pending_rentals > 0): ?>
          <div class="nav-badge warning"><?php echo $pending_rentals; ?> Pending</div>
        <?php endif; ?>
      </a>

      <a href="/book-hub/admin-contacts"
         class="nav-item <?= ($current_route === '/admin-contacts') ? 'active' : '' ?>">
        <i class="fas fa-envelope"></i>
        <span>Contact Messages</span>
        <?php if($unread_contacts > 0): ?>
          <div class="nav-badge warning"><?php echo $unread_contacts; ?> New</div>
        <?php endif; ?>
      </a>
    </div>

    <!-- Administration Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-cogs"></i> Administration
      </h4>

      <a href="/book-hub/admin-profile"
         class="nav-item <?= ($current_route === '/admin-profile') ? 'active' : '' ?>">
        <i class="fas fa-user-shield"></i>
        <span>Admin Profile</span>
      </a>

      <a href="/book-hub/admin#permissions" class="nav-item" data-section="permissions">
        <i class="fas fa-key"></i>
        <span>Permissions</span>
      </a>

      <a href="/book-hub/admin-settings"
         class="nav-item <?= ($current_route === '/admin-settings') ? 'active' : '' ?>">
        <i class="fas fa-sliders-h"></i>
        <span>System Settings</span>
      </a>
    </div>

    <!-- Tools Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-tools"></i> Tools
      </h4>

      <a href="/book-hub/admin#reports" class="nav-item" data-section="reports">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
      </a>

      <a href="/book-hub/admin#backup" class="nav-item" data-section="backup">
        <i class="fas fa-server"></i>
        <span>Backup</span>
      </a>

      <a href="/book-hub/admin#logs" class="nav-item" data-section="logs">
        <i class="fas fa-clipboard-list"></i>
        <span>Activity Logs</span>
      </a>
    </div>

  </nav>

  <!-- Footer User Card -->
  <div class="sidebar-footer">
    <div class="user-profile-card">

      <div class="user-avatar-section">
        <div class="user-avatar">
          <?= $adminInitial ?>
          <div class="status-indicator online"></div>
        </div>

        <div class="user-info">
          <div class="user-name"><?= htmlspecialchars($adminName) ?></div>

          <div class="user-role">
            <i class="fas fa-crown"></i>
            <?= htmlspecialchars($adminRole) ?>
          </div>

          <div class="user-status">
            <i class="fas fa-circle"></i>
            Online
          </div>
        </div>
      </div>

      <div class="user-actions">
        <a href="/book-hub/admin-profile" class="action-btn" title="Profile Settings">
          <i class="fas fa-user-cog"></i>
        </a>

        <button class="action-btn" id="themeToggleSidebar" title="Toggle Theme">
          <i class="fas fa-palette"></i>
        </button>

        <button class="btn-logout" onclick="confirmLogout()" title="Logout">
          <i class="fas fa-power-off"></i>
          <span class="logout-text">Logout</span>
        </button>
      </div>

    </div>
  </div>

</aside>
<!-- SIDEBAR END -->
