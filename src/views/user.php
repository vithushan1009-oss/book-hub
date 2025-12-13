<?php
session_start();
require_once __DIR__ . '/../config.php';

if(!isset($_SESSION["user_id"])) {
    header("Location: /book-hub/public/login.html");
    exit();
}

$user_id = $_SESSION["user_id"];
$conn = getDbConnection();

// Get success/error messages from session or URL
$success_message = "";
$error_message = "";
if(isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
} elseif(isset($_GET['success'])) {
    $success_message = $_GET['success'];
}
if(isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
} elseif(isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

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

// Fetch recommended books (latest 4 active books)
$recommended_books_query = "SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, created_at FROM books WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4";
$recommended_books_result = $conn->query($recommended_books_query);

// Fetch user's purchased books
$purchased_books_query = "SELECT b.id, b.title, b.author, b.isbn, b.genre, b.description, b.book_type, p.purchase_price, p.created_at as purchase_date, p.download_count, p.max_downloads 
                         FROM purchases p 
                         JOIN books b ON p.book_id = b.id 
                         WHERE p.user_id = ? AND p.status = 'completed' 
                         ORDER BY p.created_at DESC";
$purchased_books_stmt = $conn->prepare($purchased_books_query);
$purchased_books_stmt->bind_param("i", $user_id);
$purchased_books_stmt->execute();
$purchased_books_result = $purchased_books_stmt->get_result();

// Fetch user's current rentals
$rented_books_query = "SELECT b.id, b.title, b.author, b.isbn, b.genre, b.description, b.book_type, r.daily_rate, r.start_date, r.end_date, r.status, r.created_at as rental_date,
                             DATEDIFF(r.end_date, CURDATE()) as days_remaining
                         FROM rentals r 
                         JOIN books b ON r.book_id = b.id 
                         WHERE r.user_id = ? AND r.status IN ('active', 'overdue') 
                         ORDER BY r.created_at DESC";
$rented_books_stmt = $conn->prepare($rented_books_query);
$rented_books_stmt->bind_param("i", $user_id);
$rented_books_stmt->execute();
$rented_books_result = $rented_books_stmt->get_result();
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

  <!-- Success/Error Messages -->
  <?php if($success_message): ?>
    <div id="message-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
      <div class="alert alert-success" style="margin: 0;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
      </div>
    </div>
  <?php endif; ?>
  <?php if($error_message): ?>
    <div id="message-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
      <div class="alert alert-error" style="margin: 0;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
      </div>
    </div>
  <?php endif; ?>

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
        <?php if($recommended_books_result && $recommended_books_result->num_rows > 0): ?>
          <?php while($book = $recommended_books_result->fetch_assoc()): ?>
            <div class="book-card">
              <div class="book-card-image">
                <img src="/book-hub/src/handlers/book-image.php?id=<?php echo (int)$book['id']; ?>" 
                     alt="<?php echo htmlspecialchars($book['title']); ?>"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'400\'%3E%3Crect fill=\'%23ddd\' width=\'300\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                <?php if($book['book_type'] === 'physical'): ?>
                  <span class="book-badge badge-rent">For Rent</span>
                <?php else: ?>
                  <span class="book-badge badge-buy">Buy Now</span>
                <?php endif; ?>
              </div>
              <div class="book-card-content">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
                <?php if($book['genre']): ?>
                  <p class="genre" style="font-size: 0.85rem; color: var(--muted-foreground); margin-top: 0.25rem;">
                    <?php echo htmlspecialchars($book['genre']); ?>
                  </p>
                <?php endif; ?>
                <div class="book-rating">
                  <?php 
                  $rating = 4.5;
                  $full_stars = floor($rating);
                  $has_half = ($rating - $full_stars) >= 0.5;
                  for($i = 0; $i < $full_stars; $i++): ?>
                    <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <?php endfor; ?>
                  <?php if($has_half): ?>
                    <svg class="star filled" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <?php endif; ?>
                  <?php 
                  $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);
                  for($i = 0; $i < $empty_stars; $i++): ?>
                    <svg class="star empty" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <?php endfor; ?>
                  <span>(<?php echo number_format($rating, 1); ?>)</span>
                </div>
                <div class="book-footer">
                  <div class="book-price">
                    <?php if($book['book_type'] === 'physical' && $book['rental_price_per_day']): ?>
                      LKR <?php echo number_format($book['rental_price_per_day'], 2); ?><span>/day</span>
                    <?php elseif($book['book_type'] === 'online' && $book['purchase_price']): ?>
                      LKR <?php echo number_format($book['purchase_price'], 2); ?>
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </div>
                  <?php if($book['book_type'] === 'physical'): ?>
                    <button class="btn btn-accent btn-sm" onclick="window.location.href='/book-hub/public/books.php'">Rent</button>
                  <?php else: ?>
                    <button class="btn btn-secondary btn-sm" onclick="window.location.href='/book-hub/public/books.php'">Buy</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <p class="muted">No recommended books available at the moment.</p>
            <a href="/book-hub/public/books.php" class="btn btn-primary">Browse All Books</a>
          </div>
        <?php endif; ?>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="/book-hub/public/books.php" class="btn btn-primary btn-lg">View All Books</a>
      </div>
    </div>
  </section>

  <!-- My Books Section -->
  <section style="padding: 4rem 0; background-color: var(--card);">
    <div class="container">
      <div class="section-header">
        <h2>My Books</h2>
        <p>Your purchased and rented books</p>
      </div>

      <!-- Book Tabs -->
      <div class="book-tabs" style="margin-bottom: 2rem;">
        <div class="tab-buttons" style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border);">
          <button class="tab-btn active" onclick="showTab('purchased')" style="background: none; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius); cursor: pointer; font-weight: 500; color: var(--foreground); border-bottom: 2px solid var(--primary);">Purchased Books</button>
          <button class="tab-btn" onclick="showTab('rented')" style="background: none; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius); cursor: pointer; font-weight: 500; color: var(--muted-foreground);">Rented Books</button>
        </div>

        <!-- Purchased Books Tab -->
        <div id="purchased-tab" class="tab-content active">
          <div class="books-grid">
            <?php if($purchased_books_result && $purchased_books_result->num_rows > 0): ?>
              <?php while($book = $purchased_books_result->fetch_assoc()): ?>
                <div class="book-card">
                  <div class="book-card-image">
                    <img src="/book-hub/src/handlers/book-image.php?id=<?php echo (int)$book['id']; ?>&t=<?php echo time(); ?>"
                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'400\'%3E%3Crect fill=\'%23ddd\' width=\'300\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                    <span class="book-badge badge-purchased">Purchased</span>
                  </div>
                  <div class="book-card-content">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="book-genre"><?php echo htmlspecialchars($book['genre']); ?></p>
                    <div class="book-price">
                      <span class="price">Purchased: $<?php echo number_format($book['purchase_price'], 2); ?></span>
                      <br>
                      <small class="muted">Downloads: <?php echo $book['download_count']; ?>/<?php echo $book['max_downloads']; ?></small>
                    </div>
                    <div class="book-actions">
                      <button class="btn btn-primary btn-sm" onclick="downloadBook(<?php echo $book['id']; ?>)">
                        <i class="fas fa-download"></i> Download
                      </button>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p class="muted">You haven't purchased any books yet.</p>
                <a href="/book-hub/public/books.php" class="btn btn-primary">Browse Books</a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Rented Books Tab -->
        <div id="rented-tab" class="tab-content" style="display: none;">
          <div class="books-grid">
            <?php if($rented_books_result && $rented_books_result->num_rows > 0): ?>
              <?php while($book = $rented_books_result->fetch_assoc()): ?>
                <div class="book-card">
                  <div class="book-card-image">
                    <img src="/book-hub/src/handlers/book-image.php?id=<?php echo (int)$book['id']; ?>&t=<?php echo time(); ?>"
                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'400\'%3E%3Crect fill=\'%23ddd\' width=\'300\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'16\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                    <span class="book-badge <?php echo $book['status'] === 'overdue' ? 'badge-overdue' : 'badge-rented'; ?>">
                      <?php echo $book['status'] === 'overdue' ? 'Overdue' : 'Rented'; ?>
                    </span>
                  </div>
                  <div class="book-card-content">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="book-genre"><?php echo htmlspecialchars($book['genre']); ?></p>
                    <div class="book-price">
                      <span class="price">Rental: $<?php echo number_format($book['daily_rate'], 2); ?>/day</span>
                      <br>
                      <small class="muted">
                        Due: <?php echo date('M j, Y', strtotime($book['end_date'])); ?>
                        <?php if($book['days_remaining'] >= 0): ?>
                          (<?php echo $book['days_remaining']; ?> days left)
                        <?php else: ?>
                          (<?php echo abs($book['days_remaining']); ?> days overdue)
                        <?php endif; ?>
                      </small>
                    </div>
                    <div class="book-actions">
                      <?php if($book['status'] === 'active'): ?>
                        <button class="btn btn-secondary btn-sm" onclick="returnBook(<?php echo $book['id']; ?>)">
                          <i class="fas fa-undo"></i> Return Book
                        </button>
                      <?php elseif($book['status'] === 'overdue'): ?>
                        <button class="btn btn-danger btn-sm" onclick="returnBook(<?php echo $book['id']; ?>)">
                          <i class="fas fa-exclamation-triangle"></i> Return Overdue
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p class="muted">You haven't rented any books yet.</p>
                <a href="/book-hub/public/books.php" class="btn btn-primary">Browse Books</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
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

    function downloadBook(bookId) {
      // For now, redirect to a download handler
      // In production, this would check download limits and serve the file
      window.location.href = '/book-hub/src/handlers/download-book.php?id=' + bookId;
    }

    function returnBook(bookId) {
      if (confirm('Are you sure you want to return this book?')) {
        // Redirect to return book handler
        window.location.href = '/book-hub/src/handlers/rent-book-handler.php?action=return&book_id=' + bookId;
      }
    }

    function showTab(tabName) {
      // Hide all tabs
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => tab.style.display = 'none');
      
      // Remove active class from all buttons
      const buttons = document.querySelectorAll('.tab-btn');
      buttons.forEach(btn => {
        btn.classList.remove('active');
        btn.style.borderBottom = 'none';
        btn.style.color = 'var(--muted-foreground)';
      });
      
      // Show selected tab
      document.getElementById(tabName + '-tab').style.display = 'block';
      
      // Add active class to clicked button
      const activeButton = document.querySelector(`[onclick="showTab('${tabName}')"]`);
      activeButton.classList.add('active');
      activeButton.style.borderBottom = '2px solid var(--primary)';
      activeButton.style.color = 'var(--foreground)';
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

