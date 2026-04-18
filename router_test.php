<?php
// Test router loading of NotificationController

ini_set('display_errors', 0);
ini_set('html_errors', 0);
error_reporting(0);

session_start();

echo "Step 1: Session started<br>";

// Simulate what app.php does
require_once __DIR__ . '/core/Dotenv.php';
Dotenv::load();

echo "Step 2: Dotenv loaded<br>";

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Router.php';

echo "Step 3: Core files loaded<br>";

// Now try loading NotificationController
require_once __DIR__ . '/controllers/NotificationController.php';

echo "Step 4: NotificationController loaded<br>";

// Try creating instance
$controller = new NotificationController();

echo "Step 5: Controller instantiated<br>";

// Check if methods exist
$methods = get_class_methods($controller);
echo "Step 6: Methods: " . implode(', ', $methods) . "<br>";

// Test the actual API call
echo "Step 7: Calling getRecent...<br>";

// Start output buffering to catch any output
ob_start();
$controller->getRecent();
$output = ob_get_clean();

echo "Step 8: Output captured: " . htmlspecialchars(substr($output, 0, 500)) . "<br>";

// Try to parse as JSON
$data = json_decode($output, true);
if ($data) {
    echo "Step 9: JSON parsed successfully! Count: " . ($data['count'] ?? 'N/A') . "<br>";
} else {
    echo "Step 9: JSON parse failed<br>";
}
