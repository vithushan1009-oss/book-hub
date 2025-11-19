<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$adminName = $_SESSION['admin_name'] ?? 'Admin User';
$adminEmail = $_SESSION['admin_email'] ?? 'admin@bookhub.com';
$adminInitial = strtoupper(substr($adminName, 0, 1));
$currentPage = basename($_SERVER['PHP_SELF']);

// Get current time for greeting
$currentHour = (int) date('H');
if ($currentHour < 12) {
    $greeting = 'Good Morning';
} elseif ($currentHour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}
?>

<header class="top-bar">
  <div class="top-bar-left">
    <button class="mobile-menu-btn" id="mobileSidebarToggle" aria-label="Open sidebar">
      <i class="fas fa-bars"></i>
    </button>
    
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-container">
      <nav class="breadcrumb">
        <span class="breadcrumb-item">
          <i class="fas fa-home"></i>
          <span>Admin</span>
        </span>
        <i class="fas fa-chevron-right breadcrumb-separator"></i>
        <span class="breadcrumb-item active" id="currentPageBreadcrumb">Dashboard</span>
      </nav>
    </div>
  </div>

  <div class="top-bar-center">
    <div class="top-bar-search">
      <i class="fas fa-search search-icon"></i>
      <input type="text" placeholder="Search users, books, or rentals..." class="search-input">
      <button class="search-filters-btn" title="Advanced Search">
        <i class="fas fa-filter"></i>
      </button>
    </div>
  </div>

  <div class="top-bar-right">
    <!-- Quick Actions -->
    <div class="quick-actions">
      <button class="icon-btn" title="Add New User" onclick="showAddUserModal()">
        <i class="fas fa-user-plus"></i>
      </button>
      <button class="icon-btn" title="Add New Book" onclick="showAddBookModal()">
        <i class="fas fa-plus"></i>
      </button>
    </div>

    <!-- Notifications & Messages -->
    <div class="notifications-section">
      <button class="icon-btn notification-btn" id="notificationToggle" title="Notifications">
        <i class="fas fa-bell"></i>
        <span class="badge notification-badge">3</span>
        <div class="notification-dropdown" id="notificationDropdown">
          <div class="dropdown-header">
            <h3>Notifications</h3>
            <span class="mark-all-read">Mark all as read</span>
          </div>
          <div class="notification-list">
            <div class="notification-item unread">
              <div class="notification-icon success">
                <i class="fas fa-user-check"></i>
              </div>
              <div class="notification-content">
                <div class="notification-title">New User Registration</div>
                <div class="notification-text">John Doe has registered and is awaiting verification</div>
                <div class="notification-time">2 minutes ago</div>
              </div>
            </div>
            <div class="notification-item">
              <div class="notification-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div class="notification-content">
                <div class="notification-title">Book Return Overdue</div>
                <div class="notification-text">5 books are overdue and require follow-up</div>
                <div class="notification-time">1 hour ago</div>
              </div>
            </div>
            <div class="notification-item">
              <div class="notification-icon info">
                <i class="fas fa-chart-line"></i>
              </div>
              <div class="notification-content">
                <div class="notification-title">Monthly Report Ready</div>
                <div class="notification-text">November analytics report is ready for review</div>
                <div class="notification-time">3 hours ago</div>
              </div>
            </div>
          </div>
          <div class="dropdown-footer">
            <a href="#notifications" class="view-all-link">View all notifications</a>
          </div>
        </div>
      </button>

      <button class="icon-btn message-btn" title="Messages">
        <i class="fas fa-envelope"></i>
        <span class="badge message-badge">2</span>
      </button>
    </div>

    <!-- Theme Toggle -->
    <button class="icon-btn theme-toggle" id="themeToggle" title="Toggle Theme">
      <i class="fas fa-moon theme-icon"></i>
    </button>

    <!-- User Profile -->
    <div class="user-profile-section">
      <div class="greeting-text">
        <span class="greeting"><?php echo $greeting; ?>,</span>
        <span class="user-name-short"><?php echo explode(' ', $adminName)[0]; ?></span>
      </div>
      
      <div class="user-profile-dropdown" id="userProfileToggle">
        <div class="user-avatar-container">
          <div class="user-avatar">
            <?php echo $adminInitial; ?>
            <div class="online-indicator"></div>
          </div>
          <i class="fas fa-chevron-down dropdown-arrow"></i>
        </div>
        
        <div class="profile-dropdown" id="profileDropdown">
          <div class="dropdown-header">
            <div class="profile-info">
              <div class="profile-avatar">
                <?php echo $adminInitial; ?>
              </div>
              <div class="profile-details">
                <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                <div class="profile-email"><?php echo htmlspecialchars($adminEmail); ?></div>
                <div class="profile-role">Administrator</div>
              </div>
            </div>
          </div>
          
          <div class="dropdown-menu">
            <a href="/BOOKHUB/book-hub-central/public/admin-profile.php" class="dropdown-item">
              <i class="fas fa-user"></i>
              <span>My Profile</span>
            </a>
            <a href="/BOOKHUB/book-hub-central/public/admin-settings.php" class="dropdown-item">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </a>
            <a href="#" class="dropdown-item">
              <i class="fas fa-chart-bar"></i>
              <span>My Activity</span>
            </a>
            <a href="#" class="dropdown-item">
              <i class="fas fa-bell"></i>
              <span>Notification Settings</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-question-circle"></i>
              <span>Help & Support</span>
            </a>
            <a href="/BOOKHUB/book-hub-central/src/handlers/admin-logout-handler.php" class="dropdown-item logout">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
