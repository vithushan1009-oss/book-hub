// BOOK HUB - Admin Dashboard JavaScript

console.log('Admin Dashboard JavaScript loaded');

// ===== Global State =====
const state = {
  sidebarCollapsed: false,
  currentSection: 'dashboard'
};

// ===== DOM Ready =====
document.addEventListener('DOMContentLoaded', function() {
  initializeDashboard();
});

// ===== Initialize Dashboard =====
function initializeDashboard() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
  const navItems = document.querySelectorAll('.nav-item');
  const logoutBtn = document.querySelector('.btn-logout');
  const themeToggle = document.getElementById('themeToggle');
  
  // New header elements
  const userProfileToggle = document.getElementById('userProfileToggle');
  const profileDropdown = document.getElementById('profileDropdown');
  const notificationToggle = document.getElementById('notificationToggle');
  const notificationDropdown = document.getElementById('notificationDropdown');
  const currentPageBreadcrumb = document.getElementById('currentPageBreadcrumb');

  // Theme Toggle with enhanced animation (top bar and sidebar)
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
  }

  if (themeToggle) {
    themeToggle.innerHTML = savedTheme === 'dark' ? '<i class="fas fa-sun theme-icon"></i>' : '<i class="fas fa-moon theme-icon"></i>';
    themeToggle.addEventListener('click', function() {
      toggleTheme();
    });
  }

  // Sidebar Theme Toggle
  const themeToggleSidebar = document.getElementById('themeToggleSidebar');
  if (themeToggleSidebar) {
    themeToggleSidebar.innerHTML = savedTheme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    themeToggleSidebar.addEventListener('click', function() {
      toggleTheme();
    });
  }

  // User Profile Dropdown
  if (userProfileToggle && profileDropdown) {
    userProfileToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      const isOpen = profileDropdown.classList.contains('show');
      
      // Close other dropdowns
      closeAllDropdowns();
      
      if (!isOpen) {
        profileDropdown.classList.add('show');
        userProfileToggle.classList.add('active');
      }
    });
  }

  // Notification Dropdown
  if (notificationToggle && notificationDropdown) {
    notificationToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      const isOpen = notificationDropdown.classList.contains('show');
      
      // Close other dropdowns
      closeAllDropdowns();
      
      if (!isOpen) {
        notificationDropdown.classList.add('show');
        
        // Mark notifications as read after opening
        setTimeout(() => {
          markNotificationsAsRead();
        }, 500);
      }
    });

    // Mark all as read functionality
    const markAllReadBtn = notificationDropdown.querySelector('.mark-all-read');
    if (markAllReadBtn) {
      markAllReadBtn.addEventListener('click', function() {
        markNotificationsAsRead();
        showNotification('All notifications marked as read', 'success');
      });
    }
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', function() {
    closeAllDropdowns();
  });

  // Enhanced search functionality
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    const debouncedSearch = debounce(handleSearch, 300);
    searchInput.addEventListener('input', debouncedSearch);
    
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        handleSearch();
      }
    });
  }

  // Sidebar toggle (desktop)
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      state.sidebarCollapsed = !state.sidebarCollapsed;
      localStorage.setItem('sidebarCollapsed', state.sidebarCollapsed);
    });
  }

  // Mobile sidebar toggle
  if (mobileSidebarToggle) {
    mobileSidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('mobile-visible');
    });
  }

  // Navigation items with breadcrumb update
  navItems.forEach(item => {
    item.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      const sectionId = this.dataset.section;
      
      if (!href) {
        e.preventDefault();
        return;
      }
      
      const currentPath = window.location.pathname;
      const isAdminDashboard = currentPath.includes('admin.php') || currentPath.endsWith('/admin') || currentPath.endsWith('/admin/');
      
      // Helper function to extract filename from path
      const getFileName = (path) => {
        if (!path) return '';
        // Remove query string and hash
        path = path.split('?')[0].split('#')[0];
        // Get just the filename
        const parts = path.split('/');
        return parts[parts.length - 1] || '';
      };
      
      // Case 1: Hash-only link (#section) - same page section navigation
      if (href.startsWith('#')) {
        e.preventDefault();
        const hashSection = href.substring(1);
        if (hashSection && isAdminDashboard) {
          switchToSection(hashSection);
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');
          if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-visible');
          }
        }
        return;
      }
      
      // Case 2: Link with hash (e.g., admin.php#analytics)
      const hashIndex = href.indexOf('#');
      if (hashIndex !== -1) {
        const linkPath = href.substring(0, hashIndex);
        const hash = href.substring(hashIndex + 1);
        const linkFile = getFileName(linkPath);
        const currentFile = getFileName(currentPath);
        
        // If linking to admin.php and we're on admin.php, switch section
        if (linkFile === 'admin.php' && currentFile === 'admin.php' && hash) {
          e.preventDefault();
          switchToSection(hash);
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');
          if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-visible');
          }
          return;
        }
      }
      
      // Case 3: Link to different page - allow normal navigation
      const linkPath = href.split('#')[0];
      const linkFile = getFileName(linkPath);
      const currentFile = getFileName(currentPath);
      
      // If it's a different file, allow normal navigation
      if (linkFile && linkFile !== currentFile) {
        // Different page - allow browser to navigate normally
        return;
      }
      
      // Case 4: Same page (admin.php) - handle section switching
      if (linkFile === 'admin.php' && currentFile === 'admin.php') {
        // If we have a section ID, switch to that section
        if (sectionId) {
          e.preventDefault();
          switchToSection(sectionId);
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');
          if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-visible');
          }
          return;
        }
        // If no section ID but we're clicking admin.php, go to dashboard
        if (!sectionId || sectionId === 'dashboard') {
          e.preventDefault();
          switchToSection('dashboard');
          navItems.forEach(nav => nav.classList.remove('active'));
          this.classList.add('active');
          if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-visible');
          }
          return;
        }
      }
      
      // Case 5: Same page with section ID on admin dashboard - switch section
      if (sectionId && isAdminDashboard) {
        e.preventDefault();
        switchToSection(sectionId);
        navItems.forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
        if (window.innerWidth <= 768) {
          sidebar.classList.remove('mobile-visible');
        }
        return;
      }
      
      // Case 6: Same page link without section - allow normal navigation
      // Don't prevent default, let browser handle it
    });
  });

  // Logout functionality with enhanced confirmation
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      showConfirmModal(
        'Confirm Logout',
        'Are you sure you want to logout? Any unsaved changes will be lost.',
        'Logout',
        'Cancel',
        function() {
          showNotification('Logging out...', 'info');
          setTimeout(() => {
            window.location.href = '/BOOKHUB/book-hub-central/src/handlers/admin-logout-handler.php';
          }, 1000);
        }
      );
    });
  }

  // Close mobile sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768 && sidebar) {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickOnToggle = mobileSidebarToggle && mobileSidebarToggle.contains(e.target);
      
      if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('mobile-visible')) {
        sidebar.classList.remove('mobile-visible');
      }
    }
  });

  // Restore sidebar state
  const savedSidebarState = localStorage.getItem('sidebarCollapsed');
  if (savedSidebarState === 'true' && sidebar) {
    sidebar.classList.add('collapsed');
    state.sidebarCollapsed = true;
  }

  // Restore section state - only if we're on the admin.php page
  const currentPage = window.location.pathname;
  const isAdminDashboard = currentPage.includes('admin.php') || currentPage.endsWith('/admin') || currentPage.endsWith('/admin/');
  
  if (isAdminDashboard) {
    const savedSection = localStorage.getItem('currentSection');
    const hash = window.location.hash.substring(1);
    
    if (hash) {
      switchToSection(hash);
    } else if (savedSection) {
      switchToSection(savedSection);
    } else {
      // Default to dashboard section
      switchToSection('dashboard');
    }
  }

  // Handle hash changes - only on admin dashboard page
  window.addEventListener('hashchange', function() {
    const currentPage = window.location.pathname;
    const isAdminDashboard = currentPage.includes('admin.php') || currentPage.endsWith('/admin') || currentPage.endsWith('/admin/');
    
    if (isAdminDashboard) {
      const hash = window.location.hash.substring(1);
      if (hash) {
        switchToSection(hash);
      }
    }
  });

  // Responsive behavior
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 768 && sidebar) {
        sidebar.classList.remove('mobile-visible');
      }
      closeAllDropdowns();
    }, 250);
  });

  // Add smooth animations to stat cards
  animateStatCards();

  // Initialize notification count animation
  animateNotificationBadges();

  console.log('Dashboard initialized successfully');
}

// ===== Switch Section Function =====
function switchToSection(sectionId) {
  if (!sectionId) return;
  
  const navItems = document.querySelectorAll('.nav-item');
  
  // Remove active class from all nav items
  navItems.forEach(nav => nav.classList.remove('active'));
  
  // Add active to target nav item
  const targetNav = document.querySelector(`[data-section="${sectionId}"]`);
  if (targetNav) {
    targetNav.classList.add('active');
  }
  
  // Hide all sections
  document.querySelectorAll('.content-section').forEach(section => {
    section.classList.remove('active');
  });
  
  // Show selected section
  const selectedSection = document.getElementById(`${sectionId}-section`);
  if (selectedSection) {
    selectedSection.classList.add('active');
    
    // Smooth scroll to top of content area
    const contentArea = document.querySelector('.content-area');
    if (contentArea) {
      contentArea.scrollTo({ top: 0, behavior: 'smooth' });
    }
  } else {
    console.warn(`Section ${sectionId}-section not found on this page`);
  }
  
  // Update breadcrumb
  updateBreadcrumb(sectionId);
  
  // Update state
  state.currentSection = sectionId;
  localStorage.setItem('currentSection', sectionId);
}

// ===== Header Functions =====
function closeAllDropdowns() {
  const dropdowns = document.querySelectorAll('.notification-dropdown, .profile-dropdown');
  dropdowns.forEach(dropdown => {
    dropdown.classList.remove('show');
  });
  
  const toggles = document.querySelectorAll('#userProfileToggle, #notificationToggle');
  toggles.forEach(toggle => {
    toggle.classList.remove('active');
  });
}

function updateBreadcrumb(sectionId) {
  const breadcrumb = document.getElementById('currentPageBreadcrumb');
  if (breadcrumb) {
    const sectionNames = {
      'dashboard': 'Dashboard',
      'users': 'User Management',
      'books': 'Book Management',
      'rentals': 'Rental Management',
      'admins': 'Administrator Management',
      'settings': 'Settings',
      'analytics': 'Analytics',
      'categories': 'Categories',
      'permissions': 'Permissions',
      'reports': 'Reports',
      'backup': 'Backup',
      'logs': 'Activity Logs'
    };
    
    breadcrumb.textContent = sectionNames[sectionId] || 'Dashboard';
  }
}

function handleSearch() {
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    const query = searchInput.value.trim();
    if (query) {
      showNotification(`Searching for: "${query}"`, 'info');
      // Implement actual search functionality here
      console.log('Search query:', query);
    }
  }
}

function markNotificationsAsRead() {
  const notificationItems = document.querySelectorAll('.notification-item.unread');
  notificationItems.forEach(item => {
    item.classList.remove('unread');
  });
  
  // Update badge count
  const badge = document.querySelector('.notification-badge');
  if (badge) {
    badge.textContent = '0';
    badge.style.opacity = '0';
  }
}

function animateNotificationBadges() {
  const badges = document.querySelectorAll('.badge');
  badges.forEach(badge => {
    if (parseInt(badge.textContent) > 0) {
      badge.style.animation = 'pulse 2s infinite';
    }
  });
}

function showAddUserModal() {
  showNotification('Add User feature coming soon!', 'info');
}

// showAddBookModal function is defined in book-management.js
// This placeholder has been removed to avoid conflicts

function showConfirmModal(title, message, confirmText, cancelText, onConfirm) {
  // Create modal backdrop
  const backdrop = document.createElement('div');
  backdrop.className = 'modal-backdrop';
  backdrop.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease;
  `;
  
  // Create modal
  const modal = document.createElement('div');
  modal.className = 'confirm-modal';
  modal.style.cssText = `
    background: var(--admin-card-bg);
    border-radius: 1rem;
    padding: 2rem;
    max-width: 400px;
    width: 90vw;
    box-shadow: var(--shadow-lg);
    animation: scaleIn 0.2s ease;
  `;
  
  modal.innerHTML = `
    <div style="text-align: center;">
      <div style="width: 4rem; height: 4rem; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--admin-danger);">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
      </div>
      <h3 style="color: var(--admin-text); margin-bottom: 1rem; font-size: 1.25rem;">${title}</h3>
      <p style="color: var(--admin-text-muted); margin-bottom: 2rem; line-height: 1.5;">${message}</p>
      <div style="display: flex; gap: 1rem; justify-content: center;">
        <button class="modal-cancel-btn" style="padding: 0.75rem 1.5rem; border: 1px solid var(--admin-border); background: transparent; color: var(--admin-text); border-radius: 0.5rem; cursor: pointer; font-weight: 500;">${cancelText}</button>
        <button class="modal-confirm-btn" style="padding: 0.75rem 1.5rem; border: none; background: var(--admin-danger); color: white; border-radius: 0.5rem; cursor: pointer; font-weight: 500;">${confirmText}</button>
      </div>
    </div>
  `;
  
  backdrop.appendChild(modal);
  document.body.appendChild(backdrop);
  
  // Event listeners
  const cancelBtn = modal.querySelector('.modal-cancel-btn');
  const confirmBtn = modal.querySelector('.modal-confirm-btn');
  
  function closeModal() {
    backdrop.style.animation = 'fadeOut 0.2s ease';
    setTimeout(() => backdrop.remove(), 200);
  }
  
  cancelBtn.addEventListener('click', closeModal);
  backdrop.addEventListener('click', (e) => {
    if (e.target === backdrop) closeModal();
  });
  
  confirmBtn.addEventListener('click', () => {
    closeModal();
    onConfirm();
  });
}

// Enhanced logout confirmation function
function confirmLogout() {
  showConfirmModal(
    'Confirm Logout',
    'Are you sure you want to logout? You will be redirected to the login page and any unsaved changes will be lost.',
    'Logout',
    'Stay Logged In',
    function() {
      showNotification('Logging out...', 'info');
      
      // Add loading animation to logout button
      const logoutBtn = document.querySelector('.btn-logout');
      if (logoutBtn) {
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span class="logout-text">Logging out...</span>';
        logoutBtn.style.pointerEvents = 'none';
      }
      
      // Redirect after a short delay for better UX
      setTimeout(() => {
        window.location.href = '/BOOKHUB/book-hub-central/src/handlers/admin-logout-handler.php';
      }, 1500);
    }
  );
}

// ===== Animate Stat Cards =====
function animateStatCards() {
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, index * 100);
  });
}

// ===== Notification System =====
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.style.cssText = `
    position: fixed;
    top: 5rem;
    right: 1.5rem;
    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    font-size: 0.875rem;
    font-weight: 500;
    max-width: 400px;
    animation: slideInRight 0.3s ease;
  `;
  
  notification.innerHTML = `
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// ===== Theme Toggle Function =====
function toggleTheme() {
  document.body.classList.toggle('dark-mode');
  const isDark = document.body.classList.contains('dark-mode');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  
  // Update top bar theme toggle icon
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.innerHTML = isDark ? '<i class="fas fa-sun theme-icon"></i>' : '<i class="fas fa-moon theme-icon"></i>';
  }
  
  // Update sidebar theme toggle icon
  const themeToggleSidebar = document.getElementById('themeToggleSidebar');
  if (themeToggleSidebar) {
    themeToggleSidebar.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  }
  
  // Show notification
  if (typeof showNotification === 'function') {
    showNotification(`Switched to ${isDark ? 'dark' : 'light'} mode`, 'success');
  }
}

// ===== Utility Functions =====
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// ===== Add CSS Animations =====
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100%);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  
  @keyframes slideOutRight {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100%);
    }
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
  }
  
  @keyframes scaleIn {
    from {
      opacity: 0;
      transform: scale(0.9);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
`;
document.head.appendChild(style);
