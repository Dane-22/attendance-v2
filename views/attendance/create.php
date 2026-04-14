<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Add Attendance Record</h2>
    </div>
    
    <form method="POST" action="/attendance/create">
        <div class="form-group">
            <label for="employee_id">Employee *</label>
            <select id="employee_id" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach ($employees as $employee): ?>
                <option value="<?= $employee['id'] ?>">
                    <?= htmlspecialchars($employee['employee_code'] . ' - ' . $employee['first_name'] . ' ' . $employee['last_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Date *</label>
            <input type="date" id="date" name="date" value="<?= $today ?>" required>
        </div>
        
        <div class="form-group">
            <label for="check_in">Check In Time</label>
            <input type="time" id="check_in" name="check_in">
        </div>
        
        <div class="form-group">
            <label for="check_out">Check Out Time</label>
            <input type="time" id="check_out" name="check_out">
        </div>
        
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="half_day">Half Day</option>
                <option value="leave">Leave</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Optional notes..."></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Record</button>
            <a href="/attendance" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
