<?php

require_once __DIR__ . '/../core/Controller.php';

class BranchController extends Controller {
    private $branchModel;

    public function __construct() {
        $this->branchModel = $this->model('Branch');
    }

    public function index() {
        $branches = $this->branchModel->findAll();
        $totalBranches = $this->branchModel->countAll();
        
        $this->view('branch/index', [
            'title' => 'Branches',
            'branches' => $branches,
            'totalBranches' => $totalBranches
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'branch_code' => $_POST['branch_code'],
                'branch_name' => $_POST['branch_name'],
                'address' => $_POST['address'] ?: null,
                'contact_number' => $_POST['contact_number'] ?: null,
                'status' => $_POST['status'] ?: 'Active'
            ];

            // Create branch first
            if ($this->branchModel->create($data)) {
                // Create admin account for branch device login
                $adminModel = $this->model('Admin');
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $adminData = [
                    'username' => 'branch-' . strtolower($_POST['branch_code']),
                    'password' => $password,
                    'name' => 'Branch Device - ' . $_POST['branch_name'],
                    'email' => 'branch-' . strtolower($_POST['branch_code']) . '@jajr.local',
                    'role' => 'branch',
                    'branch_code' => $_POST['branch_code']
                ];
                $adminModel->create($adminData);

                $_SESSION['success'] = 'Branch created successfully';
                $this->redirect('/branches');
            } else {
                $_SESSION['error'] = 'Failed to create branch';
                $this->redirect('/branches');
            }
        }

        // Get last branch code and calculate next letter
        $lastCode = $this->branchModel->getLastBranchCode();
        $nextCode = 'A'; // Default if no branches exist

        if ($lastCode) {
            // Get the last character and increment it
            $lastChar = strtoupper(substr($lastCode, -1));
            if (ctype_alpha($lastChar) && $lastChar >= 'A' && $lastChar < 'Z') {
                $nextCode = chr(ord($lastChar) + 1);
            } elseif ($lastChar === 'Z') {
                $nextCode = 'AA'; // If we reach Z, start with AA
            }
        }

        $this->view('branch/create', [
            'title' => 'Add Branch',
            'nextBranchCode' => $nextCode
        ]);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'branch_code' => $_POST['branch_code'],
                'branch_name' => $_POST['branch_name'],
                'address' => $_POST['address'] ?: null,
                'contact_number' => $_POST['contact_number'] ?: null,
                'status' => $_POST['status'] ?: 'Active'
            ];

            if ($this->branchModel->update($id, $data)) {
                // Update admin password if provided
                if (!empty($_POST['password'])) {
                    $adminModel = $this->model('Admin');
                    $admin = $adminModel->findByBranchCode($_POST['branch_code']);
                    if ($admin) {
                        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        $adminModel->updatePassword($admin['id'], $hashedPassword);
                    }
                }

                $_SESSION['success'] = 'Branch updated successfully';
                $this->redirect('/branches');
            } else {
                $_SESSION['error'] = 'Failed to update branch';
                $this->redirect('/branches/edit/' . $id);
            }
        }

        $branch = $this->branchModel->findById($id);

        if (!$branch) {
            $_SESSION['error'] = 'Branch not found';
            $this->redirect('/branches');
        }

        $this->view('branch/edit', [
            'branch' => $branch,
            'title' => 'Edit Branch'
        ]);
    }

    public function delete($id) {
        if ($this->branchModel->delete($id)) {
            $_SESSION['success'] = 'Branch deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete branch';
        }
        $this->redirect('/branches');
    }
}
