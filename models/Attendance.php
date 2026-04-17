<?php

require_once __DIR__ . '/../core/Model.php';

class Attendance extends Model {
    protected $table = 'attendance';

    public function getAllWithEmployees() {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  ORDER BY a.date DESC, e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByEmployeeId($employeeId) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.employee_id = :employee_id 
                  ORDER BY a.date DESC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDateRange($startDate, $endDate, $employeeCode = null) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, e.position, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.date BETWEEN :start_date AND :end_date';
        
        if ($employeeCode) {
            $query .= ' AND e.employee_code = :employee_code';
        }
        
        $query .= ' ORDER BY a.date DESC, e.last_name ASC';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        
        if ($employeeCode) {
            $stmt->bindParam(':employee_code', $employeeCode);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDate($date) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.date = :date 
                  ORDER BY e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (employee_id, branch_code, date, check_in, check_out, status, notes) 
                  VALUES (:employee_id, :branch_code, :date, :check_in, :check_out, :status, :notes)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':check_in', $data['check_in']);
        $stmt->bindParam(':check_out', $data['check_out']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' SET 
                  employee_id = :employee_id, 
                  date = :date, 
                  check_in = :check_in, 
                  check_out = :check_out, 
                  status = :status, 
                  notes = :notes 
                  WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':check_in', $data['check_in']);
        $stmt->bindParam(':check_out', $data['check_out']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);
        return $stmt->execute();
    }

    public function checkDuplicate($employeeId, $date, $excludeId = null) {
        $query = 'SELECT COUNT(*) as count FROM ' . $this->table . ' 
                  WHERE employee_id = :employee_id AND date = :date';
        if ($excludeId) {
            $query .= ' AND id != :exclude_id';
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':date', $date);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function getTodayByEmployeeAndBranch($employeeId, $branchCode, $date) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE employee_id = :employee_id 
                  AND branch_code = :branch_code 
                  AND date = :date 
                  ORDER BY id DESC LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getLastTodayByEmployee($employeeId, $date) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE employee_id = :employee_id 
                  AND date = :date 
                  ORDER BY id DESC LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllTodayUnchecked() {
        $query = 'SELECT a.*, b.branch_name 
                  FROM ' . $this->table . ' a 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.date = CURDATE() 
                  AND (a.check_out IS NULL OR a.check_out = "") 
                  ORDER BY a.check_in DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateCheckOut($id, $checkOutTime) {
        $query = 'UPDATE ' . $this->table . ' SET check_out = :check_out WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':check_out', $checkOutTime);
        return $stmt->execute();
    }

    /**
     * Update employee's branch_name to match the branch where they checked in
     * @param int $employeeId Employee ID
     * @param string $branchCode Branch code
     */
    private function updateEmployeeBranch($employeeId, $branchCode) {
        // Get branch_name from branches table
        $query = 'SELECT branch_name FROM branches WHERE branch_code = :branch_code LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->execute();
        $branch = $stmt->fetch();

        if ($branch && !empty($branch['branch_name'])) {
            // Update employee's branch_name
            $updateQuery = 'UPDATE employees SET branch_name = :branch_name WHERE id = :employee_id';
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':branch_name', $branch['branch_name']);
            $updateStmt->bindParam(':employee_id', $employeeId);
            $updateStmt->execute();
        }
    }

    public function updateCheckIn($id, $checkInTime, $branchCode) {
        $query = 'UPDATE ' . $this->table . ' SET check_in = :check_in, check_out = NULL, branch_code = :branch_code, notes = :notes WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':check_in', $checkInTime);
        $stmt->bindParam(':branch_code', $branchCode);
        $notes = 'QR Scan at ' . $branchCode;
        $stmt->bindParam(':notes', $notes);
        return $stmt->execute();
    }

    /**
     * Unified attendance recording method used by both QR scanner and manual Time In/Out buttons
     *
     * @param int $employeeId Employee ID
     * @param string $branchCode Branch code where attendance is being recorded
     * @param string $date Date in Y-m-d format
     * @param string $source Source of the attendance record ('qr' or 'manual')
     * @param string $currentTime Current time in H:i:s format (optional, defaults to now)
     * @return array Result with success status, action taken, and any error messages
     */
    public function recordAttendance($employeeId, $branchCode, $date, $source = 'manual', $currentTime = null) {
        // Set Philippines timezone
        date_default_timezone_set('Asia/Manila');

        if (!$currentTime) {
            $currentTime = date('H:i:s');
        }

        // Get the last attendance record for this employee today
        $lastAttendance = $this->getLastTodayByEmployee($employeeId, $date);

        if ($lastAttendance) {
            // Employee has attendance today - check if they need to check out
            if (empty($lastAttendance['check_out'])) {
                // Check out
                $this->updateCheckOut($lastAttendance['id'], $currentTime);
                return [
                    'success' => true,
                    'action' => 'check_out',
                    'record_id' => $lastAttendance['id'],
                    'message' => 'Check-out recorded'
                ];
            }
            // Already checked out - create NEW record for new check-in session
        }

        // Create new check-in record (first time today OR new session after checkout)
        $notes = $source === 'qr' ? 'QR Scan at ' . $branchCode : 'Manual entry at ' . $branchCode;
        $data = [
            'employee_id' => $employeeId,
            'branch_code' => $branchCode,
            'date' => $date,
            'check_in' => $currentTime,
            'check_out' => null,
            'status' => 'present',
            'notes' => $notes
        ];

        $result = $this->create($data);

        if ($result) {
            // Get the ID of the newly created record
            $newRecordId = $this->db->lastInsertId();

            // Update employee's branch_name to reflect current branch
            $this->updateEmployeeBranch($employeeId, $branchCode);

            return [
                'success' => true,
                'action' => 'check_in',
                'record_id' => $newRecordId,
                'message' => 'Check-in recorded'
            ];
        }

        return [
            'success' => false,
            'action' => null,
            'error' => 'Failed to create attendance record'
        ];
    }

    public function getMonthlyReport($month, $year) {
        $query = 'SELECT 
                    e.id as employee_id,
                    e.employee_code,
                    e.first_name,
                    e.last_name,
                    e.department,
                    COUNT(CASE WHEN a.status = "present" THEN 1 END) as present_days,
                    COUNT(CASE WHEN a.status = "absent" THEN 1 END) as absent_days,
                    COUNT(CASE WHEN a.status = "late" THEN 1 END) as late_days,
                    COUNT(CASE WHEN a.status = "half_day" THEN 1 END) as half_days,
                    COUNT(CASE WHEN a.status = "leave" THEN 1 END) as leave_days
                  FROM employees e
                  LEFT JOIN ' . $this->table . ' a ON e.id = a.employee_id 
                    AND MONTH(a.date) = :month AND YEAR(a.date) = :year
                  GROUP BY e.id, e.employee_code, e.first_name, e.last_name, e.department
                  ORDER BY e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDailyStatsForMonth($month, $year) {
        $query = 'SELECT DAY(date) as day, COUNT(*) as count 
                  FROM ' . $this->table . ' 
                  WHERE MONTH(date) = :month AND YEAR(date) = :year 
                  GROUP BY DAY(date)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyStats($month, $year) {
        $query = 'SELECT 
                    COUNT(*) as total_records,
                    COUNT(CASE WHEN status = "present" AND check_out IS NULL THEN 1 END) as currently_present,
                    COUNT(CASE WHEN status = "present" AND check_out IS NOT NULL THEN 1 END) as completed_shifts,
                    COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_count
                  FROM ' . $this->table . ' 
                  WHERE MONTH(date) = :month AND YEAR(date) = :year';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getByBranchAndDateRange($branchName, $startDate, $endDate) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, e.position, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE b.branch_name = :branch_name 
                    AND a.date BETWEEN :start_date AND :end_date 
                  ORDER BY a.date ASC, e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_name', $branchName);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDateAndBranch($date, $branchCode) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, e.position, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.date = :date 
                    AND a.branch_code = :branch_code 
                  ORDER BY e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDailyStatsForMonthByBranch($month, $year, $branchName) {
        $query = 'SELECT DAY(a.date) as day, COUNT(*) as count 
                  FROM ' . $this->table . ' a 
                  JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE MONTH(a.date) = :month 
                    AND YEAR(a.date) = :year 
                    AND b.branch_name = :branch_name 
                  GROUP BY DAY(a.date)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':branch_name', $branchName);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
