<?php

require_once __DIR__ . '/../core/Controller.php';

class BranchQRController extends Controller {
    private $attendanceModel;
    private $employeeModel;
    private $branchModel;

    public function __construct() {
        $this->attendanceModel = $this->model('Attendance');
        $this->employeeModel = $this->model('Employee');
        $this->branchModel = $this->model('Branch');
    }

    public function scanner() {
        // Check if branch device is logged in
        if (empty($_SESSION['branch_code'])) {
            $this->redirect('branch/login');
            exit;
        }

        $branch = $this->branchModel->findByCode($_SESSION['branch_code']);
        
        if (!$branch) {
            $_SESSION['error'] = 'Invalid branch';
            $this->redirect('branch/login');
            exit;
        }

        $this->view('branch_qr/scanner', [
            'title' => 'Branch QR Scanner - ' . $branch['branch_name'],
            'branch' => $branch
        ]);
    }

    public function processScan() {
        // Check if branch device is logged in
        if (empty($_SESSION['branch_code'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        $qrData = $_POST['qr_data'] ?? '';
        $branchCode = $_SESSION['branch_code'];

        // Parse QR data format: JAJR-EMP:id|code|name
        if (!preg_match('/JAJR-EMP:(\d+)\|([^|]+)\|(.+)/', $qrData, $matches)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid QR code format']);
            return;
        }

        $employeeId = $matches[1];
        $employeeCode = $matches[2];
        $employeeName = $matches[3];

        // Verify employee exists and is active
        $employee = $this->employeeModel->findById($employeeId);
        if (!$employee) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Employee not found']);
            return;
        }

        if ($employee['status'] !== 'Active') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Employee is not active']);
            return;
        }

        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        // Check if employee has any attendance record today at any branch
        $lastAttendance = $this->attendanceModel->getLastTodayByEmployee($employeeId, $today);

        if ($lastAttendance) {
            // Employee has attendance today
            if ($lastAttendance['branch_code'] !== $branchCode) {
                // Employee is at a different branch
                if (empty($lastAttendance['check_out'])) {
                    // Employee hasn't checked out from previous branch
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error' => 'Employee must check out from ' . $lastAttendance['branch_code'] . ' before checking in here',
                        'current_branch' => $lastAttendance['branch_code'],
                        'action_required' => 'checkout'
                    ]);
                    return;
                }
                // Employee checked out, can check in at new branch
            } else {
                // Same branch - check if they need to check in or out
                if (empty($lastAttendance['check_out'])) {
                    // Check out
                    $this->attendanceModel->updateCheckOut($lastAttendance['id'], $currentTime);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'action' => 'check_out',
                        'employee' => [
                            'id' => $employee['id'],
                            'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                            'code' => $employee['employee_code'],
                            'time' => $currentTime
                        ]
                    ]);
                    return;
                }
                // Already checked out, create new check-in (multiple sessions allowed)
            }
        }

        // Create new check-in record
        $data = [
            'employee_id' => $employeeId,
            'branch_code' => $branchCode,
            'date' => $today,
            'check_in' => $currentTime,
            'check_out' => null,
            'status' => 'present',
            'notes' => 'QR Scan at ' . $branchCode
        ];

        if ($this->attendanceModel->create($data)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'action' => 'check_in',
                'employee' => [
                    'id' => $employee['id'],
                    'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                    'code' => $employee['employee_code'],
                    'time' => $currentTime
                ]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to record attendance']);
        }
    }

    public function login() {
        // If already logged in as branch device, redirect to scanner
        if (!empty($_SESSION['branch_code'])) {
            $this->redirect('branch/scanner');
            exit;
        }

        $error = '';
        $branches = $this->branchModel->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $branchCode = $_POST['branch_code'] ?? '';
            
            if (empty($username) || empty($password) || empty($branchCode)) {
                $error = 'Please enter username, password, and select a branch';
            } else {
                // Check admin login
                $admin = $this->model('Admin')->findByUsername($username);
                
                if (!$admin) {
                    $error = 'User not found';
                } elseif (!password_verify($password, $admin['password'])) {
                    $error = 'Invalid password';
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

    public function logout() {
        session_destroy();
        $this->redirect('/jajr-v2/');
    }
}
