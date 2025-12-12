// BOOK HUB - Contact Page JavaScript

// ===== Form Handling =====
function initContactForm() {
  const contactForm = document.getElementById('contact-form');
  
  if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      
      // Get form data
      const formData = new FormData();
      formData.append('firstName', document.getElementById('firstName')?.value || '');
      formData.append('lastName', document.getElementById('lastName')?.value || '');
      formData.append('email', document.getElementById('email')?.value || '');
      formData.append('subject', document.getElementById('subject')?.value || '');
      formData.append('message', document.getElementById('message')?.value || '');
      
      // Validate form
      if (!validateForm(formData)) {
        return;
      }
      
      // Disable button and show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      
      try {
        const response = await fetch('/book-hub/src/handlers/contact-handler.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          // Show success message
          showNotification('success', result.message);
          
          // Reset form
          contactForm.reset();
        } else {
          // Show error message
          showNotification('error', result.message || 'An error occurred. Please try again.');
        }
      } catch (error) {
        console.error('Error submitting form:', error);
        showNotification('error', 'An error occurred while sending your message. Please try again later.');
      } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    });
  }
}

function validateForm(formData) {
  const firstName = formData.get('firstName');
  const lastName = formData.get('lastName');
  const email = formData.get('email');
  const subject = formData.get('subject');
  const message = formData.get('message');
  
  if (!firstName || firstName.trim() === '') {
    showNotification('error', 'Please enter your first name');
    return false;
  }
  
  if (!lastName || lastName.trim() === '') {
    showNotification('error', 'Please enter your last name');
    return false;
  }
  
  if (!email || !isValidEmail(email)) {
    showNotification('error', 'Please enter a valid email address');
    return false;
  }
  
  if (!subject || subject.trim() === '') {
    showNotification('error', 'Please enter a subject');
    return false;
  }
  
  if (!message || message.trim() === '') {
    showNotification('error', 'Please enter a message');
    return false;
  }
  
  if (message.trim().length < 10) {
    showNotification('error', 'Message must be at least 10 characters long');
    return false;
  }
  
  return true;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Show notification toast
function showNotification(type, message) {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll('.contact-notification');
  existingNotifications.forEach(n => n.remove());
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `contact-notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
      <span>${message}</span>
    </div>
    <button class="notification-close" onclick="this.parentElement.remove()">
      <i class="fas fa-times"></i>
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
