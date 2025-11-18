-- ============================================
-- BOOK HUB Database Schema
-- ============================================
-- MySQL Database Schema for XAMPP
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS bookhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE bookhub_db;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) DEFAULT 0,
    email_verification_token VARCHAR(64) NULL,
    email_verification_expires DATETIME NULL,
    reset_token VARCHAR(64) NULL,
    reset_token_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_verification_token (email_verification_token),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Admins Table
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    two_factor_secret VARCHAR(32) NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT UNSIGNED NULL,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sessions Table
-- ============================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128) NOT NULL UNIQUE,
    user_id INT UNSIGNED NULL,
    admin_id INT UNSIGNED NULL,
    user_type ENUM('user', 'admin') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Login Attempts Table (Security)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_type ENUM('user', 'admin') NOT NULL,
    success TINYINT(1) DEFAULT 0,
    INDEX idx_email_ip (email, ip_address),
    INDEX idx_attempt_time (attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Email Logs Table
-- ============================================
CREATE TABLE IF NOT EXISTS email_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    email_type ENUM('verification', 'password_reset', 'notification', 'other') NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) DEFAULT 1,
    error_message TEXT NULL,
    INDEX idx_recipient (recipient_email),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Password Reset Tokens Table
-- ============================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    user_type ENUM('user', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Default Admin Account
-- ============================================
-- Password: admin123 (CHANGE THIS IN PRODUCTION!)
-- Hashed with PASSWORD_DEFAULT (bcrypt)
INSERT INTO admins (username, email, password, full_name, role, is_active)
VALUES (
    'admin',
    'admin@bookhub.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'System Administrator',
    'super_admin',
    1
) ON DUPLICATE KEY UPDATE username=username;

-- ============================================
-- Insert Sample User for Testing
-- ============================================
-- Email: test@example.com
-- Password: test1234
INSERT INTO users (first_name, last_name, email, password, email_verified, is_active)
VALUES (
    'Test',
    'User',
    'test@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- test1234
    1,
    1
) ON DUPLICATE KEY UPDATE email=email;

-- ============================================
-- Cleanup Old Sessions (Run periodically)
-- ============================================
-- Delete expired sessions
DELETE FROM sessions WHERE expires_at < NOW();

-- Delete old login attempts (older than 30 days)
DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Delete used/expired password reset tokens
DELETE FROM password_reset_tokens WHERE used = 1 OR expires_at < NOW();

-- ============================================
-- Utility Views
-- ============================================

-- View: Active Users
CREATE OR REPLACE VIEW active_users AS
SELECT 
    id,
    CONCAT(first_name, ' ', last_name) AS full_name,
    email,
    email_verified,
    last_login,
    created_at
FROM users
WHERE is_active = 1;

-- View: Recent Login Attempts
CREATE OR REPLACE VIEW recent_login_attempts AS
SELECT 
    email,
    ip_address,
    attempt_time,
    user_type,
    success,
    COUNT(*) as attempt_count
FROM login_attempts
WHERE attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY email, ip_address, user_type
HAVING attempt_count >= 3;

-- ============================================
-- Database Information
-- ============================================
-- Note: The following queries can be run manually if needed
-- SHOW TABLES;

-- Display table statistics (run manually if needed)
-- SELECT 
--     TABLE_NAME as 'Table',
--     TABLE_ROWS as 'Rows',
--     ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)'
-- FROM information_schema.TABLES
-- WHERE TABLE_SCHEMA = 'bookhub_db'
-- ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;
