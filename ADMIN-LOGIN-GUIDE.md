# Admin Login & Registration Guide

## ✅ FIXED: Admin Login Now Working

The admin login system has been fixed and tested. Follow this guide to access the admin dashboard.

---

## 🔑 Admin Login

### Access URL
```
http://localhost/book-hub/admin-login
```

### Default Credentials
- **Email:** `admin@bookhub.com`
- **Password:** `admin123`

### After Login
Upon successful login, you will be automatically redirected to:
```
http://localhost/book-hub/admin
```

---

## 🔄 Login Flow

### Step 1: Access Login Page
1. Open your browser
2. Navigate to `http://localhost/book-hub/admin-login`
3. You will see the Admin Login page

### Step 2: Enter Credentials
1. Email: `admin@bookhub.com`
2. Password: `admin123`
3. (Optional) Check "Remember this device" for persistent login

### Step 3: Submit Form
1. Click "Sign In to Dashboard"
2. Form submits to `/book-hub/src/handlers/admin-login-handler.php`

### Step 4: Authentication Process
The handler performs:
- ✅ Email validation
- ✅ Password verification (bcrypt)
- ✅ Account active status check
- ✅ Session creation
- ✅ Last login timestamp update

### Step 5: Redirect to Dashboard
On success:
- Session variables set: `admin_id`, `admin_email`, `admin_name`, `admin_role`
- Redirects to: `/book-hub/admin`
- Admin dashboard loads with statistics

---

## 🆕 Admin Registration

### Access URL
```
http://localhost/book-hub/admin-register
```

### Registration Form Fields
- **Full Name:** Your complete name
- **Username:** Unique username (min 3 characters)
- **Email:** Valid email address
- **Password:** Minimum 8 characters
- **Confirm Password:** Must match password
- **Role:** Choose from:
  - Super Admin (full access)
  - Admin (standard access)
  - Moderator (limited access)
- **Active Status:** Check to activate immediately

### After Registration
Upon successful registration:
- Account created in database
- Redirects to: `/book-hub/public/admin-login.html?success=...`
- Success message displayed
- You can now login with your credentials

---

## 🔧 Troubleshooting

### Issue 1: Login Fails with Correct Password
**Solution:** Reset the admin password
```bash
C:\xampp\php\php.exe C:\xampp\htdocs\book-hub\fix-admin-password.php
```

**Output:**
```
✅ Admin password has been reset successfully!
Email: admin@bookhub.com
Password: admin123
✅ Password verification test: PASSED
```

### Issue 2: "Email and password are required"
**Cause:** Form fields are empty
**Solution:** Ensure both email and password are filled

### Issue 3: "Invalid credentials"
**Causes:**
- Email doesn't exist in database
- Password is incorrect
- Account is not active

**Solution:**
1. Verify email: `admin@bookhub.com`
2. Reset password using fix script
3. Check database:
```sql
SELECT email, is_active FROM admins WHERE email = 'admin@bookhub.com';
```

### Issue 4: Redirect Loop or Not Redirecting
**Cause:** Session not being created
**Solution:**
1. Check PHP session settings
2. Clear browser cookies
3. Verify `src/admin-session-check.php` exists
4. Check browser console for JavaScript errors

### Issue 5: 404 Not Found on Dashboard
**Cause:** Routing issue
**Solution:**
1. Verify `index.php` has `/admin` route
2. Check file exists: `src/views/admin.php`
3. Apache/XAMPP is running

---

## 🧪 Testing the Flow

### Test Script
Run the comprehensive test:
```bash
C:\xampp\php\php.exe C:\xampp\htdocs\book-hub\test-admin-flow.php
```

**Expected Output:**
```
=== TESTING ADMIN LOGIN FLOW ===

1. Checking admin account...
   ✅ Admin found
2. Testing password verification...
   ✅ Password verification: PASSED
3. Testing routes...
   ✅ All routes configured
4. Testing session check...
   ✅ Admin session check exists
```

### Manual Testing Steps
1. **Open** `http://localhost/book-hub/admin-login`
2. **Enter** email: `admin@bookhub.com`
3. **Enter** password: `admin123`
4. **Click** "Sign In to Dashboard"
5. **Verify** redirect to `/book-hub/admin`
6. **Check** session is created (should see admin dashboard)

---

## 📂 Related Files

### Authentication Files
- `/public/admin-login.html` - Login page
- `/public/admin-register.html` - Registration page
- `/src/handlers/admin-login-handler.php` - Login processor
- `/src/handlers/admin-register-handler.php` - Registration processor
- `/src/admin-session-check.php` - Session validation

### Dashboard Files
- `/src/views/admin.php` - Main admin dashboard
- `/src/views/manage-users.php` - User management
- `/src/views/manage-books.php` - Book management
- `/src/views/manage-rentals.php` - Rental management

### JavaScript Files
- `/public/static/js/admin-login.js` - Login form handling
- `/public/static/js/admin.js` - Dashboard functionality

### Styling Files
- `/public/static/css/admin-login.css` - Login page styles
- `/public/static/css/admin.css` - Dashboard styles

---

## 🔒 Security Features

### Implemented Security
- ✅ Password hashing with bcrypt
- ✅ Prepared SQL statements (prevent SQL injection)
- ✅ Session-based authentication
- ✅ CSRF token ready
- ✅ Email validation
- ✅ Password strength requirements
- ✅ Active status check
- ✅ Login attempt logging

### Session Management
When logged in, the following session variables are set:
```php
$_SESSION['admin_id']     // Admin ID
$_SESSION['admin_email']  // Admin email
$_SESSION['admin_name']   // Full name
$_SESSION['admin_role']   // Role (super_admin/admin/moderator)
$_SESSION['admin']        // Email (for compatibility)
```

### Remember Me Feature
- Sets secure cookie for 30 days
- Token stored in browser
- Used for auto-login on return visits

---

## 🎯 Quick Reference

| Action | URL | Method |
|--------|-----|--------|
| Admin Login Page | `/book-hub/admin-login` | GET |
| Admin Registration Page | `/book-hub/admin-register` | GET |
| Login Handler | `/book-hub/src/handlers/admin-login-handler.php` | POST |
| Register Handler | `/book-hub/src/handlers/admin-register-handler.php` | POST |
| Admin Dashboard | `/book-hub/admin` | GET |
| Logout | `/book-hub/src/handlers/admin-logout-handler.php` | GET |

---

## ✅ Current Status

**Admin Login:** ✅ WORKING  
**Admin Registration:** ✅ WORKING  
**Dashboard Navigation:** ✅ WORKING  
**Session Management:** ✅ WORKING  
**Password Reset:** ✅ WORKING

---

## 📞 Support

If you encounter any issues:

1. **Check debug log:**
   ```
   C:\xampp\htdocs\book-hub\admin-login-debug.log
   ```

2. **Run test script:**
   ```bash
   C:\xampp\php\php.exe C:\xampp\htdocs\book-hub\test-admin-flow.php
   ```

3. **Reset password:**
   ```bash
   C:\xampp\php\php.exe C:\xampp\htdocs\book-hub\fix-admin-password.php
   ```

4. **Check database:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Select database: `bookhub_db`
   - Check `admins` table

---

**Last Updated:** December 10, 2025  
**Status:** All systems operational ✅
