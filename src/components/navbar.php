<?php
// Navigation Component - Include session check first
if (!isset($is_logged_in)) {
    require_once __DIR__ . '/../session-check.php';
}

// Prepare user variables for navbar
if ($is_logged_in && $user_data) {
    $user_name = htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']);
    $user_email = htmlspecialchars($user_data['email']);
    $user_first_name = htmlspecialchars($user_data['first_name']);
    $user_initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));
}
?>

<!-- Navigation -->
<nav>
  <div class="container">
    <div class="nav-content">
      <a href="<?php echo $is_logged_in ? '/book-hub/src/views/user.php' : '/book-hub/public/index.php'; ?>" class="nav-logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
        </svg>
        <span>BOOK <span class="accent">HUB</span></span>
      </a>

      <ul class="nav-links">
        <li><a href="<?php echo $is_logged_in ? '/book-hub/src/views/user.php' : '/book-hub/public/index.php'; ?>">Home</a></li>
        <li><a href="/book-hub/public/books.php">Books</a></li>
        <li><a href="/book-hub/public/about.php">About Us</a></li>
        <li><a href="/book-hub/public/gallery.php">Gallery</a></li>
        <li><a href="/book-hub/public/contact.php">Contact</a></li>
      </ul>

      <?php if ($is_logged_in): ?>
      <!-- Logged In User Actions -->
      <div class="user-nav-actions">
        <!-- Notification Button -->
        <button class="notification-btn" onclick="toggleNotifications()">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span class="notification-badge">3</span>
        </button>

        <!-- User Profile Button -->
        <button class="user-profile-btn" onclick="toggleProfileDropdown()">
          <div class="user-avatar"><?php echo $user_initials; ?></div>
          <div class="user-info">
            <span class="user-name-display"><?php echo $user_first_name; ?></span>
          </div>
          <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>

        <!-- Profile Dropdown Menu -->
        <div class="profile-dropdown" id="profileDropdown">
          <div class="dropdown-header">
            <p class="dropdown-header-title"><?php echo $user_name; ?></p>
            <p class="dropdown-header-subtitle"><?php echo $user_email; ?></p>
          </div>
          <div class="dropdown-menu">
            <a href="/book-hub/src/views/profile.php" class="dropdown-item">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              My Profile
            </a>
            <a href="#" class="dropdown-item">
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
      <?php else: ?>
      <!-- Not Logged In - Show Login/Register -->
      <div class="nav-cta">
        <a href="/book-hub/public/login.html" class="btn btn-outline">Sign In</a>
        <a href="/book-hub/public/register.html" class="btn btn-secondary">Get Started</a>
      </div>
      <?php endif; ?>

      <button class="mobile-menu-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
      <a href="<?php echo $is_logged_in ? '/book-hub/src/views/user.php' : '/book-hub/public/index.php'; ?>">Home</a>
      <a href="/book-hub/public/books.php">Books</a>
      <a href="/book-hub/public/about.php">About Us</a>
      <a href="/book-hub/public/gallery.php">Gallery</a>
      <a href="/book-hub/public/contact.php">Contact</a>
      
      <?php if ($is_logged_in): ?>
      <div class="dropdown-divider" style="margin: 1rem 0;"></div>
      <a href="/book-hub/src/views/profile.php">My Profile</a>
      <a href="#">My Books</a>
      <a href="#">Favorites</a>
      <a href="#">Settings</a>
      <a href="/book-hub/src/handlers/logout-handler.php" style="color: var(--destructive);">Logout</a>
      <?php else: ?>
      <div style="display: flex; flex-direction: column; gap: 0.5rem; padding-top: 1rem;">
        <a href="/book-hub/public/login.html" class="btn btn-outline" style="width: 100%;">Sign In</a>
        <a href="/BOOKHUB/book-hub-central/public/register.html" class="btn btn-secondary" style="width: 100%;">Get Started</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<?php if ($is_logged_in): ?>
<style>
  /* User Navigation Styles */
  .user-nav-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
  }

  .notification-btn {
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.625rem;
    border-radius: 50%;
    transition: all 0.3s ease;
    color: var(--foreground);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .notification-btn:hover {
    background: var(--muted);
    color: var(--primary);
  }

  .notification-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: var(--secondary);
    color: white;
    font-size: 0.65rem;
    min-width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    padding: 0 4px;
    border: 2px solid var(--background);
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

  .user-profile-btn:hover {
    background: var(--muted);
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .user-profile-btn.active {
    background: var(--muted);
    border-color: var(--primary);
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

    .notification-btn {
      padding: 0.5rem;
    }
  }
</style>

<script>
  function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    const button = document.querySelector('.user-profile-btn');
    
    dropdown.classList.toggle('active');
    button.classList.toggle('active');
  }

  function toggleNotifications() {
    alert('No new notifications at this time.');
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
<?php endif; ?>
