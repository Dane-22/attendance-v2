<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Add Employee</h2>
    </div>
    
    <form method="POST" action="/employee/create">
        <div class="form-group">
            <label for="employee_code">Employee Code *</label>
            <input type="text" id="employee_code" name="employee_code" placeholder="e.g., EMP001" required>
        </div>
        
        <div class="form-group">
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" placeholder="e.g., IT, HR, Sales">
        </div>
        
        <div class="form-group">
            <label for="position">Position</label>
            <input type="text" id="position" name="position" placeholder="e.g., Developer, Manager">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="/employee" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
