<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/JWT.php';

class LoginController extends Controller {
    private $employeeModel;
    private $adminModel;

    public function __construct() {
        $this->employeeModel = $this->model('Employee');
        $this->adminModel = $this->model('Admin');
    }

    public function index() {
        // If already logged in, redirect to dashboard
        if (!empty($_SESSION['employee_id'])) {
            $this->redirect('employee/dashboard.php');
            exit;
        }

        $error = '';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password';
            } else {
                // Check admin login
                $admin = $this->adminModel->findByUsername($username);
                
                if (!$admin) {
                    $error = 'User not found: ' . htmlspecialchars($username);
                } elseif (!$this->adminModel->verifyPassword($password, $admin['password'])) {
                    $error = 'Password mismatch. Please try again.';
                } else {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];

                    // Generate JWT token for API access
                    $jwtPayload = [
                        'user_id' => $admin['id'],
                        'username' => $admin['username'],
                        'name' => $admin['name'],
                        'role' => $admin['role'],
                        'type' => 'admin'
                    ];
                    if (!empty($admin['branch_code'])) {
                        $jwtPayload['branch_code'] = $admin['branch_code'];
                        $_SESSION['branch_code'] = $admin['branch_code'];
                        $_SESSION['is_branch_device'] = true;
                    }
                    $jwtToken = JWT::generate($jwtPayload, 86400); // 24 hours
                    $_SESSION['jwt_token'] = $jwtToken;

                    // If this is an AJAX/API request, return JSON with token
                    if ($this->isAjaxRequest()) {
                        $this->jsonResponse([
                            'success' => true,
                            'token' => $jwtToken,
                            'user' => [
                                'id' => $admin['id'],
                                'name' => $admin['name'],
                                'role' => $admin['role']
                            ],
                            'redirect' => !empty($admin['branch_code']) ? 'branch/scanner' : 'dashboard'
                        ]);
                    }

                    // Redirect branch devices to QR scanner
                    if (!empty($admin['branch_code'])) {
                        $this->redirect('branch/scanner');
                    } else {
                        $this->redirect('dashboard');
                    }
                    exit;
                }
            }
        }

        $this->view('auth/login', [
            'error' => $error,
            'title' => 'Login'
        ]);
    }

    public function qrScanner() {
        // If already logged in, redirect to dashboard
        if (!empty($_SESSION['employee_id'])) {
            $this->redirect('employee/dashboard.php');
            exit;
        }

        $error = '';
        $success = '';

        // Handle QR code login via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_data'])) {
            $qrData = $_POST['qr_data'];
            
            // Check if QR data matches an employee code
            $employee = $this->employeeModel->findByEmployeeCode($qrData);
            
            if ($employee) {
                $_SESSION['employee_id'] = $employee['id'];
                $_SESSION['employee_name'] = $employee['first_name'] . ' ' . $employee['last_name'];

                // Generate JWT token for API access
                $jwtPayload = [
                    'user_id' => $employee['id'],
                    'employee_code' => $employee['employee_code'],
                    'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                    'type' => 'employee'
                ];
                $jwtToken = JWT::generate($jwtPayload, 86400); // 24 hours
                $_SESSION['jwt_token'] = $jwtToken;

                // If this is an AJAX/API request, return JSON with token
                if ($this->isAjaxRequest()) {
                    $this->jsonResponse([
                        'success' => true,
                        'token' => $jwtToken,
                        'user' => [
                            'id' => $employee['id'],
                            'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                            'code' => $employee['employee_code']
                        ],
                        'redirect' => 'employee/dashboard.php'
                    ]);
                }

                $success = 'Login successful! Redirecting...';
                header('Refresh: 2; URL=employee/dashboard.php');
            } else {
                $error = 'Invalid QR code. Please try again.';
            }
        }

        $this->view('auth/qr-scanner', [
            'error' => $error,
            'success' => $success,
            'title' => 'QR Scanner'
        ]);
    }

    public function logout() {
        // Clear JWT token on logout
        $_SESSION['jwt_token'] = null;
        session_destroy();
        $this->redirect('/');
    }

    /**
     * Check if the current request is an AJAX/API request
     * @return bool
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function branchLogin() {
        // If already logged in as branch device, redirect to scanner
        if (!empty($_SESSION['branch_code'])) {
            $this->redirect('branch/scanner');
            exit;
        }

        $error = '';
        $branches = $this->model('Branch')->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $branchCode = $_POST['branch_code'] ?? '';
            
            if (empty($username) || empty($password) || empty($branchCode)) {
                $error = 'Please enter username, password, and select a branch';
            } else {
                // Check admin login
                $admin = $this->adminModel->findByUsername($username);
                
                if (!$admin) {
                    $error = 'User not found: ' . htmlspecialchars($username);
                } elseif (!$this->adminModel->verifyPassword($password, $admin['password'])) {
                    $error = 'Password mismatch. Please try again.';
                } elseif ($admin['branch_code'] !== $branchCode) {
                    $error = 'User not authorized for this branch';
                } else {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['branch_code'] = $admin['branch_code'];
                    $_SESSION['is_branch_device'] = true;
                    $this->redirect('branch/scanner');
                    exit;
                }
            }
        }

        $this->view('auth/branch-login', [
            'error' => $error,
            'branches' => $branches,
            'title' => 'Branch Device Login'
        ]);
    }
}
