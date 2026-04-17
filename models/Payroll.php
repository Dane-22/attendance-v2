<?php

require_once __DIR__ . '/../core/Model.php';

class Payroll extends Model {
    protected $table = 'payroll_records';

    /**
     * Get fixed government deductions based on week number and employee has_deduction flag
     */
    public function getFixedDeductions($weekNumber, $hasDeduction) {
        if (!$hasDeduction || $weekNumber < 1 || $weekNumber > 4) {
            return [
                'sss' => 0,
                'phic' => 0,
                'hdmf' => 0
            ];
        }

        $deductions = [
            1 => ['sss' => 150, 'phic' => 100, 'hdmf' => 50],
            2 => ['sss' => 100, 'phic' => 100, 'hdmf' => 50],
            3 => ['sss' => 100, 'phic' => 50, 'hdmf' => 100],
            4 => ['sss' => 0, 'phic' => 0, 'hdmf' => 0]
        ];

        return $deductions[$weekNumber];
    }

    /**
     * Calculate weekly payroll for a branch
     */
    public function calculateWeeklyPayroll($weekStart, $branchCode) {
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +5 days')); // Saturday (Mon + 5)
        $weekNumber = $this->getWeekNumber($weekStart);

        // Get all active employees for the branch
        $employees = $this->getBranchEmployees($branchCode);

        $payrollData = [];

        foreach ($employees as $employee) {
            $payroll = $this->calculateEmployeePayroll($employee, $weekStart, $weekEnd, $weekNumber);
            $payrollData[] = $payroll;
        }

        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'week_number' => $weekNumber,
            'branch_code' => $branchCode,
            'employees' => $payrollData
        ];
    }

    /**
     * Calculate payroll for a single employee
     */
    public function calculateEmployeePayroll($employee, $weekStart, $weekEnd, $weekNumber) {
        $daysWorked = $this->getEmployeeAttendanceDays($employee['id'], $weekStart, $weekEnd);
        $dailyRate = floatval($employee['daily_rate']);
        $performanceAllowance = floatval($employee['performance_allowance']);
        $hasDeduction = intval($employee['has_deduction']);

        $basicPay = $daysWorked * $dailyRate;
        $performanceTotal = $daysWorked * $performanceAllowance;
        $grossPay = $basicPay + $performanceTotal;

        $deductions = $this->getFixedDeductions($weekNumber, $hasDeduction);
        $totalDeductions = $deductions['sss'] + $deductions['phic'] + $deductions['hdmf'];

        $netPay = $grossPay - $totalDeductions;

        return [
            'employee_id' => $employee['id'],
            'employee_code' => $employee['employee_code'],
            'first_name' => $employee['first_name'],
            'last_name' => $employee['last_name'],
            'full_name' => strtoupper($employee['last_name']) . ', ' . $employee['first_name'],
            'branch_code' => $employee['branch_code'],
            'department' => $employee['department'] ?? 'N/A',
            'payroll_week_start' => $weekStart,
            'payroll_week_end' => $weekEnd,
            'week_number' => $weekNumber,
            'days_worked' => $daysWorked,
            'daily_rate' => $dailyRate,
            'basic_pay' => $basicPay,
            'performance_allowance' => $performanceTotal,
            'gross_pay' => $grossPay,
            'sss_contribution' => $deductions['sss'],
            'phic_contribution' => $deductions['phic'],
            'hdmf_contribution' => $deductions['hdmf'],
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'has_deduction' => $hasDeduction
        ];
    }

    /**
     * Get number of days worked by employee in a week (Mon-Sat only)
     */
    public function getEmployeeAttendanceDays($employeeId, $weekStart, $weekEnd) {
        $query = "SELECT COUNT(DISTINCT date) as days_worked 
                  FROM attendance 
                  WHERE employee_id = :employee_id 
                  AND date BETWEEN :week_start AND :week_end 
                  AND check_in IS NOT NULL
                  AND DAYOFWEEK(date) BETWEEN 2 AND 7"; // Monday=2, Saturday=7

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->bindParam(':week_end', $weekEnd);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['days_worked']);
    }

    /**
     * Get all active employees for a branch
     */
    public function getBranchEmployees($branchCode) {
        $query = "SELECT e.*, b.branch_name as branch_name 
                  FROM employees e 
                  LEFT JOIN branches b ON e.branch_id = b.id 
                  WHERE b.branch_code = :branch_code 
                  AND e.status = 'active'
                  ORDER BY e.last_name, e.first_name";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get week number (1-4) from a date
     */
    public function getWeekNumber($date) {
        $day = intval(date('j', strtotime($date)));
        return ceil($day / 7);
    }

    /**
     * Get current week's Monday date
     */
    public function getCurrentWeekStart() {
        $today = date('Y-m-d');
        $dayOfWeek = date('N', strtotime($today)); // 1=Monday, 7=Sunday
        $daysToSubtract = $dayOfWeek - 1;
        return date('Y-m-d', strtotime($today . " -{$daysToSubtract} days"));
    }

    /**
     * Save payroll record
     */
    public function savePayroll($data) {
        // Check if record already exists
        $existing = $this->getExistingPayroll($data['employee_id'], $data['payroll_week_start']);

        if ($existing) {
            return $this->updatePayroll($existing['id'], $data);
        }

        $query = "INSERT INTO {$this->table} 
                  (employee_id, branch_code, payroll_week_start, payroll_week_end, week_number,
                   days_worked, daily_rate, basic_pay, overtime_hours, overtime_amount,
                   performance_allowance, gross_pay, sss_contribution, phic_contribution,
                   hdmf_contribution, cash_advance, total_deductions, net_pay, status)
                  VALUES 
                  (:employee_id, :branch_code, :payroll_week_start, :payroll_week_end, :week_number,
                   :days_worked, :daily_rate, :basic_pay, :overtime_hours, :overtime_amount,
                   :performance_allowance, :gross_pay, :sss_contribution, :phic_contribution,
                   :hdmf_contribution, :cash_advance, :total_deductions, :net_pay, :status)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        $stmt->bindParam(':payroll_week_start', $data['payroll_week_start']);
        $stmt->bindParam(':payroll_week_end', $data['payroll_week_end']);
        $stmt->bindParam(':week_number', $data['week_number'], PDO::PARAM_INT);
        $stmt->bindParam(':days_worked', $data['days_worked'], PDO::PARAM_INT);
        $stmt->bindParam(':daily_rate', $data['daily_rate']);
        $stmt->bindParam(':basic_pay', $data['basic_pay']);
        $stmt->bindParam(':overtime_hours', $data['overtime_hours'] ?? 0);
        $stmt->bindParam(':overtime_amount', $data['overtime_amount'] ?? 0);
        $stmt->bindParam(':performance_allowance', $data['performance_allowance']);
        $stmt->bindParam(':gross_pay', $data['gross_pay']);
        $stmt->bindParam(':sss_contribution', $data['sss_contribution']);
        $stmt->bindParam(':phic_contribution', $data['phic_contribution']);
        $stmt->bindParam(':hdmf_contribution', $data['hdmf_contribution']);
        $stmt->bindParam(':cash_advance', $data['cash_advance'] ?? 0);
        $stmt->bindParam(':total_deductions', $data['total_deductions']);
        $stmt->bindParam(':net_pay', $data['net_pay']);
        $stmt->bindValue(':status', 'draft');

        return $stmt->execute();
    }

    /**
     * Update existing payroll record
     */
    public function updatePayroll($payrollId, $data) {
        $query = "UPDATE {$this->table} SET 
                  days_worked = :days_worked,
                  daily_rate = :daily_rate,
                  basic_pay = :basic_pay,
                  performance_allowance = :performance_allowance,
                  gross_pay = :gross_pay,
                  sss_contribution = :sss_contribution,
                  phic_contribution = :phic_contribution,
                  hdmf_contribution = :hdmf_contribution,
                  total_deductions = :total_deductions,
                  net_pay = :net_pay,
                  updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $payrollId, PDO::PARAM_INT);
        $stmt->bindParam(':days_worked', $data['days_worked'], PDO::PARAM_INT);
        $stmt->bindParam(':daily_rate', $data['daily_rate']);
        $stmt->bindParam(':basic_pay', $data['basic_pay']);
        $stmt->bindParam(':performance_allowance', $data['performance_allowance']);
        $stmt->bindParam(':gross_pay', $data['gross_pay']);
        $stmt->bindParam(':sss_contribution', $data['sss_contribution']);
        $stmt->bindParam(':phic_contribution', $data['phic_contribution']);
        $stmt->bindParam(':hdmf_contribution', $data['hdmf_contribution']);
        $stmt->bindParam(':total_deductions', $data['total_deductions']);
        $stmt->bindParam(':net_pay', $data['net_pay']);

        return $stmt->execute();
    }

    /**
     * Get existing payroll for employee and week
     */
    public function getExistingPayroll($employeeId, $weekStart) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = :employee_id 
                  AND payroll_week_start = :week_start 
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get payroll data for a week and branch
     */
    public function getPayrollByWeek($weekStart, $branchCode) {
        $query = "SELECT p.*, e.employee_code, e.first_name, e.last_name,
                         e.has_deduction, b.branch_name as branch_name
                  FROM {$this->table} p
                  JOIN employees e ON p.employee_id = e.id
                  LEFT JOIN branches b ON p.branch_code = b.branch_code
                  WHERE p.payroll_week_start = :week_start 
                  AND p.branch_code = :branch_code
                  ORDER BY e.last_name, e.first_name";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get payroll by ID with employee details
     */
    public function getPayrollById($payrollId) {
        $query = "SELECT p.*, e.employee_code, e.first_name, e.last_name, 
                         e.has_deduction, b.branch_name as branch_name
                  FROM {$this->table} p
                  JOIN employees e ON p.employee_id = e.id
                  LEFT JOIN branches b ON p.branch_code = b.branch_code
                  WHERE p.id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $payrollId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save batch payroll for all employees in a branch
     */
    public function saveBatchPayroll($payrollData) {
        $saved = 0;
        $errors = [];

        foreach ($payrollData['employees'] as $employeePayroll) {
            try {
                $result = $this->savePayroll($employeePayroll);
                if ($result) {
                    $saved++;
                }
            } catch (Exception $e) {
                $errors[] = [
                    'employee' => $employeePayroll['full_name'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'saved' => $saved,
            'total' => count($payrollData['employees']),
            'errors' => $errors
        ];
    }
}
