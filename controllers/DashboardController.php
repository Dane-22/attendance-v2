<?php

require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller {
    private $attendanceModel;
    private $employeeModel;

    public function __construct() {
        $this->attendanceModel = $this->model('Attendance');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $today = date('Y-m-d');
        $todayAttendance = $this->attendanceModel->getByDate($today);
        $employees = $this->employeeModel->findAll();
        
        $totalEmployees = count($employees);
        $presentToday = 0;
        $absentToday = 0;
        $lateToday = 0;
        $onLeaveToday = 0;
        
        $markedEmployees = [];
        foreach ($todayAttendance as $record) {
            $markedEmployees[] = $record['employee_id'];
            switch ($record['status']) {
                case 'present':
                    $presentToday++;
                    break;
                case 'absent':
                    $absentToday++;
                    break;
                case 'late':
                    $lateToday++;
                    break;
                case 'leave':
                    $onLeaveToday++;
                    break;
            }
        }
        
        $notMarked = $totalEmployees - count($markedEmployees);
        
        $month = date('m');
        $year = date('Y');
        $monthlyReport = $this->attendanceModel->getMonthlyReport($month, $year);
        
        $this->view('dashboard/index', [
            'totalEmployees' => $totalEmployees,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'lateToday' => $lateToday,
            'onLeaveToday' => $onLeaveToday,
            'notMarked' => $notMarked,
            'today' => $today,
            'monthlyReport' => $monthlyReport,
            'title' => 'Dashboard'
        ]);
    }
}
