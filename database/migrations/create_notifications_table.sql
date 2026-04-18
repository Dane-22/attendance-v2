-- Create notifications table for the notification bell feature
-- This stores all in-app notifications for admins and employees

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_type ENUM('admin', 'employee') NOT NULL DEFAULT 'admin',
    recipient_id INT NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'attendance', 'payroll', 'system', 'leave'
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255), -- optional link to related resource
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_unread (recipient_type, recipient_id, is_read),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
