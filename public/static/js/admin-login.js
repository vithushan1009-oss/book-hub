// BOOK HUB - Admin Login JavaScript

// ===== Toggle Password Visibility =====
function toggleAdminPassword() {
  const passwordInput = document.getElementById('admin-password');
  const toggleButton = passwordInput.nextElementSibling;
  const eyeIcon = toggleButton.querySelector('.eye-open');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  } else {
    passwordInput.type = 'password';
    eyeIcon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
  }
}

// ===== Admin Login Form Handling =====
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('admin-login-form');
  const emailInput = document.getElementById('admin-email');
  const passwordInput = document.getElementById('admin-password');
  const submitButton = loginForm.querySelector('.admin-submit');
  
  // Form submission
  loginForm.addEventListener('submit', function(e) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.error-message, .success-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Get form values
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    
    // Basic validation
    if (!email || !password) {
      e.preventDefault();
      showMessage('Please fill in all required fields.', 'error');
      return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      e.preventDefault();
      showMessage('Please enter a valid email address.', 'error');
      return;
    }
    
    // Show loading state
    submitButton.classList.add('loading');
    submitButton.disabled = true;
    submitButton.textContent = 'Signing in...';
    
    // Form will submit normally to PHP handler
  });
  
  // Auto-focus email field
  emailInput.focus();
  
  console.log('Admin login initialized');
});

// ===== Show Message Function =====
function showMessage(message, type) {
  const messageDiv = document.createElement('div');
  messageDiv.className = `${type}-message show`;
  messageDiv.textContent = message;
  
  const form = document.getElementById('admin-login-form');
  form.insertBefore(messageDiv, form.firstChild);
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    messageDiv.classList.remove('show');
    setTimeout(() => messageDiv.remove(), 300);
  }, 5000);
}

// ===== Check Admin Session =====
function checkAdminSession() {
  // Removed auto-redirect - let PHP handle session checking
}

// ===== Log Failed Attempt =====
function logFailedAttempt(email) {
  const attempts = JSON.parse(localStorage.getItem('failedAttempts') || '[]');
  attempts.push({
    email: email,
    timestamp: new Date().toISOString(),
    ip: 'client-side' // In production, get from server
  });
  
  // Keep only last 10 attempts
  if (attempts.length > 10) {
    attempts.shift();
  }
  
  localStorage.setItem('failedAttempts', JSON.stringify(attempts));
  console.warn('Failed login attempt logged');
}

// ===== Security Features =====

// Prevent copy/paste in password field (optional security measure)
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('admin-password');
  
  // Uncomment to enable copy/paste prevention
  // passwordInput.addEventListener('copy', function(e) {
  //   e.preventDefault();
  //   showMessage('Copying password is not allowed for security reasons.', 'error');
  // });
  
  // passwordInput.addEventListener('paste', function(e) {
  //   e.preventDefault();
  //   showMessage('Pasting into password field is not allowed for security reasons.', 'error');
  // });
});

// ===== Keyboard Shortcuts =====
document.addEventListener('keydown', function(e) {
  // Ctrl/Cmd + K to focus email field
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    document.getElementById('admin-email').focus();
  }
});

// ===== Detect Caps Lock =====
document.addEventListener('DOMContentLoaded', function() {
  const passwordInput = document.getElementById('admin-password');
  let capsLockWarning = null;
  
  passwordInput.addEventListener('keyup', function(e) {
    const isCapsLock = e.getModifierState && e.getModifierState('CapsLock');
    
    if (isCapsLock && !capsLockWarning) {
      capsLockWarning = document.createElement('div');
      capsLockWarning.className = 'caps-lock-warning';
      capsLockWarning.style.cssText = `
        font-size: 0.75rem;
        color: var(--accent);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      `;
      capsLockWarning.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
          <line x1="12" y1="9" x2="12" y2="13"/>
          <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>Caps Lock is ON</span>
      `;
      passwordInput.parentElement.parentElement.appendChild(capsLockWarning);
    } else if (!isCapsLock && capsLockWarning) {
      capsLockWarning.remove();
      capsLockWarning = null;
    }
  });
});

// ===== Form Auto-fill Detection =====
document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input');
  
  inputs.forEach(input => {
    // Check for auto-fill after a short delay
    setTimeout(() => {
      if (input.matches(':-webkit-autofill')) {
        console.log('Auto-fill detected for:', input.name);
      }
    }, 500);
  });
});

// Demo credentials helper removed for production - no demo credentials shown
