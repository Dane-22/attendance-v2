<?php ob_start(); ?>
<?php $baseUrl = dirname($_SERVER['SCRIPT_NAME']); ?>

<div class="card">
    <div class="header-actions">
        <h2>Edit Employee</h2>
    </div>

    <form method="POST" action="<?= $baseUrl ?>/employee/edit/<?= $employee['id'] ?>">
        <div class="form-group">
            <label for="employee_code">Employee Code *</label>
            <input type="text" id="employee_code" name="employee_code" value="<?= htmlspecialchars($employee['employee_code']) ?>" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($employee['first_name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($employee['last_name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" value="<?= htmlspecialchars($employee['department'] ?: '') ?>" placeholder="e.g., IT, HR, Sales">
        </div>

        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" id="position" name="position" value="<?= htmlspecialchars($employee['position'] ?: '') ?>" placeholder="e.g., Developer, Manager">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Employee</button>
            <a href="<?= $baseUrl ?>/employee" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
