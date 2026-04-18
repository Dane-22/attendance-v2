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

    private function parseQRAndGetEmployee($qrData, $branchCode) {
        $extractedCode = null;

        // Trim whitespace and newlines that QR scanners often include
        $qrData = trim($qrData);
        error_log('BranchQRController: Parsing QR data: ' . substr($qrData, 0, 100));

        // Check if V1 URL format (case-insensitive)
        $lowerQrData = strtolower($qrData);
        if (strpos($lowerQrData, 'http://') === 0 || strpos($lowerQrData, 'https://') === 0) {
            error_log('BranchQRController: Detected V1 URL format');
            $urlParts = parse_url($qrData);
            error_log('BranchQRController: URL parts: ' . json_encode($urlParts));
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $params);
                $extractedCode = $params['emp_code'] ?? null;
                error_log('BranchQRController: Extracted emp_code: ' . ($extractedCode ?? 'NULL'));
            }
        }
        // Check if V2 text format: JAJR-EMP:id|code|name
        elseif (preg_match('/JAJR-EMP:(\d+)\|([^|]+)\|(.+)/', $qrData, $matches)) {
            error_log('BranchQRController: Detected V2 text format');
            $extractedCode = $matches[2];
        }

        if (empty($extractedCode)) {
            error_log('BranchQRController: Failed to extract code from QR data');
            return ['error' => 'Invalid QR code format. Expected URL with emp_code or JAJR-EMP: format'];
        }

        // Find employee by employee_code
        $employee = $this->employeeModel->findByEmployeeCode($extractedCode);
        if (!$employee) {
            return ['error' => 'Employee not found'];
        }

        // Validate employee is assigned to current branch
        if ($employee['branch_code'] !== $branchCode) {
            return ['error' => 'Employee not assigned to this branch'];
        }

        if ($employee['status'] !== 'Active') {
            return ['error' => 'Employee is not active'];
        }

        return ['employee' => $employee];
    }

    public function previewScan() {
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

        $result = $this->parseQRAndGetEmployee($qrData, $branchCode);
        if (isset($result['error'])) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        $employee = $result['employee'];
        $today = date('Y-m-d');

        try {
            $lastAttendance = $this->attendanceModel->getLastTodayByEmployee($employee['id'], $today);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            return;
        }

        // Determine what action will be taken
        $action = 'check_in'; // default
        $message = 'Check in ' . $employee['first_name'] . ' ' . $employee['last_name'] . '?';

        if ($lastAttendance) {
            if ($lastAttendance['branch_code'] !== $branchCode) {
                if (empty($lastAttendance['check_out'])) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error' => 'Employee must check out from ' . $lastAttendance['branch_code'] . ' before checking in here',
                        'current_branch' => $lastAttendance['branch_code'],
                        'action_required' => 'checkout'
                    ]);
                    return;
                }
                // Can check in at new branch
            } else {
                // Same branch
                if (empty($lastAttendance['check_out'])) {
                    $action = 'check_out';
                    $message = 'Check out ' . $employee['first_name'] . ' ' . $employee['last_name'] . '?';
                }
                // Already checked out, will create new record (check in)
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'preview' => true,
            'action' => $action,
            'message' => $message,
            'employee' => [
                'id' => $employee['id'],
                'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'code' => $employee['employee_code']
            ]
        ]);
    }

    public function processScan() {
        // Check if branch device is logged in
        if (empty($_SESSION['branch_code'])) {
            error_log('BranchQRController: No branch_code in session');
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not authenticated - no branch_code in session']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('BranchQRController: Invalid method ' . $_SERVER['REQUEST_METHOD']);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method: ' . $_SERVER['REQUEST_METHOD']]);
            return;
        }

        $qrData = $_POST['qr_data'] ?? '';
        $branchCode = $_SESSION['branch_code'];
        error_log('BranchQRController: Processing scan for branch ' . $branchCode . ', QR: ' . substr($qrData, 0, 50));

        $result = $this->parseQRAndGetEmployee($qrData, $branchCode);
        if (isset($result['error'])) {
            header('Content-Type: application/json');
            echo json_encode($result);
            return;
        }

        $employee = $result['employee'];

        // Set Philippines timezone
        date_default_timezone_set('Asia/Manila');

        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        error_log('BranchQRController: Today=' . $today . ', Time=' . $currentTime . ', Employee=' . $employee['id']);

        try {
            // Check if employee has any attendance record today at any branch
            $lastAttendance = $this->attendanceModel->getLastTodayByEmployee($employee['id'], $today);
            error_log('BranchQRController: Last attendance: ' . ($lastAttendance ? json_encode($lastAttendance) : 'none'));
        } catch (Exception $e) {
            error_log('BranchQRController: Database error getting attendance: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            return;
        }

        // Cross-branch validation: if employee has unchecked attendance at different branch, block
        if ($lastAttendance && $lastAttendance['branch_code'] !== $branchCode && empty($lastAttendance['check_out'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Employee must check out from ' . $lastAttendance['branch_code'] . ' before checking in here',
                'current_branch' => $lastAttendance['branch_code'],
                'action_required' => 'checkout'
            ]);
            return;
        }

        // Use unified attendance recording method
        try {
            $result = $this->attendanceModel->recordAttendance(
                $employee['id'],
                $branchCode,
                $today,
                'qr',
                $currentTime
            );
            error_log('BranchQRController: recordAttendance result: ' . json_encode($result));
        } catch (Exception $e) {
            error_log('BranchQRController: Database error recording attendance: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            return;
        }

        if ($result['success']) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'action' => $result['action'],
                'employee' => [
                    'id' => $employee['id'],
                    'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                    'code' => $employee['employee_code'],
                    'time' => $currentTime
                ]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => $result['error'] ?? 'Failed to record attendance']);
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
        $this->redirect('/');
    }
}
