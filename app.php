<?php

// Start output buffering to prevent PHP errors from breaking API responses
ob_start();

// Global error handler to catch fatal errors and return JSON for API routes
set_error_handler(function($severity, $message, $file, $line) {
    // Check if this is an API request
    $isApiRequest = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
    
    if ($isApiRequest) {
        // Clean any output that might have been generated
        if (ob_get_length()) {
            ob_clean();
        }
        
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'PHP Error: ' . $message,
            'file' => basename($file),
            'line' => $line
        ]);
        exit;
    }
    
    // For non-API requests, use default error handling
    return false;
});

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Check if this is an API request
        $isApiRequest = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false;
        
        if ($isApiRequest) {
            // Clean any output that might have been generated
            if (ob_get_length()) {
                ob_clean();
            }
            
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Fatal Error: ' . $error['message'],
                'file' => basename($error['file']),
                'line' => $error['line']
            ]);
            exit;
        }
    }
});

// Load environment variables from .env file (local development)
require_once __DIR__ . '/core/Dotenv.php';
Dotenv::load();

session_start();

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Router.php';

$router = new Router();

// Login routes
$router->add('login', ['controller' => 'LoginController', 'action' => 'index']);
$router->add('login/qr', ['controller' => 'LoginController', 'action' => 'qrScanner']);
$router->add('logout', ['controller' => 'LoginController', 'action' => 'logout']);

// Dashboard
$router->add('', ['controller' => 'DashboardController', 'action' => 'index']);
$router->add('dashboard', ['controller' => 'DashboardController', 'action' => 'index']);

// Attendance routes
$router->add('attendance', ['controller' => 'AttendanceController', 'action' => 'index']);
$router->add('attendance/records', ['controller' => 'AttendanceController', 'action' => 'records']);
$router->add('attendance/all', ['controller' => 'AttendanceController', 'action' => 'all']);
$router->add('attendance/create', ['controller' => 'AttendanceController', 'action' => 'create']);
$router->add('attendance/edit/{id}', ['controller' => 'AttendanceController', 'action' => 'edit']);
$router->add('attendance/delete/{id}', ['controller' => 'AttendanceController', 'action' => 'delete']);
$router->add('attendance/report', ['controller' => 'AttendanceController', 'action' => 'report']);
$router->add('attendance/by-employee/{id}', ['controller' => 'AttendanceController', 'action' => 'byEmployee']);
$router->add('attendance/quick-mark', ['controller' => 'AttendanceController', 'action' => 'quickMark']);
$router->add('attendance-audit', ['controller' => 'AttendanceController', 'action' => 'audit']);

// Attendance AJAX API routes
$router->add('api/attendance/employees', ['controller' => 'AttendanceController', 'action' => 'getEmployeesByBranch']);
$router->add('api/attendance/mark', ['controller' => 'AttendanceController', 'action' => 'markAttendance']);
$router->add('api/attendance/stats', ['controller' => 'AttendanceController', 'action' => 'getTodayStats']);
$router->add('api/attendance/employee-calendar', ['controller' => 'AttendanceController', 'action' => 'getEmployeeCalendar']);
$router->add('api/attendance/branch-calendar', ['controller' => 'AttendanceController', 'action' => 'getBranchCalendar']);

// Activity routes
$router->add('activity-logs', ['controller' => 'ActivityController', 'action' => 'logs']);

// Branch routes
$router->add('branches', ['controller' => 'BranchController', 'action' => 'index']);
$router->add('branches/create', ['controller' => 'BranchController', 'action' => 'create']);
$router->add('branches/edit/{id}', ['controller' => 'BranchController', 'action' => 'edit']);
$router->add('branches/delete/{id}', ['controller' => 'BranchController', 'action' => 'delete']);

// Branch QR Scanner routes
$router->add('branch/scanner', ['controller' => 'BranchQRController', 'action' => 'scanner']);
$router->add('branch/scan', ['controller' => 'BranchQRController', 'action' => 'processScan']);
$router->add('branch/preview', ['controller' => 'BranchQRController', 'action' => 'previewScan']);
$router->add('branch/logout', ['controller' => 'BranchQRController', 'action' => 'logout']);

// Pages routes (coming soon)
$router->add('notifications', ['controller' => 'NotificationController', 'action' => 'index']);

// Notification API routes
$router->add('api/notifications/count', ['controller' => 'NotificationController', 'action' => 'getUnreadCount']);
$router->add('api/notifications/recent', ['controller' => 'NotificationController', 'action' => 'getRecent']);
$router->add('api/notifications/mark-read/{id}', ['controller' => 'NotificationController', 'action' => 'markAsRead']);
$router->add('api/notifications/mark-all-read', ['controller' => 'NotificationController', 'action' => 'markAllAsRead']);
$router->add('documents', ['controller' => 'PagesController', 'action' => 'documents']);
$router->add('finance', ['controller' => 'PagesController', 'action' => 'finance']);
$router->add('finance/payroll', ['controller' => 'PayrollController', 'action' => 'index']);
$router->add('finance/overtime', ['controller' => 'PagesController', 'action' => 'overtime']);
$router->add('finance/cash-advance', ['controller' => 'PagesController', 'action' => 'cashAdvance']);
$router->add('finance/billing', ['controller' => 'PagesController', 'action' => 'billing']);
$router->add('procurement', ['controller' => 'PagesController', 'action' => 'procurement']);
$router->add('settings', ['controller' => 'PagesController', 'action' => 'settings']);

// Employee routes
$router->add('employee', ['controller' => 'EmployeeController', 'action' => 'index']);
$router->add('employee/records', ['controller' => 'EmployeeController', 'action' => 'records']);
$router->add('employee/create', ['controller' => 'EmployeeController', 'action' => 'create']);
$router->add('employee/view/{id}', ['controller' => 'EmployeeController', 'action' => 'viewEmployee']);
$router->add('employee/edit/{id}', ['controller' => 'EmployeeController', 'action' => 'edit']);
$router->add('employee/delete/{id}', ['controller' => 'EmployeeController', 'action' => 'delete']);
$router->add('employee/get/{id}', ['controller' => 'EmployeeController', 'action' => 'getEmployee']);
$router->add('employee/toggle-deduction/{id}', ['controller' => 'EmployeeController', 'action' => 'toggleDeduction']);
$router->add('employee/next-code', ['controller' => 'EmployeeController', 'action' => 'getNextCode']);

// Payroll API routes
$router->add('api/payroll/calculate', ['controller' => 'PayrollController', 'action' => 'calculate']);
$router->add('api/payroll/weekly', ['controller' => 'PayrollController', 'action' => 'getWeeklyData']);
$router->add('api/payroll/export', ['controller' => 'PayrollController', 'action' => 'export']);
$router->add('api/payroll/payslip', ['controller' => 'PayrollController', 'action' => 'printPayslip']);
$router->add('api/payroll/week-options', ['controller' => 'PayrollController', 'action' => 'getWeekOptions']);

// Dispatch the request
$router->dispatch();
