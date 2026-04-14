<?php ob_start(); ?>

<div class="card">
    <div class="header-actions">
        <h2>Monthly Attendance Report</h2>
    </div>
    
    <form method="GET" action="/attendance/report" class="filter-form">
        <div class="form-group">
            <label for="month">Month</label>
            <select id="month" name="month">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= $i == $month ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <select id="year" name="year">
                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                <option value="<?= $i ?>" <?= $i == $year ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Department</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Half Day</th>
                <th>Leave</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($report) > 0): ?>
                <?php foreach ($report as $row): 
                    $total = $row['present_days'] + $row['absent_days'] + $row['late_days'] + $row['half_days'] + $row['leave_days'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['employee_code']) ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['department'] ?: 'N/A') ?></td>
                    <td style="color: #065f46; font-weight: 600;"><?= $row['present_days'] ?></td>
                    <td style="color: #991b1b; font-weight: 600;"><?= $row['absent_days'] ?></td>
                    <td style="color: #92400e; font-weight: 600;"><?= $row['late_days'] ?></td>
                    <td style="color: #1e40af; font-weight: 600;"><?= $row['half_days'] ?></td>
                    <td style="color: #374151; font-weight: 600;"><?= $row['leave_days'] ?></td>
                    <td style="font-weight: 600;"><?= $total ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #6b7280;">
                        No records found for the selected period.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
