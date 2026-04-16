<?php

require_once __DIR__ . '/../core/Controller.php';

// Set timezone to Philippines (UTC+8)
date_default_timezone_set('Asia/Manila');

class AttendanceController extends Controller {
    private $attendanceModel;
    private $employeeModel;
    private $branchModel;

    public function __construct() {
        $this->attendanceModel = $this->model('Attendance');
        $this->employeeModel = $this->model('Employee');
        $this->branchModel = $this->model('Branch');
    }

    public function index() {
        $branches = $this->branchModel->findAll();
        $today = date('Y-m-d');
        
        // Get today's attendance stats
        $todayAttendance = $this->attendanceModel->getByDate($today);
        $totalWorkers = $this->employeeModel->countAll();
        $present = count(array_filter($todayAttendance, fn($a) => $a['status'] === 'present' || $a['status'] === 'late'));
        $absent = count(array_filter($todayAttendance, fn($a) => $a['status'] === 'absent'));
        
        $this->view('attendance/site_attendance', [
            'title' => 'Site Attendance',
            'branches' => $branches,
            'totalWorkers' => $totalWorkers,
            'present' => $present,
            'absent' => $absent,
            'today' => $today
        ]);
    }

    public function getEmployeesByBranch() {
        $this->requireApiToken();

        if (!isset($_GET['branch_code'])) {
            $this->jsonResponse(['error' => 'Branch code required'], 400);
        }
        
        $branchCode = $_GET['branch_code'];
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Get all employees with their attendance status for today
        $employees = $this->employeeModel->findAll();
        $attendance = $this->attendanceModel->getByDate($date);
        
        // Create attendance lookup by employee_id
        $attendanceMap = [];
        foreach ($attendance as $a) {
            $attendanceMap[$a['employee_id']] = $a;
        }
        
        // Add attendance status to each employee
        foreach ($employees as &$emp) {
            $emp['attendance_status'] = $attendanceMap[$emp['id']]['status'] ?? null;
            $emp['check_in'] = $attendanceMap[$emp['id']]['check_in'] ?? null;
            $emp['check_out'] = $attendanceMap[$emp['id']]['check_out'] ?? null;
            $emp['attendance_id'] = $attendanceMap[$emp['id']]['id'] ?? null;
        }
        
        $this->jsonResponse(['employees' => $employees]);
    }

    public function markAttendance() {
        $this->requireApiToken();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'POST required'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['employee_id'], $data['status'])) {
            $this->jsonResponse(['error' => 'Missing required fields'], 400);
        }
        
        $employeeId = $data['employee_id'];
        $status = $data['status'];
        $branchCode = $data['branch_code'] ?? null;
        $date = $data['date'] ?? date('Y-m-d');
        $notes = $data['notes'] ?? 'Marked via site attendance';
        
        // Check if attendance already exists
        $existing = $this->attendanceModel->getLastTodayByEmployee($employeeId, $date);
        
        if ($existing) {
            // Update existing record
            if ($status === 'present' && $existing['check_in'] && !$existing['check_out']) {
                // Check-out
                $this->attendanceModel->updateCheckOut($existing['id'], date('H:i:s'));
                $this->jsonResponse(['success' => true, 'message' => 'Check-out recorded']);
            } else {
                // Update status
                $this->attendanceModel->update($existing['id'], [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'check_in' => $status === 'present' ? ($existing['check_in'] ?? date('H:i:s')) : null,
                    'check_out' => null,
                    'status' => $status,
                    'notes' => $notes
                ]);
                $this->jsonResponse(['success' => true, 'message' => 'Attendance updated']);
            }
        } else {
            // Create new record
            $this->attendanceModel->create([
                'employee_id' => $employeeId,
                'branch_code' => $branchCode,
                'date' => $date,
                'check_in' => $status === 'present' ? date('H:i:s') : null,
                'check_out' => null,
                'status' => $status,
                'notes' => $notes
            ]);
            $this->jsonResponse(['success' => true, 'message' => 'Attendance recorded']);
        }
    }

    public function getTodayStats() {
        $this->requireApiToken();

        $date = $_GET['date'] ?? date('Y-m-d');
        $attendance = $this->attendanceModel->getByDate($date);
        $totalWorkers = $this->employeeModel->countAll();
        $present = count(array_filter($attendance, fn($a) => $a['status'] === 'present' || $a['status'] === 'late'));
        $absent = count(array_filter($attendance, fn($a) => $a['status'] === 'absent'));
        
        $this->jsonResponse([
            'totalWorkers' => $totalWorkers,
            'present' => $present,
            'absent' => $absent
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
        $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $branchFilter = isset($_GET['branch']) ? $_GET['branch'] : null;
        
        // Get branches for dropdown
        $branches = $this->branchModel->findAll();
        
        // Get attendance for selected date (optionally filtered by branch)
        if ($branchFilter) {
            $dailyAttendance = $this->attendanceModel->getByDateAndBranch($selectedDate, $branchFilter);
        } else {
            $dailyAttendance = $this->attendanceModel->getByDate($selectedDate);
        }
        
        // Apply status filter
        if ($filter !== 'all') {
            $dailyAttendance = array_filter($dailyAttendance, function($a) use ($filter) {
                switch ($filter) {
                    case 'present':
                        return $a['status'] === 'present' && !$a['check_out'];
                    case 'late':
                        return $a['status'] === 'late';
                    case 'completed':
                        return $a['status'] === 'present' && $a['check_out'];
                    case 'absent':
                        return $a['status'] === 'absent';
                    default:
                        return true;
                }
            });
        }
        
        // Get month stats
        $monthStats = $this->attendanceModel->getMonthlyStats($month, $year);
        
        // Calculate real stats
        $totalRecords = count($dailyAttendance);
        $currentlyPresent = count(array_filter($dailyAttendance, fn($a) => $a['status'] === 'present' && !$a['check_out']));
        $completedShifts = count(array_filter($dailyAttendance, fn($a) => $a['status'] === 'present' && $a['check_out']));
        $absent = count(array_filter($dailyAttendance, fn($a) => $a['status'] === 'absent'));
        
        // Build calendar data (optionally filtered by branch)
        $calendarData = $this->buildCalendarData($year, $month, $branchFilter);
        
        // Prepare attendance records for table
        $attendanceRecords = [];
        foreach ($dailyAttendance as $record) {
            $attendanceRecords[] = [
                'name' => $record['first_name'] . ' ' . $record['last_name'],
                'position' => $record['position'] ?? 'Worker',
                'code' => $record['employee_code'],
                'branch' => $record['branch_name'] ?? 'N/A',
                'timeIn' => $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : '-',
                'timeOut' => $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : '-',
                'hours' => $this->calculateHours($record['check_in'], $record['check_out']),
                'status' => ucfirst($record['status'])
            ];
        }
        
        $this->view('attendance/attendance_audit', [
            'title' => 'Attendance Audit',
            'currentMonth' => date('F Y', strtotime("$year-$month-01")),
            'selectedDate' => date('F j, Y (l)', strtotime($selectedDate)),
            'calendarYear' => $year,
            'calendarMonth' => $month,
            'selectedDateValue' => $selectedDate,
            'calendarData' => $calendarData,
            'totalRecords' => $totalRecords,
            'currentlyPresent' => $currentlyPresent,
            'completedShifts' => $completedShifts,
            'absent' => $absent,
            'attendanceRecords' => $attendanceRecords,
            'filter' => $filter,
            'branchFilter' => $branchFilter,
            'branches' => $branches
        ]);
    }
    
    private function buildCalendarData($year, $month, $branchFilter = null) {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDay = date('w', strtotime("$year-$month-01"));
        $prevMonthDays = cal_days_in_month(CAL_GREGORIAN, $month == 1 ? 12 : $month - 1, $month == 1 ? $year - 1 : $year);
        
        $calendarDays = [];
        
        // Previous month days
        for ($i = $firstDay - 1; $i >= 0; $i--) {
            $calendarDays[] = ['day' => $prevMonthDays - $i, 'month' => 'prev'];
        }
        
        // Current month days with record counts (optionally filtered by branch)
        if ($branchFilter) {
            $statsByDay = $this->attendanceModel->getDailyStatsForMonthByBranch($month, $year, $branchFilter);
        } else {
            $statsByDay = $this->attendanceModel->getDailyStatsForMonth($month, $year);
        }
        
        $statsMap = [];
        foreach ($statsByDay as $stat) {
            $statsMap[intval($stat['day'])] = $stat['count'];
        }
        
        $today = date('j');
        $todayMonth = date('n');
        $todayYear = date('Y');
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayData = ['day' => $day];
            if (isset($statsMap[$day])) {
                $dayData['rec'] = $statsMap[$day];
            }
            if ($day == $today && $month == $todayMonth && $year == $todayYear) {
                $dayData['today'] = true;
            }
            $calendarDays[] = $dayData;
        }
        
        // Next month days to fill grid
        $remaining = (7 - (count($calendarDays) % 7)) % 7;
        for ($i = 1; $i <= $remaining; $i++) {
            $calendarDays[] = ['day' => $i, 'month' => 'next'];
        }
        
        return $calendarDays;
    }
    
    private function calculateHours($checkIn, $checkOut) {
        if (!$checkIn || !$checkOut) return '0.00';
        $start = strtotime($checkIn);
        $end = strtotime($checkOut);
        $hours = ($end - $start) / 3600;
        return number_format($hours, 2);
    }

    public function getEmployeeCalendar() {
        $this->requireApiToken();

        $employeeCode = $_GET['employee_code'] ?? '';
        $year = intval($_GET['year'] ?? date('Y'));
        $month = intval($_GET['month'] ?? date('n'));
        
        if (!$employeeCode) {
            $this->jsonResponse(['error' => 'Employee code required'], 400);
            return;
        }
        
        // Get employee attendance for the month
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $attendance = $this->attendanceModel->getByDateRange($startDate, $endDate, $employeeCode);
        
        // Format for calendar
        $calendarData = [];
        foreach ($attendance as $record) {
            $dateKey = $record['date'];
            $calendarData[$dateKey] = [
                'status' => $record['status'],
                'check_in' => $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : null,
                'check_out' => $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : null,
                'branch' => $record['branch_name'] ?? 'N/A'
            ];
        }
        
        $this->jsonResponse(['attendance' => $calendarData]);
    }

    public function getBranchCalendar() {
        $this->requireApiToken();

        $branchName = $_GET['branch_name'] ?? '';
        $year = intval($_GET['year'] ?? date('Y'));
        $month = intval($_GET['month'] ?? date('n'));
        
        if (!$branchName) {
            $this->jsonResponse(['error' => 'Branch name required'], 400);
            return;
        }
        
        // Get attendance for branch in date range
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $attendance = $this->attendanceModel->getByBranchAndDateRange($branchName, $startDate, $endDate);
        
        // Group by date and format for calendar
        $calendarData = [];
        foreach ($attendance as $record) {
            $dateKey = $record['date'];
            
            if (!isset($calendarData[$dateKey])) {
                $calendarData[$dateKey] = [
                    'employees' => [],
                    'status' => 'present'
                ];
            }
            
            $calendarData[$dateKey]['employees'][] = [
                'name' => $record['first_name'] . ' ' . $record['last_name'],
                'check_in' => $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : null,
                'check_out' => $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : null,
                'status' => $record['status']
            ];
        }
        
        $this->jsonResponse(['attendance' => $calendarData]);
    }
}
