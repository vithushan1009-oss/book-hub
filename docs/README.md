# BOOK HUB - Book Rental Platform

A modern book rental platform built with PHP, MySQL, and vanilla JavaScript. Features include user authentication, email verification, admin dashboard, and book management.

## 📁 Project Structure

```
book-hub-central/
├── backend/                    # PHP Backend Handlers (No Classes)
│   ├── login-handler.php       # User login processing
│   ├── register-handler.php    # User registration with email verification
│   ├── admin-login-handler.php # Admin authentication
│   ├── verify-handler.php      # Email verification handler
│   ├── logout-handler.php      # Session logout
│   └── email-functions.php     # SMTP email functions
├── css/                        # Stylesheets
│   ├── variables.css
│   ├── base.css
│   ├── components.css
│   ├── navigation.css
│   ├── footer.css
│   ├── auth.css
│   ├── admin.css
│   └── home.css
├── js/                         # JavaScript Files
│   ├── common.js
│   ├── auth.js
│   ├── admin.js
│   └── home.js
├── assets/                     # Images and media
├── database/                   # Database schema
│   └── schema.sql
├── config.php                  # Configuration loader (loads .env)
├── .env                        # Environment variables (create from .env.example)
├── .env.example                # Example environment configuration
├── index.html                  # Homepage
├── login.html                  # User login page
├── register.html               # User registration page
├── admin-login.html            # Admin login page
├── user.php                    # User dashboard
├── admin.php                   # Admin dashboard
├── profile.php                 # User profile management
├── manage-users.php            # Admin user management
├── books.html                  # Books listing
├── about.html                  # About page
├── contact.html                # Contact page
└── gallery.html                # Gallery page
```

## 🚀 Features

### User Features
- ✅ User Registration with Email Verification
- ✅ Secure Login with Password Hashing
- ✅ Email Verification via SMTP
- ✅ Rate Limiting (5 attempts, 15-minute lockout)
- ✅ Remember Me Functionality
- ✅ User Dashboard
- ✅ Profile Management
- ✅ Book Browsing

### Admin Features
- ✅ Admin Authentication
- ✅ Two-Factor Authentication Support
- ✅ Admin Dashboard with Statistics
- ✅ User Management (View, Edit, Delete)
- ✅ Activity Logging
- ✅ Secure Admin Area

### Security Features
- ✅ Password Hashing (bcrypt)
- ✅ Prepared Statements (SQL Injection Prevention)
- ✅ Session Management
- ✅ CSRF Protection Ready
- ✅ Login Attempt Tracking
- ✅ Email Verification
- ✅ Secure Cookie Handling

## 📋 Requirements

- XAMPP 3.3.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Server
- SMTP Email Account (Gmail recommended)

## ⚙️ Installation

### 1. Clone/Download Project
```bash
cd C:\xampp\htdocs\BOOKHUB
# Extract or clone the project to book-hub-central folder
```

### 2. Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** (Port 80/443)
- Start **MySQL** (Port 3306)

### 3. Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create database: `bookhub_db`
3. Import schema: `database/schema.sql`

### 4. Configure Environment
1. Copy `.env.example` to `.env`:
   ```bash
   copy .env.example .env
   ```

2. Edit `.env` file with your settings:
   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=bookhub_db
   DB_USER=root
   DB_PASS=

   # Email Configuration (Gmail Example)
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_ENCRYPTION=tls
   SMTP_USERNAME=your-email@gmail.com
   SMTP_PASSWORD=your-app-password

   # Application URL
   APP_URL=http://localhost/BOOKHUB/book-hub-central
   ```

### 5. Configure Gmail SMTP (Optional but Recommended)

#### Enable 2-Step Verification
1. Go to Google Account Settings
2. Security → 2-Step Verification → Turn On

#### Create App Password
1. Google Account → Security
2. 2-Step Verification → App passwords
3. Select "Mail" and "Windows Computer"
4. Copy the 16-character password
5. Paste into `.env` file (`SMTP_PASSWORD`)

### 6. Test Installation
Visit: http://localhost/BOOKHUB/book-hub-central

## 🔐 Default Credentials

### Admin Account
- **Email:** admin@bookhub.com
- **Password:** admin123

### Test User Account
- **Email:** test@example.com
- **Password:** test1234
- **Note:** Email verified by default

## 📧 Email Verification Flow

1. User registers → Account created with `email_verified = 0`
2. Verification email sent with token (24-hour expiry)
3. User clicks verification link
4. `backend/verify-handler.php` validates token
5. Account marked as verified → User can login

## 🗄️ Database Schema

### Tables
- `users` - User accounts
- `admins` - Administrator accounts
- `sessions` - Remember me tokens
- `login_attempts` - Failed login tracking
- `email_logs` - Email delivery logs
- `password_reset_tokens` - Password reset tokens

## 🛠️ File Structure Explanation

### Backend Handlers (No Classes)
All backend logic is in separate handler files without classes:

- **login-handler.php** - Processes user login POST requests
- **register-handler.php** - Handles registration and sends verification email
- **admin-login-handler.php** - Admin authentication
- **verify-handler.php** - Email verification via token
- **logout-handler.php** - Destroys sessions and cookies
- **email-functions.php** - SMTP email sending functions

### Frontend (HTML)
Clean HTML files post to backend handlers:

```html
<form action="backend/login-handler.php" method="POST">
  <input type="email" name="email" required>
  <input type="password" name="password" required>
  <button type="submit">Sign In</button>
</form>
```

### Configuration
- **config.php** - Loads `.env` variables, provides database connection
- **.env** - Environment variables (database, SMTP, security settings)

## 🔧 Configuration Options

### Security Settings (.env)
```env
PASSWORD_MIN_LENGTH=8              # Minimum password length
MAX_LOGIN_ATTEMPTS=5               # Max failed attempts before lockout
LOCKOUT_TIME=900                   # Lockout duration (15 minutes)
EMAIL_VERIFICATION_EXPIRY=86400    # Token expiry (24 hours)
SESSION_LIFETIME=28800             # Session timeout (8 hours)
```

## 📱 Pages Overview

### Public Pages (HTML)
- `index.html` - Homepage
- `login.html` - User login
- `register.html` - User registration
- `admin-login.html` - Admin login
- `books.html` - Book catalog
- `about.html` - About us
- `contact.html` - Contact form
- `gallery.html` - Image gallery

### Protected Pages (PHP)
- `user.php` - User dashboard (requires login)
- `admin.php` - Admin dashboard (requires admin login)
- `profile.php` - User profile management
- `manage-users.php` - Admin user management

## 🚦 Error Handling

Messages are passed via URL parameters:
```php
// Success
header('Location: login.html?success=' . urlencode('Registration successful!'));

// Error
header('Location: login.html?error=' . urlencode('Invalid credentials'));
```

JavaScript displays messages:
```javascript
const params = new URLSearchParams(window.location.search);
const error = params.get('error');
if (error) {
  messageContainer.innerHTML = `<div class="alert alert-error">${decodeURIComponent(error)}</div>`;
}
```

## 🔍 Testing Checklist

- [ ] Database connection works
- [ ] User registration creates account
- [ ] Verification email sent successfully
- [ ] Email verification link works
- [ ] User login successful
- [ ] Admin login successful
- [ ] User dashboard loads
- [ ] Admin dashboard shows statistics
- [ ] Profile update works
- [ ] User management (edit/delete) works
- [ ] Logout clears session
- [ ] Rate limiting prevents brute force

## 📚 API Endpoints

### User Authentication
- `POST /backend/register-handler.php` - Register user
- `POST /backend/login-handler.php` - User login
- `GET /backend/verify-handler.php?token=xxx` - Verify email

### Admin
- `POST /backend/admin-login-handler.php` - Admin login

### Session
- `GET /backend/logout-handler.php` - Logout

## 🐛 Troubleshooting

### Email Not Sending
1. Check SMTP credentials in `.env`
2. Verify Gmail App Password is correct
3. Check `email_logs` table for error status
4. Enable error reporting: `error_reporting(E_ALL);`

### Database Connection Error
1. Verify MySQL is running in XAMPP
2. Check database name: `bookhub_db`
3. Verify credentials in `.env`
4. Import `database/schema.sql`

### Login Not Working
1. Check if email is verified (`email_verified = 1`)
2. Check login attempts table for lockouts
3. Verify password hash in database
4. Clear browser cookies and session

### Permission Errors
1. Ensure XAMPP has write permissions
2. Check session folder permissions
3. Verify `.env` file exists and is readable

## 📝 Development Notes

### Adding New Features
1. Create handler in `backend/` folder
2. Use `config.php` for database connection
3. Follow existing pattern (no classes)
4. Use prepared statements for security
5. Pass messages via URL parameters

### Extending Database
1. Add columns to `database/schema.sql`
2. Run ALTER TABLE queries in phpMyAdmin
3. Update handlers to use new columns

## 🔒 Security Best Practices

- ✅ Never commit `.env` file to version control
- ✅ Always use prepared statements
- ✅ Hash passwords with `password_hash()`
- ✅ Validate and sanitize all inputs
- ✅ Use HTTPS in production
- ✅ Set secure cookie flags in production
- ✅ Regular security updates

## 📞 Support

For issues or questions:
- Check the database logs
- Review PHP error logs: `C:\xampp\apache\logs\error.log`
- Check browser console for JavaScript errors

## 📄 License

© 2025 BOOK HUB. All rights reserved.

## 🎯 Quick Start Commands

```bash
# Start XAMPP services
# Via XAMPP Control Panel

# Access phpMyAdmin
http://localhost/phpmyadmin

# Access application
http://localhost/BOOKHUB/book-hub-central

# View logs
C:\xampp\apache\logs\error.log
C:\xampp\mysql\data\*.err
```

---

**Note:** This project uses a clean separation between frontend (HTML) and backend (PHP handlers) with no class-based architecture. All configurations are managed via the `.env` file.
