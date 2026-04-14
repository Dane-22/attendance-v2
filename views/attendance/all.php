<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>All Attendance Records</h2>
        <a href="/attendance/create" class="btn btn-primary">Add Attendance</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Branch</th>
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
                    <td><?= date('M j, Y', strtotime($record['date'])) ?></td>
                    <td><?= htmlspecialchars($record['employee_code']) ?></td>
                    <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                    <td>
                        <?php if (!empty($record['branch_code'])): ?>
                            <span class="branch-badge"><?= htmlspecialchars($record['branch_code']) ?></span>
                            <?php if (!empty($record['branch_name'])): ?>
                                <small class="branch-name"><?= htmlspecialchars($record['branch_name']) ?></small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : '-' ?></td>
                    <td><?= $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : '-' ?></td>
                    <td>
                        <span class="status-badge status-<?= $record['status'] ?>">
                            <?= str_replace('_', ' ', $record['status']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="/attendance/edit/<?= $record['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="/attendance/delete/<?= $record['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #6b7280; padding: 24px;">
                        No attendance records found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
