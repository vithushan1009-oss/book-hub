// BOOK HUB - Admin Dashboard JavaScript

// ===== Check Authentication =====
function checkAuthentication() {
  const isLoggedIn = localStorage.getItem('adminLoggedIn');
  const loginTime = localStorage.getItem('loginTime');
  
  if (isLoggedIn !== 'true' || !loginTime) {
    // Redirect to admin login if not authenticated
    window.location.href = 'admin-login.html';
    return false;
  }
  
  // Check session expiry (8 hours)
  const loginDate = new Date(loginTime);
  const now = new Date();
  const hoursSinceLogin = (now - loginDate) / (1000 * 60 * 60);
  
  if (hoursSinceLogin >= 8) {
    // Clear expired session
    localStorage.removeItem('adminLoggedIn');
    localStorage.removeItem('adminEmail');
    localStorage.removeItem('loginTime');
    window.location.href = 'admin-login.html';
    return false;
  }
  
  return true;
}

// Run authentication check immediately
if (!checkAuthentication()) {
  // Stop script execution if not authenticated
  throw new Error('Authentication required');
}

// ===== Global State =====
const state = {
  sidebarCollapsed: false,
  currentSection: 'dashboard'
};

// ===== DOM Elements =====
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
const navItems = document.querySelectorAll('.nav-item');
const contentSections = document.querySelectorAll('.content-section');

// ===== Sidebar Functions =====
function toggleSidebar() {
  state.sidebarCollapsed = !state.sidebarCollapsed;
  sidebar.classList.toggle('collapsed');
  localStorage.setItem('sidebarCollapsed', state.sidebarCollapsed);
}

function toggleMobileSidebar() {
  sidebar.classList.toggle('mobile-visible');
}

function closeMobileSidebar() {
  sidebar.classList.remove('mobile-visible');
}

// ===== Navigation Functions =====
function switchSection(sectionId) {
  // Update state
  state.currentSection = sectionId;
  
  // Hide all sections
  contentSections.forEach(section => {
    section.classList.remove('active');
  });
  
  // Show selected section
  const selectedSection = document.getElementById(`${sectionId}-section`);
  if (selectedSection) {
    selectedSection.classList.add('active');
  }
  
  // Update active nav item
  navItems.forEach(item => {
    item.classList.remove('active');
    if (item.dataset.section === sectionId) {
      item.classList.add('active');
    }
  });
  
  // Close mobile sidebar after navigation
  if (window.innerWidth <= 768) {
    closeMobileSidebar();
  }
  
  // Save to localStorage
  localStorage.setItem('currentSection', sectionId);
  
  // Update URL hash
  window.location.hash = sectionId;
}

// ===== Event Listeners =====
document.addEventListener('DOMContentLoaded', function() {
  
  // Sidebar toggle
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
  }
  
  // Mobile sidebar toggle
  if (mobileSidebarToggle) {
    mobileSidebarToggle.addEventListener('click', toggleMobileSidebar);
  }
  
  // Navigation items
  navItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const sectionId = this.dataset.section;
      if (sectionId) {
        switchSection(sectionId);
      }
    });
  });
  
  // Close mobile sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768) {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickOnToggle = mobileSidebarToggle.contains(e.target);
      
      if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('mobile-visible')) {
        closeMobileSidebar();
      }
    }
  });
  
  // Restore sidebar state from localStorage
  const savedSidebarState = localStorage.getItem('sidebarCollapsed');
  if (savedSidebarState === 'true') {
    state.sidebarCollapsed = true;
    sidebar.classList.add('collapsed');
  }
  
  // Restore section from localStorage or URL hash
  const hash = window.location.hash.substring(1);
  const savedSection = localStorage.getItem('currentSection');
  
  if (hash) {
    switchSection(hash);
  } else if (savedSection) {
    switchSection(savedSection);
  } else {
    switchSection('dashboard');
  }
  
  // Handle browser back/forward
  window.addEventListener('hashchange', function() {
    const hash = window.location.hash.substring(1);
    if (hash) {
      switchSection(hash);
    }
  });
  
  // Logout button
  const logoutBtn = document.querySelector('.btn-logout');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function() {
      if (confirm('Are you sure you want to logout?')) {
        // Clear all admin session data
        localStorage.removeItem('currentSection');
        localStorage.removeItem('sidebarCollapsed');
        localStorage.removeItem('adminLoggedIn');
        localStorage.removeItem('adminEmail');
        localStorage.removeItem('loginTime');
        
        // Redirect to admin login page
        window.location.href = 'admin-login.html';
      }
    });
  }
  
  // Display admin email in user profile
  const adminEmail = localStorage.getItem('adminEmail');
  if (adminEmail) {
    const userNameElement = document.querySelector('.user-name');
    const userRoleElement = document.querySelector('.user-role');
    if (userNameElement) {
      userNameElement.textContent = adminEmail.split('@')[0];
    }
    if (userRoleElement) {
      userRoleElement.textContent = 'Administrator';
    }
  }
  
  // Initialize dashboard
  initDashboard();
  
  console.log('Admin Dashboard initialized');
});

// ===== Dashboard Functions =====
function initDashboard() {
  // Add animation to stat cards
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      
      requestAnimationFrame(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      });
    }, index * 100);
  });
  
  // Real-time clock for dashboard (optional)
  updateDashboardTime();
  setInterval(updateDashboardTime, 60000); // Update every minute
}

function updateDashboardTime() {
  const now = new Date();
  const timeString = now.toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });
  const dateString = now.toLocaleDateString('en-US', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
  
  // You can display this in a dashboard element if needed
  // console.log(`${dateString} - ${timeString}`);
}

// ===== Table Functions =====
function initTables() {
  // Add row click handlers
  const tableRows = document.querySelectorAll('.data-table tbody tr');
  tableRows.forEach(row => {
    row.style.cursor = 'pointer';
  });
}

// ===== Search Functionality =====
const searchInput = document.querySelector('.top-bar-search input');
if (searchInput) {
  searchInput.addEventListener('input', debounce(function(e) {
    const query = e.target.value.toLowerCase();
    console.log('Searching for:', query);
    // Implement search logic here
  }, 300));
}

// ===== Filter Functions =====
const filterInputs = document.querySelectorAll('.filter-input');
filterInputs.forEach(input => {
  input.addEventListener('input', debounce(function(e) {
    const query = e.target.value.toLowerCase();
    console.log('Filtering:', query);
    // Implement filter logic here
  }, 300));
});

const filterSelects = document.querySelectorAll('.filter-select');
filterSelects.forEach(select => {
  select.addEventListener('change', function(e) {
    const value = e.target.value;
    console.log('Filter changed:', value);
    // Implement filter logic here
  });
});

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

function showNotification(message, type = 'info') {
  // Simple notification system
  const notification = document.createElement('div');
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 5rem;
    right: 1.5rem;
    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    animation: slideInRight 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// ===== Export Report Function =====
const exportButton = document.querySelector('.section-header button');
if (exportButton && exportButton.textContent.includes('Export')) {
  exportButton.addEventListener('click', function() {
    showNotification('Report exported successfully!', 'success');
    // Implement actual export logic here
  });
}

// ===== Action Buttons (Edit, Delete) =====
document.addEventListener('click', function(e) {
  // Edit button
  if (e.target.closest('.icon-btn-sm[title="Edit"]')) {
    const row = e.target.closest('tr');
    console.log('Edit clicked for row:', row);
    showNotification('Edit functionality coming soon!', 'info');
    // Implement edit logic here
  }
  
  // Delete button
  if (e.target.closest('.icon-btn-sm[title="Delete"]')) {
    if (confirm('Are you sure you want to delete this item?')) {
      const row = e.target.closest('tr');
      console.log('Delete clicked for row:', row);
      showNotification('Item deleted successfully!', 'success');
      // Implement delete logic here
      row.style.opacity = '0';
      setTimeout(() => row.remove(), 300);
    }
  }
});

// ===== Add New Buttons =====
const addButtons = document.querySelectorAll('.header-actions .btn-secondary');
addButtons.forEach(button => {
  if (button.textContent.includes('Add')) {
    button.addEventListener('click', function() {
      const sectionName = this.textContent.replace('Add New ', '').trim();
      showNotification(`Add ${sectionName} functionality coming soon!`, 'info');
      // Implement add logic here
    });
  }
});

// ===== Responsive Behavior =====
let resizeTimer;
window.addEventListener('resize', function() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
    // Close mobile sidebar on desktop view
    if (window.innerWidth > 768) {
      sidebar.classList.remove('mobile-visible');
    }
  }, 250);
});

// ===== Initialize =====
document.addEventListener('DOMContentLoaded', function() {
  initTables();
  console.log('All admin features initialized');
});
