<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/session-check.php';

$conn = getDbConnection();

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$book_type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Build query
$where_clause = "is_active = 1";
$params = [];
$types = "";

if($search) {
    $where_clause .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if($category !== 'all') {
    $where_clause .= " AND genre = ?";
    $params[] = $category;
    $types .= "s";
}

if($book_type === 'physical') {
    $where_clause .= " AND book_type = 'physical'";
} elseif($book_type === 'online') {
    $where_clause .= " AND book_type = 'online'";
}

// Build order by clause
$order_by = "created_at DESC";
switch($sort) {
    case 'popular':
        $order_by = "created_at DESC"; // Can be changed to use view count later
        break;
    case 'newest':
        $order_by = "created_at DESC";
        break;
    case 'price-low':
        $order_by = "COALESCE(purchase_price, rental_price_per_day) ASC";
        break;
    case 'price-high':
        $order_by = "COALESCE(purchase_price, rental_price_per_day) DESC";
        break;
    case 'rating':
        $order_by = "created_at DESC"; // Can be changed to use rating later
        break;
}

// Get books
$query = "SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, created_at FROM books WHERE $where_clause ORDER BY $order_by LIMIT 50";
if(!empty($params)) {
    $stmt = $conn->prepare($query);
    if($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, created_at FROM books WHERE is_active = 1 ORDER BY created_at DESC LIMIT 50");
    }
} else {
    $result = $conn->query($query);
}

// Get unique genres for filter
$genres_result = $conn->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' AND is_active = 1 ORDER BY genre");
$genres = [];
while($row = $genres_result->fetch_assoc()) {
    $genres[] = $row['genre'];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>BOOK HUB — Browse Books</title>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
  <link rel="stylesheet" href="static/css/books.css">
</head>
<body>
  <?php require_once __DIR__ . '/../src/components/navbar.php'; ?>

  <header class="page-header">
    <div class="container">
      <h1>Our Book Collection</h1>
      <p>Explore thousands of books available for rent or purchase</p>
    </div>
  </header>

  <section class="filters">
    <div class="container">
      <div class="filter-controls">
        <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; width: 100%;">
          <div class="search-input" style="flex: 1; min-width: 200px;">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/></svg>
            <input type="text" name="search" placeholder="Search books, authors, ISBN..." value="<?php echo htmlspecialchars($search); ?>">
          </div>

          <select name="category" id="category">
            <option value="all">All Categories</option>
            <?php foreach($genres as $genre): ?>
              <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo $category === $genre ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($genre); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select name="sort" id="sort">
            <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
            <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
          </select>

          <select name="type" id="type">
            <option value="all">All Types</option>
            <option value="physical" <?php echo $book_type === 'physical' ? 'selected' : ''; ?>>Physical Books</option>
            <option value="online" <?php echo $book_type === 'online' ? 'selected' : ''; ?>>Online Books</option>
          </select>

          <button type="submit" class="btn btn-outline">Filter</button>
          <?php if($search || $category !== 'all' || $book_type !== 'all'): ?>
            <a href="?" class="btn btn-outline">Clear</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <p class="muted">Showing <strong><?php echo $result->num_rows; ?></strong> books</p>
      </div>

      <div class="books-grid">
        <?php if($result && $result->num_rows > 0): ?>
          <?php while($book = $result->fetch_assoc()): ?>
            <div class="book-card">
              <div class="book-card-image">
                <img src="/BOOKHUB/book-hub-central/src/handlers/book-image.php?id=<?php echo (int)$book['id']; ?>" 
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
                <div class="book-footer">
                  <div class="book-price">
                    <?php if($book['book_type'] === 'physical'): ?>
                      LKR <?php echo number_format($book['rental_price_per_day'], 2); ?><span>/day</span>
                    <?php else: ?>
                      LKR <?php echo number_format($book['purchase_price'], 2); ?>
                    <?php endif; ?>
                  </div>
                  <?php if($is_logged_in): ?>
                    <?php if($book['book_type'] === 'physical'): ?>
                      <button class="btn btn-accent btn-sm" onclick="openRentModal(<?php echo (int)$book['id']; ?>, '<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>', <?php echo number_format($book['rental_price_per_day'], 2); ?>)">Rent</button>
                    <?php else: ?>
                      <button class="btn btn-secondary btn-sm" onclick="purchaseBook(<?php echo (int)$book['id']; ?>, '<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>', <?php echo number_format($book['purchase_price'], 2); ?>)">Buy</button>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="/BOOKHUB/book-hub-central/public/login.html" class="btn btn-secondary btn-sm">Login to Rent/Buy</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <i class="fas fa-book" style="font-size: 3rem; color: var(--muted-foreground); margin-bottom: 1rem;"></i>
            <p style="color: var(--muted-foreground);">No books found. Try adjusting your filters.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pagination can be added here later -->
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- Rent Modal -->
  <?php if($is_logged_in): ?>
  <div id="rentModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 2rem; border-radius: 8px; max-width: 500px; width: 90%;">
      <h2 style="margin-top: 0;">Rent Book</h2>
      <form id="rentForm" method="POST" action="/BOOKHUB/book-hub-central/src/handlers/rent-book-handler.php">
        <input type="hidden" name="book_id" id="rent_book_id">
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Book:</label>
          <p id="rent_book_title" style="margin: 0; color: var(--muted-foreground);"></p>
        </div>
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Start Date:</label>
          <input type="date" name="start_date" id="rent_start_date" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
        </div>
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">End Date:</label>
          <input type="date" name="end_date" id="rent_end_date" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
        </div>
        <div style="margin-bottom: 1rem;">
          <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number:</label>
          <input type="tel" name="phone_number" required placeholder="Enter your phone number" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
        </div>
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" onclick="closeRentModal()" class="btn btn-outline">Cancel</button>
          <button type="submit" class="btn btn-accent">Submit Rental Request</button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Message Container -->
  <div id="message-container" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;"></div>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/books.js"></script>
  <script>
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('rent_start_date')?.setAttribute('min', today);
    document.getElementById('rent_end_date')?.setAttribute('min', today);

    function openRentModal(bookId, bookTitle, pricePerDay) {
      document.getElementById('rent_book_id').value = bookId;
      document.getElementById('rent_book_title').textContent = bookTitle + ' (LKR ' + pricePerDay + '/day)';
      document.getElementById('rentModal').style.display = 'flex';
    }

    function closeRentModal() {
      document.getElementById('rentModal').style.display = 'none';
      document.getElementById('rentForm').reset();
    }

    function purchaseBook(bookId, bookTitle, price) {
      if (confirm('Purchase "' + bookTitle + '" for LKR ' + price + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/BOOKHUB/book-hub-central/src/handlers/purchase-book-handler.php';
        
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
        messageContainer.innerHTML = `<div class="alert alert-success" style="padding: 16px 20px; background: #10b981; color: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.3); font-size: 15px; margin-bottom: 1rem;">${decodeURIComponent(success)}</div>`;
        params.delete('success');
        window.history.replaceState({}, '', `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`);
        setTimeout(() => {
          const alert = messageContainer.querySelector('.alert');
          if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => messageContainer.innerHTML = '', 300);
          }
        }, 5000);
      }
      
      if (error) {
        messageContainer.innerHTML = `<div class="alert alert-error" style="padding: 16px 20px; background: #ef4444; color: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(239,68,68,0.3); font-size: 15px; margin-bottom: 1rem;">${decodeURIComponent(error)}</div>`;
        params.delete('error');
        window.history.replaceState({}, '', `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`);
        setTimeout(() => {
          const alert = messageContainer.querySelector('.alert');
          if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => messageContainer.innerHTML = '', 300);
          }
        }, 5000);
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
