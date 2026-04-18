<?php

require_once __DIR__ . '/../core/Model.php';

class Employee extends Model {
    protected $table = 'employees';

    public function search($keyword) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE first_name LIKE :keyword 
                  OR last_name LIKE :keyword 
                  OR email LIKE :keyword 
                  OR employee_code LIKE :keyword 
                  ORDER BY last_name ASC';
        $stmt = $this->db->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $fields = ['employee_code', 'first_name', 'middle_name', 'last_name', 'email', 'department', 'position', 'status', 'daily_rate', 'has_deduction', 'performance_allowance'];
        $placeholders = [];
        $values = [];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $placeholders[] = ':' . $field;
                $values[$field] = $data[$field];
            }
        }

        if (isset($data['profile_image'])) {
            $placeholders[] = ':profile_image';
            $values['profile_image'] = $data['profile_image'];
        }
        
        $query = 'INSERT INTO ' . $this->table . ' 
                  (' . implode(', ', array_keys($values)) . ') 
                  VALUES (' . implode(', ', $placeholders) . ')';
        
        $stmt = $this->db->prepare($query);
        foreach ($values as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' SET
                  employee_code = :employee_code,
                  first_name = :first_name,
                  middle_name = :middle_name,
                  last_name = :last_name,
                  email = :email,
                  department = :department,
                  position = :position,
                  status = :status,
                  daily_rate = :daily_rate,
                  has_deduction = :has_deduction,
                  performance_allowance = :performance_allowance';

        if (isset($data['profile_image'])) {
            $query .= ', profile_image = :profile_image';
        }

        $query .= ' WHERE id = :id';

        $stmt = $this->db->prepare($query);

        $employee_code = $data['employee_code'];
        $first_name = $data['first_name'];
        $middle_name = $data['middle_name'] ?? null;
        $last_name = $data['last_name'];
        $email = $data['email'];
        $department = $data['department'] ?? null;
        $position = $data['position'];
        $status = $data['status'] ?? 'Active';
        $daily_rate = $data['daily_rate'] ?? 0;
        $has_deduction = $data['has_deduction'] ?? 1;
        $performance_allowance = $data['performance_allowance'] ?? 0;

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':employee_code', $employee_code);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':daily_rate', $daily_rate);
        $stmt->bindParam(':has_deduction', $has_deduction);
        $stmt->bindParam(':performance_allowance', $performance_allowance);

        if (isset($data['profile_image'])) {
            $profile_image = $data['profile_image'];
            $stmt->bindParam(':profile_image', $profile_image);
        }

        return $stmt->execute();
    }

    public function updateDeductionStatus($id, $has_deduction) {
        $query = 'UPDATE ' . $this->table . ' SET has_deduction = :has_deduction WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':has_deduction', $has_deduction, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getDepartments() {
        $query = 'SELECT DISTINCT department FROM ' . $this->table . ' WHERE department IS NOT NULL ORDER BY department';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findByEmail($email) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByEmployeeCode($code) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE employee_code = :code LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByEmployeeCodeCI($code) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE LOWER(employee_code) = LOWER(:code) LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllEmployeeCodes() {
        $query = 'SELECT id, employee_code, first_name, last_name FROM ' . $this->table . ' ORDER BY employee_code';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    public function countAll() {
        $query = 'SELECT COUNT(*) FROM ' . $this->table;
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }

    public function findByBranch($branchName) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE branch_name = :branch_name ORDER BY last_name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_name', $branchName);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateBranchName($id, $branchName) {
        $query = 'UPDATE ' . $this->table . ' SET branch_name = :branch_name WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':branch_name', $branchName);
        return $stmt->execute();
    }

    public function getNextEmployeeCode($position) {
        $currentYear = date('Y');
        $prefix = '';
        $padLength = 4;
        $pattern = '';
        
        switch($position) {
            case 'Worker':
                $prefix = 'W';
                $pattern = 'W%';
                $padLength = 4;
                break;
            case 'Admin':
                $prefix = 'ADMIN-' . $currentYear . '-';
                $pattern = 'ADMIN-' . $currentYear . '-%';
                $padLength = 3;
                break;
            case 'Engineer':
                $prefix = 'ENGINEER-' . $currentYear . '-';
                $pattern = 'ENGINEER-' . $currentYear . '-%';
                $padLength = 3;
                break;
            case 'Architect':
                $prefix = 'ARCHITECT-' . $currentYear . '-';
                $pattern = 'ARCHITECT-' . $currentYear . '-%';
                $padLength = 3;
                break;
            case 'Developer':
                $prefix = 'DEV-' . $currentYear . '-';
                $pattern = 'DEV-' . $currentYear . '-%';
                $padLength = 2;
                break;
            default:
                return null;
        }

        // Get all employee codes matching the pattern
        $query = 'SELECT employee_code FROM ' . $this->table . ' 
                  WHERE employee_code LIKE :pattern 
                  ORDER BY CAST(SUBSTRING(employee_code, LENGTH(:prefix2) + 1) AS UNSIGNED) DESC 
                  LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pattern', $pattern);
        $prefixParam = $prefix;
        $stmt->bindParam(':prefix2', $prefixParam);
        $stmt->execute();
        $lastCode = $stmt->fetchColumn();

        $nextNumber = 1;
        if ($lastCode) {
            // Extract the number from the last code
            if (preg_match('/(\d+)$/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }

        // Double check this code doesn't exist
        $newCode = $prefix . str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
        while ($this->findByEmployeeCode($newCode)) {
            $nextNumber++;
            $newCode = $prefix . str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
        }

        return $newCode;
    }
}
