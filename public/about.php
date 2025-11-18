<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us — BOOK HUB</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="static/css/variables.css">
  <link rel="stylesheet" href="static/css/base.css">
  <link rel="stylesheet" href="static/css/components.css">
  <link rel="stylesheet" href="static/css/navigation.css">
  <link rel="stylesheet" href="static/css/footer.css">
  <link rel="stylesheet" href="static/css/home.css">
</head>
<body>
  <?php require_once __DIR__ . '/../src/components/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="page-header">
    <div class="container">
      <div style="max-width: 48rem; margin: 0 auto;">
        <h1>About BOOK HUB</h1>
        <p>We're passionate about connecting readers with the stories they love. Since 2020, BOOK HUB has been your trusted partner in discovering, renting, and purchasing books.</p>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section style="padding: 5rem 0;">
    <div class="container">
      <style>
        @media (min-width: 768px) {
          .about-grid {
            grid-template-columns: repeat(2, 1fr) !important;
          }
        }
      </style>
      <div class="about-grid" style="display: grid; grid-template-columns: 1fr; gap: 3rem;">
        <div class="benefit-card" style="padding: 2rem;">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--secondary);">
              <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
            </svg>
            <h2 style="font-size: 1.875rem; margin: 0;">Our Mission</h2>
          </div>
          <p style="color: var(--muted-foreground); line-height: 1.6;">
            To make reading accessible, affordable, and enjoyable for everyone by offering a vast collection of physical and digital books. We strive to foster a love for reading and create a vibrant community of book enthusiasts.
          </p>
        </div>

        <div class="benefit-card" style="padding: 2rem;">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent);">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <h2 style="font-size: 1.875rem; margin: 0;">Our Vision</h2>
          </div>
          <p style="color: var(--muted-foreground); line-height: 1.6;">
            To become the world's most trusted and innovative library management platform, bridging the gap between traditional book rentals and modern digital publishing, while nurturing the timeless joy of reading.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="bg-muted" style="padding: 5rem 0;">
    <div class="container">
      <div class="section-header">
        <h2>Meet Our Team</h2>
        <p>Passionate book lovers dedicated to serving our community</p>
      </div>

      <div style="max-width: 64rem; margin: 0 auto;">
        <img src="assets/images/about-team.jpg" alt="BOOK HUB Team" style="width: 100%; border-radius: 1rem; box-shadow: var(--shadow-xl);">
      </div>

      <div style="text-align: center; margin-top: 2rem; max-width: 48rem; margin-left: auto; margin-right: auto;">
        <p style="color: var(--muted-foreground); line-height: 1.6;">
          Our diverse team of librarians, technologists, and book enthusiasts work together to curate the best reading experience for you. With decades of combined experience in library management and digital publishing, we're here to help you discover your next great read.
        </p>
      </div>
    </div>
  </section>

  <!-- Values -->
  <section style="padding: 5rem 0;">
    <div class="container">
      <div class="section-header">
        <h2>Our Values</h2>
        <p>The principles that guide everything we do</p>
      </div>

      <div class="benefits-grid">
        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
            </svg>
          </div>
          <h3>Love for Reading</h3>
          <p>We believe reading enriches lives and broadens perspectives</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <h3>Community Focus</h3>
          <p>Building a community of passionate readers and book enthusiasts</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>
            </svg>
          </div>
          <h3>Quality Service</h3>
          <p>Committed to providing exceptional service and book selections</p>
        </div>

        <div class="benefit-card">
          <div class="benefit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>
            </svg>
          </div>
          <h3>Accessibility</h3>
          <p>Making books accessible to everyone through flexible options</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Timeline -->
  <section class="bg-muted" style="padding: 5rem 0;">
    <div class="container">
      <div class="section-header">
        <h2>Our Journey</h2>
        <p>Key milestones in our growth story</p>
      </div>

      <div style="max-width: 64rem; margin: 0 auto;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
          <div style="display: flex; gap: 1.5rem; align-items: flex-start; background: var(--card); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md);">
            <div style="flex-shrink: 0;">
              <div style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; color: var(--secondary-foreground); font-weight: 700; font-size: 1.125rem;">
                2020
              </div>
            </div>
            <div style="flex: 1;">
              <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">BOOK HUB Founded</h3>
              <p style="color: var(--muted-foreground);">Started with 1,000 books</p>
            </div>
          </div>

          <div style="display: flex; gap: 1.5rem; align-items: flex-start; background: var(--card); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md);">
            <div style="flex-shrink: 0;">
              <div style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; color: var(--secondary-foreground); font-weight: 700; font-size: 1.125rem;">
                2021
              </div>
            </div>
            <div style="flex: 1;">
              <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">Digital Platform Launch</h3>
              <p style="color: var(--muted-foreground);">Introduced digital book purchases</p>
            </div>
          </div>

          <div style="display: flex; gap: 1.5rem; align-items: flex-start; background: var(--card); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md);">
            <div style="flex-shrink: 0;">
              <div style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; color: var(--secondary-foreground); font-weight: 700; font-size: 1.125rem;">
                2023
              </div>
            </div>
            <div style="flex: 1;">
              <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">10,000+ Books</h3>
              <p style="color: var(--muted-foreground);">Expanded collection to over 10,000 titles</p>
            </div>
          </div>

          <div style="display: flex; gap: 1.5rem; align-items: flex-start; background: var(--card); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow-md);">
            <div style="flex-shrink: 0;">
              <div style="width: 5rem; height: 5rem; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; color: var(--secondary-foreground); font-weight: 700; font-size: 1.125rem;">
                2025
              </div>
            </div>
            <div style="flex: 1;">
              <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">5,000+ Members</h3>
              <p style="color: var(--muted-foreground);">Growing community of happy readers</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php require_once __DIR__ . '/../src/components/footer.php'; ?>

  <!-- JavaScript Files -->
  <script src="static/js/common.js"></script>
</body>
</html>
