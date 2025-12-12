<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session-check.php';

$conn = getDbConnection();

// Fetch featured books (latest 4 books)
$featured_books_query = "SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, created_at FROM books WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4";
$featured_books_result = $conn->query($featured_books_query);

if (!$featured_books_result) {
    $featured_books_result = $conn->query("SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, created_at FROM books WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
}

// Get total book count for stats
$total_books_query = "SELECT COUNT(*) as total FROM books WHERE is_active = 1";
$total_books_result = $conn->query($total_books_query);
$total_books = 0;
if ($total_books_result) {
    $row = $total_books_result->fetch_assoc();
    $total_books = $row['total'];
}

// Get total users count
$total_users_query = "SELECT COUNT(*) as total FROM users WHERE email_verified = 1";
$total_users_result = $conn->query($total_users_query);
$total_users = 0;
if ($total_users_result) {
    $row = $total_users_result->fetch_assoc();
    $total_users = $row['total'];
}

// Get digital books count
$digital_books_query = "SELECT COUNT(*) as total FROM books WHERE is_active = 1 AND book_type = 'online'";
$digital_books_result = $conn->query($digital_books_query);
$digital_books = 0;
if ($digital_books_result) {
    $row = $digital_books_result->fetch_assoc();
    $digital_books = $row['total'];
}

// Get completed rentals count (as a measure of satisfaction)
$completed_rentals_query = "SELECT COUNT(*) as total FROM rentals WHERE status = 'returned'";
$completed_rentals_result = $conn->query($completed_rentals_query);
$completed_rentals = 0;
if ($completed_rentals_result) {
    $row = $completed_rentals_result->fetch_assoc();
    $completed_rentals = $row['total'];
}
?>
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
        <a href="/book-hub/public/books.php" class="btn btn-secondary btn-lg">Browse Books</a>
        <?php if (!$is_logged_in): ?>
        <a href="/book-hub/public/register.html" class="btn btn-outline btn-lg" style="border-color: white; color: white;">Get Started</a>
        <?php else: ?>
        <a href="/book-hub/src/views/user.php" class="btn btn-outline btn-lg" style="border-color: white; color: white;">My Dashboard</a>
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
          <h3><?php echo number_format($total_books); ?>+</h3>
          <p>Books Available</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <h3><?php echo number_format($total_users); ?>+</h3>
          <p>Happy Readers</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          <h3><?php echo number_format($digital_books); ?>+</h3>
          <p>Digital Titles</p>
        </div>
        <div class="stat-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
          <h3><?php echo number_format($completed_rentals); ?>+</h3>
          <p>Books Rented</p>
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
        <?php if($featured_books_result && $featured_books_result->num_rows > 0): ?>
          <?php while($book = $featured_books_result->fetch_assoc()): ?>
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
                  // Simple rating display (can be enhanced with actual ratings later)
                  $rating = 4.5; // Placeholder - can be fetched from database later
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
                  <?php if ($is_logged_in): ?>
                    <?php if($book['book_type'] === 'physical'): ?>
                      <button class="btn btn-accent btn-sm" onclick="openRentModal(<?php echo (int)$book['id']; ?>, '<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>', <?php echo number_format($book['rental_price_per_day'], 2); ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                        </svg>
                        Rent
                      </button>
                    <?php else: ?>
                      <button class="btn btn-secondary btn-sm" onclick="purchaseBook(<?php echo (int)$book['id']; ?>, '<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>', <?php echo number_format($book['purchase_price'], 2); ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                          <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        Buy
                      </button>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="/book-hub/public/login.html" class="btn btn-secondary btn-sm">Login to Rent/Buy</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <p class="muted">No featured books available at the moment.</p>
            <a href="/book-hub/public/books.php" class="btn btn-primary">Browse All Books</a>
          </div>
        <?php endif; ?>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="/book-hub/public/books.php" class="btn btn-primary btn-lg">View All Books</a>
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
      <?php if (!$is_logged_in): ?>
      <a href="/book-hub/public/register.html" class="btn btn-secondary btn-lg">Sign Up Now</a>
      <?php else: ?>
      <a href="/book-hub/public/books.php" class="btn btn-secondary btn-lg">Browse Books</a>
      <?php endif; ?>
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- Rent Modal -->
  <?php if($is_logged_in): ?>
  <div id="rentModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 2rem; border-radius: 8px; max-width: 500px; width: 90%;">
      <h2 style="margin-top: 0; color: var(--primary);">Rent Book</h2>
      <form id="rentForm" method="POST" action="/book-hub/src/handlers/rent-book-handler.php" onsubmit="return validateRentForm()">
        <input type="hidden" name="book_id" id="rent_book_id">
        <input type="hidden" id="rent_price_per_day" value="0">
        
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Book:</label>
          <p id="rent_book_title" style="margin: 0; color: var(--muted-foreground); font-weight: 500;"></p>
        </div>
        
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Start Date: <span style="color: var(--secondary);">*</span></label>
          <input type="date" name="start_date" id="rent_start_date" required 
                 style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; font-size: 1rem;">
          <small id="start_date_error" style="color: #ef4444; display: none; margin-top: 0.25rem;"></small>
        </div>
        
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">End Date: <span style="color: var(--secondary);">*</span></label>
          <input type="date" name="end_date" id="rent_end_date" required 
                 style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; font-size: 1rem;">
          <small id="end_date_error" style="color: #ef4444; display: none; margin-top: 0.25rem;"></small>
        </div>
        
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number: <span style="color: var(--secondary);">*</span></label>
          <input type="tel" name="phone_number" id="rent_phone_number" required 
                 placeholder="e.g., +94 77 123 4567" 
                 pattern="[0-9+\-\s()]{7,20}"
                 style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; font-size: 1rem;">
          <small id="phone_error" style="color: #ef4444; display: none; margin-top: 0.25rem;"></small>
        </div>
        
        <div id="rental_cost_section" style="margin-bottom: 1.5rem; padding: 1rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 8px; border: 1px solid #bae6fd; display: none;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="color: var(--muted-foreground);">Rental Period:</span>
            <span id="rental_days_display" style="font-weight: 500;">0 days</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="color: var(--muted-foreground);">Daily Rate:</span>
            <span id="daily_rate_display" style="font-weight: 500;">LKR 0.00</span>
          </div>
          <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #7dd3fc; padding-top: 0.75rem; margin-top: 0.5rem;">
            <span style="color: var(--primary);">Estimated Total:</span>
            <span id="total_cost_display" style="color: var(--secondary); font-size: 1.1rem;">LKR 0.00</span>
          </div>
        </div>
        
        <div id="form_error" style="display: none; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; color: #dc2626; margin-bottom: 1rem; font-size: 0.9rem;"></div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" onclick="closeRentModal()" class="btn btn-outline">Cancel</button>
          <button type="submit" id="submitRentBtn" class="btn btn-accent" disabled>Submit Rental Request</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/home.js"></script>
  <script src="static/js/books.js"></script>
  
  <script>
    // Get today's date in YYYY-MM-DD format
    function getTodayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }
    
    // Get max date (30 days from today)
    function getMaxDate() {
      const maxDate = new Date();
      maxDate.setDate(maxDate.getDate() + 60);
      const year = maxDate.getFullYear();
      const month = String(maxDate.getMonth() + 1).padStart(2, '0');
      const day = String(maxDate.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }
    
    // Set minimum date to today on page load
    document.addEventListener('DOMContentLoaded', function() {
      const today = getTodayDate();
      const maxDate = getMaxDate();
      const startDateInput = document.getElementById('rent_start_date');
      const endDateInput = document.getElementById('rent_end_date');
      
      if (startDateInput) {
        startDateInput.setAttribute('min', today);
        startDateInput.setAttribute('max', maxDate);
      }
      if (endDateInput) {
        endDateInput.setAttribute('min', today);
        endDateInput.setAttribute('max', maxDate);
      }
    });

    function openRentModal(bookId, bookTitle, pricePerDay) {
      document.getElementById('rent_book_id').value = bookId;
      document.getElementById('rent_book_title').textContent = bookTitle + ' (LKR ' + pricePerDay.toFixed(2) + '/day)';
      document.getElementById('rent_price_per_day').value = pricePerDay;
      document.getElementById('rentModal').style.display = 'flex';
      
      // Reset form
      document.getElementById('rentForm').reset();
      document.getElementById('rental_cost_section').style.display = 'none';
      document.getElementById('submitRentBtn').disabled = true;
      clearAllErrors();
      
      // Set minimum dates
      const today = getTodayDate();
      const maxDate = getMaxDate();
      document.getElementById('rent_start_date').setAttribute('min', today);
      document.getElementById('rent_start_date').setAttribute('max', maxDate);
      document.getElementById('rent_end_date').setAttribute('min', today);
      document.getElementById('rent_end_date').setAttribute('max', maxDate);
      
      // Add event listeners
      document.getElementById('rent_start_date').addEventListener('change', handleStartDateChange);
      document.getElementById('rent_end_date').addEventListener('change', handleEndDateChange);
      document.getElementById('rent_phone_number').addEventListener('input', validatePhone);
    }
    
    function handleStartDateChange() {
      const startDate = document.getElementById('rent_start_date').value;
      const endDateInput = document.getElementById('rent_end_date');
      const today = getTodayDate();
      
      clearError('start_date_error');
      
      if (startDate) {
        if (startDate < today) {
          showError('start_date_error', 'Start date cannot be in the past');
          document.getElementById('rent_start_date').value = '';
          return;
        }
        
        // Set end date minimum to start date
        endDateInput.setAttribute('min', startDate);
        
        // Calculate max end date (30 days from start)
        const startDateObj = new Date(startDate);
        startDateObj.setDate(startDateObj.getDate() + 30);
        const maxEndDate = startDateObj.toISOString().split('T')[0];
        endDateInput.setAttribute('max', maxEndDate);
        
        // If end date is before start date, reset it
        const endDate = endDateInput.value;
        if (endDate && endDate < startDate) {
          endDateInput.value = '';
          clearError('end_date_error');
        }
      }
      
      calculateRentalCost();
    }
    
    function handleEndDateChange() {
      const startDate = document.getElementById('rent_start_date').value;
      const endDate = document.getElementById('rent_end_date').value;
      const today = getTodayDate();
      
      clearError('end_date_error');
      
      if (endDate) {
        if (endDate < today) {
          showError('end_date_error', 'End date cannot be in the past');
          document.getElementById('rent_end_date').value = '';
          return;
        }
        
        if (startDate && endDate < startDate) {
          showError('end_date_error', 'End date must be after start date');
          document.getElementById('rent_end_date').value = '';
          return;
        }
        
        if (startDate) {
          const start = new Date(startDate);
          const end = new Date(endDate);
          const diffTime = Math.abs(end - start);
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
          
          if (diffDays > 30) {
            showError('end_date_error', 'Rental period cannot exceed 30 days');
            document.getElementById('rent_end_date').value = '';
            return;
          }
        }
      }
      
      calculateRentalCost();
    }
    
    function validatePhone() {
      const phone = document.getElementById('rent_phone_number').value.trim();
      clearError('phone_error');
      
      if (phone && !/^[0-9+\-\s()]{7,20}$/.test(phone)) {
        showError('phone_error', 'Please enter a valid phone number');
        return false;
      }
      
      updateSubmitButton();
      return true;
    }
    
    function calculateRentalCost() {
      const startDate = document.getElementById('rent_start_date').value;
      const endDate = document.getElementById('rent_end_date').value;
      const pricePerDay = parseFloat(document.getElementById('rent_price_per_day').value) || 0;
      
      if (startDate && endDate && pricePerDay > 0) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const timeDiff = end.getTime() - start.getTime();
        const rentalDays = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
        
        if (rentalDays > 0 && rentalDays <= 30) {
          const totalCost = rentalDays * pricePerDay;
          
          document.getElementById('rental_days_display').textContent = rentalDays + ' day' + (rentalDays > 1 ? 's' : '');
          document.getElementById('daily_rate_display').textContent = 'LKR ' + pricePerDay.toFixed(2);
          document.getElementById('total_cost_display').textContent = 'LKR ' + totalCost.toFixed(2);
          document.getElementById('rental_cost_section').style.display = 'block';
        } else {
          document.getElementById('rental_cost_section').style.display = 'none';
        }
      } else {
        document.getElementById('rental_cost_section').style.display = 'none';
      }
      
      updateSubmitButton();
    }
    
    function showError(elementId, message) {
      const errorEl = document.getElementById(elementId);
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
      }
    }
    
    function clearError(elementId) {
      const errorEl = document.getElementById(elementId);
      if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
      }
    }
    
    function clearAllErrors() {
      clearError('start_date_error');
      clearError('end_date_error');
      clearError('phone_error');
      document.getElementById('form_error').style.display = 'none';
    }
    
    function updateSubmitButton() {
      const startDate = document.getElementById('rent_start_date').value;
      const endDate = document.getElementById('rent_end_date').value;
      const phone = document.getElementById('rent_phone_number').value.trim();
      const hasErrors = document.querySelector('[id$="_error"]:not([style*="display: none"])');
      
      const isValid = startDate && endDate && phone && phone.length >= 7 && !hasErrors;
      document.getElementById('submitRentBtn').disabled = !isValid;
    }
    
    function validateRentForm() {
      const startDate = document.getElementById('rent_start_date').value;
      const endDate = document.getElementById('rent_end_date').value;
      const phone = document.getElementById('rent_phone_number').value.trim();
      const today = getTodayDate();
      let errors = [];
      
      if (!startDate) {
        errors.push('Start date is required');
      } else if (startDate < today) {
        errors.push('Start date cannot be in the past');
      }
      
      if (!endDate) {
        errors.push('End date is required');
      } else if (endDate < today) {
        errors.push('End date cannot be in the past');
      } else if (startDate && endDate < startDate) {
        errors.push('End date must be after start date');
      }
      
      if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        if (diffDays > 30) {
          errors.push('Rental period cannot exceed 30 days');
        }
      }
      
      if (!phone) {
        errors.push('Phone number is required');
      } else if (!/^[0-9+\-\s()]{7,20}$/.test(phone)) {
        errors.push('Please enter a valid phone number');
      }
      
      if (errors.length > 0) {
        document.getElementById('form_error').innerHTML = errors.join('<br>');
        document.getElementById('form_error').style.display = 'block';
        return false;
      }
      
      return true;
    }

    function closeRentModal() {
      document.getElementById('rentModal').style.display = 'none';
      document.getElementById('rentForm').reset();
      document.getElementById('rental_cost_section').style.display = 'none';
      clearAllErrors();
    }

    function purchaseBook(bookId, bookTitle, price) {
      if (confirm('Purchase "' + bookTitle + '" for LKR ' + price + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/book-hub/src/handlers/purchase-book-handler.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'book_id';
        input.value = bookId;
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
      }
    }

    // Display messages
    function displayMessages() {
      const params = new URLSearchParams(window.location.search);
      const messageContainer = document.getElementById('message-container');
      const success = params.get('success');
      const error = params.get('error');
      
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
      
      if (error) {
        messageContainer.innerHTML = `<div class="alert alert-error" style="padding: 16px 20px; background: #ef4444; color: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(239,68,68,0.3); font-size: 15px;">${decodeURIComponent(error)}</div>`;
        params.delete('error');
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

    // Close modal when clicking outside
    document.getElementById('rentModal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        closeRentModal();
      }
    });
  </script>
</body>
</html>

