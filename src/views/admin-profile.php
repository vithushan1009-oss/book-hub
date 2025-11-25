<?php
require_once __DIR__ . '/../admin-session-check.php';

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? '';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Profile - BOOK HUB</title>
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/variables.css">
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/base.css">
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/admin.css">
  </head>
  <body>
    <div class="admin-page">
      <?php require_once __DIR__ . '/../components/admin-sidebar.php'; ?>
      <div class="main-content">
        <?php require_once __DIR__ . '/../components/admin-topbar.php'; ?>

        <div class="content-area">
          <div class="section-header">
            <h1>Admin Profile</h1>
          </div>
          <div class="content-card" style="background: var(--admin-card-bg); border:1px solid var(--admin-border); border-radius:1rem; padding:1.5rem; max-width: 720px;">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
              <div class="user-avatar" style="width:3rem; height:3rem;"><?= strtoupper(substr($admin_name,0,1)) ?></div>
              <div>
                <h2 style="margin:0; font-size:1.25rem;"><?= htmlspecialchars($admin_name) ?></h2>
                <div style="color: var(--admin-text-muted)">ID: <?= htmlspecialchars($admin_id) ?></div>
              </div>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
              <div>
                <div style="color: var(--admin-text-muted); font-size:.85rem;">Email</div>
                <div><?= htmlspecialchars($admin_email) ?></div>
              </div>
              <div>
                <div style="color: var(--admin-text-muted); font-size:.85rem;">Role</div>
                <div><?= htmlspecialchars($_SESSION['admin_role'] ?? 'Administrator') ?></div>
              </div>
            </div>
            <div style="margin-top:1.5rem;">
              <a class="btn btn-primary" href="/BOOKHUB/book-hub-central/admin-settings"><i class="fas fa-user-cog"></i> Edit Settings</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
  </body>
  </html>
