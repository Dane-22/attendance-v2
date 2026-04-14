<?php ob_start(); ?>

<div class="card">
    <h2>Dashboard</h2>
    <p style="color: #6b7280;">Today's Date: <?= date('F j, Y', strtotime($today)) ?></p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #2563eb;"><?= $totalEmployees ?></div>
        <div style="color: #6b7280;">Total Employees</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #065f46;"><?= $presentToday ?></div>
        <div style="color: #6b7280;">Present Today</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #991b1b;"><?= $absentToday ?></div>
        <div style="color: #6b7280;">Absent Today</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #92400e;"><?= $lateToday ?></div>
        <div style="color: #6b7280;">Late Today</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #374151;"><?= $onLeaveToday ?></div>
        <div style="color: #6b7280;">On Leave</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2.5rem; font-weight: bold; color: #6b7280;"><?= $notMarked ?></div>
        <div style="color: #6b7280;">Not Marked</div>
    </div>
</div>

<div class="card">
    <div class="header-actions">
        <h3>Quick Actions</h3>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="/attendance/create" class="btn btn-primary">Mark Attendance</a>
        <a href="/employee/create" class="btn btn-primary">Add Employee</a>
        <a href="/attendance/report" class="btn btn-secondary">View Reports</a>
    </div>
</div>

<div class="card">
    <h3>Monthly Summary - <?= date('F Y') ?></h3>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($monthlyReport) > 0): ?>
                <?php foreach (array_slice($monthlyReport, 0, 5) as $row): 
                    $total = $row['present_days'] + $row['absent_days'] + $row['late_days'] + $row['half_days'] + $row['leave_days'];
                    $attendanceRate = $total > 0 ? round((($row['present_days'] + $row['late_days']) / $total) * 100, 1) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['department'] ?: 'N/A') ?></td>
                    <td><?= $row['present_days'] ?></td>
                    <td><?= $row['absent_days'] ?></td>
                    <td><?= $row['late_days'] ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: <?= $attendanceRate ?>%; background: <?= $attendanceRate >= 80 ? '#065f46' : ($attendanceRate >= 60 ? '#92400e' : '#991b1b') ?>; height: 100%;"></div>
                            </div>
                            <span style="font-size: 0.875rem; font-weight: 500;"><?= $attendanceRate ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280;">
                        No records found for this month.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (count($monthlyReport) > 5): ?>
    <div style="margin-top: 1rem; text-align: center;">
        <a href="/attendance/report" class="btn btn-secondary">View All Employees</a>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
