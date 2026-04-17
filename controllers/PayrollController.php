<?php

class PayrollController extends Controller {
    private $payrollModel;
    private $branchModel;
    private $employeeModel;

    public function __construct() {
        $this->payrollModel = $this->model('Payroll');
        $this->branchModel = $this->model('Branch');
        $this->employeeModel = $this->model('Employee');
    }

    /**
     * Main payroll page
     */
    public function index() {
        $this->requireJWT();

        // Get all branches for filter
        $branches = $this->branchModel->findAll();

        // Get current branch from session or default to first branch
        $currentBranch = $_SESSION['branch_code'] ?? ($_SESSION['admin_branch'] ?? ($branches[0]['code'] ?? ''));

        // Get current week info
        $weekStart = $this->payrollModel->getCurrentWeekStart();
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +5 days'));
        $weekNumber = $this->payrollModel->getWeekNumber($weekStart);

        $data = [
            'branches' => $branches,
            'current_branch' => $currentBranch,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'week_number' => $weekNumber,
            'page_title' => 'Weekly Payroll Report'
        ];

        $this->view('finance/payroll', $data);
    }

    /**
     * Calculate payroll for a week and branch
     */
    public function calculate() {
        $this->requireJWT();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request method'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $weekStart = $input['week_start'] ?? $this->payrollModel->getCurrentWeekStart();
        $branchCode = $input['branch_code'] ?? ($_SESSION['branch_code'] ?? $_SESSION['admin_branch']);

        if (!$branchCode) {
            $this->jsonResponse(['error' => 'Branch code is required'], 400);
            return;
        }

        try {
            $payrollData = $this->payrollModel->calculateWeeklyPayroll($weekStart, $branchCode);

            // Optionally save the calculated payroll
            $saveResults = $this->payrollModel->saveBatchPayroll($payrollData);

            $this->jsonResponse([
                'success' => true,
                'data' => $payrollData,
                'save_results' => $saveResults
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to calculate payroll: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get payroll data for a specific week
     */
    public function getWeeklyData() {
        $this->requireJWT();

        $weekStart = $_GET['week_start'] ?? $this->payrollModel->getCurrentWeekStart();
        $branchCode = $_GET['branch_code'] ?? ($_SESSION['branch_code'] ?? $_SESSION['admin_branch']);

        if (!$branchCode) {
            $this->jsonResponse(['error' => 'Branch code is required'], 400);
            return;
        }

        try {
            // First try to get saved payroll records
            $savedPayroll = $this->payrollModel->getPayrollByWeek($weekStart, $branchCode);

            if (!empty($savedPayroll)) {
                $weekEnd = $savedPayroll[0]['payroll_week_end'];
                $weekNumber = $savedPayroll[0]['week_number'];
            } else {
                // Calculate if not saved
                $payrollData = $this->payrollModel->calculateWeeklyPayroll($weekStart, $branchCode);
                $savedPayroll = $payrollData['employees'];
                $weekEnd = $payrollData['week_end'];
                $weekNumber = $payrollData['week_number'];
            }

            $this->jsonResponse([
                'success' => true,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'week_number' => $weekNumber,
                'branch_code' => $branchCode,
                'payroll' => $savedPayroll
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to get payroll data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export payroll to Excel
     */
    public function export() {
        $this->requireJWT();

        $weekStart = $_GET['week_start'] ?? $this->payrollModel->getCurrentWeekStart();
        $branchCode = $_GET['branch_code'] ?? ($_SESSION['branch_code'] ?? $_SESSION['admin_branch']);

        if (!$branchCode) {
            $this->jsonResponse(['error' => 'Branch code is required'], 400);
            return;
        }

        try {
            $payrollData = $this->payrollModel->calculateWeeklyPayroll($weekStart, $branchCode);

            // Generate Excel file
            $filename = 'payroll_' . $branchCode . '_week' . $payrollData['week_number'] . '_' . $weekStart . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($output, [
                'Employee Code', 'Name', 'Week', 'Days Worked', 'Daily Rate',
                'Basic Pay', 'Performance Allowance', 'Gross Pay',
                'SSS', 'PhilHealth', 'Pag-IBIG', 'Total Deductions', 'Net Pay'
            ]);

            // Data rows
            foreach ($payrollData['employees'] as $emp) {
                fputcsv($output, [
                    $emp['employee_code'],
                    $emp['full_name'],
                    'Week ' . $emp['week_number'],
                    $emp['days_worked'],
                    number_format($emp['daily_rate'], 2),
                    number_format($emp['basic_pay'], 2),
                    number_format($emp['performance_allowance'], 2),
                    number_format($emp['gross_pay'], 2),
                    number_format($emp['sss_contribution'], 2),
                    number_format($emp['phic_contribution'], 2),
                    number_format($emp['hdmf_contribution'], 2),
                    number_format($emp['total_deductions'], 2),
                    number_format($emp['net_pay'], 2)
                ]);
            }

            fclose($output);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate payslip for an employee
     */
    public function printPayslip() {
        $this->requireJWT();

        $employeeId = $_GET['employee_id'] ?? null;
        $weekStart = $_GET['week_start'] ?? $this->payrollModel->getCurrentWeekStart();
        $branchCode = $_GET['branch_code'] ?? ($_SESSION['branch_code'] ?? $_SESSION['admin_branch']);

        if (!$employeeId || !$branchCode) {
            $this->jsonResponse(['error' => 'Employee ID and Branch code are required'], 400);
            return;
        }

        try {
            // Get employee payroll data
            $employee = $this->employeeModel->getById($employeeId);
            if (!$employee) {
                $this->jsonResponse(['error' => 'Employee not found'], 404);
                return;
            }

            $payroll = $this->payrollModel->calculateEmployeePayroll(
                $employee,
                $weekStart,
                date('Y-m-d', strtotime($weekStart . ' +5 days')),
                $this->payrollModel->getWeekNumber($weekStart)
            );

            $this->jsonResponse([
                'success' => true,
                'payslip' => $payroll
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to generate payslip: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get week options for a year
     */
    public function getWeekOptions() {
        $this->requireJWT();

        $year = intval($_GET['year'] ?? date('Y'));
        $month = intval($_GET['month'] ?? date('n'));

        $weeks = [];
        $firstDay = date('Y-m-d', strtotime("{$year}-{$month}-01"));

        // Find first Monday of the month
        $dayOfWeek = date('N', strtotime($firstDay));
        $firstMonday = date('Y-m-d', strtotime($firstDay . ' -' . ($dayOfWeek - 1) . ' days'));

        // Generate 4 weeks starting from first Monday
        for ($i = 0; $i < 4; $i++) {
            $weekStart = date('Y-m-d', strtotime($firstMonday . " +{$i} weeks"));
            $weekEnd = date('Y-m-d', strtotime($weekStart . ' +5 days'));
            $weekNumber = $this->payrollModel->getWeekNumber($weekStart);

            $weeks[] = [
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'week_number' => $weekNumber,
                'label' => "Week {$weekNumber}: " . date('M d', strtotime($weekStart)) . ' - ' . date('M d', strtotime($weekEnd))
            ];
        }

        $this->jsonResponse(['success' => true, 'weeks' => $weeks]);
    }
}
