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

    public function getByDateRange($startDate, $endDate) {
        $query = 'SELECT a.*, e.first_name, e.last_name, e.employee_code, e.department, b.branch_name 
                  FROM ' . $this->table . ' a 
                  JOIN employees e ON a.employee_id = e.id 
                  LEFT JOIN branches b ON a.branch_code = b.branch_code 
                  WHERE a.date BETWEEN :start_date AND :end_date 
                  ORDER BY a.date DESC, e.last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
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

    public function updateCheckOut($id, $checkOutTime) {
        $query = 'UPDATE ' . $this->table . ' SET check_out = :check_out WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':check_out', $checkOutTime);
        return $stmt->execute();
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
}
