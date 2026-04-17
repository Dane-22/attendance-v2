<?php ob_start(); ?>
<?php $baseUrl = dirname($_SERVER['SCRIPT_NAME']); ?>

<div class="card">
    <div class="header-actions">
        <h2>Attendance for <?= date('F j, Y', strtotime($date)) ?></h2>
        <a href="<?= $baseUrl ?>/attendance/create" class="btn btn-primary">Add Attendance</a>
    </div>

    <form method="GET" action="<?= $baseUrl ?>/attendance" class="filter-form">
        <div class="form-group">
            <label for="date">Select Date</label>
            <input type="date" id="date" name="date" value="<?= $date ?>">
        </div>
        <button type="submit" class="btn btn-primary">View</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Department</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance) > 0): ?>
                <?php foreach ($attendance as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['employee_code']) ?></td>
                    <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                    <td><?= htmlspecialchars($record['department'] ?: 'N/A') ?></td>
                    <td><?= $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : '-' ?></td>
                    <td><?= $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : '-' ?></td>
                    <td>
                        <span class="status-badge status-<?= $record['status'] ?>">
                            <?= str_replace('_', ' ', $record['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= $baseUrl ?>/attendance/edit/<?= $record['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="<?= $baseUrl ?>/attendance/delete/<?= $record['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280;">
                        No attendance records found for this date.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Quick Mark Attendance</h3>
    <p style="margin-bottom: 1rem; color: #6b7280;">
        Select employees who are present today (<?= date('F j, Y', strtotime($date)) ?>)
    </p>
    
    <form method="POST" action="<?= $baseUrl ?>/attendance/quick-mark">
        <input type="hidden" name="date" value="<?= $date ?>">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" id="select-all" onchange="toggleAll(this)">
                    </th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="employees[]" value="<?= $employee['id'] ?>">
                    </td>
                    <td><?= htmlspecialchars($employee['employee_code']) ?></td>
                    <td><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                    <td><?= htmlspecialchars($employee['department'] ?: 'N/A') ?></td>
                    <td>
                        <select name="status[<?= $employee['id'] ?>]" class="status-select">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">Mark Attendance</button>
        </div>
    </form>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('input[name="employees[]"]');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
