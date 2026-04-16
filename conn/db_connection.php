<?php
// Database connection for JAJR Attendance System
// Uses environment variables (for Docker/production) with hardcoded fallbacks (for legacy/local)

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_DATABASE') ?: 'attendance-system';
$username = getenv('DB_USERNAME') ?: 'attendance_user';
$password = getenv('DB_PASSWORD') ?: 'JaJr12390786@';

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
