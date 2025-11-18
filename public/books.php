<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>BOOK HUB — Browse Books</title>
  
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
        <div class="search-input">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/></svg>
          <input type="text" placeholder="Search books, authors, ISBN...">
        </div>

        <select id="category">
          <option value="all">All Categories</option>
          <option value="fiction">Fiction</option>
          <option value="non-fiction">Non-Fiction</option>
          <option value="fantasy">Fantasy</option>
          <option value="science">Science</option>
          <option value="biography">Biography</option>
        </select>

        <select id="sort">
          <option value="popular">Most Popular</option>
          <option value="newest">Newest First</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="rating">Highest Rated</option>
        </select>

        <button class="btn btn-outline">Filters</button>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <p class="muted">Showing <strong>8</strong> books</p>
      </div>

      <div class="books-grid">
        <!-- repeated book cards similar to index.html, referencing src assets -->
        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-1.jpg" alt="Fiction Fomen"><span class="book-badge badge-buy">Buy Now</span></div>
          <div class="book-card-content"><h3>Fiction Fomen</h3><p class="author">Shen Gerdings</p><div class="book-footer"><div class="book-price">$12.99</div><button class="btn btn-secondary btn-sm">Buy</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-2.jpg" alt="Nook"><span class="book-badge badge-rent">For Rent</span></div>
          <div class="book-card-content"><h3>Nook</h3><p class="author">Bab Giuing</p><div class="book-footer"><div class="book-price">$3.99<span>/week</span></div><button class="btn btn-accent btn-sm">Rent</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-3.jpg" alt="Mystic Tales"><span class="book-badge badge-buy">Buy Now</span></div>
          <div class="book-card-content"><h3>Mystic Tales</h3><p class="author">Fantasy Author</p><div class="book-footer"><div class="book-price">$15.99</div><button class="btn btn-secondary btn-sm">Buy</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-4.jpg" alt="Science Wonders"><span class="book-badge badge-rent">For Rent</span></div>
          <div class="book-card-content"><h3>Science Wonders</h3><p class="author">Knowledge Seeker</p><div class="book-footer"><div class="book-price">$4.99<span>/week</span></div><button class="btn btn-accent btn-sm">Rent</button></div></div>
        </div>

        <!-- duplicates to simulate more items -->
        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-1.jpg" alt="Fiction Fomen"><span class="book-badge badge-buy">Buy Now</span></div>
          <div class="book-card-content"><h3>Fiction Fomen</h3><p class="author">Shen Gerdings</p><div class="book-footer"><div class="book-price">$12.99</div><button class="btn btn-secondary btn-sm">Buy</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-2.jpg" alt="Nook"><span class="book-badge badge-rent">For Rent</span></div>
          <div class="book-card-content"><h3>Nook</h3><p class="author">Bab Giuing</p><div class="book-footer"><div class="book-price">$3.99<span>/week</span></div><button class="btn btn-accent btn-sm">Rent</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-3.jpg" alt="Mystic Tales"><span class="book-badge badge-buy">Buy Now</span></div>
          <div class="book-card-content"><h3>Mystic Tales</h3><p class="author">Fantasy Author</p><div class="book-footer"><div class="book-price">$15.99</div><button class="btn btn-secondary btn-sm">Buy</button></div></div>
        </div>

        <div class="book-card">
          <div class="book-card-image"><img src="assets/images/book-4.jpg" alt="Science Wonders"><span class="book-badge badge-rent">For Rent</span></div>
          <div class="book-card-content"><h3>Science Wonders</h3><p class="author">Knowledge Seeker</p><div class="book-footer"><div class="book-price">$4.99<span>/week</span></div><button class="btn btn-accent btn-sm">Rent</button></div></div>
        </div>

      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button class="btn btn-outline">Previous</button>
        <button class="btn btn-outline active">1</button>
        <button class="btn btn-outline">2</button>
        <button class="btn btn-outline">3</button>
        <button class="btn btn-outline">Next</button>
      </div>

    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script src="static/js/books.js"></script>
</body>
</html>
