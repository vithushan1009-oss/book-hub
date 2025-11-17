// BOOK HUB - Books Page JavaScript

// ===== Filter Functionality =====
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
  
  // Check URL for search parameter
  const urlParams = new URLSearchParams(window.location.search);
  const searchQuery = urlParams.get('search');
  if (searchQuery && searchInput) {
    searchInput.value = searchQuery;
    filterBooks();
  }
}

function filterBooks() {
  const category = document.getElementById('category')?.value || 'all';
  const sortBy = document.getElementById('sort')?.value || 'popular';
  const searchQuery = document.querySelector('.filters input[type="text"]')?.value.toLowerCase() || '';

  console.log('Filtering books:', { category, sortBy, searchQuery });
  
  // Get all book cards
  const bookCards = document.querySelectorAll('.book-card');
  
  bookCards.forEach(card => {
    const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
    const author = card.querySelector('.author')?.textContent.toLowerCase() || '';
    
    // Simple search filter
    const matchesSearch = !searchQuery || 
                         title.includes(searchQuery) || 
                         author.includes(searchQuery);
    
    if (matchesSearch) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
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
  const animateElements = document.querySelectorAll('.book-card');
  animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
}

// ===== Initialize Books Page Functions =====
window.addEventListener('DOMContentLoaded', function() {
  initFilters();
  initBookCards();
  initPagination();
  initScrollAnimations();
  
  console.log('BOOK HUB - Books page initialized');
});
