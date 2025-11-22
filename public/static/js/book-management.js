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
        // Form submission handlers with validation
        const addForm = document.getElementById('addBookForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                if (!BookManagement.validateAddBookForm()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
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
                // Clear all form errors
                this.clearFormErrors('addBookForm');
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
            setTimeout(function() {
                const firstInput = document.getElementById('add_title');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 100);
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
    },

    // Validate Add Book Form
    validateAddBookForm: function() {
        let isValid = true;
        const errors = [];

        // Clear previous error messages
        this.clearFormErrors('addBookForm');

        // Get form elements
        const title = document.getElementById('add_title');
        const author = document.getElementById('add_author');
        const bookType = document.getElementById('add_book_type');
        const totalQuantity = document.getElementById('add_total_quantity');
        const rentalPrice = document.getElementById('add_rental_price');
        const purchasePrice = document.getElementById('add_purchase_price');
        const coverImage = document.getElementById('add_cover_image');
        const pdfFile = document.getElementById('add_pdf_file');

        // Validate title
        if (!title || !title.value.trim()) {
            this.showFieldError('add_title', 'Title is required');
            isValid = false;
        } else if (title.value.trim().length < 2) {
            this.showFieldError('add_title', 'Title must be at least 2 characters');
            isValid = false;
        }

        // Validate author
        if (!author || !author.value.trim()) {
            this.showFieldError('add_author', 'Author is required');
            isValid = false;
        } else if (author.value.trim().length < 2) {
            this.showFieldError('add_author', 'Author name must be at least 2 characters');
            isValid = false;
        }

        // Validate book type specific fields
        if (bookType && bookType.value === 'physical') {
            // Physical book validation
            if (!totalQuantity || !totalQuantity.value || parseInt(totalQuantity.value) < 1) {
                this.showFieldError('add_total_quantity', 'Quantity must be at least 1');
                isValid = false;
            }

            if (!rentalPrice || !rentalPrice.value || parseFloat(rentalPrice.value) <= 0) {
                this.showFieldError('add_rental_price', 'Rental price must be greater than 0');
                isValid = false;
            }
        } else if (bookType && bookType.value === 'online') {
            // Online book validation
            if (!purchasePrice || !purchasePrice.value || parseFloat(purchasePrice.value) <= 0) {
                this.showFieldError('add_purchase_price', 'Purchase price must be greater than 0');
                isValid = false;
            }

            // PDF file is optional but validate if provided
            if (pdfFile && pdfFile.files.length > 0) {
                const file = pdfFile.files[0];
                if (file.type !== 'application/pdf') {
                    this.showFieldError('add_pdf_file', 'Only PDF files are allowed');
                    isValid = false;
                } else if (file.size > 50 * 1024 * 1024) {
                    this.showFieldError('add_pdf_file', 'PDF file size must be less than 50MB');
                    isValid = false;
                }
            }
        }

        // Validate cover image if provided
        if (coverImage && coverImage.files.length > 0) {
            const file = coverImage.files[0];
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!allowedTypes.includes(file.type)) {
                this.showFieldError('add_cover_image', 'Only JPG, PNG, GIF, and WebP images are allowed');
                isValid = false;
            } else if (file.size > 5 * 1024 * 1024) {
                this.showFieldError('add_cover_image', 'Image size must be less than 5MB');
                isValid = false;
            }
        }

        if (!isValid) {
            // Show error notification
            this.showFormError('Please fix the errors in the form');
        }

        return isValid;
    },

    // Show field error
    showFieldError: function(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        // Remove existing error
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        // Add error class to field
        field.classList.add('error');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = 'var(--error)';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        errorDiv.textContent = message;
        
        // Insert after the input field
        field.parentElement.appendChild(errorDiv);
    },

    // Clear all form errors
    clearFormErrors: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // Remove all error classes
        const errorFields = form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));

        // Remove all error messages
        const errorMessages = form.querySelectorAll('.field-error');
        errorMessages.forEach(msg => msg.remove());
    },

    // Show form error notification
    showFormError: function(message) {
        // You can customize this to show a notification or alert
        alert(message);
        // Or create a better notification system
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

