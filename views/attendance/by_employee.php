<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Attendance History - <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
        <a href="/attendance" class="btn btn-secondary">Back to Attendance</a>
    </div>
    
    <div style="margin-bottom: 1.5rem; color: #6b7280;">
        <strong>Employee Code:</strong> <?= htmlspecialchars($employee['employee_code']) ?> | 
        <strong>Department:</strong> <?= htmlspecialchars($employee['department'] ?: 'N/A') ?> | 
        <strong>Email:</strong> <?= htmlspecialchars($employee['email']) ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($records) > 0): ?>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= date('M j, Y (l)', strtotime($record['date'])) ?></td>
                    <td><?= $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : '-' ?></td>
                    <td><?= $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : '-' ?></td>
                    <td>
                        <span class="status-badge status-<?= $record['status'] ?>">
                            <?= str_replace('_', ' ', $record['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($record['notes'] ?: '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #6b7280;">
                        No attendance records found for this employee.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
