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
            const settingsModal = document.getElementById('userSettingsModal');
            const deleteModal = document.getElementById('deleteUserModal');
            
            if (event.target === settingsModal) {
                UserManagement.closeUserSettings();
            }
            if (event.target === deleteModal) {
                UserManagement.closeDeleteModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                UserManagement.closeUserSettings();
                UserManagement.closeDeleteModal();
            }
        });
    },

    // Setup event listeners
    setupEventListeners: function() {
        // Form submission handlers
        const deleteForm = document.getElementById('deleteUserForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                // Additional confirmation can be added here
                console.log('Delete form submitted');
            });
        }

        // Add event delegation for settings buttons
        document.addEventListener('click', function(event) {
            // Check if click is on settings button or its child (icon)
            const settingsButton = event.target.closest('.btn-settings');
            if (settingsButton) {
                event.preventDefault();
                event.stopPropagation();
                const userData = settingsButton.getAttribute('data-user-data');
                if (userData) {
                    try {
                        const user = JSON.parse(userData);
                        UserManagement.showUserSettings(user);
                    } catch (e) {
                        console.error('Error parsing user data:', e, userData);
                        alert('Error loading user data. Please refresh the page.');
                    }
                } else {
                    console.error('No user data found on button');
                }
            }
        });
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

    // Show User Settings
    showUserSettings: function(user) {
        try {
            console.log('showUserSettings called with:', user);
            
            // Parse user data if it's a string
            if (typeof user === 'string') {
                try {
                    user = JSON.parse(user);
                } catch (e) {
                    console.error('Failed to parse user JSON:', e);
                    alert('Error parsing user data. Please refresh the page.');
                    return;
                }
            }

            const modal = document.getElementById('userSettingsModal');
            if (!modal) {
                console.error('Modal element not found!');
                alert('Settings modal not found. Please refresh the page.');
                return;
            }

            const userIdField = document.getElementById('settings_user_id');
            const verifyUserIdField = document.getElementById('settings_verify_user_id');
            const userNameField = document.getElementById('settings_user_name');
            const userEmailField = document.getElementById('settings_user_email');
            const activeIcon = document.getElementById('settings_active_icon');
            const activeText = document.getElementById('settings_active_text');
            const verifyForm = document.getElementById('settings_verify_form');

            if (!userIdField || !verifyUserIdField || !userNameField || !userEmailField) {
                console.error('Required form elements not found!', {
                    userIdField: !!userIdField,
                    verifyUserIdField: !!verifyUserIdField,
                    userNameField: !!userNameField,
                    userEmailField: !!userEmailField
                });
                alert('Error: Form elements not found. Please refresh the page.');
                return;
            }

            // Set user data
            userIdField.value = user.id || '';
            verifyUserIdField.value = user.id || '';
            userNameField.textContent = (user.first_name || '') + ' ' + (user.last_name || '');
            userEmailField.textContent = user.email || '';

            // Update active status button
            if (activeIcon && activeText) {
                const isActive = user.is_active == 1 || user.is_active === true || user.is_active === '1';
                if (isActive) {
                    activeIcon.className = 'fas fa-ban';
                    activeText.textContent = 'Deactivate User';
                } else {
                    activeIcon.className = 'fas fa-check';
                    activeText.textContent = 'Activate User';
                }
            }

            // Show/hide verify button based on verification status
            if (verifyForm) {
                const isVerified = user.email_verified == 1 || user.email_verified === true || user.email_verified === '1';
                if (isVerified) {
                    verifyForm.style.display = 'none';
                } else {
                    verifyForm.style.display = 'inline';
                }
            }
            
            // Show modal
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            // Force reflow
            modal.offsetHeight;
            setTimeout(function() {
                modal.style.transition = 'opacity 0.3s ease';
                modal.style.opacity = '1';
            }, 10);

            console.log('Modal should be visible now');
        } catch (error) {
            console.error('Error opening settings modal:', error);
            alert('Error opening settings: ' + error.message);
        }
    },

    // Close User Settings
    closeUserSettings: function() {
        const modal = document.getElementById('userSettingsModal');
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

function showUserSettings(user) {
    try {
        UserManagement.showUserSettings(user);
    } catch (error) {
        console.error('Error in showUserSettings:', error);
        alert('Error opening settings. Please check console for details.');
    }
}

// Handle settings button click - Direct approach
function handleSettingsClick(button, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    console.log('handleSettingsClick called');
    const userData = button.getAttribute('data-user-data');
    console.log('User data from button:', userData ? 'Found' : 'Not found');
    
    if (userData) {
        try {
            const user = JSON.parse(userData);
            console.log('Parsed user:', user);
            UserManagement.showUserSettings(user);
        } catch (e) {
            console.error('Error parsing user data:', e);
            console.log('User data string:', userData.substring(0, 100));
            alert('Error loading user data: ' + e.message);
        }
    } else {
        console.error('No user data found on button');
        alert('User data not found. Please refresh the page.');
    }
    return false;
}

function closeUserSettings() {
    UserManagement.closeUserSettings();
}

function deleteUser(id, name) {
    UserManagement.deleteUser(id, name);
}

function closeDeleteModal() {
    UserManagement.closeDeleteModal();
}

function showDeleteFromSettings() {
    const userIdField = document.getElementById('settings_user_id');
    const userNameField = document.getElementById('settings_user_name');
    if (userIdField && userNameField) {
        UserManagement.closeUserSettings();
        setTimeout(function() {
            UserManagement.deleteUser(userIdField.value, userNameField.textContent);
        }, 300);
    }
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

