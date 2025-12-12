<?php
/**
 * Admin Contact Management Page
 * View and manage contact form submissions
 */

require_once __DIR__ . '/../admin-session-check.php';
require_once __DIR__ . '/../config.php';

$conn = getDbConnection();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $contactId = (int)($_POST['contact_id'] ?? 0);
    
    switch ($action) {
        case 'mark_read':
            $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $contactId);
            if ($stmt->execute()) {
                $success_message = "Message marked as read.";
            } else {
                $error_message = "Failed to update message.";
            }
            $stmt->close();
            break;
            
        case 'mark_replied':
            $adminId = $_SESSION['admin_id'] ?? null;
            $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied', replied_at = NOW(), replied_by = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ii", $adminId, $contactId);
            if ($stmt->execute()) {
                $success_message = "Message marked as replied.";
            } else {
                $error_message = "Failed to update message.";
            }
            $stmt->close();
            break;
            
        case 'archive':
            $stmt = $conn->prepare("UPDATE contact_messages SET status = 'archived', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $contactId);
            if ($stmt->execute()) {
                $success_message = "Message archived.";
            } else {
                $error_message = "Failed to archive message.";
            }
            $stmt->close();
            break;
            
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $contactId);
            if ($stmt->execute()) {
                $success_message = "Message deleted successfully.";
            } else {
                $error_message = "Failed to delete message.";
            }
            $stmt->close();
            break;
            
        case 'add_note':
            $note = $_POST['admin_notes'] ?? '';
            $stmt = $conn->prepare("UPDATE contact_messages SET admin_notes = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $note, $contactId);
            if ($stmt->execute()) {
                $success_message = "Note saved successfully.";
            } else {
                $error_message = "Failed to save note.";
            }
            $stmt->close();
            break;
    }
}

// Get database statistics
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
$unread_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")->fetch_assoc()['count'];
$read_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'read'")->fetch_assoc()['count'];
$replied_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'replied'")->fetch_assoc()['count'];
$archived_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'archived'")->fetch_assoc()['count'];
$recent_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];

// Fetch messages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'all';

$where_clause = "1=1";
$params = [];
$types = "";

if ($search) {
    $where_clause .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = $search_param;
    }
    $types .= "sssss";
}

if ($status_filter !== 'all') {
    $where_clause .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM contact_messages WHERE $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    if ($count_stmt) {
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

// Get messages with pagination
$query = "SELECT * FROM contact_messages WHERE $where_clause 
          ORDER BY CASE WHEN status = 'unread' THEN 0 ELSE 1 END, created_at DESC 
          LIMIT $per_page OFFSET $offset";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
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
    <title>Contact Management — BOOK HUB</title>
    <link rel="stylesheet" href="/book-hub/public/static/vendor/fontawesome-free-6.5.1-web/fontawesome-free-6.5.1-web/css/all.css">
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
                <h1>Contact Management</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="exportContacts()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Messages</div>
                        <div class="stat-value"><?php echo $total_messages; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> All time
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Unread</div>
                        <div class="stat-value"><?php echo $unread_messages; ?></div>
                        <div class="stat-change <?php echo $unread_messages > 0 ? 'negative' : 'neutral'; ?>">
                            <i class="fas fa-<?php echo $unread_messages > 0 ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                            <?php echo $unread_messages > 0 ? 'Action required' : 'All clear'; ?>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Replied</div>
                        <div class="stat-value"><?php echo $replied_messages; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-check-circle"></i> Responded
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">This Week</div>
                        <div class="stat-value"><?php echo $recent_messages; ?></div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i> Recent activity
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="content-card">
                <form method="GET" action="/book-hub/admin-contacts" class="card-filters">
                    <input type="text" name="search" class="filter-input" placeholder="Search by name, email, or subject..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="status" class="filter-select">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                        <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                        <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                        <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <?php if ($search || $status_filter !== 'all'): ?>
                        <a href="/book-hub/admin-contacts" class="btn btn-outline">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>

                <!-- Contact Messages Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($msg = $result->fetch_assoc()): ?>
                                    <tr class="<?php echo $msg['status'] === 'unread' ? 'row-highlight' : ''; ?>">
                                        <td>#<?php echo (int)$msg['id']; ?></td>
                                        <td>
                                            <div class="user-name-cell">
                                                <div class="user-avatar-small">
                                                    <?php echo strtoupper(substr($msg['first_name'], 0, 1) . substr($msg['last_name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></div>
                                                    <div class="user-email-small"><?php echo htmlspecialchars($msg['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: column;">
                                                <strong style="color: var(--foreground);"><?php echo htmlspecialchars($msg['subject']); ?></strong>
                                                <small style="color: var(--muted-foreground); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars(substr($msg['message'], 0, 60)); ?>...
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $status_config = [
                                                'unread' => ['class' => 'warning', 'icon' => 'fa-circle', 'label' => 'Unread'],
                                                'read' => ['class' => 'primary', 'icon' => 'fa-envelope-open', 'label' => 'Read'],
                                                'replied' => ['class' => 'success', 'icon' => 'fa-reply', 'label' => 'Replied'],
                                                'archived' => ['class' => 'muted', 'icon' => 'fa-archive', 'label' => 'Archived']
                                            ];
                                            $config = $status_config[$msg['status']] ?? $status_config['unread'];
                                            ?>
                                            <span class="status-badge <?php echo $config['class']; ?>">
                                                <i class="fas <?php echo $config['icon']; ?>"></i> <?php echo $config['label']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span title="<?php echo date('F j, Y g:i A', strtotime($msg['created_at'])); ?>">
                                                <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button type="button" class="btn-icon" title="View Message" 
                                                        onclick='viewMessage(<?php echo json_encode($msg, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($msg['status'] === 'unread'): ?>
                                                    <form method="POST" style="display:inline">
                                                        <input type="hidden" name="action" value="mark_read">
                                                        <input type="hidden" name="contact_id" value="<?php echo $msg['id']; ?>">
                                                        <button type="submit" class="btn-icon" title="Mark as Read">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($msg['status'] !== 'replied'): ?>
                                                    <form method="POST" style="display:inline">
                                                        <input type="hidden" name="action" value="mark_replied">
                                                        <input type="hidden" name="contact_id" value="<?php echo $msg['id']; ?>">
                                                        <button type="submit" class="btn-icon" title="Mark as Replied">
                                                            <i class="fas fa-reply"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($msg['status'] !== 'archived'): ?>
                                                    <form method="POST" style="display:inline">
                                                        <input type="hidden" name="action" value="archive">
                                                        <input type="hidden" name="contact_id" value="<?php echo $msg['id']; ?>">
                                                        <button type="submit" class="btn-icon" title="Archive">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <button type="button" class="btn-icon btn-danger" title="Delete" 
                                                        onclick="confirmDelete(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name'], ENT_QUOTES); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>No messages found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter !== 'all' ? '&status=' . urlencode($status_filter) : ''; ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?> (<?php echo $total_records; ?> total)
                        </div>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status_filter !== 'all' ? '&status=' . urlencode($status_filter) : ''; ?>" class="pagination-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div id="viewMessageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-envelope"></i> Message Details</h2>
            <button class="modal-close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">From</label>
                    <p style="margin: 0; font-weight: 500;" id="modal_sender"></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">Email</label>
                    <p style="margin: 0;" id="modal_email"></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">Subject</label>
                    <p style="margin: 0; font-weight: 500;" id="modal_subject"></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.25rem;">Received</label>
                    <p style="margin: 0;" id="modal_date"></p>
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.5rem;">Message</label>
                <div id="modal_message" style="background: var(--muted); padding: 1rem; border-radius: var(--radius); line-height: 1.6; white-space: pre-wrap;"></div>
            </div>
            
            <div>
                <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--muted-foreground); display: block; margin-bottom: 0.5rem;">Admin Notes</label>
                <form method="POST" id="notesForm">
                    <input type="hidden" name="action" value="add_note">
                    <input type="hidden" name="contact_id" id="modal_contact_id">
                    <textarea name="admin_notes" id="modal_notes" rows="3" class="form-control" 
                              placeholder="Add internal notes about this message..." style="width: 100%; margin-bottom: 0.5rem;"></textarea>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-save"></i> Save Note
                    </button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
            <a href="#" id="modal_reply_btn" class="btn btn-primary">
                <i class="fas fa-reply"></i> Reply via Email
            </a>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the message from <strong id="delete_sender_name"></strong>?</p>
            <p class="warning-text">This action cannot be undone.</p>
        </div>
        <form method="POST" id="deleteForm">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="contact_id" id="delete_contact_id">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Message
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Files -->
<script src="/book-hub/public/static/js/admin.js"></script>
<script>
function viewMessage(msg) {
    document.getElementById('modal_sender').textContent = msg.first_name + ' ' + msg.last_name;
    document.getElementById('modal_email').textContent = msg.email;
    document.getElementById('modal_subject').textContent = msg.subject;
    document.getElementById('modal_date').textContent = new Date(msg.created_at).toLocaleString();
    document.getElementById('modal_message').textContent = msg.message;
    document.getElementById('modal_contact_id').value = msg.id;
    document.getElementById('modal_notes').value = msg.admin_notes || '';
    document.getElementById('modal_reply_btn').href = 'mailto:' + msg.email + '?subject=Re: ' + encodeURIComponent(msg.subject);
    
    document.getElementById('viewMessageModal').classList.add('active');
    
    // Mark as read if unread
    if (msg.status === 'unread') {
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=mark_read&contact_id=' + msg.id
        });
    }
}

function closeViewModal() {
    document.getElementById('viewMessageModal').classList.remove('active');
}

function confirmDelete(id, name) {
    document.getElementById('delete_contact_id').value = id;
    document.getElementById('delete_sender_name').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

function exportContacts() {
    alert('Export functionality coming soon!');
}

// Close modal on outside click
document.querySelectorAll('.modal').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(function(modal) {
            modal.classList.remove('active');
        });
    }
});
</script>

<style>
/* Additional styles for contact management */
.row-highlight {
    background: rgba(230, 83, 60, 0.05) !important;
}

.row-highlight:hover {
    background: rgba(230, 83, 60, 0.1) !important;
}

.btn-icon.btn-danger {
    color: var(--danger);
}

.btn-icon.btn-danger:hover {
    background: var(--danger);
    color: white;
}

.warning-text {
    color: var(--danger);
    font-size: 0.875rem;
}

.form-control {
    padding: 0.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--card);
    color: var(--foreground);
    font-size: 0.875rem;
    resize: vertical;
}

.form-control:focus {
    outline: none;
    border-color: var(--secondary);
    box-shadow: 0 0 0 3px rgba(230, 83, 60, 0.1);
}
</style>

</body>
</html>
