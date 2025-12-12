<?php
// Check if user is already logged in
session_start();
if(isset($_SESSION["user_id"])) {
    header("Location: /book-hub/src/views/user.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — BOOK HUB</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
  <link rel="stylesheet" href="static/css/auth.css">
</head>
<body>
  <!-- Navigation -->
  <nav>
    <div class="container">
      <div class="nav-content">
        <a href="index.php" class="nav-logo">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
          </svg>
          <span>BOOK <span class="accent">HUB</span></span>
        </a>

        <ul class="nav-links">
          <li><a href="index.php">Home</a></li>
          <li><a href="books.php">Books</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="gallery.php">Gallery</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>

        <div class="nav-cta">
          <a href="login.php" class="btn btn-outline">Sign In</a>
          <a href="register.php" class="btn btn-secondary">Get Started</a>
        </div>

        <button class="mobile-menu-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
      </div>

      <div class="mobile-menu">
        <a href="index.php">Home</a>
        <a href="books.php">Books</a>
        <a href="about.php">About Us</a>
        <a href="gallery.php">Gallery</a>
        <a href="contact.php">Contact</a>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; padding-top: 1rem;">
          <a href="login.php" class="btn btn-outline" style="width: 100%;">Sign In</a>
          <a href="register.php" class="btn btn-secondary" style="width: 100%;">Get Started</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Auth Container -->
  <div class="auth-container">
    <div class="auth-wrapper">
      <!-- Left Side - Image & Info -->
      <div class="auth-image">
        <div class="auth-overlay"></div>
        <div class="auth-content">
          <h2>Welcome Back to BOOK HUB</h2>
          <p>Continue your reading journey and discover thousands of amazing books</p>
          <div class="auth-features">
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
              </svg>
              <span>Thousands of Books Available</span>
            </div>
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              <span>Instant Digital Downloads</span>
            </div>
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
              </svg>
              <span>Personalized Recommendations</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Login Form -->
      <div class="auth-form-container">
        <div class="auth-form-wrapper">
          <div class="auth-header">
            <h1>Sign In</h1>
            <p>Welcome back! Please enter your credentials</p>
          </div>

          <div id="message-container">
            <?php if(isset($_GET['success'])): ?>
              <div class="alert alert-success" style="padding: 12px 16px; background: #10b981; color: white; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($_GET['success']); ?>
              </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
              <div class="alert alert-error" style="padding: 12px 16px; background: #ef4444; color: white; border-radius: 8px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($_GET['error']); ?>
              </div>
            <?php endif; ?>
          </div>

          <form class="auth-form" action="/book-hub/src/handlers/login-handler.php" method="POST">
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="checkbox-label">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
              </label>
              <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </form>

          <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Create Account</a></p>
          </div>

          <div class="auth-divider">
            <span>Admin Access</span>
          </div>

          <a href="admin-login.php" class="btn btn-outline btn-block" style="margin-top: 0.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Admin Login
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="static/js/common.js"></script>
  <script src="static/js/auth.js"></script>
</body>
</html>
