# Book Hub Database Setup Guide

## Quick Setup for XAMPP

### 1. Start XAMPP Services
- Start Apache and MySQL from XAMPP Control Panel

### 2. Import Database
```bash
# Option 1: Using phpMyAdmin
1. Open http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: database/bookhub_database.sql
4. Click "Go"

# Also import the contact messages table:
5. Choose file: database/contact_messages_table.sql
6. Click "Go"

# Option 2: Using MySQL Command Line
mysql -u root -p < database/bookhub_database.sql
mysql -u root -p bookhub_db < database/contact_messages_table.sql
```

### 3. Configure Environment
Create `.env` file in project root:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=bookhub_db
DB_USER=root
DB_PASS=

# Application Settings
APP_NAME="BOOK HUB"
APP_URL=http://localhost/book-hub
TIMEZONE=Asia/Colombo

# Email Configuration (optional)
ENABLE_EMAIL=false
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=noreply@bookhub.com
SMTP_FROM_NAME="BOOK HUB"

# Security Settings
PASSWORD_MIN_LENGTH=8
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_TIME=900
EMAIL_VERIFICATION_EXPIRY=86400
SESSION_LIFETIME=28800
```

### 4. Default Login Credentials

**Admin Login:**
- Email: admin@bookhub.com
- Password: admin123

**Test User Login:**
- Email: test@example.com  
- Password: test1234

## Database Features

### Core Tables
- **users** - User accounts with email verification
- **admins** - Administrator accounts with role-based access
- **books** - Physical and digital book inventory
- **rentals** - Book rental management with status tracking
- **purchases** - Digital book purchases
- **book_reviews** - User reviews and ratings

### Security Features
- **login_attempts** - Failed login tracking and rate limiting
- **password_reset_tokens** - Secure password reset functionality
- **user_sessions** - Session management
- **audit_logs** - Complete activity tracking

### System Features
- **notifications** - User notification system
- **email_logs** - Email delivery tracking
- **system_settings** - Configurable application settings

### Automated Features
- Auto-update book availability when rentals are approved/returned
- Auto-calculate book ratings from reviews
- Automatic late fee calculation
- Rental status updates (overdue detection)

## Useful Database Views

### Active Rentals
```sql
SELECT * FROM active_rentals_view WHERE rental_status = 'OVERDUE';
```

### Book Performance
```sql
SELECT * FROM book_statistics_view ORDER BY total_rentals DESC;
```

### User Activity
```sql
SELECT * FROM user_rental_history_view ORDER BY total_spent DESC;
```

## Maintenance Queries

### Clean expired tokens
```sql
DELETE FROM password_reset_tokens WHERE expires_at < NOW();
DELETE FROM user_sessions WHERE expires_at < NOW();
```

### Update overdue rentals
```sql
UPDATE rentals SET status = 'overdue' 
WHERE status = 'approved' AND end_date < CURDATE();
```

### Generate daily reports
```sql
-- Today's registrations
SELECT COUNT(*) as new_users FROM users WHERE DATE(created_at) = CURDATE();

-- Today's rentals
SELECT COUNT(*) as new_rentals FROM rentals WHERE DATE(created_at) = CURDATE();

-- Revenue this month
SELECT SUM(total_cost) as monthly_revenue 
FROM rentals 
WHERE status = 'completed' 
AND MONTH(created_at) = MONTH(CURDATE());
```

## Backup & Restore

### Create Backup
```bash
mysqldump -u root -p bookhub_db > backup_$(date +%Y%m%d).sql
```

### Restore Backup
```bash
mysql -u root -p bookhub_db < backup_20231202.sql
```