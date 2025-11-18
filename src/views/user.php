<?php
require_once __DIR__ . '/../config.php';
session_start();

if(!isset($_SESSION["user_id"])) {
    header("Location:login.html");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — BOOK HUB</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="../static/css/variables.css">
  <link rel="stylesheet" href="../static/css/base.css">
  <link rel="stylesheet" href="../static/css/navigation.css">
  <link rel="stylesheet" href="../static/css/components.css">
  <link rel="stylesheet" href="../static/css/footer.css">
  <link rel="stylesheet" href="../static/css/home.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-container">
      <a href="user" class="nav-brand">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
        </svg>
        <span>BOOK HUB</span>
      </a>

      <div class="nav-menu">
        <a href="user" class="nav-link">Home</a>
        <a href="books.html" class="nav-link">Books</a>
        <a href="about.html" class="nav-link">About</a>
        <a href="gallery.html" class="nav-link">Gallery</a>
        <a href="contact.html" class="nav-link">Contact</a>
      </div>

      <div class="nav-actions">
        <div class="user-menu">
          <button class="user-button">
            <i class='fas fa-user-circle' style='font-size:28px'></i>
            <span><?php echo htmlspecialchars($user['first_name']); ?></span>
          </button>
          <div class="user-dropdown">
            <a href="profile"><i class="fas fa-user"></i> Profile</a>
            <a href="backend/logout-handler.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>

      <button class="mobile-toggle" aria-label="Toggle mobile menu">
        <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>
  </nav>

  <main>
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
          <p class="hero-subtitle">Continue your reading journey and discover new books</p>
          <div class="hero-actions">
            <a href="books.html" class="btn btn-primary btn-lg">Browse Books</a>
            <a href="profile" class="btn btn-ghost btn-lg">My Profile</a>
          </div>
        </div>
      </div>
    </section>

    <section class="featured-books">
      <div class="container">
        <h2 class="section-title">Featured Books</h2>
        <div class="books-grid">
          <div class="book-card">
            <img src="../assets/images/book-1.jpg" alt="Book Cover" class="book-cover" onerror="this.src='https://via.placeholder.com/200x300?text=Book+Cover'">
            <h3 class="book-title">The Great Adventure</h3>
            <p class="book-author">John Doe</p>
            <button class="btn btn-primary btn-sm">Rent Now</button>
          </div>
          <div class="book-card">
            <img src="../assets/images/book-2.jpg" alt="Book Cover" class="book-cover" onerror="this.src='https://via.placeholder.com/200x300?text=Book+Cover'">
            <h3 class="book-title">Mystery Island</h3>
            <p class="book-author">Jane Smith</p>
            <button class="btn btn-primary btn-sm">Rent Now</button>
          </div>
          <div class="book-card">
            <img src="../assets/images/book-3.jpg" alt="Book Cover" class="book-cover" onerror="this.src='https://via.placeholder.com/200x300?text=Book+Cover'">
            <h3 class="book-title">Science Wonders</h3>
            <p class="book-author">Dr. Einstein</p>
            <button class="btn btn-primary btn-sm">Rent Now</button>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 BOOK HUB. All rights reserved.</p>
    </div>
  </footer>

  <script src="../static/js/common.js"></script>
  <script src="../static/js/home.js"></script>
</body>
</html>
