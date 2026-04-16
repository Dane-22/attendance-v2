<?php
// Database configuration - uses environment variables with fallbacks
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_DATABASE') ?: 'attendance-system',
    'username' => getenv('DB_USERNAME') ?: 'attendance_user',
    'password' => getenv('DB_PASSWORD') ?: 'JaJr12390786@'
];
