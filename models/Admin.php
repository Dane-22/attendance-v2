<?php

require_once __DIR__ . '/../core/Model.php';

class Admin extends Model {
    protected $table = 'admins';

    public function findByUsername($username) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (username, password, name, email, role, branch_code) 
                  VALUES (:username, :password, :name, :email, :role, :branch_code)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = 'UPDATE ' . $this->table . ' SET 
                  username = :username, 
                  password = :password, 
                  name = :name, 
                  email = :email, 
                  role = :role,
                  branch_code = :branch_code 
                  WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':branch_code', $data['branch_code']);
        return $stmt->execute();
    }

    public function updatePassword($id, $password) {
        $query = 'UPDATE ' . $this->table . ' SET password = :password WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $password);
        return $stmt->execute();
    }

    public function findByBranchCode($branchCode) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE branch_code = :branch_code AND role = "branch" LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':branch_code', $branchCode);
        $stmt->execute();
        return $stmt->fetch();
    }
}
