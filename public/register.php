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
  <title>Sign Up — BOOK HUB</title>
  
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
          <h2>Join BOOK HUB Today</h2>
          <p>Create your account and start exploring thousands of amazing books</p>
          <div class="auth-features">
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Free account creation</span>
            </div>
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Access to exclusive content</span>
            </div>
            <div class="auth-feature">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Track your reading progress</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Register Form -->
      <div class="auth-form-container">
        <div class="auth-form-wrapper">
          <div class="auth-header">
            <h1>Create Account</h1>
            <p>Fill in the details below to create your account</p>
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

          <form class="auth-form" action="/book-hub/src/handlers/register-handler.php" method="POST">
            <div class="form-row">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Kamal" required>
              </div>
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Kumar" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
              <span class="form-hint">Must be at least 8 characters</span>
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm Password</label>
              <div class="password-wrapper">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="checkbox-label">
                <input type="checkbox" name="terms" required>
                <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
              </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
          </form>

          <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="static/js/common.js"></script>
  <script src="static/js/auth.js"></script>
</body>
</html>
