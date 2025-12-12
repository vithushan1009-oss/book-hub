<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<h1 align="center">📚 BOOK HUB</h1>

<p align="center">
  <strong>A Modern Book Rental & Purchase Platform</strong>
</p>

<p align="center">
  <em>Discover, Rent, and Purchase Books Online — Built for Book Lovers in Jaffna, Sri Lanka 🇱🇰</em>
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-demo">Demo</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-usage">Usage</a> •
  <a href="#-api">API</a> •
  <a href="#-contributing">Contributing</a> •
  <a href="#-license">License</a>
</p>

---

## 🌟 Overview

**BOOK HUB** is a full-featured book rental and purchase platform designed for libraries and bookstores. It provides a seamless experience for users to browse, rent physical books, or purchase digital editions, while offering administrators powerful tools to manage inventory, users, and analytics.

### 🎯 Key Highlights

- 🔐 **Secure Authentication** — Email verification, bcrypt hashing, rate limiting
- 📖 **Dual Book System** — Support for both physical rentals and digital purchases
- 📊 **Admin Dashboard** — Real-time analytics with interactive charts
- 💳 **Rental Management** — Complete booking system with cost calculation
- 🎨 **Modern UI** — Responsive design with beautiful user experience
- 🌏 **Localized** — Built for Sri Lankan market with LKR currency

---

## ✨ Features

### 👤 User Features

| Feature | Description |
|---------|-------------|
| 🔑 **Secure Registration** | Email verification with token-based validation |
| 🔐 **Smart Login** | Rate limiting, remember me, session management |
| 📚 **Book Browsing** | Search, filter by genre, view details |
| 📅 **Easy Rentals** | Date picker, cost calculator, validation |
| 💰 **Digital Purchases** | One-click purchase for online books |
| 👤 **Profile Management** | Update personal info and preferences |
| 📱 **Responsive Design** | Works on desktop, tablet, and mobile |

### 🛡️ Admin Features

| Feature | Description |
|---------|-------------|
| 📊 **Analytics Dashboard** | User growth, revenue, rental trends charts |
| 👥 **User Management** | View, edit, activate/deactivate users |
| 📖 **Book Management** | Add, edit, delete books with images |
| 📋 **Rental Management** | Approve, reject, track all rentals |
| 📧 **Contact Management** | View and respond to user inquiries |
| ⚙️ **System Settings** | Configure application parameters |

### 🔒 Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (output escaping)
- ✅ CSRF protection ready
- ✅ Login attempt tracking & lockout
- ✅ Secure session management
- ✅ Email verification system

---

## 🖼️ Screenshots

<details>
<summary>📸 Click to view screenshots</summary>

### Homepage
![Homepage](public/assets/images/hero-library.jpg)

### Book Collection
*Browse through our extensive collection of books*

### Admin Dashboard
*Powerful analytics and management tools*

</details>

---

## 🛠️ Tech Stack

| Layer | Technologies |
|-------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript (ES6+) |
| **Backend** | PHP 8.x |
| **Database** | MySQL 5.7+ / MariaDB 10.4+ |
| **Server** | Apache (XAMPP) |
| **Charts** | Chart.js |
| **Icons** | Font Awesome 6.5.1 |

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- 🐘 **PHP** >= 8.0
- 🐬 **MySQL** >= 5.7 or MariaDB >= 10.4
- 🌐 **Apache** Web Server
- 📦 **XAMPP** 3.3.0+ (recommended) or similar stack

---

## 🚀 Installation

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/yourusername/book-hub.git
cd book-hub
```

### 2️⃣ Move to XAMPP Directory

```bash
# Windows
move book-hub C:\xampp\htdocs\

# Linux/Mac
mv book-hub /opt/lampp/htdocs/
```

### 3️⃣ Start XAMPP Services

```bash
# Start Apache and MySQL from XAMPP Control Panel
# Or using command line:
xampp_start
```

### 4️⃣ Create Database

```bash
# Option 1: Using phpMyAdmin
# 1. Open http://localhost/phpmyadmin
# 2. Click "Import" tab
# 3. Select file: database/bookhub_database.sql
# 4. Click "Go"

# Option 2: Using MySQL CLI
mysql -u root -p < database/bookhub_database.sql
```

### 5️⃣ Configure Environment

```bash
# Copy example environment file
cp .env.example .env

# Edit with your settings
nano .env  # or use any text editor
```

**Required `.env` Configuration:**

```env
# Database
DB_HOST=localhost
DB_NAME=bookhub_db
DB_USER=root
DB_PASS=

# Application
APP_NAME="BOOK HUB"
APP_URL=http://localhost/book-hub

# Timezone
TIMEZONE=Asia/Colombo
```

### 6️⃣ Access the Application

```
🌐 User Portal:  http://localhost/book-hub/public/index.php
👤 User Login:   http://localhost/book-hub/public/login.php
🔐 Admin Login:  http://localhost/book-hub/public/admin-login.php
```

---

## 🔑 Default Credentials

### 👨‍💼 Admin Account
| Field | Value |
|-------|-------|
| Email | `admin@bookhub.com` |
| Password | `admin123` |

### 👤 Test User Account
| Field | Value |
|-------|-------|
| Email | `test@example.com` |
| Password | `test1234` |

> ⚠️ **Important:** Change these credentials immediately in production!

---

## 📁 Project Structure

```
book-hub/
├── 📂 database/
│   ├── bookhub_database.sql      # Main database schema
│   └── contact_messages_table.sql # Contact form table
│
├── 📂 docs/
│   └── README.md                  # Documentation
│
├── 📂 public/
│   ├── 📂 assets/
│   │   └── images/               # Book covers & images
│   ├── 📂 static/
│   │   ├── css/                  # Stylesheets
│   │   ├── js/                   # JavaScript files
│   │   └── vendor/               # Third-party libraries
│   ├── index.php                 # Homepage
│   ├── books.php                 # Book listing
│   ├── login.php                 # User login
│   ├── register.php              # User registration
│   ├── contact.php               # Contact page
│   ├── about.php                 # About page
│   ├── gallery.php               # Gallery page
│   └── admin-login.php           # Admin login
│
├── 📂 src/
│   ├── 📂 components/
│   │   ├── navbar.php            # Navigation component
│   │   ├── footer.php            # Footer component
│   │   ├── admin-sidebar.php     # Admin sidebar
│   │   └── admin-topbar.php      # Admin topbar
│   │
│   ├── 📂 handlers/
│   │   ├── login-handler.php     # User login logic
│   │   ├── register-handler.php  # Registration logic
│   │   ├── rent-book-handler.php # Book rental logic
│   │   └── ...                   # Other handlers
│   │
│   ├── 📂 views/
│   │   ├── user.php              # User dashboard
│   │   ├── profile.php           # User profile
│   │   ├── admin.php             # Admin dashboard
│   │   ├── manage-books.php      # Book management
│   │   ├── manage-users.php      # User management
│   │   ├── manage-rentals.php    # Rental management
│   │   └── admin-analytics.php   # Analytics dashboard
│   │
│   ├── config.php                # Configuration loader
│   └── session-check.php         # Session management
│
├── .env.example                  # Environment template
├── .htaccess                     # Apache configuration
├── index.php                     # Entry point
└── README.md                     # This file
```

---

## ⚙️ Configuration

### 📧 Email Setup (Optional)

To enable email verification:

1. **Enable 2-Step Verification** on your Gmail account
2. **Generate App Password:**
   - Google Account → Security → 2-Step Verification → App passwords
   - Select "Mail" and generate password
3. **Update `.env`:**

```env
ENABLE_EMAIL=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-16-char-app-password
```

### 🔐 Security Settings

```env
PASSWORD_MIN_LENGTH=8        # Minimum password length
MAX_LOGIN_ATTEMPTS=5         # Failed attempts before lockout
LOCKOUT_TIME=900             # Lockout duration (seconds)
SESSION_LIFETIME=28800       # Session timeout (8 hours)
EMAIL_VERIFICATION_EXPIRY=86400  # Token expiry (24 hours)
```

---

## 🗄️ Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts with verification status |
| `admins` | Administrator accounts with roles |
| `books` | Book inventory (physical & digital) |
| `rentals` | Book rental records |
| `purchases` | Digital book purchases |
| `book_reviews` | User reviews and ratings |

### Security Tables

| Table | Description |
|-------|-------------|
| `login_attempts` | Failed login tracking |
| `user_sessions` | Active session management |
| `audit_logs` | Activity logging |

### System Tables

| Table | Description |
|-------|-------------|
| `notifications` | User notifications |
| `email_logs` | Email delivery tracking |
| `contact_messages` | Contact form submissions |

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### 📝 Contribution Guidelines

- Follow existing code style
- Write clear commit messages
- Update documentation as needed
- Test your changes thoroughly

---

## 🐛 Known Issues

- [ ] Mobile menu animation needs improvement
- [ ] Image upload size validation
- [ ] Pagination on large datasets

---

## 📜 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2025 BOOK HUB

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 👨‍💻 Author

**BOOK HUB Team**

- 📍 Location: Jaffna, Sri Lanka
- 📧 Email: contact@bookhub.com
- 🌐 Website: [bookhub.com](https://bookhub.com)

---

## 🙏 Acknowledgments

- 📚 [Font Awesome](https://fontawesome.com) — Icons
- 📊 [Chart.js](https://www.chartjs.org) — Charts & Analytics
- 🎨 Design inspiration from modern library systems

---

<p align="center">
  Made with ❤️ in Jaffna, Sri Lanka 🇱🇰
</p>

<p align="center">
  <a href="#-book-hub">⬆️ Back to Top</a>
</p>
