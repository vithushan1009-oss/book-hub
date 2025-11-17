// BOOK HUB - Authentication Page Scripts

document.addEventListener('DOMContentLoaded', function() {
  initPasswordToggle();
  initPasswordStrength();
  initFormValidation();
  initSocialAuth();
  initFormAnimations();
});

// Password Toggle Visibility
function initPasswordToggle() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const input = this.closest('.input-with-icon').querySelector('input');
      const eyeIcon = this.querySelector('.eye-icon');
      const eyeOffIcon = this.querySelector('.eye-off-icon');
      
      if (input.type === 'password') {
        input.type = 'text';
        if (eyeIcon) eyeIcon.style.display = 'none';
        if (eyeOffIcon) eyeOffIcon.style.display = 'block';
      } else {
        input.type = 'password';
        if (eyeIcon) eyeIcon.style.display = 'block';
        if (eyeOffIcon) eyeOffIcon.style.display = 'none';
      }
    });
  });
}

// Password Strength Indicator
function initPasswordStrength() {
  const passwordInput = document.getElementById('password');
  const strengthFill = document.querySelector('.strength-fill');
  const strengthText = document.querySelector('.strength-text');
  
  if (!passwordInput || !strengthFill || !strengthText) return;
  
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    
    // Update strength bar
    strengthFill.className = 'strength-fill';
    
    if (password.length === 0) {
      strengthFill.style.width = '0';
      strengthText.textContent = '';
      return;
    }
    
    if (strength < 40) {
      strengthFill.classList.add('weak');
      strengthText.textContent = 'Weak password';
      strengthText.style.color = '#ef4444';
    } else if (strength < 70) {
      strengthFill.classList.add('medium');
      strengthText.textContent = 'Medium password';
      strengthText.style.color = 'var(--accent)';
    } else {
      strengthFill.classList.add('strong');
      strengthText.textContent = 'Strong password';
      strengthText.style.color = '#10b981';
    }
  });
}

function calculatePasswordStrength(password) {
  let strength = 0;
  
  if (password.length >= 8) strength += 25;
  if (password.length >= 12) strength += 15;
  if (/[a-z]/.test(password)) strength += 15;
  if (/[A-Z]/.test(password)) strength += 15;
  if (/[0-9]/.test(password)) strength += 15;
  if (/[^a-zA-Z0-9]/.test(password)) strength += 15;
  
  return Math.min(strength, 100);
}

// Form Validation
function initFormValidation() {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  
  if (loginForm) {
    loginForm.addEventListener('submit', handleLoginSubmit);
  }
  
  if (registerForm) {
    registerForm.addEventListener('submit', handleRegisterSubmit);
  }
}

function handleLoginSubmit(e) {
  e.preventDefault();
  
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const submitBtn = e.target.querySelector('.auth-submit');
  
  // Clear previous errors
  clearErrors();
  
  // Validation
  let isValid = true;
  
  if (!isValidEmail(email)) {
    showError('email', 'Please enter a valid email address');
    isValid = false;
  }
  
  if (password.length < 6) {
    showError('password', 'Password must be at least 6 characters');
    isValid = false;
  }
  
  if (!isValid) return;
  
  // Show loading state
  submitBtn.classList.add('loading');
  submitBtn.disabled = true;
  
  // Simulate API call
  setTimeout(() => {
    submitBtn.classList.remove('loading');
    submitBtn.disabled = false;
    
    // Success animation
    showSuccessMessage('Login successful! Redirecting...');
    
    // Redirect to home page after 1.5 seconds
    setTimeout(() => {
      window.location.href = 'index.html';
    }, 1500);
  }, 1500);
}

function handleRegisterSubmit(e) {
  e.preventDefault();
  
  const firstName = document.getElementById('first-name').value;
  const lastName = document.getElementById('last-name').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm-password').value;
  const terms = document.getElementById('terms').checked;
  const submitBtn = e.target.querySelector('.auth-submit');
  
  // Clear previous errors
  clearErrors();
  
  // Validation
  let isValid = true;
  
  if (firstName.trim().length < 2) {
    showError('first-name', 'First name must be at least 2 characters');
    isValid = false;
  }
  
  if (lastName.trim().length < 2) {
    showError('last-name', 'Last name must be at least 2 characters');
    isValid = false;
  }
  
  if (!isValidEmail(email)) {
    showError('email', 'Please enter a valid email address');
    isValid = false;
  }
  
  if (password.length < 8) {
    showError('password', 'Password must be at least 8 characters');
    isValid = false;
  }
  
  if (password !== confirmPassword) {
    showError('confirm-password', 'Passwords do not match');
    isValid = false;
  }
  
  if (!terms) {
    showError('terms', 'You must accept the terms and conditions');
    isValid = false;
  }
  
  if (!isValid) return;
  
  // Show loading state
  submitBtn.classList.add('loading');
  submitBtn.disabled = true;
  
  // Simulate API call
  setTimeout(() => {
    submitBtn.classList.remove('loading');
    submitBtn.disabled = false;
    
    // Success animation
    showSuccessMessage('Account created successfully! Redirecting...');
    
    // Redirect to login page after 1.5 seconds
    setTimeout(() => {
      window.location.href = 'login.html';
    }, 1500);
  }, 1500);
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  
  const formGroup = field.closest('.form-group') || field.closest('.checkbox-label');
  if (!formGroup) return;
  
  // Add error class to input
  field.style.borderColor = '#ef4444';
  
  // Create or update error message
  let errorMsg = formGroup.querySelector('.error-message');
  if (!errorMsg) {
    errorMsg = document.createElement('span');
    errorMsg.className = 'error-message';
    errorMsg.style.cssText = 'color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;';
    formGroup.appendChild(errorMsg);
  }
  errorMsg.textContent = message;
  
  // Shake animation
  field.style.animation = 'shake 0.3s ease';
  setTimeout(() => {
    field.style.animation = '';
  }, 300);
}

function clearErrors() {
  const errorMessages = document.querySelectorAll('.error-message');
  errorMessages.forEach(msg => msg.remove());
  
  const inputs = document.querySelectorAll('.auth-form input');
  inputs.forEach(input => {
    input.style.borderColor = '';
  });
}

function showSuccessMessage(message) {
  // Create success notification
  const notification = document.createElement('div');
  notification.className = 'success-notification';
  notification.style.cssText = `
    position: fixed;
    top: 6rem;
    right: 2rem;
    background: #10b981;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    animation: slideInRight 0.3s ease-out;
  `;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Social Authentication
function initSocialAuth() {
  const socialButtons = document.querySelectorAll('.social-btn');
  
  socialButtons.forEach(button => {
    button.addEventListener('click', function() {
      const provider = this.querySelector('span').textContent;
      
      // Add pulse animation
      this.style.animation = 'pulse 0.3s ease';
      setTimeout(() => {
        this.style.animation = '';
      }, 300);
      
      // Simulate social auth
      console.log(`Authenticating with ${provider}...`);
      showSuccessMessage(`Connecting to ${provider}...`);
    });
  });
}

// Form Input Animations
function initFormAnimations() {
  const inputs = document.querySelectorAll('.auth-form input');
  
  inputs.forEach(input => {
    // Focus animation
    input.addEventListener('focus', function() {
      const icon = this.parentElement.querySelector('svg');
      if (icon) {
        icon.style.transform = 'scale(1.1)';
        icon.style.color = 'var(--primary)';
      }
    });
    
    // Blur animation
    input.addEventListener('blur', function() {
      const icon = this.parentElement.querySelector('svg');
      if (icon) {
        icon.style.transform = 'scale(1)';
        icon.style.color = 'var(--muted-foreground)';
      }
    });
    
    // Add smooth transitions to icons
    const icon = input.parentElement.querySelector('svg');
    if (icon) {
      icon.style.transition = 'all 0.3s ease';
    }
  });
}

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
  }
  
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(0.95); }
  }
  
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100px);
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
      transform: translateX(100px);
    }
  }
`;
document.head.appendChild(style);
