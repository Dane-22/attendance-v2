<?php
// Database configuration
// Local: Uses .env file or WAMP defaults (root/no password)
// Production: Uses server environment variables

// Detect if running on localhost/WAMP or ngrok
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']) ||
           strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false ||
           strpos($_SERVER['HTTP_HOST'] ?? '', 'ngrok') !== false ||
           strpos($_SERVER['HTTP_HOST'] ?? '', 'attendances.xandree.com') !== false;

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_DATABASE') ?: 'attendance-system',
    'username' => getenv('DB_USERNAME') ?: ($isLocal ? 'root' : 'attendance_user'),
    'password' => getenv('DB_PASSWORD') ?: ($isLocal ? '' : 'JaJr12390786@')
];
