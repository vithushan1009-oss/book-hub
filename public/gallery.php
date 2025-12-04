<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery — BOOK HUB</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
</head>
<body>
  <?php require_once __DIR__ . '/../src/components/navbar.php'; ?>

  <!-- Page Header -->
  <section class="page-header">
    <div class="container">
      <div style="max-width: 48rem; margin: 0 auto;">
        <h1>Gallery</h1>
        <p>Explore our beautiful library spaces, book collections, and community events</p>
      </div>
    </div>
  </section>

  <!-- Category Filter -->
  <section style="padding: 2rem 0;">
    <div class="container">
      <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem;">
        <button class="btn btn-secondary" data-category="all">All</button>
        <button class="btn btn-outline" data-category="library">Library</button>
        <button class="btn btn-outline" data-category="books">Books</button>
        <button class="btn btn-outline" data-category="events">Events</button>
      </div>
    </div>
  </section>

  <!-- Gallery Grid -->
  <section style="padding: 3rem 0 5rem;">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        
        <!-- Gallery Item 1 -->
        <div class="gallery-item" data-category="library">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/hero-library.jpg" alt="Modern Reading Space" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Modern Reading Space</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">library</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 2 -->
        <div class="gallery-item" data-category="books">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/book-1.jpg" alt="Fiction Collection" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Fiction Collection</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">books</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 3 -->
        <div class="gallery-item" data-category="books">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/book-2.jpg" alt="Non-Fiction Selection" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Non-Fiction Selection</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">books</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 4 -->
        <div class="gallery-item" data-category="books">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/book-3.jpg" alt="Fantasy Novels" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Fantasy Novels</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">books</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 5 -->
        <div class="gallery-item" data-category="books">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/book-4.jpg" alt="Science & Education" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Science & Education</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">books</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 6 -->
        <div class="gallery-item" data-category="events">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/about-team.jpg" alt="Community Reading Event" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Community Reading Event</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">events</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 7 -->
        <div class="gallery-item" data-category="library">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/hero-library.jpg" alt="Cozy Reading Corner" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Cozy Reading Corner</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">library</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Gallery Item 8 -->
        <div class="gallery-item" data-category="books">
          <div style="position: relative; aspect-ratio: 1; overflow: hidden; border-radius: var(--radius); box-shadow: var(--shadow-lg); cursor: pointer;">
            <img src="assets/images/book-1.jpg" alt="Latest Arrivals" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44, 62, 80, 0.8), transparent); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: flex-end;">
              <div style="padding: 1.5rem; color: white;">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">Latest Arrivals</h3>
                <p style="font-size: 0.875rem; opacity: 0.8; text-transform: capitalize;">books</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
  <script>
    // Gallery hover effect
    document.querySelectorAll('.gallery-item > div').forEach(item => {
      const overlay = item.querySelector('div:last-child');
      const img = item.querySelector('img');
      
      item.addEventListener('mouseenter', () => {
        overlay.style.opacity = '1';
        img.style.transform = 'scale(1.1)';
      });
      
      item.addEventListener('mouseleave', () => {
        overlay.style.opacity = '0';
        img.style.transform = 'scale(1)';
      });
    });

    // Category filter
    const filterButtons = document.querySelectorAll('[data-category]');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
      button.addEventListener('click', () => {
        const category = button.dataset.category;
        
        // Update active button
        filterButtons.forEach(btn => {
          btn.classList.remove('btn-secondary');
          btn.classList.add('btn-outline');
        });
        button.classList.remove('btn-outline');
        button.classList.add('btn-secondary');

        // Filter items
        galleryItems.forEach(item => {
          if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>
</html>

