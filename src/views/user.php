<?php
require_once __DIR__ . '/../config.php';
session_start();

if(!isset($_SESSION["user_id"])) {
    header("Location: ../../login.html");
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
  
  <link rel="stylesheet" href="../public/static/css/variables.css">
  <link rel="stylesheet" href="../public/static/css/base.css">
  <link rel="stylesheet" href="../public/static/css/components.css">
  <link rel="stylesheet" href="../public/static/css/navigation.css">
  <link rel="stylesheet" href="../public/static/css/footer.css">
  <link rel="stylesheet" href="../public/static/css/home.css">
  <style>
    /* User Navigation Styles */
    .user-nav-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .notification-btn {
      position: relative;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: 50%;
      transition: background 0.3s;
    }

    .notification-btn:hover {
      background: var(--muted);
    }

    .notification-badge {
      position: absolute;
      top: 0;
      right: 0;
      background: var(--secondary);
      color: white;
      font-size: 0.75rem;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .user-profile-btn {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      background: none;
      border: 1px solid var(--border);
      padding: 0.5rem 1rem;
      border-radius: 2rem;
      cursor: pointer;
      transition: all 0.3s;
      position: relative;
    }

    .user-profile-btn:hover {
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
    }

    .user-info {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      text-align: left;
    }

    .user-name {
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--foreground);
    }

    .user-email {
      font-size: 0.75rem;
      color: var(--muted-foreground);
    }

    .dropdown-arrow {
      width: 16px;
      height: 16px;
      transition: transform 0.3s;
    }

    .user-profile-btn.active .dropdown-arrow {
      transform: rotate(180deg);
    }

    .profile-dropdown {
      position: absolute;
      top: calc(100% + 0.5rem);
      right: 0;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-xl);
      min-width: 280px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s;
      z-index: 1000;
    }

    .profile-dropdown.active {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-header {
      padding: 1rem;
      border-bottom: 1px solid var(--border);
    }

    .dropdown-header-title {
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--foreground);
      margin-bottom: 0.25rem;
    }

    .dropdown-header-subtitle {
      font-size: 0.75rem;
      color: var(--muted-foreground);
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
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: var(--muted);
    }

    .dropdown-item svg {
      width: 18px;
      height: 18px;
      stroke: var(--muted-foreground);
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
    }

    .dropdown-item.logout svg {
      stroke: var(--destructive);
    }

    /* Dashboard Stats */
    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      transition: all 0.3s;
    }

    .stat-card:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
    }

    .stat-icon.primary {
      background: rgba(102, 126, 234, 0.1);
      color: var(--primary);
    }

    .stat-icon.secondary {
      background: rgba(237, 100, 166, 0.1);
      color: var(--secondary);
    }

    .stat-icon.accent {
      background: rgba(255, 159, 28, 0.1);
      color: var(--accent);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--foreground);
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.875rem;
      color: var(--muted-foreground);
    }

    @media (max-width: 768px) {
      .user-info {
        display: none;
      }

      .user-profile-btn {
        padding: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav>
    <div class="container">
      <div class="nav-content">
        <a href="user" class="nav-logo">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
          </svg>
          <span>BOOK <span class="accent">HUB</span></span>
        </a>

        <ul class="nav-links">
          <li><a href="user">Home</a></li>
          <li><a href="../public/books.html">Books</a></li>
          <li><a href="../public/about.html">About Us</a></li>
          <li><a href="../public/gallery.html">Gallery</a></li>
          <li><a href="../public/contact.html">Contact</a></li>
        </ul>

        <div class="user-nav-actions">
          <!-- Notification Button -->
          <button class="notification-btn" onclick="alert('No new notifications')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notification-badge">3</span>
          </button>

          <!-- User Profile Dropdown -->
          <div style="position: relative;">
            <button class="user-profile-btn" onclick="toggleProfileDropdown()">
              <div class="user-avatar"><?php echo $user_initials; ?></div>
              <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($user['first_name']); ?></span>
                <span class="user-email"><?php echo $user_email; ?></span>
              </div>
              <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>

            <div class="profile-dropdown" id="profileDropdown">
              <div class="dropdown-header">
                <div class="dropdown-header-title"><?php echo $user_name; ?></div>
                <div class="dropdown-header-subtitle"><?php echo $user_email; ?></div>
              </div>
              <div class="dropdown-menu">
                <a href="profile" class="dropdown-item">
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
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6"/>
                    <path d="M17 7l-10 10"/>
                    <path d="M7 7l10 10"/>
                  </svg>
                  Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="../handlers/logout-handler.php" class="dropdown-item logout">
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
        </div>

        <button class="mobile-menu-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
      </div>

      <div class="mobile-menu">
        <a href="user">Home</a>
        <a href="../public/books.html">Books</a>
        <a href="../public/about.html">About Us</a>
        <a href="../public/gallery.html">Gallery</a>
        <a href="../public/contact.html">Contact</a>
        <div class="dropdown-divider" style="margin: 1rem 0;"></div>
        <a href="profile">My Profile</a>
        <a href="#">My Books</a>
        <a href="#">Settings</a>
        <a href="../handlers/logout-handler.php" style="color: var(--destructive);">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-bg">
      <img src="../public/assets/images/hero-library.jpg" alt="Library">
      <div class="hero-overlay"></div>
    </div>

    <div class="hero-content container">
      <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 📚</h1>
      <p>Continue your reading journey and discover amazing books</p>

      <div class="hero-cta">
        <a href="../public/books.html" class="btn btn-secondary btn-lg">Browse Books</a>
        <a href="profile" class="btn btn-outline btn-lg" style="border-color: white; color: white;">My Profile</a>
      </div>
    </div>
  </section>

  <!-- Dashboard Stats -->
  <section>
    <div class="container">
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
            </svg>
          </div>
          <div class="stat-value">12</div>
          <div class="stat-label">Books Rented</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
          </div>
          <div class="stat-value">8</div>
          <div class="stat-label">Books Purchased</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon accent">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </div>
          <div class="stat-value">5</div>
          <div class="stat-label">Favorites</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured Books -->
  <section>
    <div class="container">
      <div class="section-header">
        <h2>Recommended for You</h2>
        <p>Based on your reading history</p>
      </div>

      <div class="books-grid">
        <div class="book-card">
          <div class="book-card-image">
            <img src="../public/assets/images/book-1.jpg" alt="Fiction Fomen">
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
            <img src="../public/assets/images/book-2.jpg" alt="Nook">
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
            <img src="../public/assets/images/book-3.jpg" alt="Mystic Tales">
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
            <img src="../public/assets/images/book-4.jpg" alt="Science Wonders">
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
        <a href="../public/books.html" class="btn btn-primary btn-lg">View All Books</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-bottom">
        <p>&copy; 2025 BOOK HUB. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script src="../public/static/js/common.js"></script>
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      const button = document.querySelector('.user-profile-btn');
      
      dropdown.classList.toggle('active');
      button.classList.toggle('active');
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
  </script>
</body>
</html>
