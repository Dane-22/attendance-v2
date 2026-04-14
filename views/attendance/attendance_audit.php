<?php
$title = 'Attendance Audit';
$currentMonth = 'April 2026';
$totalRecords = 57;
$currentlyPresent = 56;
$completedShifts = 1;
$absent = 15;
$selectedDate = 'April 14, 2026 (Tuesday)';

// Calendar data - days with record counts
$calendarDays = [
    ['day' => 28, 'month' => 'prev'], ['day' => 30, 'month' => 'prev'], ['day' => 31, 'month' => 'prev'],
    ['day' => 1, 'rec' => 92], ['day' => 2, 'rec' => 74], ['day' => 3, 'rec' => 77], ['day' => 4, 'rec' => 74],
    ['day' => 5, 'rec' => 2], ['day' => 6, 'rec' => 74], ['day' => 7, 'rec' => 77], ['day' => 8, 'rec' => 66], ['day' => 9, 'rec' => 63], ['day' => 10, 'rec' => 67],
    ['day' => 11, 'rec' => 1, 'selected' => true, 'label' => '1 rec'], ['day' => 12, 'rec' => 70], ['day' => 13, 'rec' => 77], ['day' => 14, 'rec' => 39, 'today' => true],
    ['day' => 15], ['day' => 16], ['day' => 17], ['day' => 18],
    ['day' => 19], ['day' => 20], ['day' => 21], ['day' => 22], ['day' => 23], ['day' => 24], ['day' => 25],
    ['day' => 26], ['day' => 27], ['day' => 28], ['day' => 29], ['day' => 30], ['day' => 1, 'month' => 'next'], ['day' => 2, 'month' => 'next'],
];

$attendanceRecords = [
    ['name' => 'ALFREDO BAGUIO', 'position' => 'Worker', 'code' => 'E0006', 'branch' => 'BCDA - Admin', 'timeIn' => '06:49 AM', 'timeOut' => '-', 'hours' => '5.18 hrs', 'status' => 'Present'],
    ['name' => 'ROLLY BALTAZAR', 'position' => 'Worker', 'code' => 'E0007', 'branch' => 'BCDA - Admin', 'timeIn' => '06:51 AM', 'timeOut' => '-', 'hours' => '5.15 hrs', 'status' => 'Present'],
    ['name' => 'DANIEL BACHILLER', 'position' => 'Worker', 'code' => 'E0005', 'branch' => 'BCDA - Admin', 'timeIn' => '06:51 AM', 'timeOut' => '-', 'hours' => '5.15 hrs', 'status' => 'Present'],
    ['name' => 'AARIZ MARLOU', 'position' => 'Worker', 'code' => 'E0001', 'branch' => 'BCDA - Control Tower', 'timeIn' => '06:44 AM', 'timeOut' => '-', 'hours' => '5.28 hrs', 'status' => 'Present'],
    ['name' => 'CESAR ABUBO', 'position' => 'Worker', 'code' => 'E0002', 'branch' => 'BCDA - Admin', 'timeIn' => '06:51 AM', 'timeOut' => '-', 'hours' => '5.16 hrs', 'status' => 'Present'],
    ['name' => 'MARLON AGUILAR', 'position' => 'Worker', 'code' => 'E0003', 'branch' => 'Sto. Rosario', 'timeIn' => '06:49 AM', 'timeOut' => '-', 'hours' => '5.18 hrs', 'status' => 'Present'],
];

ob_start();
?>

<style>
    .audit-container {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 24px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
    }

    .btn-action.outline {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-action.accent {
        background: var(--accent-color);
        color: #000000;
    }

    .btn-action.green {
        background: #22c55e;
        color: #ffffff;
    }

    .btn-action.blue {
        background: #3b82f6;
        color: #ffffff;
    }

    /* Calendar Styles */
    .calendar-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .calendar-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
    }

    .nav-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .today-btn {
        padding: 6px 12px;
        background: #f97316;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }

    .weekday {
        text-align: center;
        color: var(--accent-color);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .calendar-day {
        aspect-ratio: 1;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        font-size: 0.875rem;
        border: 1px solid transparent;
    }

    .calendar-day:hover {
        border-color: var(--border-color);
    }

    .calendar-day .day-num {
        font-weight: 500;
        color: var(--text-primary);
    }

    .calendar-day .rec-count {
        font-size: 0.65rem;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .calendar-day.other-month .day-num {
        color: var(--text-secondary);
        opacity: 0.5;
    }

    .calendar-day.selected {
        background: var(--accent-color);
        border-color: var(--accent-color);
    }

    .calendar-day.selected .day-num,
    .calendar-day.selected .rec-count {
        color: #000000;
    }

    .calendar-day.today {
        border: 2px solid var(--accent-color);
    }

    .calendar-legend {
        display: flex;
        gap: 16px;
        margin-top: 20px;
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 2px;
    }

    .legend-dot.selected {
        background: var(--accent-color);
    }

    .legend-dot.records {
        border: 1px solid var(--accent-color);
    }

    .legend-dot.today {
        border: 2px solid var(--accent-color);
    }

    /* Right Panel */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        font-family: monospace;
    }

    .stat-value.white { color: var(--text-primary); }
    .stat-value.green { color: #22c55e; }
    .stat-value.orange { color: #f97316; }
    .stat-value.red { color: #ef4444; }

    .date-header {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .date-header i {
        color: var(--accent-color);
        font-size: 1.2rem;
    }

    .date-text {
        font-weight: 600;
        color: var(--text-primary);
    }

    .info-banner {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05));
        border: 1px solid var(--accent-color);
        border-radius: 10px;
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--accent-color);
        font-size: 0.875rem;
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-secondary);
        font-size: 0.875rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-tab.active {
        background: var(--text-secondary);
        color: var(--bg-primary);
    }

    .filter-tab i {
        font-size: 0.7rem;
    }

    /* Attendance Table */
    .attendance-table-container {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table th {
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .attendance-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .attendance-table tr:last-child td {
        border-bottom: none;
    }

    .emp-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .emp-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .emp-position {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge.present {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }

    .table-actions {
        display: flex;
        gap: 8px;
    }

    .table-action-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        background: var(--bg-primary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    @media (max-width: 1200px) {
        .audit-container {
            grid-template-columns: 1fr;
        }
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        .attendance-table th:nth-child(4),
        .attendance-table th:nth-child(5),
        .attendance-table th:nth-child(6),
        .attendance-table td:nth-child(4),
        .attendance-table td:nth-child(5),
        .attendance-table td:nth-child(6) {
            display: none;
        }
    }
</style>

<!-- Action Buttons -->
<div class="action-buttons">
    <button class="btn-action outline"><i class="fas fa-calendar-week"></i> This Week</button>
    <button class="btn-action outline"><i class="fas fa-calendar-alt"></i> This Month</button>
    <button class="btn-action accent"><i class="fas fa-calendar-day"></i> Today</button>
    <button class="btn-action green"><i class="fas fa-file-excel"></i> Export Excel</button>
</div>

<div class="audit-container">
    <!-- Left Panel - Calendar -->
    <div class="left-panel">
        <div class="calendar-card">
            <div class="calendar-header">
                <span class="calendar-title"><?= $currentMonth ?></span>
                <div class="calendar-nav">
                    <button class="nav-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="today-btn">Today</button>
                    <button class="nav-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="weekdays">
                <div class="weekday">Sun</div>
                <div class="weekday">Mon</div>
                <div class="weekday">Tue</div>
                <div class="weekday">Wed</div>
                <div class="weekday">Thu</div>
                <div class="weekday">Fri</div>
                <div class="weekday">Sat</div>
            </div>

            <div class="calendar-grid">
                <?php foreach ($calendarDays as $day): ?>
                <div class="calendar-day <?= isset($day['month']) ? 'other-month' : '' ?> <?= isset($day['selected']) ? 'selected' : '' ?> <?= isset($day['today']) ? 'today' : '' ?>">
                    <span class="day-num"><?= $day['day'] ?></span>
                    <?php if (isset($day['rec'])): ?>
                    <span class="rec-count"><?= $day['rec'] ?> rec</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-dot selected"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot records"></div>
                    <span>Has Records</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot today"></div>
                    <span>Today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Records</div>
                <div class="stat-value white"><?= $totalRecords ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Currently Present</div>
                <div class="stat-value green"><?= $currentlyPresent ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Completed Shifts</div>
                <div class="stat-value orange"><?= $completedShifts ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Absent</div>
                <div class="stat-value red"><?= $absent ?></div>
            </div>
        </div>

        <!-- Date Header -->
        <div class="date-header">
            <i class="fas fa-calendar"></i>
            <span class="date-text"><?= $selectedDate ?></span>
        </div>

        <!-- Info Banner -->
        <!-- <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <span>Auto-absent mode active: Employees without time-in records are shown as Absent (Auto)</span>
        </div> -->

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab active"><i class="fas fa-th-large"></i> All</button>
            <button class="filter-tab"><i class="fas fa-check-circle"></i> Present</button>
            <button class="filter-tab"><i class="fas fa-clock"></i> Late</button>
            <button class="filter-tab"><i class="fas fa-check-double"></i> Completed</button>
            <button class="filter-tab"><i class="fas fa-times-circle"></i> Absent</button>
            <button class="filter-tab"><i class="fas fa-ban"></i> Voided</button>
        </div>

        <!-- Attendance Table -->
        <div class="attendance-table-container">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Branch</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td>
                            <div class="emp-info">
                                <span class="emp-name"><?= htmlspecialchars($record['name']) ?></span>
                                <span class="emp-position"><?= htmlspecialchars($record['position']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($record['code']) ?></td>
                        <td><?= htmlspecialchars($record['branch']) ?></td>
                        <td><?= $record['timeIn'] ?></td>
                        <td><?= $record['timeOut'] ?></td>
                        <td><?= $record['hours'] ?></td>
                        <td><span class="status-badge present"><?= $record['status'] ?></span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn"><i class="fas fa-eye"></i></button>
                                <button class="table-action-btn"><i class="fas fa-edit"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
