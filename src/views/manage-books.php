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

// Handle delete
if(isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $success_message = "Book deleted successfully!";
    } else {
        $error_message = "Failed to delete book.";
    }
}

// Handle update
if(isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $book_type = $_POST['book_type'];
    $publisher = trim($_POST['publisher']);
    $publication_date = !empty($_POST['publication_date']) ? $_POST['publication_date'] : null;
    
    // Physical book fields
    $total_quantity = $book_type === 'physical' ? (int)$_POST['total_quantity'] : 1;
    $rental_price_per_day = $book_type === 'physical' && !empty($_POST['rental_price_per_day']) ? (float)$_POST['rental_price_per_day'] : null;
    
    // Online book fields
    $purchase_price = $book_type === 'online' && !empty($_POST['purchase_price']) ? (float)$_POST['purchase_price'] : null;
    
    // Handle image upload
    $cover_image = null;
    $cover_image_type = null;
    if(isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cover_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if(in_array($file['type'], $allowed_types)) {
            $cover_image = file_get_contents($file['tmp_name']);
            $cover_image_type = $file['type'];
        }
    }
    
    // Handle PDF upload for online books
    $pdf_file = null;
    $pdf_file_name = null;
    $pdf_file_size = null;
    $pdf_file_type = null;
    if($book_type === 'online' && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['pdf_file'];
        if($file['type'] === 'application/pdf') {
            $pdf_file = file_get_contents($file['tmp_name']);
            $pdf_file_name = $file['name'];
            $pdf_file_size = $file['size'];
            $pdf_file_type = $file['type'];
        }
    }
    
    // Build update query
    if($cover_image && $pdf_file) {
        $sql = "UPDATE books SET title = ?, author = ?, isbn = ?, genre = ?, description = ?, book_type = ?, total_quantity = ?, rental_price_per_day = ?, purchase_price = ?, cover_image = ?, cover_image_type = ?, pdf_file = ?, pdf_file_name = ?, pdf_file_size = ?, pdf_file_type = ?, publisher = ?, publication_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiddssssssssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $cover_image, $cover_image_type, $pdf_file, $pdf_file_name, $pdf_file_size, $pdf_file_type, $publisher, $publication_date, $id);
    } elseif($cover_image) {
        $sql = "UPDATE books SET title = ?, author = ?, isbn = ?, genre = ?, description = ?, book_type = ?, total_quantity = ?, rental_price_per_day = ?, purchase_price = ?, cover_image = ?, cover_image_type = ?, publisher = ?, publication_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiddssssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $cover_image, $cover_image_type, $publisher, $publication_date, $id);
    } elseif($pdf_file) {
        $sql = "UPDATE books SET title = ?, author = ?, isbn = ?, genre = ?, description = ?, book_type = ?, total_quantity = ?, rental_price_per_day = ?, purchase_price = ?, pdf_file = ?, pdf_file_name = ?, pdf_file_size = ?, pdf_file_type = ?, publisher = ?, publication_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiddssssssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $pdf_file, $pdf_file_name, $pdf_file_size, $pdf_file_type, $publisher, $publication_date, $id);
    } else {
        $sql = "UPDATE books SET title = ?, author = ?, isbn = ?, genre = ?, description = ?, book_type = ?, total_quantity = ?, rental_price_per_day = ?, purchase_price = ?, publisher = ?, publication_date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssiddssi", $title, $author, $isbn, $genre, $description, $book_type, $total_quantity, $rental_price_per_day, $purchase_price, $publisher, $publication_date, $id);
    }
    
    if($stmt->execute()) {
        $success_message = "Book updated successfully!";
    } else {
        $error_message = "Failed to update book: " . $conn->error;
    }
}

// Get database statistics
$total_books = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$physical_books = $conn->query("SELECT COUNT(*) as count FROM books WHERE book_type = 'physical'")->fetch_assoc()['count'];
$online_books = $conn->query("SELECT COUNT(*) as count FROM books WHERE book_type = 'online'")->fetch_assoc()['count'];
$active_books = $conn->query("SELECT COUNT(*) as count FROM books WHERE is_active = 1")->fetch_assoc()['count'];

// Fetch books with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clause = "1=1";
$params = [];
$types = "";

if($search) {
    $where_clause .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM books WHERE $where_clause";
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

// Get books with pagination
$query = "SELECT id, title, author, isbn, genre, description, book_type, total_quantity, rental_price_per_day, purchase_price, publisher, publication_date, is_active, created_at FROM books WHERE $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
if(!empty($params)) {
    $stmt = $conn->prepare($query);
    if($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT id, title, author, isbn, genre, book_type, total_quantity, rental_price_per_day, purchase_price, is_active, created_at FROM books ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
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
    <title>Book Management — BOOK HUB</title>
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
                <h1>Book Management</h1>
            </div>

            <!-- Success/Error Messages -->
            <?php if(isset($success_message) || isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message ?? $_GET['success']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($error_message) || isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message ?? $_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Books</div>
                        <div class="stat-value"><?php echo $total_books; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Physical Books</div>
                        <div class="stat-value"><?php echo $physical_books; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Online Books</div>
                        <div class="stat-value"><?php echo $online_books; ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Active Books</div>
                        <div class="stat-value"><?php echo $active_books; ?></div>
                    </div>
                </div>
            </div>

            <!-- Search and Add Book -->
            <div class="content-card">
                <div class="card-filters">
                    <form method="GET" action="" class="filter-form">
                        <div class="search-group" style="flex: 1; max-width: 100%;">
                            <i class="fas fa-search search-icon-input"></i>
                            <input type="text" 
                                   name="search" 
                                   class="filter-input" 
                                   placeholder="Search by title, author, or ISBN..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <?php if($search): ?>
                            <a href="?" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-primary" onclick="showAddBookModal()">
                            <i class="fas fa-book-medical"></i> Add New Book
                        </button>
                    </form>
                </div>

                <!-- Books Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo (int)$row['id']; ?></td>
                                        <td>
                                            <div class="book-cover-small">
                                                <img src="/book-hub/src/handlers/book-image.php?id=<?php echo (int)$row['id']; ?>" 
                                                     alt="<?php echo htmlspecialchars($row['title']); ?>"
                                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'80\'%3E%3Crect fill=\'%23ddd\' width=\'60\' height=\'80\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'12\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="book-title-cell">
                                                <div class="book-name"><?php echo htmlspecialchars($row['title']); ?></div>
                                                <?php if($row['isbn']): ?>
                                                    <div class="book-isbn">ISBN: <?php echo htmlspecialchars($row['isbn']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                                        <td>
                                            <?php if($row['book_type'] === 'physical'): ?>
                                                <span class="status-badge primary">
                                                    <i class="fas fa-book"></i> Physical
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge accent">
                                                    <i class="fas fa-file-pdf"></i> Online
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($row['book_type'] === 'physical'): ?>
                                                LKR <?php echo number_format($row['rental_price_per_day'], 2); ?>/day
                                                <?php if($row['total_quantity']): ?>
                                                    <br><small style="color: var(--muted-foreground);">Qty: <?php echo (int)$row['total_quantity']; ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                LKR <?php echo number_format($row['purchase_price'], 2); ?>
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
                                            <div class="action-buttons">
                                                <button class="btn-icon btn-edit" onclick="editBook(<?php echo htmlspecialchars(json_encode($row)); ?>)" title="Edit Book">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon btn-delete" onclick="deleteBook(<?php echo (int)$row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['title'])); ?>')" title="Delete Book">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="empty-state">
                                        <i class="fas fa-book"></i>
                                        <p>No books found</p>
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

<!-- Add Book Modal -->
<div id="addBookModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-book-medical"></i> Add New Book</h2>
            <button class="modal-close" onclick="closeAddBookModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="/book-hub/src/handlers/add-book-handler.php" enctype="multipart/form-data" id="addBookForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="add_title">
                        <i class="fas fa-heading"></i> Title <span class="required">*</span>
                    </label>
                    <input type="text" name="title" id="add_title" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_author">
                            <i class="fas fa-user"></i> Author <span class="required">*</span>
                        </label>
                        <input type="text" name="author" id="add_author" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="add_isbn">
                            <i class="fas fa-barcode"></i> ISBN
                        </label>
                        <input type="text" name="isbn" id="add_isbn">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_genre">
                            <i class="fas fa-tags"></i> Genre
                        </label>
                        <input type="text" name="genre" id="add_genre" list="genre-list">
                        <datalist id="genre-list">
                            <option value="Fiction">
                            <option value="Non-Fiction">
                            <option value="Fantasy">
                            <option value="Science">
                            <option value="Biography">
                            <option value="History">
                            <option value="Technology">
                            <option value="Business">
                        </datalist>
                    </div>
                    
                    <div class="form-group">
                        <label for="add_book_type">
                            <i class="fas fa-book"></i> Book Type <span class="required">*</span>
                        </label>
                        <select name="book_type" id="add_book_type" required onchange="toggleBookTypeFields()">
                            <option value="physical">Physical Book</option>
                            <option value="online">Online Book</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="add_description">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <textarea name="description" id="add_description" rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="add_publisher">
                            <i class="fas fa-building"></i> Publisher
                        </label>
                        <input type="text" name="publisher" id="add_publisher">
                    </div>
                    
                    <div class="form-group">
                        <label for="add_publication_date">
                            <i class="fas fa-calendar"></i> Publication Date
                        </label>
                        <input type="date" name="publication_date" id="add_publication_date">
                    </div>
                </div>
                
                <!-- Physical Book Fields -->
                <div id="physical-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_total_quantity">
                                <i class="fas fa-boxes"></i> Total Quantity <span class="required">*</span>
                            </label>
                            <input type="number" name="total_quantity" id="add_total_quantity" min="1" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_rental_price">
                                <i class="fas fa-dollar-sign"></i> Rental Price (per day) - LKR <span class="required">*</span>
                            </label>
                            <input type="number" name="rental_price_per_day" id="add_rental_price" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                
                <!-- Online Book Fields -->
                <div id="online-fields" style="display: none;">
                    <div class="form-group">
                        <label for="add_purchase_price">
                            <i class="fas fa-dollar-sign"></i> Purchase Price - LKR <span class="required">*</span>
                        </label>
                        <input type="number" name="purchase_price" id="add_purchase_price" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="add_pdf_file">
                            <i class="fas fa-file-pdf"></i> PDF File
                        </label>
                        <input type="file" name="pdf_file" id="add_pdf_file" accept=".pdf">
                        <small class="form-help">Upload PDF file for online book</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="add_cover_image">
                        <i class="fas fa-image"></i> Cover Image
                    </label>
                    <input type="file" name="cover_image" id="add_cover_image" accept="image/*">
                    <small class="form-help">Recommended: 300x400px, JPG/PNG/WebP</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddBookModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Book
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Book Modal -->
<div id="editBookModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Book</h2>
            <button class="modal-close" onclick="closeEditBookModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" id="editBookForm">
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_book_id">
                
                <div class="form-group">
                    <label for="edit_title">
                        <i class="fas fa-heading"></i> Title <span class="required">*</span>
                    </label>
                    <input type="text" name="title" id="edit_title" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_author">
                            <i class="fas fa-user"></i> Author <span class="required">*</span>
                        </label>
                        <input type="text" name="author" id="edit_author" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_isbn">
                            <i class="fas fa-barcode"></i> ISBN
                        </label>
                        <input type="text" name="isbn" id="edit_isbn">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_genre">
                            <i class="fas fa-tags"></i> Genre
                        </label>
                        <input type="text" name="genre" id="edit_genre" list="genre-list-edit">
                        <datalist id="genre-list-edit">
                            <option value="Fiction">
                            <option value="Non-Fiction">
                            <option value="Fantasy">
                            <option value="Science">
                            <option value="Biography">
                            <option value="History">
                            <option value="Technology">
                            <option value="Business">
                        </datalist>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_book_type">
                            <i class="fas fa-book"></i> Book Type <span class="required">*</span>
                        </label>
                        <select name="book_type" id="edit_book_type" required onchange="toggleEditBookTypeFields()">
                            <option value="physical">Physical Book</option>
                            <option value="online">Online Book</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <textarea name="description" id="edit_description" rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_publisher">
                            <i class="fas fa-building"></i> Publisher
                        </label>
                        <input type="text" name="publisher" id="edit_publisher">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_publication_date">
                            <i class="fas fa-calendar"></i> Publication Date
                        </label>
                        <input type="date" name="publication_date" id="edit_publication_date">
                    </div>
                </div>
                
                <!-- Physical Book Fields -->
                <div id="edit-physical-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_total_quantity">
                                <i class="fas fa-boxes"></i> Total Quantity <span class="required">*</span>
                            </label>
                            <input type="number" name="total_quantity" id="edit_total_quantity" min="1" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_rental_price">
                                <i class="fas fa-dollar-sign"></i> Rental Price (per day) - LKR <span class="required">*</span>
                            </label>
                            <input type="number" name="rental_price_per_day" id="edit_rental_price" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                
                <!-- Online Book Fields -->
                <div id="edit-online-fields" style="display: none;">
                    <div class="form-group">
                        <label for="edit_purchase_price">
                            <i class="fas fa-dollar-sign"></i> Purchase Price - LKR <span class="required">*</span>
                        </label>
                        <input type="number" name="purchase_price" id="edit_purchase_price" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_pdf_file">
                            <i class="fas fa-file-pdf"></i> PDF File (Leave empty to keep existing)
                        </label>
                        <input type="file" name="pdf_file" id="edit_pdf_file" accept=".pdf">
                        <small class="form-help">Upload new PDF file to replace existing</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_cover_image">
                        <i class="fas fa-image"></i> Cover Image (Leave empty to keep existing)
                    </label>
                    <input type="file" name="cover_image" id="edit_cover_image" accept="image/*">
                    <small class="form-help">Upload new image to replace existing</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditBookModal()">Cancel</button>
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBookModal" class="modal">
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <button class="modal-close" onclick="closeDeleteBookModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete book <strong id="delete_book_title"></strong>?</p>
            <p class="warning-text">This action cannot be undone.</p>
        </div>
        <form method="POST" action="" id="deleteBookForm">
            <input type="hidden" name="id" id="delete_book_id">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteBookModal()">Cancel</button>
                <button type="submit" name="delete" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Book
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Files -->
<script src="/book-hub/public/static/js/admin.js"></script>
<script src="/book-hub/public/static/js/book-management.js"></script>
</body>
</html>


