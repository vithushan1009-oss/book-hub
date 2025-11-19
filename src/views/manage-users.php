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
    if($stmt->execute()) {
        $success_message = "User deleted successfully!";
    } else {
        $error_message = "Failed to delete user.";
    }
}

// Handle update
if(isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    
    // Validate email
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $first_name, $last_name, $email, $id);
        if($stmt->execute()) {
            $success_message = "User updated successfully!";
        } else {
            $error_message = "Failed to update user.";
        }
    } else {
        $error_message = "Invalid email address.";
    }
}

// Get database statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$verified_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE email_verified = 1")->fetch_assoc()['count'];
$pending_users = $total_users - $verified_users;
$recent_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// Get database info
$db_name = $conn->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name'];
$db_size_query = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?";
$db_size_stmt = $conn->prepare($db_size_query);
$db_size_stmt->bind_param("s", $db_name);
$db_size_stmt->execute();
$db_size = $db_size_stmt->get_result()->fetch_assoc()['size_mb'] ?? 0;

// Fetch users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$where_clause = "1=1";
$params = [];
$types = "";

if($search) {
    $where_clause .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if($filter_status === 'verified') {
    $where_clause .= " AND email_verified = 1";
} elseif($filter_status === 'pending') {
    $where_clause .= " AND email_verified = 0";
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM users WHERE $where_clause";
if(!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    if($count_stmt) {
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_records = $count_result->fetch_assoc()['total'];
    } else {
        $total_records = 0;
    }
} else {
    $count_result = $conn->query($count_query);
    $total_records = $count_result->fetch_assoc()['total'];
}
$total_pages = ceil($total_records / $per_page);

// Get users with pagination
$query = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
if(!empty($params)) {
    $stmt = $conn->prepare($query);
    if($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    }
} else {
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management — BOOK HUB</title>
    <link rel="stylesheet" href="/BOOKHUB/book-hub-central/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <button class="btn btn-primary" onclick="showAddUserModal()">
                        <i class="fas fa-user-plus"></i> Add New User
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value"><?php echo $total_users; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Verified Users</div>
                        <div class="stat-value"><?php echo $verified_users; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Pending Verification</div>
                        <div class="stat-value"><?php echo $pending_users; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">New This Week</div>
                        <div class="stat-value"><?php echo $recent_users; ?></div>
                    </div>
                </div>
            </div>

            <!-- Database Details Card -->
            <div class="dashboard-card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3><i class="fas fa-database"></i> Database Information</h3>
                </div>
                <div class="card-content">
                    <div class="db-info-grid">
                        <div class="db-info-item">
                            <div class="db-info-label">
                                <i class="fas fa-database"></i> Database Name
                            </div>
                            <div class="db-info-value"><?php echo htmlspecialchars($db_name); ?></div>
                        </div>
                        <div class="db-info-item">
                            <div class="db-info-label">
                                <i class="fas fa-hdd"></i> Database Size
                            </div>
                            <div class="db-info-value"><?php echo number_format($db_size, 2); ?> MB</div>
                        </div>
                        <div class="db-info-item">
                            <div class="db-info-label">
                                <i class="fas fa-server"></i> Server Version
                            </div>
                            <div class="db-info-value"><?php echo $conn->server_info; ?></div>
                        </div>
                        <div class="db-info-item">
                            <div class="db-info-label">
                                <i class="fas fa-plug"></i> Connection Status
                            </div>
                            <div class="db-info-value">
                                <span class="status-badge success">
                                    <i class="fas fa-check-circle"></i> Connected
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="content-card">
                <div class="card-filters">
                    <form method="GET" action="" class="filter-form">
                        <div class="search-group">
                            <i class="fas fa-search search-icon-input"></i>
                            <input type="text" 
                                   name="search" 
                                   class="filter-input" 
                                   placeholder="Search by name or email..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <select name="status" class="filter-select">
                            <option value="">All Status</option>
                            <option value="verified" <?php echo $filter_status === 'verified' ? 'selected' : ''; ?>>Verified</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <?php if($search || $filter_status): ?>
                            <a href="?" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo (int)$row['id']; ?></td>
                                        <td>
                                            <div class="user-name-cell">
                                                <div class="user-avatar-small">
                                                    <?php echo strtoupper(substr($row['first_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                                                    <div class="user-email-small"><?php echo htmlspecialchars($row['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <?php if($row['email_verified']): ?>
                                                <span class="status-badge success">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php echo $row['last_login'] ? date('M d, Y', strtotime($row['last_login'])) : 'Never'; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-icon btn-edit" onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" onclick="deleteUser(<?php echo (int)$row['id']; ?>, '<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>')" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>No users found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $filter_status ? '&status=' . urlencode($filter_status) : ''; ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?> (<?php echo $total_records; ?> total)
                        </div>
                        
                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $filter_status ? '&status=' . urlencode($filter_status) : ''; ?>" class="pagination-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-edit"></i> Edit User</h2>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="" id="editUserForm">
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_user_id">
                
                <div class="form-group">
                    <label for="edit_first_name">
                        <i class="fas fa-user"></i> First Name
                    </label>
                    <input type="text" name="first_name" id="edit_first_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_last_name">
                        <i class="fas fa-user"></i> Last Name
                    </label>
                    <input type="text" name="last_name" id="edit_last_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteUserModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete user <strong id="delete_user_name"></strong>?</p>
            <p class="warning-text">This action cannot be undone.</p>
        </div>
        <form method="POST" action="" id="deleteUserForm">
            <input type="hidden" name="id" id="delete_user_id">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" name="delete" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Files -->
<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
<script src="/BOOKHUB/book-hub-central/public/static/js/user-management.js"></script>
</body>
</html>
