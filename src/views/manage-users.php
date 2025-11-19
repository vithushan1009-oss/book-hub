<?php
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Handle delete
if(isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle update
if(isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $id);
    $stmt->execute();
}

// Fetch users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users — BOOK HUB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
          <h1>User Management</h1>
          <div class="header-actions">
            <button class="btn btn-primary">
              <i class="fas fa-user-plus"></i> Add New User
            </button>
          </div>
        </div>

        <div class="content-card" style="background: var(--admin-card-bg); border:1px solid var(--admin-border); border-radius:1rem; overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Verified</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <form method="POST" action="">
                                    <tr>
                                        <td><?= (int)$row['id'] ?></td>
                                        <td><input type="text" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" /></td>
                                        <td><input type="text" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" /></td>
                                        <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" /></td>
                                        <td><?= $row['email_verified'] ? '<span class="status-badge success">Verified</span>' : '<span class="status-badge pending">Pending</span>' ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                            <button class="btn btn-primary" type="submit" name="update"><i class="fas fa-save"></i> Save</button>
                                            <button class="btn btn-secondary" type="submit" name="delete" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                </form>
                                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        </div>
    </main>
</div>

<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
</body>
</html>
