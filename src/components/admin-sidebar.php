<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);
$base = '/BOOKHUB/book-hub-central/public';

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';
$adminInitial = strtoupper(substr($adminName, 0, 1));
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

      <a href="<?= $base ?>/admin"
         class="nav-item <?= $current === 'admin' ? 'active' : '' ?>"
         data-section="dashboard">
        <i class="fas fa-chart-pie"></i>
        <span>Dashboard</span>
        <div class="nav-badge">Live</div>
      </a>

      <a href="<?= $base ?>/admin#analytics" class="nav-item" data-section="analytics">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
    </div>

    <!-- Content Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-database"></i> Content
      </h4>

      <a href="<?= $base ?>/admin-users"
         class="nav-item <?= $current === 'admin-users' ? 'active' : '' ?>">
        <i class="fas fa-users"></i>
        <span>User Management</span>
        <div class="nav-count">4</div>
      </a>

      <a href="<?= $base ?>/admin-books"
         class="nav-item <?= $current === 'admin-books' ? 'active' : '' ?>">
        <i class="fas fa-book"></i>
        <span>Book Management</span>
      </a>

      <a href="<?= $base ?>/admin#rentals" class="nav-item" data-section="rentals">
        <i class="fas fa-handshake"></i>
        <span>Rentals</span>
        <div class="nav-badge warning">12 Active</div>
      </a>
    </div>

    <!-- Administration Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-cogs"></i> Administration
      </h4>

      <a href="<?= $base ?>/admin-profile"
         class="nav-item <?= $current === 'admin-profile' ? 'active' : '' ?>">
        <i class="fas fa-user-shield"></i>
        <span>Admin Profile</span>
      </a>

      <a href="<?= $base ?>/admin#permissions" class="nav-item" data-section="permissions">
        <i class="fas fa-key"></i>
        <span>Permissions</span>
      </a>

      <a href="<?= $base ?>/admin-settings"
         class="nav-item <?= $current === 'admin-settings' ? 'active' : '' ?>">
        <i class="fas fa-sliders-h"></i>
        <span>System Settings</span>
      </a>
    </div>

    <!-- Tools Section -->
    <div class="nav-section">
      <h4 class="nav-section-title">
        <i class="fas fa-tools"></i> Tools
      </h4>

      <a href="<?= $base ?>/admin#reports" class="nav-item" data-section="reports">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
      </a>

      <a href="<?= $base ?>/admin#backup" class="nav-item" data-section="backup">
        <i class="fas fa-server"></i>
        <span>Backup</span>
      </a>

      <a href="<?= $base ?>/admin#logs" class="nav-item" data-section="logs">
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
        <a href="<?= $base ?>/admin-profile" class="action-btn" title="Profile Settings">
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
