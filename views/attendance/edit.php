<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Edit Attendance Record</h2>
    </div>
    
    <form method="POST" action="/attendance/edit/<?= $record['id'] ?>">
        <div class="form-group">
            <label for="employee_id">Employee *</label>
            <select id="employee_id" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach ($employees as $employee): ?>
                <option value="<?= $employee['id'] ?>" <?= $employee['id'] == $record['employee_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($employee['employee_code'] . ' - ' . $employee['first_name'] . ' ' . $employee['last_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Date *</label>
            <input type="date" id="date" name="date" value="<?= $record['date'] ?>" required>
        </div>
        
        <div class="form-group">
            <label for="check_in">Check In Time</label>
            <input type="time" id="check_in" name="check_in" value="<?= $record['check_in'] ?>">
        </div>
        
        <div class="form-group">
            <label for="check_out">Check Out Time</label>
            <input type="time" id="check_out" name="check_out" value="<?= $record['check_out'] ?>">
        </div>
        
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="present" <?= $record['status'] == 'present' ? 'selected' : '' ?>>Present</option>
                <option value="absent" <?= $record['status'] == 'absent' ? 'selected' : '' ?>>Absent</option>
                <option value="late" <?= $record['status'] == 'late' ? 'selected' : '' ?>>Late</option>
                <option value="half_day" <?= $record['status'] == 'half_day' ? 'selected' : '' ?>>Half Day</option>
                <option value="leave" <?= $record['status'] == 'leave' ? 'selected' : '' ?>>Leave</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Optional notes..."><?= htmlspecialchars($record['notes'] ?: '') ?></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Record</button>
            <a href="/attendance" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
