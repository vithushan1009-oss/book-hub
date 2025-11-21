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
                      <button class="btn btn-accent btn-sm" onclick="rentBook(<?php echo (int)$book['id']; ?>)">Rent</button>
                    <?php else: ?>
                      <button class="btn btn-secondary btn-sm" onclick="purchaseBook(<?php echo (int)$book['id']; ?>)">Buy</button>
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

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/books.js"></script>
  <script>
    function rentBook(bookId) {
      // TODO: Implement rental functionality
      alert('Rental functionality coming soon!');
    }
    
    function purchaseBook(bookId) {
      // TODO: Implement purchase functionality
      alert('Purchase functionality coming soon!');
    }
  </script>
</body>
</html>
