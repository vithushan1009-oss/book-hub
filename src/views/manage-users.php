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


// Handle toggle active status
if(isset($_POST['toggle_active'])) {
    $id = (int)$_POST['id'];
    $current_status = $conn->query("SELECT is_active FROM users WHERE id = $id")->fetch_assoc()['is_active'];
    $new_status = $current_status ? 0 : 1;
    $sql = "UPDATE users SET is_active = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $id);
    if($stmt->execute()) {
        $success_message = "User status updated successfully!";
    } else {
        $error_message = "Failed to update user status.";
    }
}

// Handle toggle verification
if(isset($_POST['toggle_verification'])) {
    $id = (int)$_POST['id'];
    $current_status = $conn->query("SELECT email_verified FROM users WHERE id = $id")->fetch_assoc()['email_verified'];
    $new_status = $current_status ? 0 : 1;
    $sql = "UPDATE users SET email_verified = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_status, $id);
    if($stmt->execute()) {
        $success_message = "User verification status updated successfully!";
    } else {
        $error_message = "Failed to update verification status.";
    }
}

// Get database statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$verified_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE email_verified = 1")->fetch_assoc()['count'];
$pending_users = $total_users - $verified_users;
$recent_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// Fetch users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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
    <link rel="stylesheet" href="/book-hub/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                <h1>User Management</h1>
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


            <!-- Search -->
            <div class="content-card">
                <div class="card-filters">
                    <form method="GET" action="" class="filter-form">
                        <div class="search-group" style="flex: 1; max-width: 100%;">
                            <i class="fas fa-search search-icon-input"></i>
                            <input type="text" 
                                   name="search" 
                                   class="filter-input" 
                                   placeholder="Search by name or email..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <?php if($search): ?>
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
                                <th>Verification</th>
                                <th>Active Status</th>
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
                                        <td>
                                            <?php if($row['is_active']): ?>
                                                <span class="status-badge success">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge pending">
                                                    <i class="fas fa-ban"></i> Inactive
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php echo $row['last_login'] ? date('M d, Y', strtotime($row['last_login'])) : 'Never'; ?>
                                        </td>
                                        <td style="position: relative; width: 80px; min-width: 80px;">
                                            <button type="button" 
                                                    class="btn-icon btn-settings" 
                                                    title="User Settings"
                                                    data-user-id="<?php echo (int)$row['id']; ?>"
                                                    data-user-data='<?php echo htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'
                                                    onclick="handleSettingsClick(this, event)">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-state">
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
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?> (<?php echo $total_records; ?> total)
                        </div>
                        
                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="pagination-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- User Settings Popup -->
<div id="userSettingsModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-cog"></i> User Settings</h2>
            <button class="modal-close" onclick="closeUserSettings()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 1rem;">
                <strong id="settings_user_name"></strong><br>
                <small style="color: var(--text-tertiary);" id="settings_user_email"></small>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <form method="POST" action="" id="settings_active_form" style="display: inline;" onsubmit="return confirm('Are you sure you want to change the active status of this user?');">
                    <input type="hidden" name="id" id="settings_user_id">
                    <button type="submit" name="toggle_active" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-check" id="settings_active_icon"></i> 
                        <span id="settings_active_text">Toggle Active Status</span>
                    </button>
                </form>
                <form method="POST" action="" id="settings_verify_form" style="display: none;" onsubmit="return confirm('Are you sure you want to verify this user?');">
                    <input type="hidden" name="id" id="settings_verify_user_id">
                    <button type="submit" name="toggle_verification" class="btn btn-success" style="width: 100%;">
                        <i class="fas fa-check-circle"></i> Verify User
                    </button>
                </form>
                <button type="button" class="btn btn-danger" onclick="showDeleteFromSettings()" style="width: 100%;">
                    <i class="fas fa-trash"></i> Remove User
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeUserSettings()">Close</button>
        </div>
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
<script src="/book-hub/public/static/js/admin.js"></script>
<script src="/book-hub/public/static/js/user-management.js"></script>
<script>
// Debug: Test if modal exists
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('userSettingsModal');
    console.log('Modal element found:', !!modal);
    if (modal) {
        console.log('Modal display:', window.getComputedStyle(modal).display);
        console.log('Modal z-index:', window.getComputedStyle(modal).zIndex);
    }
    
    // Test button click
    const testButton = document.querySelector('.btn-settings');
    if (testButton) {
        console.log('Settings button found:', !!testButton);
        console.log('Button data:', testButton.getAttribute('data-user-data') ? 'Has data' : 'No data');
    }
});
</script>
</body>
</html>

