<?php
// Test API endpoint directly
ini_set('display_errors', 0);
ini_set('html_errors', 0);
error_reporting(0);

session_start();

// Check session
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in', 'session' => $_SESSION]);
    exit;
}

try {
    require_once __DIR__ . '/core/Database.php';
    require_once __DIR__ . '/core/Model.php';
    require_once __DIR__ . '/models/Notification.php';
    require_once __DIR__ . '/models/Admin.php';
    
    $notificationModel = new Notification();
    $adminModel = new Admin();
    
    $recipientId = $_SESSION['admin_id'];
    
    // Test getRecent
    $notifications = $notificationModel->getRecent('admin', $recipientId, 10);
    
    // Add metadata
    foreach ($notifications as &$notification) {
        $notification['relative_time'] = Notification::getRelativeTime($notification['created_at']);
        $notification['icon'] = Notification::getTypeIcon($notification['type']);
        $notification['color'] = Notification::getTypeColor($notification['type']);
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'count' => count($notifications)
    ]);
    
} catch (Throwable $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
