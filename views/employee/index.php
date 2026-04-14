<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Employees</h2>
        <a href="/employee/create" class="btn btn-primary">Add Employee</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Position</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($employees) > 0): ?>
                <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?= htmlspecialchars($employee['employee_code']) ?></td>
                    <td><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                    <td><?= htmlspecialchars($employee['email']) ?></td>
                    <td><?= htmlspecialchars($employee['department'] ?: 'N/A') ?></td>
                    <td><?= htmlspecialchars($employee['position'] ?: 'N/A') ?></td>
                    <td class="actions">
                        <a href="/attendance/by-employee/<?= $employee['id'] ?>" class="btn btn-secondary btn-sm">Attendance</a>
                        <a href="/employee/edit/<?= $employee['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="/employee/delete/<?= $employee['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will also delete all attendance records for this employee.')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280;">
                        No employees found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
