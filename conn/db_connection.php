<?php
// Database connection for JAJR Attendance System
// Local: Uses .env file or WAMP defaults (root/no password)
// Production: Uses server environment variables

// Detect if running on localhost/WAMP or ngrok
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']) ||
           strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false ||
           strpos($_SERVER['HTTP_HOST'] ?? '', 'ngrok') !== false;

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'attendance-system';
$username = getenv('DB_USERNAME') ?: ($isLocal ? 'root' : 'attendance_user');
$password = getenv('DB_PASSWORD') ?: ($isLocal ? '' : 'JaJr12390786@');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Also create a mysqli connection for legacy code
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
