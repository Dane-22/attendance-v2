<?php
$title = 'Add Branch';
ob_start();
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
?>

<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        color: var(--text-primary);
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .form-container {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        font-size: 0.875rem;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent-color);
    }

    .form-select {
        width: 100%;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        font-size: 0.875rem;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-primary {
        background: var(--accent-color);
        color: #000000;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    .btn-secondary {
        background: var(--bg-primary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        border-color: var(--accent-color);
        color: var(--text-primary);
    }

    .required {
        color: #ef4444;
    }

    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-input {
        padding-right: 45px;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 8px;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: var(--accent-color);
    }
</style>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<div class="page-header">
    <div class="page-title">Add Branch</div>
    <div class="page-subtitle">Create a new branch location</div>
</div>

<div class="form-container">
    <form method="POST" action="<?= $baseUrl ?>/branches/create">
        <div class="form-group">
            <label class="form-label">Branch Code <span class="required">*</span></label>
            <input type="text" name="branch_code" class="form-input" required
                   value="<?= htmlspecialchars($nextBranchCode) ?>" readonly
                   style="background: var(--bg-secondary); cursor: not-allowed; font-weight: 600; color: var(--accent-color);">
            <small style="color: var(--text-secondary); font-size: 0.75rem;">
                Auto-generated based on last branch code (<?= htmlspecialchars($nextBranchCode) ?>)
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">Branch Name <span class="required">*</span></label>
            <input type="text" name="branch_name" class="form-input" required 
                   placeholder="e.g., Main Office, Makati Branch" maxlength="100">
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-input" 
                   placeholder="e.g., 123 Main Street, Makati City" maxlength="255">
        </div>

        <div class="form-group">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-input" 
                   placeholder="e.g., +63 912 345 6789" maxlength="20">
        </div>

        <div class="form-group">
            <label class="form-label">Branch Password <span class="required">*</span></label>
            <div class="password-wrapper">
                <input type="password" name="password" id="branchPassword" class="form-input password-input" required
                       placeholder="Password for branch device login" minlength="6">
                <button type="button" class="password-toggle" onclick="togglePassword('branchPassword', this)" title="Show/Hide Password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <small style="color: var(--text-secondary); font-size: 0.75rem;">
                Used for branch device QR scanner login. Minimum 6 characters.
            </small>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Branch
            </button>
            <a href="<?= $baseUrl ?>/branches" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
