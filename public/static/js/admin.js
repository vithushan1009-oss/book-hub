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

  // Theme Toggle
  if (themeToggle) {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      document.body.classList.add('dark-mode');
      themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    themeToggle.addEventListener('click', function() {
      document.body.classList.toggle('dark-mode');
      const isDark = document.body.classList.contains('dark-mode');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      themeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
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

  // Navigation items
  navItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const sectionId = this.dataset.section;
      
      if (sectionId) {
        // Remove active class from all nav items
        navItems.forEach(nav => nav.classList.remove('active'));
        
        // Add active class to clicked item
        this.classList.add('active');
        
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => {
          section.classList.remove('active');
        });
        
        // Show selected section
        const selectedSection = document.getElementById(`${sectionId}-section`);
        if (selectedSection) {
          selectedSection.classList.add('active');
        }
        
        // Close mobile sidebar
        if (window.innerWidth <= 768) {
          sidebar.classList.remove('mobile-visible');
        }
        
        // Update state
        state.currentSection = sectionId;
        localStorage.setItem('currentSection', sectionId);
      }
    });
  });

  // Logout functionality
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (confirm('Are you sure you want to logout?')) {
        // Redirect to logout handler
        window.location.href = '/BOOKHUB/book-hub-central/src/handlers/admin-logout-handler.php';
      }
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

  // Restore section state
  const savedSection = localStorage.getItem('currentSection');
  const hash = window.location.hash.substring(1);
  
  if (hash) {
    switchToSection(hash);
  } else if (savedSection) {
    switchToSection(savedSection);
  }

  // Handle hash changes
  window.addEventListener('hashchange', function() {
    const hash = window.location.hash.substring(1);
    if (hash) {
      switchToSection(hash);
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
    }, 250);
  });

  // Add smooth animations to stat cards
  animateStatCards();

  console.log('Dashboard initialized successfully');
}

// ===== Switch Section Function =====
function switchToSection(sectionId) {
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
  }
  
  state.currentSection = sectionId;
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
`;
document.head.appendChild(style);
