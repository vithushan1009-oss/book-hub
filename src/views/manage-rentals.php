<?php
require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Get success/error messages from session or URL
if(isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
} elseif(isset($_GET['success'])) {
    $success_message = $_GET['success'];
}

if(isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
} elseif(isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

// Handle status update
if(isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $new_status = trim($_POST['status']);
    
    $allowed_statuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
    if(in_array($new_status, $allowed_statuses)) {
        $sql = "UPDATE rentals SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $id);
        if($stmt->execute()) {
            $success_message = "Rental status updated successfully!";
        } else {
            $error_message = "Failed to update rental status.";
        }
    } else {
        $error_message = "Invalid status.";
    }
}

// Handle delete
if(isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM rentals WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $success_message = "Rental deleted successfully!";
    } else {
        $error_message = "Failed to delete rental.";
    }
}

// Get database statistics
$total_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals")->fetch_assoc()['count'];
$pending_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'approved'")->fetch_assoc()['count'];
$active_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'approved' AND start_date <= CURDATE() AND end_date >= CURDATE()")->fetch_assoc()['count'];
$completed_rentals = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'completed'")->fetch_assoc()['count'];

// Fetch rentals with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'all';

$where_clause = "1=1";
$params = [];
$types = "";

if($search) {
    $where_clause .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR b.title LIKE ? OR b.author LIKE ?)";
    $search_param = "%$search%";
    for($i = 0; $i < 5; $i++) {
        $params[] = $search_param;
    }
    $types .= "sssss";
}

if($status_filter !== 'all') {
    $where_clause .= " AND r.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM rentals r
                INNER JOIN users u ON r.user_id = u.id
                INNER JOIN books b ON r.book_id = b.id
                WHERE $where_clause";

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

// Get rentals with pagination
$query = "SELECT r.id, r.user_id, r.book_id, r.start_date, r.end_date, r.phone_number, r.status,
                 CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email,
                 b.title as book_title, b.author as book_author, b.rental_price_per_day
          FROM rentals r
          INNER JOIN users u ON r.user_id = u.id
          INNER JOIN books b ON r.book_id = b.id
          WHERE $where_clause
          ORDER BY r.id DESC
          LIMIT $per_page OFFSET $offset";

if(!empty($params)) {
    $stmt = $conn->prepare($query);
    if($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT r.id, r.user_id, r.book_id, r.start_date, r.end_date, r.phone_number, r.status,
                                       CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email,
                                       b.title as book_title, b.author as book_author, b.rental_price_per_day
                                FROM rentals r
                                INNER JOIN users u ON r.user_id = u.id
                                INNER JOIN books b ON r.book_id = b.id
                                ORDER BY r.id DESC
                                LIMIT $per_page OFFSET $offset");
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
    <title>Rental Management — BOOK HUB</title>
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
                <h1>Rental Management</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="exportRentals()">
                        <i class="fas fa-download"></i> Export
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
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Rentals</div>
                        <div class="stat-value"><?php echo $total_rentals; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> All time
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Pending</div>
                        <div class="stat-value"><?php echo $pending_rentals; ?></div>
                        <div class="stat-change <?php echo $pending_rentals > 0 ? 'negative' : 'neutral'; ?>">
                            <i class="fas fa-<?php echo $pending_rentals > 0 ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                            <?php echo $pending_rentals > 0 ? 'Action required' : 'All clear'; ?>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Active Rentals</div>
                        <div class="stat-value"><?php echo $active_rentals; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> Currently active
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value"><?php echo $completed_rentals; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> Successfully completed
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="content-card">
                <form method="GET" action="/BOOKHUB/book-hub-central/admin-rentals" class="card-filters">
                    <input type="text" name="search" class="filter-input" placeholder="Search by user name, email, or book title..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="status" class="filter-select">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <?php if($search || $status_filter !== 'all'): ?>
                        <a href="/BOOKHUB/book-hub-central/admin-rentals" class="btn btn-outline">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Rentals Table -->
                <div style="overflow-x: auto; margin-top: 1.5rem;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Book</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Total Cost</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                                <?php while($rental = $result->fetch_assoc()): 
                                    $start = new DateTime($rental['start_date']);
                                    $end = new DateTime($rental['end_date']);
                                    $days = $start->diff($end)->days + 1;
                                    $total_cost = $days * (float)$rental['rental_price_per_day'];
                                    $status_class = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'primary',
                                        'cancelled' => 'muted'
                                    ][$rental['status']] ?? 'muted';
                                ?>
                                    <tr>
                                        <td>#<?php echo $rental['id']; ?></td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <strong><?php echo htmlspecialchars($rental['user_name']); ?></strong>
                                                <small style="color: var(--muted-foreground);"><?php echo htmlspecialchars($rental['user_email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <strong><?php echo htmlspecialchars($rental['book_title']); ?></strong>
                                                <small style="color: var(--muted-foreground);">by <?php echo htmlspecialchars($rental['book_author']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($rental['start_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($rental['end_date'])); ?></td>
                                        <td><?php echo $days; ?> day<?php echo $days !== 1 ? 's' : ''; ?></td>
                                        <td><strong>LKR <?php echo number_format($total_cost, 2); ?></strong></td>
                                        <td><?php echo htmlspecialchars($rental['phone_number']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <i class="fas fa-<?php 
                                                    echo $rental['status'] === 'pending' ? 'clock' : 
                                                        ($rental['status'] === 'approved' ? 'check-circle' : 
                                                        ($rental['status'] === 'rejected' ? 'times-circle' : 
                                                        ($rental['status'] === 'completed' ? 'check-double' : 'ban'))); 
                                                ?>"></i>
                                                <?php echo ucfirst($rental['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                <?php if($rental['status'] === 'pending'): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Approve this rental request?');">
                                                        <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Reject this rental request?');">
                                                        <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-danger" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php elseif($rental['status'] === 'approved'): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Mark this rental as completed?');">
                                                        <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary" title="Mark as Completed">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this rental? This action cannot be undone.');">
                                                    <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-sm btn-outline" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 3rem; color: var(--muted-foreground);">
                                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                        <p>No rentals found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                    <div class="pagination" style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem;">
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter !== 'all' ? '&status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter !== 'all' ? '&status=' . urlencode($status_filter) : ''; ?>" 
                               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter !== 'all' ? '&status=' . urlencode($status_filter) : ''; ?>" class="btn btn-outline">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div style="text-align: center; margin-top: 1rem; color: var(--muted-foreground);">
                        Showing page <?php echo $page; ?> of <?php echo $total_pages; ?> (<?php echo $total_records; ?> total rentals)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="/BOOKHUB/book-hub-central/public/static/js/admin.js"></script>
<script>
    function exportRentals() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'rentals');
        window.location.href = `/BOOKHUB/book-hub-central/admin-rentals?${params.toString()}`;
    }
</script>
</body>
</html>

