<?php

require_once __DIR__ . '/../core/Controller.php';

class EmployeeController extends Controller {
    private $employeeModel;

    public function __construct() {
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $employees = $this->employeeModel->findAll();
        $totalEmployees = $this->employeeModel->countAll();
        
        $this->view('employee/employee_list', [
            'title' => 'Employees',
            'employees' => $employees,
            'totalEmployees' => $totalEmployees
        ]);
    }

    public function records() {
        $employees = $this->employeeModel->findAll();
        
        $this->view('employee/index', [
            'employees' => $employees,
            'title' => 'Employees'
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $position = $_POST['position'] ?: '';
            $employeeCode = $_POST['employee_code'] ?: '';
            
            // Verify code doesn't exist, keep generating until we find a unique one
            $attempts = 0;
            while ($this->employeeModel->findByEmployeeCode($employeeCode) && $attempts < 10) {
                $employeeCode = $this->employeeModel->getNextEmployeeCode($position);
                $attempts++;
            }
            
            $data = [
                'employee_code' => $employeeCode,
                'first_name' => $_POST['first_name'],
                'middle_name' => $_POST['middle_name'] ?: null,
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'position' => $position,
                'status' => $_POST['status'] ?: 'Active',
                'daily_rate' => $_POST['daily_rate'] ?: 0,
                'has_deduction' => isset($_POST['has_deduction']) ? 1 : 0
            ];

            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $uploadDir = __DIR__ . '/../uploads/employees/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $data['profile_image'] = 'uploads/employees/' . $fileName;
                }
            }

            if ($this->employeeModel->create($data)) {
                $_SESSION['success'] = 'Employee created successfully';
                $this->redirect('/employee');
            } else {
                $_SESSION['error'] = 'Failed to create employee';
                $this->redirect('/employee');
            }
        }
        
        $this->view('employee/create', [
            'title' => 'Add Employee'
        ]);
    }

    public function viewEmployee($id) {
        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            $_SESSION['error'] = 'Employee not found';
            $this->redirect('/employee');
        }
        
        $this->view('employee/view', [
            'employee' => $employee,
            'title' => 'Employee Details'
        ]);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'employee_code' => $_POST['employee_code'],
                'first_name' => $_POST['first_name'],
                'middle_name' => $_POST['middle_name'] ?: null,
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'position' => $_POST['position'] ?: null,
                'status' => $_POST['status'] ?: 'Active',
                'daily_rate' => $_POST['daily_rate'] ?: 0,
                'has_deduction' => isset($_POST['has_deduction']) ? 1 : 0
            ];

            // Handle profile image upload
            error_log('Edit employee POST - FILES: ' . print_r($_FILES, true));
            if (isset($_FILES['profile_image'])) {
                error_log('Profile image found - Error code: ' . $_FILES['profile_image']['error']);
                if ($_FILES['profile_image']['error'] === 0) {
                    $uploadDir = __DIR__ . '/../uploads/employees/';
                    error_log('Upload dir: ' . $uploadDir);
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    error_log('Attempting to move: ' . $_FILES['profile_image']['tmp_name'] . ' to ' . $uploadFile);

                    // Compress image before saving
                    $compressedFile = $this->compressImage($_FILES['profile_image']['tmp_name'], $uploadFile, 800, 500000);
                    if ($compressedFile) {
                        $data['profile_image'] = 'uploads/employees/' . $fileName;
                        error_log('Upload successful (compressed): ' . $data['profile_image']);
                    } else {
                        error_log('Upload failed - image compression failed');
                        $_SESSION['error'] = 'Employee updated but profile image upload failed. Please try again.';
                    }
                } else {
                    $uploadErrors = [
                        1 => 'File exceeds upload_max_filesize',
                        2 => 'File exceeds MAX_FILE_SIZE',
                        3 => 'File was partially uploaded',
                        4 => 'No file was uploaded',
                        6 => 'Missing temporary folder',
                        7 => 'Failed to write file to disk',
                        8 => 'File upload stopped by extension'
                    ];
                    $errorMsg = $uploadErrors[$_FILES['profile_image']['error']] ?? 'Unknown upload error';
                    error_log('Upload error: ' . $errorMsg);
                    $_SESSION['error'] = 'Employee updated but profile image upload failed: ' . $errorMsg;
                }
            } else {
                error_log('No profile_image in FILES');
            }

            if ($this->employeeModel->update($id, $data)) {
                if (!isset($_SESSION['error'])) {
                    $_SESSION['success'] = 'Employee updated successfully';
                }
                $this->redirect('/employee');
            } else {
                $_SESSION['error'] = 'Failed to update employee';
                $this->redirect('/employee/edit/' . $id);
            }
        }

        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            $_SESSION['error'] = 'Employee not found';
            $this->redirect('/employee');
        }
        
        $this->view('employee/edit', [
            'employee' => $employee,
            'title' => 'Edit Employee'
        ]);
    }

    public function delete($id) {
        if ($this->employeeModel->delete($id)) {
            $_SESSION['success'] = 'Employee deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete employee';
        }
        $this->redirect('/employee');
    }

    public function getNextCode() {
        $currentUser = $this->requireJWT();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $position = $_POST['position'] ?? '';
            if ($position) {
                $nextCode = $this->employeeModel->getNextEmployeeCode($position);
                echo json_encode(['code' => $nextCode]);
                return;
            }
        }
        echo json_encode(['code' => '']);
    }

    public function getEmployee($id) {
        $currentUser = $this->requireJWT();

        header('Content-Type: application/json');
        $employee = $this->employeeModel->findById($id);
        
        if (!$employee) {
            echo json_encode(['error' => 'Employee not found']);
            return;
        }
        
        echo json_encode($employee);
    }

    public function toggleDeduction($id) {
        $this->requireJWT();

        header('Content-Type: application/json');

        // Get current employee status
        $employee = $this->employeeModel->findById($id);
        if (!$employee) {
            echo json_encode(['success' => false, 'error' => 'Employee not found']);
            return;
        }

        // Toggle the deduction status
        $newStatus = empty($employee['has_deduction']) ? 1 : 0;
        error_log("Toggling deduction for employee $id: current=" . ($employee['has_deduction'] ?? 'null') . ", new=$newStatus");

        if (method_exists($this->employeeModel, 'updateDeductionStatus')) {
            $result = $this->employeeModel->updateDeductionStatus($id, $newStatus);
            error_log("updateDeductionStatus result: " . ($result ? 'true' : 'false'));
            if ($result) {
                $message = $newStatus ? 'Employee now has deductions' : 'Employee deductions removed';
                echo json_encode(['success' => true, 'message' => $message, 'has_deduction' => $newStatus]);
            } else {
                $errorInfo = $this->employeeModel->getLastError();
                error_log("Update failed: " . print_r($errorInfo, true));
                echo json_encode(['success' => false, 'error' => 'Failed to update deduction status']);
            }
        } else {
            error_log("Method updateDeductionStatus does not exist");
            echo json_encode(['success' => false, 'error' => 'Method not found']);
        }
    }

    /**
     * Compress and resize image to reduce file size
     * @param string $source Source image path
     * @param string $destination Destination path
     * @param int $maxWidth Maximum width in pixels
     * @param int $maxSize Maximum file size in bytes (default 500KB)
     * @return bool True on success
     */
    private function compressImage($source, $destination, $maxWidth = 800, $maxSize = 500000) {
        // Get image info
        $info = getimagesize($source);
        if (!$info) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $type = $info[2];

        // Calculate new dimensions
        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
            $newHeight = intval($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create image from source
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($source);
                break;
            default:
                // Unsupported type, just move the file
                return move_uploaded_file($source, $destination);
        }

        if (!$image) {
            return false;
        }

        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save with compression
        $quality = 85;
        $success = false;

        // Try to meet size requirement by adjusting quality
        do {
            ob_start();
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($resized, null, $quality);
                    break;
                case IMAGETYPE_PNG:
                    $pngQuality = intval(($quality / 100) * 9);
                    imagepng($resized, null, $pngQuality);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($resized);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($resized, null, $quality);
                    break;
            }
            $imageData = ob_get_clean();

            if (strlen($imageData) <= $maxSize || $quality <= 50) {
                $success = true;
                file_put_contents($destination, $imageData);
            } else {
                $quality -= 10;
            }
        } while (!$success && $quality > 50);

        // If still too large, just save what we have
        if (!$success) {
            file_put_contents($destination, $imageData);
        }

        imagedestroy($image);
        imagedestroy($resized);

        return true;
    }
}
