// BOOK HUB - Contact Page JavaScript
// Updated: 2025-12-12

// ===== Form Handling =====
function initContactForm() {
  const contactForm = document.getElementById('contact-form');
  
  if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      
      // Get form values directly
      const firstName = document.getElementById('firstName')?.value?.trim() || '';
      const lastName = document.getElementById('lastName')?.value?.trim() || '';
      const email = document.getElementById('email')?.value?.trim() || '';
      const subject = document.getElementById('subject')?.value?.trim() || '';
      const message = document.getElementById('message')?.value?.trim() || '';
      
      // Validate form
      if (!firstName) {
        showContactNotification('error', 'Please enter your first name');
        document.getElementById('firstName')?.focus();
        return;
      }
      
      if (!lastName) {
        showContactNotification('error', 'Please enter your last name');
        document.getElementById('lastName')?.focus();
        return;
      }
      
      if (!email || !isValidEmail(email)) {
        showContactNotification('error', 'Please enter a valid email address');
        document.getElementById('email')?.focus();
        return;
      }
      
      if (!subject) {
        showContactNotification('error', 'Please enter a subject');
        document.getElementById('subject')?.focus();
        return;
      }
      
      if (!message) {
        showContactNotification('error', 'Please enter a message');
        document.getElementById('message')?.focus();
        return;
      }
      
      if (message.length < 10) {
        showContactNotification('error', 'Message must be at least 10 characters long');
        document.getElementById('message')?.focus();
        return;
      }
      
      // Create form data for submission
      const formData = new FormData();
      formData.append('firstName', firstName);
      formData.append('lastName', lastName);
      formData.append('email', email);
      formData.append('subject', subject);
      formData.append('message', message);
      
      // Disable button and show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      
      try {
        // Try different base paths for the handler
        let response;
        const handlerPaths = [
          '/book-hub/src/handlers/contact-handler.php',
          '../src/handlers/contact-handler.php',
          '../../src/handlers/contact-handler.php'
        ];
        
        for (const path of handlerPaths) {
          try {
            response = await fetch(path, {
              method: 'POST',
              body: formData
            });
            if (response.ok || response.status === 400 || response.status === 500) {
              break;
            }
          } catch (fetchError) {
            continue;
          }
        }
        
        if (!response) {
          throw new Error('Could not connect to server');
        }
        
        const result = await response.json();
        
        if (result.success) {
          // Show success message
          showContactNotification('success', result.message);
          
          // Reset form
          contactForm.reset();
          
          // Clear any error states
          contactForm.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('error');
          });
        } else {
          // Show error message
          showContactNotification('error', result.message || 'An error occurred. Please try again.');
        }
      } catch (error) {
        console.error('Error submitting form:', error);
        showContactNotification('error', 'An error occurred while sending your message. Please try again later.');
      } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    });
    
    // Add input event listeners for real-time validation feedback
    const inputs = contactForm.querySelectorAll('input, textarea');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        validateInput(this);
      });
      input.addEventListener('input', function() {
        // Remove error state on input
        this.closest('.form-group')?.classList.remove('error');
      });
    });
  }
}

function validateInput(input) {
  const formGroup = input.closest('.form-group');
  const value = input.value.trim();
  
  if (input.required && !value) {
    formGroup?.classList.add('error');
    return false;
  }
  
  if (input.type === 'email' && value && !isValidEmail(value)) {
    formGroup?.classList.add('error');
    return false;
  }
  
  formGroup?.classList.remove('error');
  return true;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Show notification toast - renamed to avoid conflicts with other scripts
function showContactNotification(type, message) {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll('.contact-notification');
  existingNotifications.forEach(n => n.remove());
  
  // Icons - use FontAwesome if available, otherwise use SVG
  const successIcon = typeof FontAwesome !== 'undefined' || document.querySelector('.fas') 
    ? '<i class="fas fa-check-circle"></i>'
    : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
  
  const errorIcon = typeof FontAwesome !== 'undefined' || document.querySelector('.fas')
    ? '<i class="fas fa-exclamation-circle"></i>'
    : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
  
  const closeIcon = typeof FontAwesome !== 'undefined' || document.querySelector('.fas')
    ? '<i class="fas fa-times"></i>'
    : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `contact-notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      ${type === 'success' ? successIcon : errorIcon}
      <span>${message}</span>
    </div>
    <button class="notification-close" onclick="this.parentElement.remove()">
      ${closeIcon}
    </button>
  `;
  
  // Add styles if not already present
  if (!document.getElementById('contact-notification-styles')) {
    const styles = document.createElement('style');
    styles.id = 'contact-notification-styles';
    styles.textContent = `
      .contact-notification {
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      }
      .contact-notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
      }
      .contact-notification.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
      }
      .contact-notification .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }
      .contact-notification .notification-content i {
        font-size: 1.25rem;
      }
      .contact-notification .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.25rem;
        opacity: 0.8;
        transition: opacity 0.2s;
      }
      .contact-notification .notification-close:hover {
        opacity: 1;
      }
      @keyframes slideIn {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    `;
    document.head.appendChild(styles);
  }
  
  // Add to page
  document.body.appendChild(notification);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentElement) {
      notification.style.animation = 'slideIn 0.3s ease reverse';
      setTimeout(() => notification.remove(), 300);
    }
  }, 5000);
}

// ===== Initialize Contact Page Functions =====
window.addEventListener('DOMContentLoaded', function() {
  initContactForm();
  
  console.log('BOOK HUB - Contact page initialized');
});
