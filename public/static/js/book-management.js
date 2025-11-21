/**
 * BOOK HUB - Book Management JavaScript
 * Handles book management functionality including modals, editing, and deletion
 */

// ===== Book Management Module =====
const BookManagement = {
    // Initialize book management functionality
    init: function() {
        this.setupModals();
        this.setupEventListeners();
        this.setupAutoCloseAlerts();
        console.log('Book Management module initialized');
    },

    // Setup modal functionality
    setupModals: function() {
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const addModal = document.getElementById('addBookModal');
            const editModal = document.getElementById('editBookModal');
            const deleteModal = document.getElementById('deleteBookModal');
            
            if (event.target === addModal) {
                BookManagement.closeAddBookModal();
            }
            if (event.target === editModal) {
                BookManagement.closeEditBookModal();
            }
            if (event.target === deleteModal) {
                BookManagement.closeDeleteBookModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                BookManagement.closeAddBookModal();
                BookManagement.closeEditBookModal();
                BookManagement.closeDeleteBookModal();
            }
        });
    },

    // Setup event listeners
    setupEventListeners: function() {
        // Form submission handlers
        const addForm = document.getElementById('addBookForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                // Validation can be added here
                console.log('Add book form submitted');
            });
        }

        const editForm = document.getElementById('editBookForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Validation can be added here
                console.log('Edit book form submitted');
            });
        }

        const deleteForm = document.getElementById('deleteBookForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                // Additional confirmation can be added here
                console.log('Delete book form submitted');
            });
        }
    },

    // Auto-close alert messages after 5 seconds
    setupAutoCloseAlerts: function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    },

    // Show Add Book Modal
    showAddBookModal: function() {
        const modal = document.getElementById('addBookModal');
        if (modal) {
            // Reset form
            const form = document.getElementById('addBookForm');
            if (form) {
                form.reset();
                toggleBookTypeFields();
            }
            
            // Show modal with animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.transition = 'opacity 0.3s ease';
                modal.style.opacity = '1';
            }, 10);

            // Focus on first input
            const firstInput = document.getElementById('add_title');
            if (firstInput) {
                firstInput.focus();
            }
        }
    },

    // Close Add Book Modal
    closeAddBookModal: function() {
        const modal = document.getElementById('addBookModal');
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 300);
        }
    },

    // Edit Book Function
    editBook: function(book) {
        try {
            // Parse book data if it's a string
            if (typeof book === 'string') {
                book = JSON.parse(book);
            }

            // Set form values
            const bookIdField = document.getElementById('edit_book_id');
            const titleField = document.getElementById('edit_title');
            const authorField = document.getElementById('edit_author');
            const isbnField = document.getElementById('edit_isbn');
            const genreField = document.getElementById('edit_genre');
            const descriptionField = document.getElementById('edit_description');
            const bookTypeField = document.getElementById('edit_book_type');
            const publisherField = document.getElementById('edit_publisher');
            const publicationDateField = document.getElementById('edit_publication_date');
            const totalQuantityField = document.getElementById('edit_total_quantity');
            const rentalPriceField = document.getElementById('edit_rental_price');
            const purchasePriceField = document.getElementById('edit_purchase_price');
            const modal = document.getElementById('editBookModal');

            if (bookIdField && titleField && authorField && modal) {
                bookIdField.value = book.id || '';
                titleField.value = book.title || '';
                authorField.value = book.author || '';
                if (isbnField) isbnField.value = book.isbn || '';
                if (genreField) genreField.value = book.genre || '';
                if (descriptionField) descriptionField.value = book.description || '';
                if (bookTypeField) {
                    bookTypeField.value = book.book_type || 'physical';
                    toggleEditBookTypeFields();
                }
                if (publisherField) publisherField.value = book.publisher || '';
                if (publicationDateField) {
                    if (book.publication_date) {
                        // Format date for input field (YYYY-MM-DD)
                        const date = new Date(book.publication_date);
                        publicationDateField.value = date.toISOString().split('T')[0];
                    }
                }
                if (totalQuantityField) totalQuantityField.value = book.total_quantity || 1;
                if (rentalPriceField) rentalPriceField.value = book.rental_price_per_day || '';
                if (purchasePriceField) purchasePriceField.value = book.purchase_price || '';
                
                // Show modal with animation
                modal.style.display = 'flex';
                modal.style.opacity = '0';
                setTimeout(function() {
                    modal.style.transition = 'opacity 0.3s ease';
                    modal.style.opacity = '1';
                }, 10);

                // Focus on first input
                titleField.focus();
            } else {
                console.error('Edit modal elements not found');
            }
        } catch (error) {
            console.error('Error opening edit modal:', error);
        }
    },

    // Close Edit Book Modal
    closeEditBookModal: function() {
        const modal = document.getElementById('editBookModal');
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 300);
        }
    },

    // Delete Book Function
    deleteBook: function(id, title) {
        try {
            const bookIdField = document.getElementById('delete_book_id');
            const bookTitleField = document.getElementById('delete_book_title');
            const modal = document.getElementById('deleteBookModal');

            if (bookIdField && bookTitleField && modal) {
                bookIdField.value = id || '';
                bookTitleField.textContent = title || 'this book';
                
                // Show modal with animation
                modal.style.display = 'flex';
                modal.style.opacity = '0';
                setTimeout(function() {
                    modal.style.transition = 'opacity 0.3s ease';
                    modal.style.opacity = '1';
                }, 10);
            } else {
                console.error('Delete modal elements not found');
            }
        } catch (error) {
            console.error('Error opening delete modal:', error);
        }
    },

    // Close Delete Book Modal
    closeDeleteBookModal: function() {
        const modal = document.getElementById('deleteBookModal');
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 300);
        }
    }
};

// ===== Global Functions (for inline onclick handlers) =====
// These functions are exposed globally to work with inline onclick attributes

function showAddBookModal() {
    BookManagement.showAddBookModal();
}

function closeAddBookModal() {
    BookManagement.closeAddBookModal();
}

function editBook(book) {
    BookManagement.editBook(book);
}

function closeEditBookModal() {
    BookManagement.closeEditBookModal();
}

function deleteBook(id, title) {
    BookManagement.deleteBook(id, title);
}

function closeDeleteBookModal() {
    BookManagement.closeDeleteBookModal();
}

// Toggle book type fields for add form
function toggleBookTypeFields() {
    const bookType = document.getElementById('add_book_type')?.value;
    const physicalFields = document.getElementById('physical-fields');
    const onlineFields = document.getElementById('online-fields');
    
    if (bookType === 'physical') {
        if (physicalFields) physicalFields.style.display = 'block';
        if (onlineFields) onlineFields.style.display = 'none';
        
        // Set required attributes
        const totalQuantity = document.getElementById('add_total_quantity');
        const rentalPrice = document.getElementById('add_rental_price');
        if (totalQuantity) totalQuantity.required = true;
        if (rentalPrice) rentalPrice.required = true;
        
        const purchasePrice = document.getElementById('add_purchase_price');
        if (purchasePrice) purchasePrice.required = false;
    } else if (bookType === 'online') {
        if (physicalFields) physicalFields.style.display = 'none';
        if (onlineFields) onlineFields.style.display = 'block';
        
        // Set required attributes
        const totalQuantity = document.getElementById('add_total_quantity');
        const rentalPrice = document.getElementById('add_rental_price');
        if (totalQuantity) totalQuantity.required = false;
        if (rentalPrice) rentalPrice.required = false;
        
        const purchasePrice = document.getElementById('add_purchase_price');
        if (purchasePrice) purchasePrice.required = true;
    }
}

// Toggle book type fields for edit form
function toggleEditBookTypeFields() {
    const bookType = document.getElementById('edit_book_type')?.value;
    const physicalFields = document.getElementById('edit-physical-fields');
    const onlineFields = document.getElementById('edit-online-fields');
    
    if (bookType === 'physical') {
        if (physicalFields) physicalFields.style.display = 'block';
        if (onlineFields) onlineFields.style.display = 'none';
        
        // Set required attributes
        const totalQuantity = document.getElementById('edit_total_quantity');
        const rentalPrice = document.getElementById('edit_rental_price');
        if (totalQuantity) totalQuantity.required = true;
        if (rentalPrice) rentalPrice.required = true;
        
        const purchasePrice = document.getElementById('edit_purchase_price');
        if (purchasePrice) purchasePrice.required = false;
    } else if (bookType === 'online') {
        if (physicalFields) physicalFields.style.display = 'none';
        if (onlineFields) onlineFields.style.display = 'block';
        
        // Set required attributes
        const totalQuantity = document.getElementById('edit_total_quantity');
        const rentalPrice = document.getElementById('edit_rental_price');
        if (totalQuantity) totalQuantity.required = false;
        if (rentalPrice) rentalPrice.required = false;
        
        const purchasePrice = document.getElementById('edit_purchase_price');
        if (purchasePrice) purchasePrice.required = true;
    }
}

// ===== Initialize on DOM Ready =====
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        BookManagement.init();
    });
} else {
    // DOM is already ready
    BookManagement.init();
}

// ===== Export for module systems (if needed) =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BookManagement;
}

