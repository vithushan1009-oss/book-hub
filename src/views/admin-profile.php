<?php
require_once __DIR__ . '/../config.php';
session_start();

// Admin-only access
if (!isset($_SESSION['admin_id'])) {
    header('Location: /BOOKHUB/book-hub-central/public/admin-login.html');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? '';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Profile - BookHub</title>
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/css/admin.css">
  </head>
  <body>
    <main class="admin-container">
      <header class="admin-header">
        <h1>Admin Profile</h1>
        <nav style="margin-left:auto">
          <a href="/BOOKHUB/book-hub-central/src/views/admin.php">Dashboard</a>
          <a href="/BOOKHUB/book-hub-central/src/handlers/logout-handler.php" style="margin-left:1rem; color:#c00">Log Out</a>
        </nav>
      </header>

      <section class="profile-card" style="max-width:720px; margin:2rem auto; padding:1.5rem; border:1px solid #eee; border-radius:8px; background:#fff;">
        <h2><?php echo htmlspecialchars($admin_name); ?></h2>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($admin_id); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin_email); ?></p>
        <p style="margin-top:1rem;"><a href="/BOOKHUB/book-hub-central/src/views/settings.php">Edit Settings</a></p>
      </section>
    </main>
  </body>
</html>
