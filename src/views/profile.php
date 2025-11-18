<?php
require_once __DIR__ . '/../config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDbConnection();
$success_message = "";

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    } elseif (isset($_POST['delete'])) {
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        session_destroy();
        header('Location: login.html');
        exit();
    } elseif (isset($_POST['logoutuser'])) {
        session_destroy();
        header('Location: backend/logout-handler.php');
        exit();
    } elseif (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — BOOK HUB</title>
    <link rel="stylesheet" href="../static/css/variables.css">
    <link rel="stylesheet" href="../static/css/base.css">
    <link rel="stylesheet" href="../static/css/components.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .profile-popup {
            background-color: #fff;
            padding: 40px;
            width: 500px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            position: relative;
        }

        .profile-popup h2 {
            text-align: center;
            color: #667eea;
            margin-bottom: 20px;
        }

        .success-message {
            color: #10b981;
            font-weight: bold;
            margin-bottom: 20px;
            background: #d1fae5;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
        }

        .close-button {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 30px;
            color: #667eea;
            cursor: pointer;
            text-decoration: none;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 12px 0 6px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="email"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f7fbff;
            color: #004e92;
        }

        form button {
            padding: 12px 24px;
            margin-top: 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        form button:hover {
            background: #5568d3;
        }

        #delete_button {
            background: #ef4444;
        }

        #delete_button:hover {
            background: #dc2626;
        }

        #logout_button {
            background: #f59e0b;
        }

        #logout_button:hover {
            background: #d97706;
        }
    </style>
</head>
<body>
    <div class="profile-popup">
        <h2>Your Profile</h2>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <a href="user"><span class="close-button">&times;</span></a>
        
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            
            <button type="submit" name="update_profile">Update Profile</button>
            <button type="submit" name="delete" id="delete_button" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</button>
            <button type="submit" name="logoutuser" id="logout_button">Logout</button>
        </form>
    </div>
</body>
</html>
