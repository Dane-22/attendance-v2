<?php
$title = 'Activity Logs';
$totalActivities = 1245;

$timeFilters = [
    ['label' => 'Today', 'active' => false],
    ['label' => 'Yesterday', 'active' => false],
    ['label' => 'Last 7 Days', 'active' => true],
    ['label' => 'Last 30 Days', 'active' => false],
    ['label' => 'Custom Range', 'active' => false],
];

$activities = [
    [
        'user' => 'CESAR ABUBO',
        'avatar' => 'C',
        'action' => 'added a new manual attendance record for MARLON AGUILAR',
        'time' => '33 minutes ago',
        'type' => 'create',
        'color' => '#22c55e'
    ],
    [
        'user' => 'SUPER ADMIN',
        'avatar' => 'SA',
        'action' => 'updated Attendance Audit settings and modified auto-absent configuration',
        'time' => '2 hours ago',
        'type' => 'update',
        'color' => '#3b82f6'
    ],
    [
        'user' => 'CESAR ABUBO',
        'avatar' => 'C',
        'action' => 'exported attendance data for Finance department',
        'time' => '5 hours ago',
        'type' => 'export',
        'color' => '#f59e0b'
    ],
    [
        'user' => 'DANIEL BACHILLER',
        'avatar' => 'D',
        'action' => 'generated monthly report for April 2025',
        'time' => 'Yesterday at 3:45 PM',
        'type' => 'report',
        'color' => '#8b5cf6'
    ],
    [
        'user' => 'AARIZ MARLOU',
        'avatar' => 'A',
        'action' => 'voided attendance record #12345 for system correction',
        'time' => 'Yesterday at 2:30 PM',
        'type' => 'delete',
        'color' => '#ef4444'
    ],
    [
        'user' => 'ELAINE AGUILAR',
        'avatar' => 'E',
        'action' => 'approved leave request for MARLON AGUILAR',
        'time' => 'Yesterday at 1:15 PM',
        'type' => 'approve',
        'color' => '#10b981'
    ],
    [
        'user' => 'ROLLY BALTAZAR',
        'avatar' => 'R',
        'action' => 'logged in from IP 192.168.1.100',
        'time' => 'Yesterday at 11:30 AM',
        'type' => 'login',
        'color' => '#6b7280'
    ],
    [
        'user' => 'SUPER ADMIN',
        'avatar' => 'SA',
        'action' => 'created new employee account for KYLE ARRIETA',
        'time' => '2 days ago',
        'type' => 'create',
        'color' => '#22c55e'
    ],
];

ob_start();
?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .page-title-section h1 {
        color: var(--text-primary);
        font-size: 1.5rem;
        margin-bottom: 6px;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .total-count {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 24px;
        text-align: right;
    }

    .total-label {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .total-value {
        color: var(--accent-color);
        font-size: 1.75rem;
        font-weight: 700;
        font-family: monospace;
    }

    .filter-section {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .search-row {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-box i {
        color: var(--text-secondary);
    }

    .search-box input {
        background: transparent;
        border: none;
        color: var(--text-primary);
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }

    .filter-dropdown {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 150px;
    }

    .time-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .time-pill {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-secondary);
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .time-pill:hover {
        border-color: var(--accent-color);
        color: var(--text-primary);
    }

    .time-pill.active {
        background: var(--accent-color);
        color: #000000;
        border-color: var(--accent-color);
        font-weight: 500;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-item {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--activity-color, var(--accent-color));
    }

    .activity-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-color), #FFA500);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000000;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-text {
        color: var(--text-primary);
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .activity-text strong {
        color: var(--accent-color);
        font-weight: 600;
    }

    .activity-time {
        color: var(--text-secondary);
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .pagination-bar {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .pagination-info strong {
        color: var(--accent-color);
    }

    .page-btns {
        display: flex;
        gap: 4px;
    }

    .page-btn {
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
        font-size: 0.875rem;
    }

    .page-btn.active {
        background: var(--accent-color);
        color: #000000;
        border-color: var(--accent-color);
    }

    .page-btn.nav {
        width: auto;
        padding: 0 12px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 16px;
        }
        .total-count {
            text-align: left;
            width: 100%;
        }
        .search-row {
            flex-direction: column;
        }
        .filter-dropdown {
            width: 100%;
        }
        .activity-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .activity-time {
            margin-left: 60px;
        }
        .pagination-bar {
            flex-direction: column;
            gap: 12px;
        }
    }
</style>

<div class="page-header">
    <div class="page-title-section">
        <h1>Activity Logs</h1>
        <div class="page-subtitle">View system activity and user actions</div>
    </div>
    <div class="total-count">
        <div class="total-label">Total Activities</div>
        <div class="total-value"><?= number_format($totalActivities) ?></div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <div class="search-row">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search activities...">
        </div>
        <div class="filter-dropdown">
            <span>All Activities</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
    <div class="time-filters">
        <?php foreach ($timeFilters as $filter): ?>
        <button class="time-pill <?= $filter['active'] ? 'active' : '' ?>"><?= $filter['label'] ?></button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Activity List -->
<div class="activity-list">
    <?php foreach ($activities as $activity): ?>
    <div class="activity-item" style="--activity-color: <?= $activity['color'] ?>">
        <div class="activity-avatar"><?= $activity['avatar'] ?></div>
        <div class="activity-content">
            <div class="activity-text">
                <strong><?= htmlspecialchars($activity['user']) ?></strong> <?= htmlspecialchars($activity['action']) ?>
            </div>
        </div>
        <div class="activity-time"><?= $activity['time'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div class="pagination-bar">
    <div class="pagination-info">
        Showing <strong>1</strong> to <strong>10</strong> of <strong><?= number_format($totalActivities) ?></strong> results
    </div>
    <div class="page-btns">
        <button class="page-btn">1</button>
        <button class="page-btn active">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn">...</button>
        <button class="page-btn">125</button>
        <button class="page-btn nav">Next <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></button>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
