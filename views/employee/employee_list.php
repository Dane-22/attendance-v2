<?php
// Note: $employees and $totalEmployees are passed from EmployeeController

ob_start();
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);

// Helper function to format employee name
function formatEmployeeName($emp) {
    $name = strtoupper($emp['last_name']) . ', ' . strtoupper($emp['first_name']);
    if (!empty($emp['middle_name'])) {
        $name .= ' ' . strtoupper(substr($emp['middle_name'], 0, 1)) . '.';
    }
    return $name;
}

// Helper function to get avatar initials
function getAvatarInitials($emp) {
    if (!empty($emp['profile_image'])) {
        return null; // Has profile image
    }
    $initials = '';
    if (!empty($emp['first_name'])) {
        $initials .= strtoupper(substr($emp['first_name'], 0, 1));
    }
    if (!empty($emp['last_name'])) {
        $initials .= strtoupper(substr($emp['last_name'], 0, 1));
    }
    return $initials ?: '??';
}
?>

<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .btn-add {
        background: var(--accent-color);
        color: #000000;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .btn-add:hover {
        opacity: 0.9;
    }

    .search-section {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .search-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        width: 100%;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-box input {
        background: transparent;
        border: none;
        color: var(--text-primary);
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }

    .search-box i {
        color: var(--text-secondary);
    }

    .pagination-bar {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .pagination-info {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .pagination-info strong {
        color: var(--accent-color);
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .show-label {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .show-dropdown {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 6px 12px;
        color: var(--text-primary);
        font-size: 0.875rem;
        cursor: pointer;
    }

    .page-btns {
        display: flex;
        gap: 4px;
    }

    .page-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .page-btn.active {
        background: var(--accent-color);
        color: #000000;
        border-color: var(--accent-color);
        font-weight: 600;
    }

    .page-btn.nav {
        width: auto;
        padding: 0 10px;
    }

    .section-title {
        color: var(--accent-color);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .employee-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .employee-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        display: grid;
        grid-template-columns: auto 1fr auto auto auto auto;
        align-items: center;
        gap: 20px;
    }

    .employee-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-color), #FFA500);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000000;
        font-weight: 600;
        font-size: 0.9rem;
        overflow: hidden;
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    .avatar-initials {
        color: #000000;
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1;
    }

    /* When image fails to load, show initials as fallback */
    .employee-avatar.show-initials::before {
        content: attr(data-initials);
        color: #000000;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .employee-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .employee-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .employee-email {
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .employee-code {
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-family: monospace;
    }

    .employee-position {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .employee-status {
        color: #22c55e;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .deduction-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .deduction-badge.with {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }

    .deduction-badge.without {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .employee-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        background: var(--bg-primary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--accent-color);
        color: #000000;
    }

    @media (max-width: 1024px) {
        .employee-card {
            grid-template-columns: auto 1fr auto;
            gap: 12px;
        }
        .employee-code, .employee-position {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .page-header-row {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }
        .pagination-bar {
            flex-direction: column;
            gap: 12px;
        }
        .employee-card {
            grid-template-columns: auto 1fr;
        }
        .employee-status, .deduction-badge {
            display: none;
        }
    }
</style>

<div class="page-header">
    <div class="page-header-row">
        <div>
            <div class="page-subtitle">Manage employee records</div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 8px;">
                Total Employees: <strong style="color: var(--accent-color);"><?= $totalEmployees ?></strong>
            </div>
        </div>
        <button class="btn-add" onclick="openAddEmployeeModal()">
            <i class="fas fa-user-plus"></i> Add Employee
        </button>
    </div>
</div>

<!-- Search Section -->
<div class="search-section">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search employees by name, code,">
    </div>
</div>

<!-- Pagination Bar -->
<div class="pagination-bar">
    <div class="pagination-info">
        Showing <strong>1</strong> to <strong>10</strong> of <strong><?= $totalEmployees ?></strong> employees
    </div>
    <div class="pagination-controls">
        <span class="show-label">Show:</span>
        <select class="show-dropdown">
            <option>10</option>
            <option>25</option>
            <option>50</option>
        </select>
        <div class="page-btns">
            <button class="page-btn nav"><i class="fas fa-chevron-left"></i></button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">...</button>
            <button class="page-btn">8</button>
            <button class="page-btn nav"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- Employee List -->
<div class="section-title">Existing Employees</div>

<div class="employee-list">
    <?php foreach ($employees as $employee): ?>
    <div class="employee-card">
        <div class="employee-avatar" data-initials="<?= getAvatarInitials($employee) ?? '' ?>">
            <?php $initials = getAvatarInitials($employee); ?>
            <?php if ($initials): ?>
                <span class="avatar-initials"><?= $initials ?></span>
            <?php else: ?>
                <?php
                $imgPath = $employee['profile_image'];
                // If path doesn't start with 'uploads/', prepend the uploads directory
                if (!str_starts_with($imgPath, 'uploads/')) {
                    $imgPath = 'uploads/profile_images/' . $imgPath;
                }
                ?>
                <img src="<?= $baseUrl ?>/<?= htmlspecialchars($imgPath) ?>" alt="" onerror="this.style.display='none'; this.parentElement.classList.add('show-initials');" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php endif; ?>
        </div>
        <div class="employee-info">
            <div class="employee-name"><?= htmlspecialchars(formatEmployeeName($employee)) ?></div>
            <div class="employee-email"><?= htmlspecialchars($employee['email']) ?></div>
        </div>
        <div class="employee-code"><?= htmlspecialchars($employee['employee_code']) ?></div>
        <div class="employee-position"><?= htmlspecialchars($employee['position']) ?></div>
        <div class="employee-status"><?= htmlspecialchars($employee['status']) ?></div>
        <div class="deduction-badge <?= !empty($employee['has_deduction']) ? 'with' : 'without' ?>" onclick="toggleDeduction(<?= $employee['id'] ?>, <?= !empty($employee['has_deduction']) ? 1 : 0 ?>)" style="cursor: pointer;" title="Click to toggle deduction status">
            <i class="fas fa-<?= !empty($employee['has_deduction']) ? 'check' : 'times' ?>-circle"></i>
            <?= !empty($employee['has_deduction']) ? 'With Deductions' : 'No Deductions' ?>
        </div>
        <div class="employee-actions">
            <button class="action-btn" title="View Details" onclick="viewEmployee(<?= $employee['id'] ?>)"><i class="fas fa-th-large"></i></button>
            <button class="action-btn" title="Edit" onclick="editEmployee(<?= $employee['id'] ?>)"><i class="fas fa-user-edit"></i></button>
            <button class="action-btn" title="QR Code" onclick="showQRCode(<?= $employee['id'] ?>, '<?= htmlspecialchars(formatEmployeeName($employee)) ?>', '<?= htmlspecialchars($employee['employee_code']) ?>')"><i class="fas fa-qrcode"></i></button>
            <button class="action-btn" title="Delete" onclick="deleteEmployee(<?= $employee['id'] ?>)"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Employee</h2>
            <button class="modal-close" onclick="closeAddEmployeeModal()">&times;</button>
        </div>
        <?php $baseUrl = dirname($_SERVER['SCRIPT_NAME']); ?>
        <form id="addEmployeeForm" method="POST" action="<?= $baseUrl ?>/employee/create" enctype="multipart/form-data">
            
            <!-- Profile Information -->
            <div class="form-section">
                <h3 class="form-section-title">Profile Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Employee Code <span class="required">*</span></label>
                        <input type="text" id="employee_code" name="employee_code" readonly class="readonly-field">
                    </div>
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name">
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="form-section-title">Contact Information</h3>
                <div class="form-group full-width">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" required>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="form-section">
                <h3 class="form-section-title">Employment Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Position <span class="required">*</span></label>
                        <select id="position" name="position" required onchange="generateEmployeeCode()">
                            <option value="">Select Position</option>
                            <option value="Worker">Worker</option>
                            <option value="Admin">Admin</option>
                            <option value="Engineer">Engineer</option>
                            <option value="Architect">Architect</option>
                            <option value="Developer">Developer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Daily Rate (₱)</label>
                        <input type="number" name="daily_rate" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Performance Allowance (₱)</label>
                        <input type="number" name="performance_allowance" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group full-width">
                        <label>Government Deductions</label>
                        <div class="toggle-container">
                            <label class="toggle-switch">
                                <input type="checkbox" name="has_deduction" checked>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">With SSS/PhilHealth/PagIBIG</span>
                        </div>
                    </div>
                    <div class="form-group full-width info-text">
                        <i class="fas fa-info-circle"></i>
                        Use "Reset Password" button below to set password to default (jajrconstruction)
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="form-section">
                <h3 class="form-section-title">Profile Image</h3>
                <div class="profile-image-section">
                    <div class="profile-preview" id="profilePreview">
                        <img src="<?= $baseUrl ?>/assets/images/default-avatar.svg" alt="Default Avatar" id="previewImg">
                    </div>
                    <div class="profile-upload">
                        <label for="profile_image" class="upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i> Choose New Profile Image
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" hidden onchange="previewImage(event)">
                        <p class="upload-hint">Max file size: 10MB • Auto-compressed to ~500KB • Formats: JPG, PNG, GIF, WebP</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAddEmployeeModal()">Cancel</button>
                <button type="button" class="btn-reset" onclick="resetPassword()"><i class="fas fa-key"></i> Reset Password</button>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Employee</h2>
            <button class="modal-close" onclick="closeEditEmployeeModal()">&times;</button>
        </div>
        <form id="editEmployeeForm" method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" id="edit_employee_id" name="employee_id">
            
            <!-- Profile Information -->
            <div class="form-section">
                <h3 class="form-section-title">Profile Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Employee Code</label>
                        <input type="text" id="edit_employee_code" name="employee_code" readonly class="readonly-field">
                    </div>
                    <div class="form-group">
                        <label>First Name <span class="required">*</span></label>
                        <input type="text" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" id="edit_middle_name" name="middle_name">
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required">*</span></label>
                        <input type="text" id="edit_last_name" name="last_name" required>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="form-section-title">Contact Information</h3>
                <div class="form-group full-width">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="form-section">
                <h3 class="form-section-title">Employment Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Position <span class="required">*</span></label>
                        <select id="edit_position" name="position" required>
                            <option value="">Select Position</option>
                            <option value="Worker">Worker</option>
                            <option value="Admin">Admin</option>
                            <option value="Engineer">Engineer</option>
                            <option value="Architect">Architect</option>
                            <option value="Developer">Developer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit_status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Daily Rate (₱)</label>
                        <input type="number" id="edit_daily_rate" name="daily_rate" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Performance Allowance (₱)</label>
                        <input type="number" id="edit_performance_allowance" name="performance_allowance" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group full-width">
                        <label>Government Deductions</label>
                        <div class="toggle-container">
                            <label class="toggle-switch">
                                <input type="checkbox" id="edit_has_deduction" name="has_deduction">
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">With SSS/PhilHealth/PagIBIG</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="form-section">
                <h3 class="form-section-title">Profile Image</h3>
                <div class="profile-image-section">
                    <div class="profile-preview" id="editProfilePreview">
                        <img src="<?= $baseUrl ?>/assets/images/default-avatar.svg" alt="Avatar" id="editPreviewImg">
                    </div>
                    <div class="profile-upload">
                        <label for="edit_profile_image" class="upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i> Choose New Profile Image
                        </label>
                        <input type="file" id="edit_profile_image" name="profile_image" accept="image/*" hidden onchange="previewEditImage(event)">
                        <p class="upload-hint">Max file size: 10MB • Auto-compressed to ~500KB • Formats: JPG, PNG, GIF, WebP</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditEmployeeModal()">Cancel</button>
                <button type="button" class="btn-reset" onclick="resetEditPassword()"><i class="fas fa-key"></i> Reset Password</button>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        overflow-y: auto;
    }

    .modal.show {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 40px 20px;
    }

    .modal-content {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--text-primary);
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    #addEmployeeForm,
    #editEmployeeForm {
        padding: 24px 32px;
    }

    .form-section {
        margin-bottom: 32px;
    }

    .form-section-title {
        color: var(--accent-color);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        color: var(--text-primary);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .required {
        color: #ef4444;
    }

    .form-group input,
    .form-group select {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: var(--accent-color);
    }

    .form-group input.readonly-field {
        background: rgba(255, 215, 0, 0.1);
        border-color: var(--accent-color);
        color: var(--accent-color);
        font-weight: 600;
    }

    /* Enhanced Form Styling */
    .form-group input::placeholder,
    .form-group select option:first-child {
        color: var(--text-secondary);
        opacity: 0.7;
    }

    .form-group input:hover,
    .form-group select:hover {
        border-color: var(--accent-color);
        opacity: 0.8;
    }

    /* Form Section Hover Effect */
    .form-section:hover .form-section-title {
        color: var(--accent-color);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    /* Dark Theme Enhancements */
    [data-theme="dark"] .modal-content {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    [data-theme="dark"] .form-group input,
    [data-theme="dark"] .form-group select {
        background: #0d0d0d;
        border-color: #333;
    }

    [data-theme="dark"] .form-group input:focus,
    [data-theme="dark"] .form-group select:focus {
        background: #141414;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }

    [data-theme="dark"] .profile-image-section {
        background: #0d0d0d;
    }

    [data-theme="dark"] .toggle-container {
        background: #0d0d0d;
    }

    [data-theme="dark"] .info-text {
        background: #0d0d0d;
        border: 1px solid var(--border-color);
    }

    /* Toggle Switch */
    .toggle-container {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px 16px;
    }

    .toggle-switch {
        position: relative;
        width: 48px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--bg-secondary);
        transition: .3s;
        border-radius: 24px;
        border: 2px solid var(--border-color);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: var(--text-secondary);
        transition: .3s;
        border-radius: 50%;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: rgba(34, 197, 94, 0.2);
        border-color: #22c55e;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
        background-color: #22c55e;
    }

    .toggle-label {
        color: #22c55e;
        font-size: 0.875rem;
    }

    .info-text {
        color: var(--text-secondary);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-primary);
        padding: 12px;
        border-radius: 8px;
    }

    /* Profile Image Section */
    .profile-image-section {
        display: flex;
        align-items: center;
        gap: 24px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
    }

    .profile-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--accent-color);
        flex-shrink: 0;
    }

    .profile-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-upload {
        flex: 1;
    }

    .upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 215, 0, 0.1);
        border: 2px dashed var(--accent-color);
        color: var(--accent-color);
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .upload-btn:hover {
        background: var(--accent-color);
        color: #000000;
    }

    .upload-hint {
        color: var(--text-secondary);
        font-size: 0.75rem;
        margin-top: 12px;
        margin-bottom: 0;
    }

    /* Modal Actions */
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }

    .btn-cancel {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
    }

    .btn-reset {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid var(--accent-color);
        color: var(--accent-color);
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-reset:hover {
        background: var(--accent-color);
        color: #000000;
    }

    .btn-save {
        background: var(--accent-color);
        border: none;
        color: #000000;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .btn-save:hover {
        opacity: 0.9;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    /* Dark Theme Button Enhancements */
    [data-theme="dark"] .btn-cancel {
        background: #0d0d0d;
        border-color: #333;
    }

    [data-theme="dark"] .btn-cancel:hover {
        background: #1a1a1a;
        border-color: var(--accent-color);
    }

    [data-theme="dark"] .upload-btn {
        background: rgba(255, 215, 0, 0.05);
    }

    [data-theme="dark"] .upload-btn:hover {
        background: var(--accent-color);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .modal-content {
            max-width: 100%;
        }
        .profile-image-section {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    async function generateEmployeeCode() {
        const position = document.getElementById('position').value;
        const codeField = document.getElementById('employee_code');
        
        if (!position) {
            codeField.value = '';
            return;
        }

        // Fetch next code from server
        try {
            const basePath = '<?= $baseUrl ?>';
        const response = await fetch(`${basePath}/employee/next-code`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'position=' + encodeURIComponent(position)
            });
            const data = await response.json();
            console.log('Server response:', data);
            if (data.code) {
                codeField.value = data.code;
            } else {
                // Fallback: generate locally if server fails
                generateLocalCode(position);
            }
        } catch (error) {
            console.error('Error fetching employee code:', error);
            generateLocalCode(position);
        }
    }

    function generateLocalCode(position) {
        const codeField = document.getElementById('employee_code');
        const currentYear = new Date().getFullYear();
        let prefix = '';
        let padLength = 4;
        
        switch(position) {
            case 'Worker':
                prefix = 'W'; padLength = 4; break;
            case 'Admin':
                prefix = 'ADMIN-' + currentYear + '-'; padLength = 3; break;
            case 'Engineer':
                prefix = 'ENGINEER-' + currentYear + '-'; padLength = 3; break;
            case 'Architect':
                prefix = 'ARCHITECT-' + currentYear + '-'; padLength = 3; break;
            case 'Developer':
                prefix = 'DEV-' + currentYear + '-'; padLength = 2; break;
        }
        
        // Start from a high number to avoid conflicts
        const nextNumber = Math.floor(Math.random() * 9000) + 1000;
        codeField.value = prefix + String(nextNumber).padStart(padLength, '0');
    }

    // Ensure code is generated before form submission
    document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
        const codeField = document.getElementById('employee_code');
        if (!codeField.value) {
            e.preventDefault();
            alert('Please select a position to generate employee code');
            return false;
        }
    });

    function openAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeAddEmployeeModal() {
        document.getElementById('addEmployeeModal').classList.remove('show');
        document.body.style.overflow = '';
        document.getElementById('addEmployeeForm').reset();
        document.getElementById('employee_code').value = '';
        document.getElementById('previewImg').src = '<?= $baseUrl ?>/assets/images/default-avatar.svg';
    }

    // Store compressed image for add form
    let compressedAddImageBlob = null;

    async function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Check if file is already small enough
        if (file.size <= 500 * 1024) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
            compressedAddImageBlob = null;
            return;
        }

        // Show compression message
        const uploadHint = document.querySelector('#addEmployeeModal .upload-hint');
        const originalText = uploadHint.textContent;
        uploadHint.textContent = 'Compressing image... Please wait.';
        uploadHint.style.color = 'var(--accent-color)';

        try {
            const result = await compressImageClientSide(file);
            compressedAddImageBlob = result.blob;

            // Update preview
            document.getElementById('previewImg').src = result.dataUrl;

            // Update hint with compression info
            const originalMB = (result.originalSize / 1024 / 1024).toFixed(2);
            const compressedKB = Math.round(result.compressedSize / 1024);
            uploadHint.textContent = `Compressed from ${originalMB}MB to ${compressedKB}KB ✓`;
            uploadHint.style.color = '#4caf50';

            console.log(`Add image compressed: ${originalMB}MB → ${compressedKB}KB`);
        } catch (error) {
            console.error('Compression failed:', error);
            uploadHint.textContent = 'Compression failed. Using original file.';
            uploadHint.style.color = '#f44336';

            // Fall back to original
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
            compressedAddImageBlob = null;
        }

        // Reset hint after 3 seconds
        setTimeout(() => {
            uploadHint.textContent = originalText;
            uploadHint.style.color = '';
        }, 3000);
    }

    // Intercept add form submission to use compressed image
    document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
        if (compressedAddImageBlob) {
            e.preventDefault();

            // Create new FormData with compressed image
            const formData = new FormData(this);
            formData.delete('profile_image');
            formData.append('profile_image', compressedAddImageBlob, 'compressed_profile.jpg');

            // Submit via fetch
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok || response.redirected) {
                    window.location.reload();
                } else {
                    alert('Failed to create employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating employee');
            });

            compressedAddImageBlob = null;
        }
    });

    function resetPassword() {
        alert('Password will be reset to default: jajrconstruction');
    }

    function viewEmployee(id) {
        const basePath = '<?= $baseUrl ?>';
        window.location.href = basePath + '/employee/view/' + id;
    }

    async function editEmployee(id) {
        try {
            const basePath = '<?= $baseUrl ?>';
            const response = await fetch(`${basePath}/employee/get/${id}`);
            const employee = await response.json();
            
            if (employee.error) {
                alert('Employee not found');
                return;
            }
            
            // Populate form fields
            document.getElementById('edit_employee_id').value = employee.id;
            document.getElementById('edit_employee_code').value = employee.employee_code;
            document.getElementById('edit_first_name').value = employee.first_name;
            document.getElementById('edit_middle_name').value = employee.middle_name || '';
            document.getElementById('edit_last_name').value = employee.last_name;
            document.getElementById('edit_email').value = employee.email;
            document.getElementById('edit_position').value = employee.position;
            document.getElementById('edit_status').value = employee.status || 'Active';
            document.getElementById('edit_daily_rate').value = employee.daily_rate || '';
            document.getElementById('edit_performance_allowance').value = employee.performance_allowance || '';
            document.getElementById('edit_has_deduction').checked = employee.has_deduction == 1;
            
            // Set profile image
            const previewImg = document.getElementById('editPreviewImg');
            if (employee.profile_image) {
                previewImg.src = basePath + '/' + employee.profile_image;
            } else {
                previewImg.src = basePath + '/assets/images/default-avatar.svg';
            }
            
            // Set form action
            document.getElementById('editEmployeeForm').action = basePath + '/employee/edit/' + id;
            
            // Show modal
            document.getElementById('editEmployeeModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Error fetching employee:', error);
            alert('Error loading employee data');
        }
    }

    function closeEditEmployeeModal() {
        document.getElementById('editEmployeeModal').classList.remove('show');
        document.body.style.overflow = '';
        document.getElementById('editEmployeeForm').reset();
        document.getElementById('editPreviewImg').src = '<?= $baseUrl ?>/assets/images/default-avatar.svg';
    }

    // Compress image client-side before upload
    function compressImageClientSide(file, maxWidth = 1200, maxSizeKB = 500, quality = 0.85) {
        return new Promise((resolve, reject) => {
            if (!file.type.startsWith('image/')) {
                reject(new Error('File is not an image'));
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;

                    // Calculate new dimensions
                    if (width > maxWidth) {
                        height = Math.round(height * (maxWidth / width));
                        width = maxWidth;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Try to compress to target size
                    let compressedQuality = quality;
                    let blob;

                    const tryCompression = () => {
                        canvas.toBlob((result) => {
                            blob = result;
                            if (blob.size > maxSizeKB * 1024 && compressedQuality > 0.5) {
                                compressedQuality -= 0.1;
                                tryCompression();
                            } else {
                                resolve({
                                    blob: blob,
                                    dataUrl: canvas.toDataURL('image/jpeg', compressedQuality),
                                    originalSize: file.size,
                                    compressedSize: blob.size
                                });
                            }
                        }, 'image/jpeg', compressedQuality);
                    };

                    tryCompression();
                };
                img.onerror = () => reject(new Error('Failed to load image'));
                img.src = e.target.result;
            };
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsDataURL(file);
        });
    }

    // Store compressed image for form submission
    let compressedImageBlob = null;

    async function previewEditImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Check if file is already small enough
        if (file.size <= 500 * 1024) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editPreviewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
            compressedImageBlob = null; // Use original file
            return;
        }

        // Show compression message
        const uploadHint = document.querySelector('#editEmployeeModal .upload-hint');
        const originalText = uploadHint.textContent;
        uploadHint.textContent = 'Compressing image... Please wait.';
        uploadHint.style.color = 'var(--accent-color)';

        try {
            const result = await compressImageClientSide(file);
            compressedImageBlob = result.blob;

            // Update preview
            document.getElementById('editPreviewImg').src = result.dataUrl;

            // Update hint with compression info
            const originalMB = (result.originalSize / 1024 / 1024).toFixed(2);
            const compressedKB = Math.round(result.compressedSize / 1024);
            uploadHint.textContent = `Compressed from ${originalMB}MB to ${compressedKB}KB ✓`;
            uploadHint.style.color = '#4caf50';

            console.log(`Image compressed: ${originalMB}MB → ${compressedKB}KB`);
        } catch (error) {
            console.error('Compression failed:', error);
            uploadHint.textContent = 'Compression failed. Using original file.';
            uploadHint.style.color = '#f44336';

            // Fall back to original
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editPreviewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
            compressedImageBlob = null;
        }

        // Reset hint after 3 seconds
        setTimeout(() => {
            uploadHint.textContent = originalText;
            uploadHint.style.color = '';
        }, 3000);
    }

    // Intercept form submission to use compressed image
    document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
        if (compressedImageBlob) {
            e.preventDefault();

            // Create new FormData with compressed image
            const formData = new FormData(this);
            formData.delete('profile_image');
            formData.append('profile_image', compressedImageBlob, 'compressed_profile.jpg');

            // Submit via fetch
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok || response.redirected) {
                    window.location.reload();
                } else {
                    alert('Failed to update employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating employee');
            });

            compressedImageBlob = null;
        }
    });

    function resetEditPassword() {
        alert('Password will be reset to default: jajrconstruction');
    }

    function deleteEmployee(id) {
        if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
            const basePath = '<?= $baseUrl ?>';
            fetch(`${basePath}/employee/delete/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('Employee deleted successfully');
                    window.location.reload();
                } else {
                    alert('Failed to delete employee');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting employee');
            });
        }
    }

    function showQRCode(id, name, code) {
        const modal = document.createElement('div');
        modal.className = 'qr-modal';
        modal.innerHTML = `
            <div class="qr-modal-content">
                <div class="qr-header">
                    <h3>Employee QR Code</h3>
                    <button class="qr-close" onclick="this.closest('.qr-modal').remove()">&times;</button>
                </div>
                <div class="qr-body">
                    <div id="qrcode"></div>
                    <div class="qr-info">
                        <p><strong>${name}</strong></p>
                        <p>${code}</p>
                    </div>
                </div>
                <div class="qr-actions">
                    <button class="btn-cancel" onclick="this.closest('.qr-modal').remove()">Close</button>
                    <button class="btn-save" onclick="downloadQRCode()">Download</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Generate QR code using a simple library or API
        const qrData = `JAJR-EMP:${id}|${code}|${name}`;
        const qrDiv = modal.querySelector('#qrcode');
        qrDiv.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrData)}" alt="QR Code">`;
    }

    function downloadQRCode() {
        const qrImg = document.querySelector('#qrcode img');
        if (qrImg) {
            const link = document.createElement('a');
            link.href = qrImg.src;
            link.download = 'employee-qr.png';
            link.click();
        }
    }

    async function toggleDeduction(employeeId, currentStatus) {
        const basePath = '<?= $baseUrl ?>';
        const newStatus = currentStatus ? 0 : 1;
        const newStatusText = newStatus ? 'With Deductions' : 'No Deductions';

        try {
            const response = await fetch(`${basePath}/employee/toggle-deduction/${employeeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Update the UI
                const badge = document.querySelector(`.employee-card:has([onclick*="toggleDeduction(${employeeId},"]) .deduction-badge`) ||
                              document.querySelector(`.deduction-badge[onclick*="toggleDeduction(${employeeId},"]`);

                if (badge) {
                    badge.className = `deduction-badge ${newStatus ? 'with' : 'without'}`;
                    badge.innerHTML = `<i class="fas fa-${newStatus ? 'check' : 'times'}-circle"></i> ${newStatusText}`;
                    badge.setAttribute('onclick', `toggleDeduction(${employeeId}, ${newStatus})`);
                }

                alert(data.message || 'Deduction status updated successfully');
            } else {
                alert('Error: ' + (data.error || 'Failed to update deduction status'));
            }
        } catch (error) {
            console.error('Error toggling deduction:', error);
            alert('Error: Failed to update deduction status. Please try again.');
        }
    }

    // QR Modal Styles
    const qrStyles = document.createElement('style');
    qrStyles.textContent = `
        .qr-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .qr-modal-content {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
        }
        .qr-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .qr-header h3 {
            margin: 0;
            color: var(--text-primary);
        }
        .qr-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .qr-body {
            text-align: center;
            margin-bottom: 20px;
        }
        #qrcode img {
            border-radius: 8px;
            border: 4px solid var(--accent-color);
        }
        .qr-info {
            margin-top: 16px;
            color: var(--text-secondary);
        }
        .qr-info p {
            margin: 4px 0;
        }
        .qr-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
    `;
    document.head.appendChild(qrStyles);

    // Close modal when clicking outside
    document.getElementById('addEmployeeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddEmployeeModal();
        }
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
