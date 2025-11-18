// BOOK HUB - Home Page JavaScript

// ===== Search Functionality =====
function initSearch() {
  const searchInput = document.querySelector('.hero-search input');
  const searchBtn = document.querySelector('.hero-search button');

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
  const searchInput = document.querySelector('.hero-search input');
  if (searchInput) {
    const query = searchInput.value.trim();
    if (query) {
      console.log('Searching for:', query);
      // Redirect to books page with search query
      window.location.href = `books.html?search=${encodeURIComponent(query)}`;
    }
  }
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

// ===== Initialize Home Page Functions =====
window.addEventListener('DOMContentLoaded', function() {
  initSearch();
  initBookCards();
  initCTAButtons();
  initScrollAnimations();
  
  console.log('BOOK HUB - Home page initialized');
});
