<?php
require_once __DIR__ . '/../config.php';
session_start();

if(!isset($_SESSION['admin'])) {
    header("Location: admin-login.html");
    exit();
}

$conn = getDbConnection();

// Handle delete
if(isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('User deleted successfully!');</script>";
}

// Handle update
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $id);
    $stmt->execute();
    echo "<script>alert('User updated successfully!');</script>";
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
    <link rel="stylesheet" href="../static/css/admin.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        button {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[name="update"] {
            background: #10b981;
            color: white;
        }
        button[name="delete"] {
            background: #ef4444;
            color: white;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <aside class="sidebar">
        <h2>BOOK HUB Admin</h2>
        <nav class="menu">
            <ul>
                <li><a href="admin"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-users" class="active"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="manage-books.php"><i class="fas fa-book"></i> Books</a></li>
            </ul>
        </nav>
        <form method="post" action="backend/logout-handler.php">
            <button name="logoff" type="submit" class="logout-btn">Log Out</button>
        </form>
    </aside>

    <main class="main-content">
        <h1>Manage Users</h1>
        <table>
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
                <tr>
                    <form method="POST" action="">
                        <td><?= $row['id'] ?></td>
                        <td><input type="text" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>"></td>
                        <td><input type="text" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>"></td>
                        <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>"></td>
                        <td><?= $row['email_verified'] ? 'Yes' : 'No' ?></td>
                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                        <td>
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="update">Update</button>
                            <button type="submit" name="delete" onclick="return confirm('Delete this user?')">Delete</button>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>

</body>
</html>
