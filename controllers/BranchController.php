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

            if ($this->branchModel->create($data)) {
                $_SESSION['success'] = 'Branch created successfully';
                $this->redirect('/branches');
            } else {
                $_SESSION['error'] = 'Failed to create branch';
                $this->redirect('/branches');
            }
        }
        
        $this->view('branch/create', [
            'title' => 'Add Branch'
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
