<?php
session_start();
require_once __DIR__ . '/../config.php';

if(!isset($_SESSION["user_id"])) {
    header("Location: /book-hub/public/login.html");
    exit();
}

$user_id = $_SESSION["user_id"];
$conn = getDbConnection();

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$user_email = htmlspecialchars($user['email']);
$user_initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — BOOK HUB</title>
  
  <link rel="stylesheet" href="/book-hub/public/static/css/variables.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/base.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/components.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/navigation.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/footer.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/books.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/home.css">
  <style>
    /* Page Layout Fixes */
    body.home-page {
      overflow-x: hidden;
    }
    
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

    /* Dashboard Stats */
    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin: 0 auto;
      max-width: 1200px;
      width: 100%;
    }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.75rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .stat-card:hover {
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
      transform: translateY(-4px);
      border-color: var(--primary);
    }

    .stat-card:hover::before {
      opacity: 1;
    }

    .stat-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.25rem;
    }

    .stat-icon.primary {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(102, 126, 234, 0.05));
      color: var(--primary);
    }

    .stat-icon.secondary {
      background: linear-gradient(135deg, rgba(237, 100, 166, 0.15), rgba(237, 100, 166, 0.05));
      color: var(--secondary);
    }

    .stat-icon.accent {
      background: linear-gradient(135deg, rgba(255, 159, 28, 0.15), rgba(255, 159, 28, 0.05));
      color: var(--accent);
    }

    .stat-value {
      font-size: 2.25rem;
      font-weight: 700;
      color: var(--foreground);
      margin-bottom: 0.5rem;
      line-height: 1;
    }

    .stat-label {
      font-size: 0.9375rem;
      color: var(--muted-foreground);
      font-weight: 500;
    }

    /* Mobile Responsive */
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

      .dashboard-stats {
        grid-template-columns: 1fr;
      }
    }

    /* Additional Layout Improvements */
    section {
      position: relative;
      overflow: visible;
    }
    
    .container {
      position: relative;
      z-index: 1;
    }
    
    .hero {
      margin-bottom: 0;
    }
    
    .section-header {
      margin-bottom: 2rem;
    }
    
    .books-grid {
      margin-bottom: 2rem;
    }
  </style>
</head>
<?php // Add home-page class so logged-in home uses same styles as public index ?>
<body class="home-page">
  <?php require_once __DIR__ . '/../components/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-bg">
      <img src="/book-hub/public/assets/images/hero-library.jpg" alt="Library">
      <div class="hero-overlay"></div>
    </div>

    <div class="hero-content container">
      <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 📚</h1>
      <p>Continue your reading journey and discover amazing books</p>

      <div class="hero-cta">
        <a href="/book-hub/public/books.php" class="btn btn-secondary btn-lg">Browse Books</a>
        <a href="/book-hub/src/views/profile.php" class="btn btn-outline btn-lg" style="border-color: white; color: white;">My Profile</a>
      </div>
    </div>
  </section>

  <!-- Featured Books -->
  <section style="padding: 4rem 0;">
    <div class="container">
      <div class="section-header">
        <h2>Recommended for You</h2>
        <p>Based on your reading history</p>
      </div>

      <div class="books-grid">
        <div class="book-card">
          <div class="book-card-image">
            <img src="/book-hub/public/assets/images/book-1.jpg" alt="Fiction Fomen">
            <span class="book-badge badge-buy">Buy Now</span>
          </div>
          <div class="book-card-content">
            <h3>Fiction Fomen</h3>
            <p class="author">Shen Gerdings</p>
            <div class="book-rating">
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>(5.0)</span>
            </div>
            <div class="book-footer">
              <div class="book-price">$12.99</div>
              <button class="btn btn-secondary btn-sm">Buy</button>
            </div>
          </div>
        </div>

        <div class="book-card">
          <div class="book-card-image">
            <img src="/book-hub/public/assets/images/book-2.jpg" alt="Nook">
            <span class="book-badge badge-rent">For Rent</span>
          </div>
          <div class="book-card-content">
            <h3>Nook</h3>
            <p class="author">Bab Giuing</p>
            <div class="book-rating">
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star empty" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>(4.0)</span>
            </div>
            <div class="book-footer">
              <div class="book-price">$3.99<span>/week</span></div>
              <button class="btn btn-accent btn-sm">Rent</button>
            </div>
          </div>
        </div>

        <div class="book-card">
          <div class="book-card-image">
            <img src="/book-hub/public/assets/images/book-3.jpg" alt="Mystic Tales">
            <span class="book-badge badge-buy">Buy Now</span>
          </div>
          <div class="book-card-content">
            <h3>Mystic Tales</h3>
            <p class="author">Fantasy Author</p>
            <div class="book-rating">
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>(5.0)</span>
            </div>
            <div class="book-footer">
              <div class="book-price">$15.99</div>
              <button class="btn btn-secondary btn-sm">Buy</button>
            </div>
          </div>
        </div>

        <div class="book-card">
          <div class="book-card-image">
            <img src="/book-hub/public/assets/images/book-4.jpg" alt="Science Wonders">
            <span class="book-badge badge-rent">For Rent</span>
          </div>
          <div class="book-card-content">
            <h3>Science Wonders</h3>
            <p class="author">Knowledge Seeker</p>
            <div class="book-rating">
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <svg class="star empty" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <span>(4.0)</span>
            </div>
            <div class="book-footer">
              <div class="book-price">$4.99<span>/week</span></div>
              <button class="btn btn-accent btn-sm">Rent</button>
            </div>
          </div>
        </div>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="/book-hub/public/books.php" class="btn btn-primary btn-lg">View All Books</a>
      </div>
    </div>
  </section>

  <?php require_once __DIR__ . '/../components/footer.php'; ?>

  <script src="/book-hub/public/static/js/common.js"></script>
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      const button = document.querySelector('.user-profile-btn');
      
      dropdown.classList.toggle('active');
      button.classList.toggle('active');
    }

    function toggleNotifications() {
      // Placeholder for notifications functionality
      alert('No new notifications at this time.');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('profileDropdown');
      const button = document.querySelector('.user-profile-btn');
      
      if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('active');
        button.classList.remove('active');
      }
    });

    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        mobileMenu.classList.toggle('active');
      });
    }
  </script>
</body>
</html>

