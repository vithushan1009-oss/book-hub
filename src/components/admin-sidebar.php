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
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <i class="fas fa-book-reader"></i>
      <span>BOOK <span class="accent">HUB</span></span>
    </div>
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">
      <h4 class="nav-section-title">Main</h4>
      <a href="<?= $base ?>/admin.php#dashboard" class="nav-item <?= $current === 'admin.php' ? 'active' : '' ?>" data-section="dashboard">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
      </a>
      <a href="<?= $base ?>/admin-users.php#users" class="nav-item <?= $current === 'admin-users.php' ? 'active' : '' ?>" data-section="users">
        <i class="fas fa-users"></i>
        <span>Users</span>
      </a>
      <a href="<?= $base ?>/books.php" class="nav-item">
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
      <a href="<?= $base ?>/admin-profile.php" class="nav-item <?= $current === 'admin-profile.php' ? 'active' : '' ?>">
        <i class="fas fa-user-shield"></i>
        <span>Profile</span>
      </a>
      <a href="<?= $base ?>/admin-settings.php" class="nav-item <?= $current === 'admin-settings.php' ? 'active' : '' ?>">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="user-profile">
      <div class="user-avatar"><?= $adminInitial ?></div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($adminName) ?></div>
        <div class="user-role"><?= htmlspecialchars($adminRole) ?></div>
      </div>
    </div>
    <a class="btn-logout" href="/BOOKHUB/book-hub-central/src/handlers/admin-logout-handler.php" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</aside>
