// BOOK HUB - Contact Page JavaScript

// ===== Form Handling =====
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
      
      // Validate form
      if (!validateForm(formData)) {
        return;
      }
      
      console.log('Form submitted:', formData);
      alert('Thank you for your message!\nIn a full application, this would send your message to our team.');
      
      // Reset form
      contactForm.reset();
    });
  }
}

function validateForm(data) {
  if (!data.name || data.name.trim() === '') {
    alert('Please enter your name');
    return false;
  }
  
  if (!data.email || !isValidEmail(data.email)) {
    alert('Please enter a valid email address');
    return false;
  }
  
  if (!data.subject || data.subject.trim() === '') {
    alert('Please enter a subject');
    return false;
  }
  
  if (!data.message || data.message.trim() === '') {
    alert('Please enter a message');
    return false;
  }
  
  return true;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// ===== Initialize Contact Page Functions =====
window.addEventListener('DOMContentLoaded', function() {
  initContactForm();
  
  console.log('BOOK HUB - Contact page initialized');
});
