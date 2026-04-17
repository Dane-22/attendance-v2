<?php
$title = 'Attendance Audit';

// Data passed from controller:
// $currentMonth, $selectedDate, $calendarData, $totalRecords, $currentlyPresent, $completedShifts, $absent, $attendanceRecords
// $calendarYear, $calendarMonth, $selectedDateValue, $filter

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
        background: var(--bg-secondary);
    }

    .attendance-table tbody tr {
        background: var(--bg-secondary);
    }

    .attendance-table tbody tr:hover td {
        background: var(--bg-primary);
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
        text-decoration: none;
    }

    .emp-name:hover {
        color: var(--accent-color);
    }

    .branch-link {
        color: var(--text-primary);
        text-decoration: none;
    }

    .branch-link:hover {
        color: var(--accent-color);
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

    .source-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        color: var(--accent-color);
        font-size: 0.875rem;
        cursor: help;
    }

    .source-badge:hover {
        background: var(--accent-color);
        color: #000000;
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

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-secondary);
        border: 2px solid var(--accent-color);
        border-radius: 16px;
        width: 90%;
        max-width: 900px;
        max-height: 90vh;
        overflow: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent);
    }

    .modal-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-title h2 {
        color: var(--text-primary);
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .position-badge {
        background: var(--accent-color);
        color: #000;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .modal-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--accent-color);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-nav span {
        color: var(--accent-color);
        font-weight: 600;
        font-size: 1rem;
        min-width: 120px;
        text-align: center;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-calendar {
        padding: 24px;
    }

    .modal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }

    .modal-weekday {
        text-align: center;
        color: var(--accent-color);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 8px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 8px;
    }

    .modal-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .modal-day {
        aspect-ratio: 1;
        border-radius: 12px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        min-height: 80px;
        max-height: 120px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .modal-day::-webkit-scrollbar {
        width: 4px;
    }

    .modal-day::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .modal-day .day-num {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .modal-day .day-info {
        font-size: 0.65rem;
        color: var(--text-secondary);
        line-height: 1.3;
    }

    .modal-day .status-pill {
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        text-align: center;
        font-weight: 600;
    }

    .modal-day.present {
        border-color: #22c55e;
        background: rgba(34, 197, 94, 0.1);
    }

    .modal-day.present .status-pill {
        background: #22c55e;
        color: #fff;
    }

    .modal-day.late {
        border-color: #f97316;
        background: rgba(249, 115, 22, 0.1);
    }

    .modal-day.late .status-pill {
        background: #f97316;
        color: #fff;
    }

    .modal-day.absent {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    .modal-day.absent .status-pill {
        background: #ef4444;
        color: #fff;
    }

    .modal-day.no-record {
        opacity: 0.5;
    }

    .modal-day.today {
        border: 2px solid var(--accent-color);
    }

    .modal-day.other-month {
        opacity: 0.3;
    }

    /* Session item styles */
    .session-item {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        padding: 4px 6px;
        margin-bottom: 3px;
        font-size: 0.65rem;
        border-left: 2px solid transparent;
        flex-shrink: 0;
    }

    .session-item.present {
        border-left-color: #22c55e;
    }

    .session-item.late {
        border-left-color: #f97316;
    }

    .session-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2px;
    }

    .session-number {
        background: rgba(255, 255, 255, 0.1);
        color: var(--accent-color);
        font-size: 0.6rem;
        font-weight: 700;
        padding: 1px 4px;
        border-radius: 3px;
    }

    .session-status {
        font-size: 0.6rem;
        padding: 1px 4px;
        border-radius: 3px;
        font-weight: 600;
    }

    .session-status.present {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }

    .session-status.late {
        background: rgba(249, 115, 22, 0.2);
        color: #f97316;
    }

    .session-time {
        color: var(--text-secondary);
        font-size: 0.7rem;
        line-height: 1.2;
    }

    .session-time .check-in {
        color: #22c55e;
    }

    .session-time .check-out {
        color: #ef4444;
    }

    .session-branch {
        color: var(--accent-color);
        font-size: 0.65rem;
        margin-top: 2px;
        opacity: 0.8;
    }

    .day-total {
        background: rgba(255, 215, 0, 0.15);
        color: var(--accent-color);
        font-size: 0.65rem;
        padding: 3px 6px;
        border-radius: 4px;
        text-align: center;
        margin-top: 4px;
        font-weight: 600;
        border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .modal-legend {
        display: flex;
        justify-content: center;
        gap: 24px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
    }

    .modal-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .legend-box {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    .legend-box.present {
        background: #22c55e;
    }

    .legend-box.late {
        background: #f97316;
    }

    .legend-box.absent {
        background: #ef4444;
    }

    .legend-box.no-record {
        background: var(--border-color);
    }

    /* Branch Filter Styles */
    .branch-filter-form {
        margin: 0;
    }

    .branch-filter-select {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        min-width: 180px;
        outline: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23fbbf24' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .branch-filter-select:hover {
        border-color: var(--accent-color);
    }

    .branch-filter-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.2);
    }

    .branch-filter-select option {
        background: var(--bg-secondary);
        color: var(--text-primary);
        padding: 8px;
    }

    /* Branch Modal Specific Styles */
    .branch-modal .modal-content {
        max-width: 1400px;
        width: 95%;
    }

    .branch-modal-header {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
    }

    .branch-modal-title {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .branch-modal-title h2 {
        color: var(--text-primary);
        font-size: 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .all-employees-btn {
        background: rgba(255, 215, 0, 0.2);
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        cursor: pointer;
    }

    .branch-calendar-container {
        padding: 20px;
        max-height: calc(90vh - 100px);
        overflow-y: auto;
    }

    .branch-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }

    .branch-weekday {
        text-align: center;
        color: var(--accent-color);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        padding: 12px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 8px;
    }

    .branch-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .branch-day {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        min-height: 200px;
        display: flex;
        flex-direction: column;
    }

    .branch-day.other-month {
        opacity: 0.3;
    }

    .branch-day.today {
        border: 2px solid var(--accent-color);
    }

    .branch-day-header {
        padding: 8px 12px;
        border-bottom: 1px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .branch-day-content {
        flex: 1;
        padding: 8px;
        overflow-y: auto;
        max-height: 180px;
    }

    .branch-employee-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 8px;
        margin-bottom: 4px;
        border-radius: 6px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .branch-employee-item:hover {
        opacity: 0.8;
    }

    .branch-employee-item.present {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
    }

    .branch-employee-item.late {
        background: rgba(251, 191, 36, 0.15);
        color: #fbbf24;
    }

    .branch-employee-item.absent {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .branch-employee-name {
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .branch-employee-time {
        font-size: 0.65rem;
        opacity: 0.8;
    }

    .branch-day-summary {
        padding: 8px 12px;
        border-top: 1px solid var(--border-color);
        font-size: 0.7rem;
        color: var(--text-secondary);
        text-align: center;
    }

    .more-btn {
        background: rgba(255, 255, 255, 0.1);
        color: var(--accent-color);
        border: 1px dashed var(--accent-color);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        cursor: pointer;
        text-align: center;
        margin-top: 4px;
    }

    .more-btn:hover {
        background: rgba(255, 215, 0, 0.2);
    }

    .no-employees {
        text-align: center;
        color: var(--text-secondary);
        font-size: 0.75rem;
        padding: 20px 0;
    }
</style>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= date('Y-m-d', strtotime('-7 days')) ?><?= isset($_GET['branch']) ? '&branch=' . urlencode($_GET['branch']) : '' ?>" class="btn-action outline"><i class="fas fa-calendar-week"></i> This Week</a>
    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?year=<?= date('Y') ?>&month=<?= date('n') ?><?= isset($_GET['branch']) ? '&branch=' . urlencode($_GET['branch']) : '' ?>" class="btn-action outline"><i class="fas fa-calendar-alt"></i> This Month</a>
    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= date('Y-m-d') ?>" class="btn-action accent"><i class="fas fa-calendar-day"></i> Today</a>
    
    <!-- Branch Filter Dropdown -->
    <form method="GET" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit" class="branch-filter-form">
        <input type="hidden" name="date" value="<?= $selectedDateValue ?>">
        <?php if (isset($_GET['filter'])): ?>
        <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter']) ?>">
        <?php endif; ?>
        <select name="branch" class="branch-filter-select" onchange="this.form.submit()">
            <option value="">🏢 All Branches</option>
            <?php foreach ($branches as $branch): ?>
            <option value="<?= htmlspecialchars($branch['branch_name']) ?>" <?= (isset($_GET['branch']) && $_GET['branch'] == $branch['branch_name']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['branch_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
    
    <button class="btn-action green" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Export Excel</button>
</div>

<div class="audit-container">
    <!-- Left Panel - Calendar -->
    <div class="left-panel">
        <div class="calendar-card">
            <div class="calendar-header">
                <span class="calendar-title"><?= $currentMonth ?></span>
                <div class="calendar-nav">
                    <?php 
                        $prevMonth = $calendarMonth == 1 ? 12 : $calendarMonth - 1;
                        $prevYear = $calendarMonth == 1 ? $calendarYear - 1 : $calendarYear;
                        $nextMonth = $calendarMonth == 12 ? 1 : $calendarMonth + 1;
                        $nextYear = $calendarMonth == 12 ? $calendarYear + 1 : $calendarYear;
                    ?>
                    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="nav-btn"><i class="fas fa-chevron-left"></i></a>
                    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?year=<?= date('Y') ?>&month=<?= date('n') ?>&date=<?= date('Y-m-d') ?>" class="today-btn">Today</a>
                    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="nav-btn"><i class="fas fa-chevron-right"></i></a>
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
                <?php foreach ($calendarData as $day): 
                    $isSelected = isset($selectedDateValue) && date('j', strtotime($selectedDateValue)) == $day['day'] && !isset($day['month']);
                ?>
                <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?year=<?= $calendarYear ?>&month=<?= $calendarMonth ?>&date=<?= $calendarYear ?>-<?= str_pad($calendarMonth, 2, '0', STR_PAD_LEFT) ?>-<?= str_pad($day['day'], 2, '0', STR_PAD_LEFT) ?>" 
                   class="calendar-day <?= isset($day['month']) ? 'other-month' : '' ?> <?= $isSelected ? 'selected' : '' ?> <?= isset($day['today']) ? 'today' : '' ?>">
                    <span class="day-num"><?= $day['day'] ?></span>
                    <?php if (isset($day['rec'])): ?>
                    <span class="rec-count"><?= $day['rec'] ?> rec</span>
                    <?php endif; ?>
                </a>
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
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= $selectedDateValue ?>&filter=all" class="filter-tab <?= $filter == 'all' ? 'active' : '' ?>"><i class="fas fa-th-large"></i> All</a>
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= $selectedDateValue ?>&filter=present" class="filter-tab <?= $filter == 'present' ? 'active' : '' ?>"><i class="fas fa-check-circle"></i> Present</a>
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= $selectedDateValue ?>&filter=late" class="filter-tab <?= $filter == 'late' ? 'active' : '' ?>"><i class="fas fa-clock"></i> Late</a>
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= $selectedDateValue ?>&filter=completed" class="filter-tab <?= $filter == 'completed' ? 'active' : '' ?>"><i class="fas fa-check-double"></i> Completed</a>
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/attendance-audit?date=<?= $selectedDateValue ?>&filter=absent" class="filter-tab <?= $filter == 'absent' ? 'active' : '' ?>"><i class="fas fa-times-circle"></i> Absent</a>
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
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td>
                            <div class="emp-info">
                                <a href="#" class="emp-name" onclick="openEmployeeModal('<?= htmlspecialchars($record['code']) ?>', '<?= htmlspecialchars($record['name']) ?>'); return false;"><?= htmlspecialchars($record['name']) ?></a>
                                <span class="emp-position"><?= htmlspecialchars($record['position']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($record['code']) ?></td>
                        <td><a href="#" class="branch-link" onclick="openBranchModal('<?= htmlspecialchars($record['branch']) ?>'); return false;"><?= htmlspecialchars($record['branch']) ?></a></td>
                        <td><?= $record['timeIn'] ?></td>
                        <td><?= $record['timeOut'] ?></td>
                        <td><?= $record['hours'] ?></td>
                        <td><span class="status-badge present"><?= $record['status'] ?></span></td>
                        <td>
                            <?php
                            $sourceIcon = '';
                            $sourceTitle = '';
                            if (!empty($record['notes'])) {
                                if (strpos($record['notes'], 'QR Scan') !== false) {
                                    $sourceIcon = 'fa-qrcode';
                                    $sourceTitle = 'QR Scan';
                                } elseif (strpos($record['notes'], 'Manual entry') !== false) {
                                    $sourceIcon = 'fa-hand-pointer';
                                    $sourceTitle = 'Manual Entry';
                                } else {
                                    $sourceIcon = 'fa-question';
                                    $sourceTitle = 'Unknown';
                                }
                            }
                            ?>
                            <?php if ($sourceIcon): ?>
                            <span class="source-badge" title="<?= htmlspecialchars($record['notes']) ?>">
                                <i class="fas <?= $sourceIcon ?>"></i>
                            </span>
                            <?php endif; ?>
                        </td>
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

<!-- Branch Attendance Modal -->
<div id="branchModal" class="modal branch-modal">
    <div class="modal-content branch-modal-content">
        <div class="modal-header branch-modal-header">
            <div class="modal-title branch-modal-title">
                <h2 id="modalBranchName">Branch Name</h2>
                <button class="all-employees-btn">ALL EMPLOYEES</button>
            </div>
            <div class="modal-nav">
                <button class="modal-nav-btn" onclick="changeBranchMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                <span id="modalBranchMonthYear">April 2026</span>
                <button class="modal-nav-btn" onclick="changeBranchMonth(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="modal-close" onclick="closeBranchModal()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="branch-calendar-container">
            <div class="branch-weekdays">
                <div class="branch-weekday">SUN</div>
                <div class="branch-weekday">MON</div>
                <div class="branch-weekday">TUE</div>
                <div class="branch-weekday">WED</div>
                <div class="branch-weekday">THU</div>
                <div class="branch-weekday">FRI</div>
                <div class="branch-weekday">SAT</div>
            </div>
            <div id="modalBranchCalendarGrid" class="branch-calendar-grid">
                <!-- Calendar days will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Employee Attendance Modal -->
<div id="employeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h2 id="modalEmployeeName">Employee Name</h2>
                <span id="modalEmployeePosition" class="position-badge">Worker</span>
            </div>
            <div class="modal-nav">
                <button class="modal-nav-btn" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                <span id="modalMonthYear">April 2026</span>
                <button class="modal-nav-btn" onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="modal-close" onclick="closeEmployeeModal()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-calendar">
            <div class="modal-weekdays">
                <div class="modal-weekday">SUN</div>
                <div class="modal-weekday">MON</div>
                <div class="modal-weekday">TUE</div>
                <div class="modal-weekday">WED</div>
                <div class="modal-weekday">THU</div>
                <div class="modal-weekday">FRI</div>
                <div class="modal-weekday">SAT</div>
            </div>
            <div id="modalCalendarGrid" class="modal-calendar-grid">
                <!-- Calendar days will be populated by JavaScript -->
            </div>
        </div>
        
        <div class="modal-legend">
            <div class="modal-legend-item">
                <div class="legend-box present"></div>
                <span>Present</span>
            </div>
            <div class="modal-legend-item">
                <div class="legend-box late"></div>
                <span>Late</span>
            </div>
            <div class="modal-legend-item">
                <div class="legend-box absent"></div>
                <span>Absent</span>
            </div>
            <div class="modal-legend-item">
                <div class="legend-box no-record"></div>
                <span>No Record</span>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    const table = document.querySelector('.attendance-table');
    let csv = [];

    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        if (!th.textContent.includes('Actions')) {
            headers.push('"' + th.textContent.trim() + '"');
        }
    });
    csv.push(headers.join(','));

    // Get rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach((td, index) => {
            if (index < 8) { // Skip actions column (now index 8)
                let text = td.textContent.trim();
                if (index === 0) {
                    // Employee column - get just the name
                    text = td.querySelector('.emp-name')?.textContent || text;
                } else if (index === 7) {
                    // Source column - get the icon title or text
                    const sourceBadge = td.querySelector('.source-badge');
                    text = sourceBadge ? sourceBadge.getAttribute('title') || 'Unknown' : '';
                }
                row.push('"' + text + '"');
            }
        });
        csv.push(row.join(','));
    });
    
    // Download
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'attendance_<?= $selectedDateValue ?>.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Modal variables
let currentModalYear = <?= date('Y') ?>;
let currentModalMonth = <?= date('n') ?> - 1; // 0-based
let currentEmployeeCode = '';

function openEmployeeModal(empCode, empName) {
    currentEmployeeCode = empCode;
    document.getElementById('modalEmployeeName').textContent = empName;
    document.getElementById('employeeModal').classList.add('active');
    loadEmployeeCalendar();
}

function closeEmployeeModal() {
    document.getElementById('employeeModal').classList.remove('active');
}

function changeMonth(direction) {
    currentModalMonth += direction;
    if (currentModalMonth > 11) {
        currentModalMonth = 0;
        currentModalYear++;
    } else if (currentModalMonth < 0) {
        currentModalMonth = 11;
        currentModalYear--;
    }
    loadEmployeeCalendar();
}

async function loadEmployeeCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('modalMonthYear').textContent = `${monthNames[currentModalMonth]} ${currentModalYear}`;
    
    // Fetch employee attendance data for the month
    const year = currentModalYear;
    const month = currentModalMonth + 1;
    const basePath = '<?= dirname($_SERVER['SCRIPT_NAME']) ?>';
    
    try {
        const response = await fetch(`${basePath}/api/attendance/employee-calendar?employee_code=${currentEmployeeCode}&year=${year}&month=${month}`);
        const data = await response.json();
        renderEmployeeCalendar(data.attendance || {});
    } catch (error) {
        // Fallback: render empty calendar
        renderEmployeeCalendar({});
    }
}

function renderEmployeeCalendar(attendanceData) {
    const grid = document.getElementById('modalCalendarGrid');
    const year = currentModalYear;
    const month = currentModalMonth;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();
    
    let html = '';
    
    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        html += `<div class="modal-day other-month"><span class="day-num">${prevMonthDays - i}</span></div>`;
    }
    
    // Current month days
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const records = attendanceData[dateKey];
        
        let dayClass = '';
        let dayContent = `<span class="day-num">${day}</span>`;
        
        if (isCurrentMonth && day === today.getDate()) {
            dayClass += ' today';
        }
        
        if (records && records.length > 0) {
            // Use status from first record for day styling
            dayClass += ` ${records[0].status}`;

            // Build content for all records with enhanced styling
            let recordsHtml = '';
            let totalMinutes = 0;

            records.forEach((rec, index) => {
                const sessionNum = index + 1;
                const hasLate = rec.status === 'late';

                // Calculate hours for this session
                let sessionMinutes = 0;
                if (rec.check_in && rec.check_out) {
                    const checkIn = new Date(`2000-01-01 ${rec.check_in}`);
                    const checkOut = new Date(`2000-01-01 ${rec.check_out}`);
                    sessionMinutes = (checkOut - checkIn) / 60000;
                    if (sessionMinutes > 0) totalMinutes += sessionMinutes;
                }
                const hours = Math.floor(sessionMinutes / 60);
                const mins = Math.round(sessionMinutes % 60);
                const timeDisplay = sessionMinutes > 0 ? `${hours}h ${mins}m` : '';

                recordsHtml += `
                    <div class="session-item ${rec.status}">
                        <div class="session-header">
                            <span class="session-number">#${sessionNum}</span>
                            ${hasLate ? '<span class="session-status late">Late</span>' : '<span class="session-status present">Present</span>'}
                        </div>
                        <div class="session-time">
                            <span class="check-in">${rec.check_in || '--:--'}</span> -
                            <span class="check-out">${rec.check_out || '--:--'}</span>
                            ${timeDisplay ? `<span style="float:right;opacity:0.7">${timeDisplay}</span>` : ''}
                        </div>
                        <div class="session-branch">${rec.branch || ''}</div>
                    </div>
                `;
            });

            dayContent += recordsHtml;

            // Show total hours if multiple sessions
            if (records.length > 1 && totalMinutes > 0) {
                const totalHours = (totalMinutes / 60).toFixed(1);
                dayContent += `<div class="day-total">Total: ${totalHours} hrs</div>`;
            } else if (records.length === 1) {
                dayContent += `<span class="status-pill" style="margin-top:4px;display:block;">${records[0].status}</span>`;
            }
        } else {
            dayClass += ' no-record';
            dayContent += '<span class="status-pill">No Record</span>';
        }
        
        html += `<div class="modal-day${dayClass}">${dayContent}</div>`;
    }
    
    // Next month days
    const remaining = (7 - ((firstDay + daysInMonth) % 7)) % 7;
    for (let i = 1; i <= remaining; i++) {
        html += `<div class="modal-day other-month"><span class="day-num">${i}</span></div>`;
    }
    
    grid.innerHTML = html;
}

// Close modal when clicking outside
document.getElementById('employeeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmployeeModal();
    }
});

// Branch modal variables
let currentBranchModalYear = <?= date('Y') ?>;
let currentBranchModalMonth = <?= date('n') ?> - 1;
let currentBranchName = '';

function openBranchModal(branchName) {
    currentBranchName = branchName;
    document.getElementById('modalBranchName').textContent = branchName;
    document.getElementById('branchModal').classList.add('active');
    loadBranchCalendar();
}

function closeBranchModal() {
    document.getElementById('branchModal').classList.remove('active');
}

function changeBranchMonth(direction) {
    currentBranchModalMonth += direction;
    if (currentBranchModalMonth > 11) {
        currentBranchModalMonth = 0;
        currentBranchModalYear++;
    } else if (currentBranchModalMonth < 0) {
        currentBranchModalMonth = 11;
        currentBranchModalYear--;
    }
    loadBranchCalendar();
}

async function loadBranchCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('modalBranchMonthYear').textContent = `${monthNames[currentBranchModalMonth]} ${currentBranchModalYear}`;
    
    // Fetch branch attendance data for the month
    const year = currentBranchModalYear;
    const month = currentBranchModalMonth + 1;
    const basePath = '<?= dirname($_SERVER['SCRIPT_NAME']) ?>';
    
    try {
        const response = await fetch(`${basePath}/api/attendance/branch-calendar?branch_name=${encodeURIComponent(currentBranchName)}&year=${year}&month=${month}`);
        const data = await response.json();
        renderBranchCalendar(data.attendance || {});
    } catch (error) {
        // Fallback: render empty calendar
        renderBranchCalendar({});
    }
}

function renderBranchCalendar(attendanceData) {
    const grid = document.getElementById('modalBranchCalendarGrid');
    const year = currentBranchModalYear;
    const month = currentBranchModalMonth;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();
    
    let html = '';
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
    const maxEmployeesToShow = 8;
    
    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        html += `<div class="branch-day other-month"><div class="branch-day-header">${prevMonthDays - i}</div><div class="branch-day-content"><div class="no-employees">No employees</div></div></div>`;
    }
    
    // Current month days
    for (let day = 1; day <= daysInMonth; day++) {
        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayData = attendanceData[dateKey];
        
        let dayClass = '';
        if (isCurrentMonth && day === today.getDate()) {
            dayClass = ' today';
        }
        
        let dayContent = `<div class="branch-day-header">${day}</div>`;
        dayContent += `<div class="branch-day-content">`;
        
        if (dayData && dayData.employees && dayData.employees.length > 0) {
            const employees = dayData.employees;
            const displayEmployees = employees.slice(0, maxEmployeesToShow);
            const remainingCount = employees.length - maxEmployeesToShow;
            
            // Count by status
            let lateCount = employees.filter(e => e.status === 'late').length;
            let absentCount = employees.filter(e => e.status === 'absent').length;
            
            displayEmployees.forEach(emp => {
                const timeIn = emp.check_in || '--:--';
                const timeOut = emp.check_out || '--:--';
                dayContent += `
                    <div class="branch-employee-item ${emp.status}">
                        <span class="branch-employee-name">${emp.name}</span>
                        <span class="branch-employee-time">${timeIn} - ${timeOut}</span>
                    </div>
                `;
            });
            
            if (remainingCount > 0) {
                dayContent += `<div class="more-btn">+${remainingCount} more</div>`;
            }
            
            dayContent += `</div>`;
            
            // Summary
            let summaryText = `${employees.length} employees`;
            if (lateCount > 0) summaryText += ` (${lateCount} Late)`;
            if (absentCount > 0) summaryText += ` (${absentCount} Absent)`;
            
            dayContent += `<div class="branch-day-summary">${summaryText}</div>`;
        } else {
            dayContent += `<div class="no-employees">No employees</div></div>`;
        }
        
        html += `<div class="branch-day${dayClass}">${dayContent}</div>`;
    }
    
    // Next month days
    const remaining = (7 - ((firstDay + daysInMonth) % 7)) % 7;
    for (let i = 1; i <= remaining; i++) {
        html += `<div class="branch-day other-month"><div class="branch-day-header">${i}</div><div class="branch-day-content"><div class="no-employees">No employees</div></div></div>`;
    }
    
    grid.innerHTML = html;
}

// Close branch modal when clicking outside
document.getElementById('branchModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBranchModal();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
