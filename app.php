<?php

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
$router->add('branch/logout', ['controller' => 'BranchQRController', 'action' => 'logout']);

// Pages routes (coming soon)
$router->add('notifications', ['controller' => 'PagesController', 'action' => 'notifications']);
$router->add('documents', ['controller' => 'PagesController', 'action' => 'documents']);
$router->add('finance', ['controller' => 'PagesController', 'action' => 'finance']);
$router->add('finance/payroll', ['controller' => 'PagesController', 'action' => 'payroll']);
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
$router->add('employee/next-code', ['controller' => 'EmployeeController', 'action' => 'getNextCode']);

// Dispatch the request
$router->dispatch();
