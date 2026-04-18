<?php
/**
 * Notification System Setup Script
 * Run this to create the notifications table
 */

require_once __DIR__ . '/core/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        recipient_type ENUM('admin', 'employee') NOT NULL DEFAULT 'admin',
        recipient_id INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(255),
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_at TIMESTAMP NULL,
        INDEX idx_recipient (recipient_type, recipient_id),
        INDEX idx_unread (recipient_type, recipient_id, is_read),
        INDEX idx_created (created_at DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "✅ Notifications table created successfully!\n";
    
    // Test the connection
    $stmt = $db->query("SELECT COUNT(*) FROM notifications");
    $count = $stmt->fetchColumn();
    echo "📊 Current notification count: {$count}\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
