<?php
require_once __DIR__ . '/../admin-session-check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Settings — BOOK HUB</title>
  <link rel="stylesheet" href="/book-hub/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/book-hub/public/static/css/variables.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/base.css">
  <link rel="stylesheet" href="/book-hub/public/static/css/admin.css">
</head>
<body>
  <div class="admin-page">
    <?php require_once __DIR__ . '/../components/admin-sidebar.php'; ?>
    <div class="main-content">
      <?php require_once __DIR__ . '/../components/admin-topbar.php'; ?>
      <div class="content-area">
        <div class="section-header">
          <h1>Settings</h1>
        </div>
        <div class="content-card" style="background: var(--admin-card-bg); border:1px solid var(--admin-border); border-radius:1rem; padding:1.5rem;">
          <p style="color: var(--admin-text-muted)">Settings features coming soon.</p>
        </div>
      </div>
    </div>
  </div>
  <script src="/book-hub/public/static/js/admin.js"></script>
</body>
</html>

