<?php

require_once __DIR__ . '/../core/Controller.php';

class EmployeeController extends Controller {
    private $employeeModel;

    public function __construct() {
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $employees = $this->employeeModel->findAll();
        $totalEmployees = $this->employeeModel->countAll();
        
        $this->view('employee/employee_list', [
            'title' => 'Employees',
            'employees' => $employees,
            'totalEmployees' => $totalEmployees
        ]);
    }

    public function records() {
        $employees = $this->employeeModel->findAll();
        
        $this->view('employee/index', [
            'employees' => $employees,
            'title' => 'Employees'
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $position = $_POST['position'] ?: '';
            $employeeCode = $_POST['employee_code'] ?: '';
            
            // Verify code doesn't exist, keep generating until we find a unique one
            $attempts = 0;
            while ($this->employeeModel->findByEmployeeCode($employeeCode) && $attempts < 10) {
                $employeeCode = $this->employeeModel->getNextEmployeeCode($position);
                $attempts++;
            }
            
            $data = [
                'employee_code' => $employeeCode,
                'first_name' => $_POST['first_name'],
                'middle_name' => $_POST['middle_name'] ?: null,
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'position' => $position,
                'status' => $_POST['status'] ?: 'Active',
                'daily_rate' => $_POST['daily_rate'] ?: 0,
                'has_deductions' => isset($_POST['has_deductions']) ? 1 : 0
            ];

            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/employees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $data['profile_image'] = 'uploads/employees/' . $fileName;
                }
            }

            if ($this->employeeModel->create($data)) {
                $_SESSION['success'] = 'Employee created successfully';
                $this->redirect('/employee');
            } else {
                $_SESSION['error'] = 'Failed to create employee';
                $this->redirect('/employee');
            }
        }
        
        $this->view('employee/create', [
            'title' => 'Add Employee'
        ]);
    }

    public function viewEmployee($id) {
        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            $_SESSION['error'] = 'Employee not found';
            $this->redirect('/employee');
        }
        
        $this->view('employee/view', [
            'employee' => $employee,
            'title' => 'Employee Details'
        ]);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'employee_code' => $_POST['employee_code'],
                'first_name' => $_POST['first_name'],
                'middle_name' => $_POST['middle_name'] ?: null,
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'position' => $_POST['position'] ?: null,
                'status' => $_POST['status'] ?: 'Active',
                'daily_rate' => $_POST['daily_rate'] ?: 0,
                'has_deductions' => isset($_POST['has_deductions']) ? 1 : 0
            ];

            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/employees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $data['profile_image'] = 'uploads/employees/' . $fileName;
                }
            }

            if ($this->employeeModel->update($id, $data)) {
                $_SESSION['success'] = 'Employee updated successfully';
                $this->redirect('/employee');
            } else {
                $_SESSION['error'] = 'Failed to update employee';
                $this->redirect('/employee/edit/' . $id);
            }
        }

        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            $_SESSION['error'] = 'Employee not found';
            $this->redirect('/employee');
        }
        
        $this->view('employee/edit', [
            'employee' => $employee,
            'title' => 'Edit Employee'
        ]);
    }

    public function delete($id) {
        if ($this->employeeModel->delete($id)) {
            $_SESSION['success'] = 'Employee deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete employee';
        }
        $this->redirect('/employee');
    }

    public function getNextCode() {
        $currentUser = $this->requireJWT();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $position = $_POST['position'] ?? '';
            if ($position) {
                $nextCode = $this->employeeModel->getNextEmployeeCode($position);
                echo json_encode(['code' => $nextCode]);
                return;
            }
        }
        echo json_encode(['code' => '']);
    }

    public function getEmployee($id) {
        $currentUser = $this->requireJWT();

        header('Content-Type: application/json');
        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            echo json_encode(['error' => 'Employee not found']);
            return;
        }
        
        echo json_encode($employee);
    }
}
