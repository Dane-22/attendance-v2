<?php

require_once __DIR__ . '/../core/Model.php';

class Branch extends Model {
    protected $table = 'branches';

    public function findAll() {
        $query = 'SELECT id, branch_code, branch_name, address, contact_number, status FROM ' . $this->table . ' ORDER BY branch_name ASC';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByCode($code) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE branch_code = :code LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (branch_code, branch_name, address, contact_number, status) 
                  VALUES (:branch_code, :branch_name, :address, :contact_number, :status)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        $stmt->bindParam(':branch_name', $data['branch_name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':contact_number', $data['contact_number']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' SET 
                  branch_code = :branch_code, 
                  branch_name = :branch_name, 
                  address = :address, 
                  contact_number = :contact_number, 
                  status = :status 
                  WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        $stmt->bindParam(':branch_name', $data['branch_name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':contact_number', $data['contact_number']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function countAll() {
        $query = 'SELECT COUNT(*) FROM ' . $this->table;
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }

    public function getLastBranchCode() {
        $query = 'SELECT branch_code FROM ' . $this->table . ' ORDER BY branch_code DESC LIMIT 1';
        $stmt = $this->db->query($query);
        $result = $stmt->fetch();
        return $result ? $result['branch_code'] : null;
    }
}
