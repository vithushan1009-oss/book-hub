/**
 * BOOK HUB - User Management JavaScript
 * Handles user management functionality including modals, editing, and deletion
 */

// ===== User Management Module =====
const UserManagement = {
    // Initialize user management functionality
    init: function() {
        this.setupModals();
        this.setupEventListeners();
        this.setupAutoCloseAlerts();
        console.log('User Management module initialized');
    },

    // Setup modal functionality
    setupModals: function() {
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const editModal = document.getElementById('editUserModal');
            const deleteModal = document.getElementById('deleteUserModal');
            
            if (event.target === editModal) {
                UserManagement.closeEditModal();
            }
            if (event.target === deleteModal) {
                UserManagement.closeDeleteModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                UserManagement.closeEditModal();
                UserManagement.closeDeleteModal();
            }
        });
    },

    // Setup event listeners
    setupEventListeners: function() {
        // Form submission handlers
        const editForm = document.getElementById('editUserForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Validation can be added here
                console.log('Edit form submitted');
            });
        }

        const deleteForm = document.getElementById('deleteUserForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                // Additional confirmation can be added here
                console.log('Delete form submitted');
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

    // Edit User Function
    editUser: function(user) {
        try {
            // Parse user data if it's a string
            if (typeof user === 'string') {
                user = JSON.parse(user);
            }

            // Set form values
            const userIdField = document.getElementById('edit_user_id');
            const firstNameField = document.getElementById('edit_first_name');
            const lastNameField = document.getElementById('edit_last_name');
            const emailField = document.getElementById('edit_email');
            const modal = document.getElementById('editUserModal');

            if (userIdField && firstNameField && lastNameField && emailField && modal) {
                userIdField.value = user.id || '';
                firstNameField.value = user.first_name || '';
                lastNameField.value = user.last_name || '';
                emailField.value = user.email || '';
                
                // Show modal with animation
                modal.style.display = 'flex';
                modal.style.opacity = '0';
                setTimeout(function() {
                    modal.style.transition = 'opacity 0.3s ease';
                    modal.style.opacity = '1';
                }, 10);

                // Focus on first input
                firstNameField.focus();
            } else {
                console.error('Edit modal elements not found');
            }
        } catch (error) {
            console.error('Error opening edit modal:', error);
        }
    },

    // Close Edit Modal
    closeEditModal: function() {
        const modal = document.getElementById('editUserModal');
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 300);
        }
    },

    // Delete User Function
    deleteUser: function(id, name) {
        try {
            const userIdField = document.getElementById('delete_user_id');
            const userNameField = document.getElementById('delete_user_name');
            const modal = document.getElementById('deleteUserModal');

            if (userIdField && userNameField && modal) {
                userIdField.value = id || '';
                userNameField.textContent = name || 'this user';
                
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

    // Close Delete Modal
    closeDeleteModal: function() {
        const modal = document.getElementById('deleteUserModal');
        if (modal) {
            modal.style.transition = 'opacity 0.3s ease';
            modal.style.opacity = '0';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 300);
        }
    },

    // Add User Modal (placeholder)
    showAddUserModal: function() {
        // This will be implemented later
        if (typeof showNotification === 'function') {
            showNotification('Add User feature coming soon!', 'info');
        } else {
            alert('Add User feature coming soon!');
        }
    }
};

// ===== Global Functions (for inline onclick handlers) =====
// These functions are exposed globally to work with inline onclick attributes

function editUser(user) {
    UserManagement.editUser(user);
}

function closeEditModal() {
    UserManagement.closeEditModal();
}

function deleteUser(id, name) {
    UserManagement.deleteUser(id, name);
}

function closeDeleteModal() {
    UserManagement.closeDeleteModal();
}

function showAddUserModal() {
    UserManagement.showAddUserModal();
}

// ===== Initialize on DOM Ready =====
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        UserManagement.init();
    });
} else {
    // DOM is already ready
    UserManagement.init();
}

// ===== Export for module systems (if needed) =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UserManagement;
}

