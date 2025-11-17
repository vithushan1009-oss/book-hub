// BOOK HUB - Static Site JavaScript

// ===== Navigation Functionality =====
document.addEventListener('DOMContentLoaded', function() {
  const nav = document.querySelector('nav');
  const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
  const mobileMenu = document.querySelector('.mobile-menu');
  const mobileMenuLinks = document.querySelectorAll('.mobile-menu a');
  
  // Scroll Effect for Navigation
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  });

  // Mobile Menu Toggle
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function() {
      mobileMenu.classList.toggle('active');
      
      // Toggle icon
      const icon = mobileMenuBtn.querySelector('svg use');
      if (icon) {
        const currentIcon = icon.getAttribute('href');
        const newIcon = currentIcon.includes('menu') ? '#icon-x' : '#icon-menu';
        icon.setAttribute('href', newIcon);
      }
    });

    // Close mobile menu when clicking a link
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
      });
    });
  }

  // Active Link Highlighting
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('.nav-links a, .mobile-menu a');
  
  navLinks.forEach(link => {
    const linkPath = new URL(link.href).pathname;
    if (currentPath === linkPath || (currentPath === '/' && linkPath.includes('index.html'))) {
      link.classList.add('active');
    }
  });
});

// ===== Search Functionality =====
function initSearch() {
  const searchInput = document.querySelector('.hero-search input, .search-input input');
  const searchBtn = document.querySelector('.hero-search button, .search-input + button');

  if (searchBtn) {
    searchBtn.addEventListener('click', performSearch);
  }

  if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch();
      }
    });
  }
}

function performSearch() {
  const searchInput = document.querySelector('.hero-search input, .search-input input');
  if (searchInput) {
    const query = searchInput.value.trim();
    if (query) {
      console.log('Searching for:', query);
      // In a real application, this would filter books or redirect to search results
      alert('Search functionality: "' + query + '"\nIn a full application, this would show filtered results.');
    }
  }
}

// ===== Filter Functionality (Books Page) =====
function initFilters() {
  const categorySelect = document.getElementById('category');
  const sortSelect = document.getElementById('sort');
  const searchInput = document.querySelector('.filters input[type="text"]');

  if (categorySelect) {
    categorySelect.addEventListener('change', filterBooks);
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', filterBooks);
  }

  if (searchInput) {
    searchInput.addEventListener('input', debounce(filterBooks, 300));
  }
}

function filterBooks() {
  const category = document.getElementById('category')?.value || 'all';
  const sortBy = document.getElementById('sort')?.value || 'popular';
  const searchQuery = document.querySelector('.filters input[type="text"]')?.value.toLowerCase() || '';

  console.log('Filtering books:', { category, sortBy, searchQuery });
  
  // In a real application, this would filter and sort the book cards
  // For now, we'll just log the filter criteria
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

// ===== Book Card Interactions =====
function initBookCards() {
  const bookCards = document.querySelectorAll('.book-card');
  
  bookCards.forEach(card => {
    const button = card.querySelector('.btn');
    
    if (button && !button.disabled) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const bookTitle = card.querySelector('h3').textContent;
        const actionType = this.textContent.trim().includes('Rent') ? 'rent' : 'buy';
        
        handleBookAction(bookTitle, actionType);
      });
    }
  });
}

function handleBookAction(title, action) {
  const message = action === 'rent' 
    ? `Renting "${title}"\nIn a full application, this would add the book to your rental cart.`
    : `Purchasing "${title}"\nIn a full application, this would add the book to your shopping cart.`;
  
  alert(message);
  console.log(`${action} book:`, title);
}

// ===== Smooth Scroll =====
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });
}

// ===== Pagination =====
function initPagination() {
  const paginationButtons = document.querySelectorAll('.pagination button');
  
  paginationButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all buttons
      paginationButtons.forEach(btn => btn.classList.remove('active'));
      
      // Add active class to clicked button
      if (!this.textContent.includes('Previous') && !this.textContent.includes('Next')) {
        this.classList.add('active');
      }
      
      // Scroll to top of page
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      console.log('Page changed to:', this.textContent);
      // In a real application, this would load different books
    });
  });
}

// ===== CTA Button Actions =====
function initCTAButtons() {
  const ctaButtons = document.querySelectorAll('.btn-primary, .btn-secondary');
  
  ctaButtons.forEach(button => {
    // Skip book card buttons (already handled)
    if (button.closest('.book-card')) return;
    
    button.addEventListener('click', function(e) {
      const text = this.textContent.trim();
      
      if (text.includes('Browse') || text.includes('View All')) {
        window.location.href = 'books.html';
      } else if (text.includes('Sign In')) {
        alert('Sign In functionality\nIn a full application, this would open a login form.');
      } else if (text.includes('Sign Up') || text.includes('Get Started')) {
        alert('Sign Up functionality\nIn a full application, this would open a registration form.');
      }
    });
  });
}

// ===== Scroll Animations =====
function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Observe elements that should animate on scroll
  const animateElements = document.querySelectorAll('.book-card, .benefit-card, .stat-item');
  animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
}

// ===== Initialize All Functions =====
window.addEventListener('DOMContentLoaded', function() {
  initSearch();
  initFilters();
  initBookCards();
  initSmoothScroll();
  initPagination();
  initCTAButtons();
  initScrollAnimations();
  
  console.log('BOOK HUB initialized successfully!');
});

// ===== Form Handling (Contact Page) =====
function initContactForm() {
  const contactForm = document.getElementById('contact-form');
  
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = {
        name: document.getElementById('name')?.value,
        email: document.getElementById('email')?.value,
        subject: document.getElementById('subject')?.value,
        message: document.getElementById('message')?.value
      };
      
      console.log('Form submitted:', formData);
      alert('Thank you for your message!\nIn a full application, this would send your message to our team.');
      
      // Reset form
      contactForm.reset();
    });
  }
}

// Add contact form initialization to DOMContentLoaded
window.addEventListener('DOMContentLoaded', initContactForm);
