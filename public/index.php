<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="BOOK HUB - Discover your next great read. Rent physical books or buy digital editions instantly.">
  <title>BOOK HUB - Discover Your Next Great Read</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
  <link rel="stylesheet" href="static/css/books.css">
  <link rel="stylesheet" href="static/css/home.css">
</head>
<body class="home-page">
  <!-- Success Message Container -->
  <div id="message-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;"></div>
  
  <?php require_once __DIR__ . '/../src/components/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-bg">
      <img src="assets/images/hero-library.jpg" alt="Library">
      <div class="hero-overlay"></div>
    </div>

    <div class="hero-content container">
      <h1>Discover Your Next Great Read</h1>
      <p>Rent physical books or buy digital editions instantly</p>

      <div class="hero-cta">
        <a href="/BOOKHUB/book-hub-central/public/books.php" class="btn btn-secondary btn-lg">Browse Books</a>
        <?php if (!$is_logged_in): ?>
        <a href="/BOOKHUB/book-hub-central/public/register.html" class="btn btn-outline btn-lg" style="border-color: white; color: white;">Get Started</a>
        <?php endif; ?>
      </div>

      <!-- Search Bar -->
      <div class="hero-search">
        <div class="search-box">
          <input type="text" placeholder="Search for books, authors, or genres...">
          <button class="btn btn-secondary btn-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
            Search
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats-section">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
          </svg>
          <h3>10,000+</h3>
          <p>Books Available</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <h3>5,000+</h3>
          <p>Happy Readers</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          <h3>3,000+</h3>
          <p>Digital Titles</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
          <h3>4.9/5</h3>
          <p>Average Rating</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Featured Books -->
  <section>
    <div class="container">
      <div class="section-header">
        <h2>Featured Books</h2>
        <p>Discover our handpicked selection of must-read titles</p>
      </div>

      <div class="books-grid">
        <!-- Book Card 1 -->
        <div class="book-card">
          <div class="book-card-image">
            <img src="assets/images/book-1.jpg" alt="Fiction Fomen">
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
              <button class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                  <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
                Buy
              </button>
            </div>
          </div>
        </div>

        <!-- Book Card 2 -->
        <div class="book-card">
          <div class="book-card-image">
            <img src="assets/images/book-2.jpg" alt="Nook">
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
              <button class="btn btn-accent btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                </svg>
                Rent
              </button>
            </div>
          </div>
        </div>

        <!-- Book Card 3 -->
        <div class="book-card">
          <div class="book-card-image">
            <img src="assets/images/book-3.jpg" alt="Mystic Tales">
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
              <button class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                  <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
                Buy
              </button>
            </div>
          </div>
        </div>

        <!-- Book Card 4 -->
        <div class="book-card">
          <div class="book-card-image">
            <img src="assets/images/book-4.jpg" alt="Science Wonders">
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
              <button class="btn btn-accent btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                </svg>
                Rent
              </button>
            </div>
          </div>
        </div>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="books.html" class="btn btn-primary btn-lg">View All Books</a>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="bg-muted">
    <div class="container">
      <div class="section-header">
        <h2>Why Choose BOOK HUB?</h2>
        <p>Experience the best in book rental and digital purchases</p>
      </div>

      <div class="benefits-grid">
        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
            </svg>
          </div>
          <h3>Vast Collection</h3>
          <p>Access thousands of books across all genres and categories</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
              <polyline points="17 6 23 6 23 12"/>
            </svg>
          </div>
          <h3>Easy Rental</h3>
          <p>Simple rental process with flexible periods and affordable rates</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/>
              <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
          </div>
          <h3>Instant Downloads</h3>
          <p>Buy digital books and download them instantly in PDF format</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"/>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
          </div>
          <h3>Affordable Prices</h3>
          <p>Competitive pricing for both rentals and digital purchases</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="container">
      <h2>Ready to Start Your Reading Journey?</h2>
      <p>Join thousands of readers and get access to our vast collection of books today</p>
      <a href="/BOOKHUB/book-hub-central/public/register.html" class="btn btn-secondary btn-lg">Sign Up Now</a>
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/home.js"></script>
  
  <!-- Message Display Script -->
  <script>
  function displayMessages() {
    const params = new URLSearchParams(window.location.search);
    const messageContainer = document.getElementById('message-container');
    const success = params.get('success');
    if (success) {
      messageContainer.innerHTML = `<div class="alert alert-success" style="padding: 16px 20px; background: #10b981; color: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); font-size: 15px;">${decodeURIComponent(success)}</div>`;
      params.delete('success');
      window.history.replaceState({}, '', `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`);
      setTimeout(() => {
        const alert = messageContainer.querySelector('.alert');
        if (alert) {
          alert.style.opacity = '0';
          alert.style.transition = 'opacity 0.3s';
          setTimeout(() => messageContainer.innerHTML = '', 300);
        }
      }, 4000);
    }
  }
  document.addEventListener('DOMContentLoaded', displayMessages);
  </script>
</body>
</html>
