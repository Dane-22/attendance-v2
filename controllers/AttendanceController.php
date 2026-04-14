<?php

require_once __DIR__ . '/../core/Controller.php';

class AttendanceController extends Controller {
    private $attendanceModel;
    private $employeeModel;

    public function __construct() {
        $this->attendanceModel = $this->model('Attendance');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $this->view('attendance/site_attendance', [
            'title' => 'Site Attendance'
        ]);
    }

    public function records() {
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $attendance = $this->attendanceModel->getByDate($date);
        $employees = $this->employeeModel->findAll();
        
        $this->view('attendance/index', [
            'attendance' => $attendance,
            'employees' => $employees,
            'date' => $date,
            'title' => 'Attendance Records'
        ]);
    }

    public function all() {
        $attendance = $this->attendanceModel->getAllWithEmployees();
        
        $this->view('attendance/all', [
            'attendance' => $attendance,
            'title' => 'All Attendance Records'
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'employee_id' => $_POST['employee_id'],
                'date' => $_POST['date'],
                'check_in' => $_POST['check_in'] ?: null,
                'check_out' => $_POST['check_out'] ?: null,
                'status' => $_POST['status'],
                'notes' => $_POST['notes'] ?: null
            ];

            if ($this->attendanceModel->checkDuplicate($data['employee_id'], $data['date'])) {
                $_SESSION['error'] = 'Attendance record already exists for this employee on this date';
                $this->redirect('/attendance/create');
            }

            if ($this->attendanceModel->create($data)) {
                $_SESSION['success'] = 'Attendance record created successfully';
                $this->redirect('/attendance');
            } else {
                $_SESSION['error'] = 'Failed to create attendance record';
                $this->redirect('/attendance/create');
            }
        }

        $employees = $this->employeeModel->findAll();
        $today = date('Y-m-d');
        
        $this->view('attendance/create', [
            'employees' => $employees,
            'today' => $today,
            'title' => 'Add Attendance'
        ]);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'employee_id' => $_POST['employee_id'],
                'date' => $_POST['date'],
                'check_in' => $_POST['check_in'] ?: null,
                'check_out' => $_POST['check_out'] ?: null,
                'status' => $_POST['status'],
                'notes' => $_POST['notes'] ?: null
            ];

            if ($this->attendanceModel->checkDuplicate($data['employee_id'], $data['date'], $id)) {
                $_SESSION['error'] = 'Attendance record already exists for this employee on this date';
                $this->redirect('/attendance/edit/' . $id);
            }

            if ($this->attendanceModel->update($id, $data)) {
                $_SESSION['success'] = 'Attendance record updated successfully';
                $this->redirect('/attendance');
            } else {
                $_SESSION['error'] = 'Failed to update attendance record';
                $this->redirect('/attendance/edit/' . $id);
            }
        }

        $record = $this->attendanceModel->findById($id);
        $employees = $this->employeeModel->findAll();
        
        if (!$record) {
            $_SESSION['error'] = 'Attendance record not found';
            $this->redirect('/attendance');
        }
        
        $this->view('attendance/edit', [
            'record' => $record,
            'employees' => $employees,
            'title' => 'Edit Attendance'
        ]);
    }

    public function delete($id) {
        if ($this->attendanceModel->delete($id)) {
            $_SESSION['success'] = 'Attendance record deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete attendance record';
        }
        $this->redirect('/attendance');
    }

    public function report() {
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        $report = $this->attendanceModel->getMonthlyReport($month, $year);
        
        $this->view('attendance/report', [
            'report' => $report,
            'month' => $month,
            'year' => $year,
            'title' => 'Monthly Attendance Report'
        ]);
    }

    public function byEmployee($employeeId) {
        $records = $this->attendanceModel->getByEmployeeId($employeeId);
        $employee = $this->employeeModel->findById($employeeId);
        
        $this->view('attendance/by_employee', [
            'records' => $records,
            'employee' => $employee,
            'title' => 'Attendance - ' . $employee['first_name'] . ' ' . $employee['last_name']
        ]);
    }

    public function quickMark() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['date'] ?? date('Y-m-d');
            $employees = $_POST['employees'] ?? [];
            $statuses = $_POST['status'] ?? [];
            
            $success = 0;
            $failed = 0;
            
            foreach ($employees as $employeeId) {
                $status = $statuses[$employeeId] ?? 'present';
                
                if ($this->attendanceModel->checkDuplicate($employeeId, $date)) {
                    $failed++;
                    continue;
                }
                
                $data = [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'check_in' => $status == 'present' ? '09:00:00' : null,
                    'check_out' => $status == 'present' ? '17:00:00' : null,
                    'status' => $status,
                    'notes' => 'Quick marked'
                ];
                
                if ($this->attendanceModel->create($data)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
            
            if ($success > 0) {
                $_SESSION['success'] = "Attendance marked for {$success} employee(s)";
            }
            if ($failed > 0) {
                $_SESSION['error'] = "Failed to mark attendance for {$failed} employee(s) (already exists)";
            }
            
            $this->redirect('/attendance?date=' . $date);
        }
    }

    public function audit() {
        $this->view('attendance/attendance_audit', [
            'title' => 'Attendance Audit'
        ]);
    }
}
