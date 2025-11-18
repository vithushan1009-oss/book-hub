# 📚 BOOK HUB - Professional File Structure

## ✅ Industry-Standard MVC-Like Architecture

```
book-hub-central/                    ← Project Root
│
├── 📂 public/                       ← Web-Accessible Files ONLY
│   ├── 📂 static/                   ← Static Assets (CSS, JS)
│   │   ├── 📂 css/                  ← Stylesheets (10 files)
│   │   │   ├── variables.css        ← CSS custom properties
│   │   │   ├── base.css             ← Reset & base styles
│   │   │   ├── components.css       ← Reusable components
│   │   │   ├── navigation.css       ← Navigation styles
│   │   │   ├── footer.css           ← Footer styles
│   │   │   ├── auth.css             ← Auth pages styles
│   │   │   ├── admin-login.css      ← Admin login styles
│   │   │   ├── admin.css            ← Admin dashboard styles
│   │   │   ├── home.css             ← Homepage styles
│   │   │   └── books.css            ← Books page styles
│   │   │
│   │   └── 📂 js/                   ← JavaScript Modules (7 files)
│   │       ├── common.js            ← Common utilities
│   │       ├── auth.js              ← Auth form handling
│   │       ├── admin-login.js       ← Admin login interactions
│   │       ├── admin.js             ← Admin dashboard logic
│   │       ├── home.js              ← Homepage interactions
│   │       ├── books.js             ← Books filtering/search
│   │       └── contact.js           ← Contact form handling
│   │
│   ├── 📂 assets/                   ← Media Files
│   │   ├── 📂 images/               ← Image Files (6 files)
│   │   │   ├── about-team.jpg
│   │   │   ├── book-1.jpg
│   │   │   ├── book-2.jpg
│   │   │   ├── book-3.jpg
│   │   │   ├── book-4.jpg
│   │   │   └── hero-library.jpg
│   │   │
│   │   └── 📂 uploads/              ← User Uploads (Created)
│   │       └── (book covers, user avatars, etc.)
│   │
│   ├── 📄 index.html                ← Homepage
│   ├── 📄 login.html                ← User login
│   ├── 📄 register.html             ← User registration
│   ├── 📄 admin-login.html          ← Admin login
│   ├── 📄 books.html                ← Books catalog
│   ├── 📄 about.html                ← About us
│   ├── 📄 contact.html              ← Contact form
│   └── 📄 gallery.html              ← Photo gallery
│
├── 📂 src/                          ← Application Source Code (PROTECTED)
│   ├── 📂 handlers/                 ← Request Handlers (6 files)
│   │   ├── admin-login-handler.php  ← Admin authentication
│   │   ├── email-functions.php      ← SMTP email utilities
│   │   ├── login-handler.php        ← User login processing
│   │   ├── logout-handler.php       ← Session logout
│   │   ├── register-handler.php     ← Registration + verification
│   │   └── verify-handler.php       ← Email verification
│   │
│   ├── 📂 views/                    ← Protected PHP Pages (4 files)
│   │   ├── user.php                 ← User dashboard
│   │   ├── admin.php                ← Admin dashboard
│   │   ├── profile.php              ← User profile management
│   │   └── manage-users.php         ← Admin user management
│   │
│   └── 📄 config.php                ← Configuration Loader
│
├── 📂 database/                     ← Database Files
│   └── 📄 schema.sql                ← Complete database structure
│
├── 📂 docs/                         ← Documentation (5 files)
│   ├── 📄 README.md                 ← Main documentation
│   ├── 📄 QUICKSTART.md             ← Quick setup guide
│   ├── 📄 PROJECT-SUMMARY.md        ← Project overview
│   ├── 📄 EMAIL-SETUP-GUIDE.md      ← Email configuration
│   └── 📄 FILE-STRUCTURE.md         ← This file
│
├── ⚙️ Configuration Files (Root)
│   ├── 📄 index.php                 ← Front Controller (Router)
│   ├── 📄 .env                      ← Environment variables (SECRET!)
│   ├── 📄 .env.example              ← Environment template
│   ├── 📄 .htaccess                 ← Apache configuration
│   └── 📄 .gitignore                ← Git ignore rules
│
└── 📂 .git/                         ← Git Repository
```

---

## 🎯 Key Architecture Benefits

### 1. **Clear Separation of Concerns**
- **public/** = Only web-accessible files
- **src/** = Protected application logic
- **database/** = Schema and migrations
- **docs/** = Documentation

### 2. **Security by Design**
- ✅ `.env` file protected by `.htaccess`
- ✅ `src/` directory blocked from direct access
- ✅ All sensitive files outside public directory
- ✅ Router (`index.php`) controls all access

### 3. **Professional Standards**
- ✅ MVC-like structure
- ✅ Front Controller pattern
- ✅ Modular CSS and JavaScript
- ✅ RESTful-style routing

### 4. **Scalability**
- ✅ Easy to add new pages in `public/`
- ✅ Easy to add new handlers in `src/handlers/`
- ✅ Easy to add new views in `src/views/`
- ✅ Easy to organize assets by type

---

## 🔄 How It Works

### Request Flow

```
Browser Request
     ↓
.htaccess (mod_rewrite)
     ↓
index.php (Front Controller)
     ↓
Route Analysis
     ├─→ Public HTML Page (public/*.html)
     ├─→ Protected View (src/views/*.php)
     ├─→ Handler (src/handlers/*-handler.php)
     └─→ Static Asset (public/static/*, public/assets/*)
     ↓
Response to Browser
```

### URL Routing Examples

| URL | File Served | Type |
|-----|-------------|------|
| `/` or `/index` | `public/index.html` | Public HTML |
| `/login` | `public/login.html` | Public HTML |
| `/books` | `public/books.html` | Public HTML |
| `/user` | `src/views/user.php` | Protected View |
| `/admin` | `src/views/admin.php` | Protected View |
| `/handler/login-handler` | `src/handlers/login-handler.php` | POST Handler |
| `/static/css/base.css` | `public/static/css/base.css` | Static Asset |
| `/assets/images/book-1.jpg` | `public/assets/images/book-1.jpg` | Image |

---

## 📊 File Count Summary

| Directory | Files | Purpose |
|-----------|-------|---------|
| **public/** | 8 HTML | Frontend pages |
| **public/static/css/** | 10 CSS | Modular stylesheets |
| **public/static/js/** | 7 JS | JavaScript modules |
| **public/assets/images/** | 6 JPG | Images |
| **src/handlers/** | 6 PHP | Request processing |
| **src/views/** | 4 PHP | Protected pages |
| **database/** | 1 SQL | Database schema |
| **docs/** | 5 MD | Documentation |
| **Root Config** | 5 files | App configuration |
| **TOTAL** | **52 files** | **Clean & organized** |

---

## 🛡️ Security Features

### 1. `.htaccess` Protection
```apache
# Protect .env file
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect src/ directory
RewriteRule ^src/ - [F,L]

# Protect database files
<FilesMatch "\.(sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Front Controller (`index.php`)
- All requests routed through single entry point
- Validates routes before serving files
- Protects against directory traversal
- Sets appropriate content types

### 3. Directory Structure
- Application code in `src/` (protected)
- Only `public/` files accessible via web
- Database files outside web root
- Configuration files protected

---

## 📝 File Organization Best Practices

### HTML Files (`public/`)
```html
<!-- Clean URLs - router handles .html extension -->
<a href="/index">Home</a>
<a href="/login">Login</a>
<a href="/books">Books</a>

<!-- Static assets - relative to public/ -->
<link href="static/css/base.css">
<script src="static/js/common.js"></script>
<img src="assets/images/book-1.jpg">

<!-- Forms - post to handlers via router -->
<form action="../handler/login-handler" method="POST">
```

### PHP Views (`src/views/`)
```php
<?php
// Load configuration
require_once __DIR__ . '/../config.php';

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Static assets - relative from view perspective -->
    <link href="../static/css/base.css">
    <script src="../static/js/common.js"></script>
</head>
```

### PHP Handlers (`src/handlers/`)
```php
<?php
// Load configuration
require_once __DIR__ . '/../config.php';

// Get database connection
$conn = getDbConnection();

// Process form data
$email = $_POST['email'];

// Redirect after processing
header('Location: ../../user');
exit;
```

---

## 🎨 CSS Organization

### Modular Approach
1. **variables.css** - CSS custom properties (colors, spacing, shadows)
2. **base.css** - Reset and base element styles
3. **components.css** - Reusable UI components (buttons, cards, forms)
4. **navigation.css** - Header and navigation menu
5. **footer.css** - Footer styles
6. **auth.css** - Login/register pages
7. **admin-login.css** - Admin login specific
8. **admin.css** - Admin dashboard
9. **home.css** - Homepage specific
10. **books.css** - Books page specific

### Load Order
```html
<!-- Core styles first -->
<link rel="stylesheet" href="static/css/variables.css">
<link rel="stylesheet" href="static/css/base.css">
<link rel="stylesheet" href="static/css/components.css">

<!-- Layout components -->
<link rel="stylesheet" href="static/css/navigation.css">
<link rel="stylesheet" href="static/css/footer.css">

<!-- Page-specific styles last -->
<link rel="stylesheet" href="static/css/home.css">
```

---

## 💻 JavaScript Organization

### Feature-Based Modules
1. **common.js** - Navigation, mobile menu, scroll effects, utilities
2. **auth.js** - Login/register form handling, validation, errors
3. **admin-login.js** - Admin login interactions
4. **admin.js** - Admin dashboard functionality, stats
5. **home.js** - Homepage interactions, sliders, animations
6. **books.js** - Book filtering, search, pagination
7. **contact.js** - Contact form handling, validation

### Loading Strategy
```html
<!-- Common utilities on every page -->
<script src="static/js/common.js"></script>

<!-- Page-specific modules -->
<script src="static/js/auth.js"></script>
```

---

## 🚀 Development Workflow

### Adding a New Public Page
1. Create HTML file in `public/`
2. Use relative paths: `static/css/`, `static/js/`, `assets/`
3. Forms POST to `../handler/your-handler`
4. Router automatically serves at `/page-name`

### Adding a New Protected Page
1. Create PHP file in `src/views/`
2. Include: `require_once __DIR__ . '/../config.php';`
3. Add authentication check
4. Use relative paths: `../static/`, `../assets/`
5. Update router in `index.php` protected views array

### Adding a New Handler
1. Create PHP file in `src/handlers/` as `*-handler.php`
2. Include: `require_once __DIR__ . '/../config.php';`
3. Process POST data
4. Redirect: `header('Location: ../../destination');`
5. Access via `/handler/your-handler`

### Adding New Styles
1. Create CSS file in `public/static/css/`
2. Link in HTML: `<link href="static/css/your-file.css">`
3. Use CSS variables from `variables.css`

### Adding New Scripts
1. Create JS file in `public/static/js/`
2. Link in HTML: `<script src="static/js/your-file.js"></script>`
3. Use functions from `common.js` for utilities

---

## 🔧 Configuration

### Environment Variables (`.env`)
```env
# Database
DB_HOST=localhost
DB_NAME=bookhub_db
DB_USER=root
DB_PASS=

# Email (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=BOOK HUB

# Security
PASSWORD_MIN_LENGTH=8
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_DURATION=900
TOKEN_EXPIRY=86400

# Application
APP_NAME=BOOK HUB
APP_URL=http://localhost/BOOKHUB/book-hub-central
TIMEZONE=Asia/Colombo
```

### Configuration Loader (`src/config.php`)
```php
<?php
/**
 * Load environment variables and provide utilities
 */

// Load .env file
function loadEnv($file = '.env') {
    // Implementation...
}

// Get environment variable
function env($key, $default = null) {
    return getenv($key) ?: $default;
}

// Get database connection
function getDbConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(
            env('DB_HOST'),
            env('DB_USER'),
            env('DB_PASS'),
            env('DB_NAME')
        );
    }
    return $conn;
}

// Load environment variables
loadEnv(__DIR__ . '/../.env');
date_default_timezone_set(env('TIMEZONE', 'UTC'));
```

---

## 📚 Documentation Structure

### docs/README.md
- Complete project documentation
- Installation instructions
- Feature list
- Database schema
- API documentation
- Security features

### docs/QUICKSTART.md
- 5-minute setup guide
- Essential configuration
- Testing steps
- Common issues

### docs/PROJECT-SUMMARY.md
- Architecture overview
- Before/after comparison
- File structure explanation
- Code examples

### docs/EMAIL-SETUP-GUIDE.md
- SMTP configuration
- Gmail App Password setup
- Email templates
- Troubleshooting

### docs/FILE-STRUCTURE.md
- This file
- Complete directory tree
- File organization
- Best practices

---

## 🎉 Benefits of This Structure

### For Developers
✅ **Easy to Navigate** - Logical folder organization  
✅ **Easy to Maintain** - Clear separation of concerns  
✅ **Easy to Extend** - Add new features systematically  
✅ **Easy to Debug** - Each file has single responsibility  
✅ **Easy to Test** - Modular code structure  

### For Security
✅ **Protected Source Code** - `src/` not web-accessible  
✅ **Protected Configuration** - `.env` blocked by `.htaccess`  
✅ **Protected Database** - Schema files not accessible  
✅ **Controlled Access** - Router validates all requests  

### For Performance
✅ **Modular CSS** - Load only what you need  
✅ **Modular JS** - Smaller file sizes  
✅ **Static Asset Caching** - Browser caching enabled  
✅ **Efficient Routing** - Single entry point  

### For Scalability
✅ **MVC-Like Pattern** - Industry standard  
✅ **Easy to Add Features** - Clear structure  
✅ **Easy to Refactor** - Modular organization  
✅ **Team-Friendly** - Everyone knows where things go  

---

## 🚦 Quick Reference

### File Locations
```
Public HTML:        public/*.html
PHP Views:          src/views/*.php
Handlers:           src/handlers/*-handler.php
CSS:                public/static/css/*.css
JavaScript:         public/static/js/*.js
Images:             public/assets/images/*.jpg
Configuration:      src/config.php + .env
Database:           database/schema.sql
Documentation:      docs/*.md
Router:             index.php
```

### URL Structure
```
Homepage:           /  or  /index
Login:              /login
User Dashboard:     /user
Admin Dashboard:    /admin
Login Handler:      /handler/login-handler
Static CSS:         /static/css/base.css
Images:             /assets/images/book-1.jpg
```

---

## 📞 Next Steps

1. ✅ Review new structure
2. ✅ Update `.env` with your settings
3. ✅ Test public pages (/, /login, /books, etc.)
4. ✅ Test authentication flow
5. ✅ Test admin login
6. ✅ Verify static assets loading
7. ✅ Check database connectivity
8. ✅ Test email verification

---

**Project Status:** ✅ **PRODUCTION-READY**

**Architecture:** MVC-Like with Front Controller Pattern  
**File Count:** 52 clean, organized files  
**Security:** Protected source code, .env, database  
**Documentation:** Complete and comprehensive  

**Access:** http://localhost/BOOKHUB/book-hub-central

---

**Last Updated:** November 18, 2025  
**Version:** 3.0 (Professional Structure)  
**Author:** BOOK HUB Development Team
