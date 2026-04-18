<?php
/**
 * Test Notification API - Run this to diagnose the issue
 */

session_start();

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    echo "❌ Not logged in. Please log in first.\n";
    echo "Session data: " . print_r($_SESSION, true) . "\n";
    exit;
}

echo "✅ Logged in as admin ID: " . $_SESSION['admin_id'] . "\n\n";

// Test 1: Check if Notification model loads
echo "Test 1: Loading Notification model...\n";
try {
    require_once __DIR__ . '/models/Notification.php';
    $notificationModel = new Notification();
    echo "✅ Notification model loaded\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 2: Check unread count
echo "Test 2: Getting unread count...\n";
try {
    $count = $notificationModel->getUnreadCount('admin', $_SESSION['admin_id']);
    echo "✅ Unread count: {$count}\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Check recent notifications
echo "Test 3: Getting recent notifications...\n";
try {
    $notifications = $notificationModel->getRecent('admin', $_SESSION['admin_id'], 5);
    echo "✅ Found " . count($notifications) . " notifications\n";
    if (count($notifications) > 0) {
        echo "First notification: " . $notifications[0]['title'] . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Check all notifications
echo "Test 4: Getting all notifications...\n";
try {
    $result = $notificationModel->getAll('admin', $_SESSION['admin_id'], [], 1, 10);
    echo "✅ Total notifications: " . $result['total'] . "\n";
    echo "Page: " . $result['page'] . ", Per page: " . $result['per_page'] . "\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
}

echo "All tests completed!\n";
