<?php
$title = 'Notifications';

// Helper function for relative time
function getRelativeTime($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}

// Get icon based on type
function getTypeIcon($type) {
    $icons = [
        'attendance' => 'fa-clock',
        'payroll' => 'fa-dollar-sign',
        'system' => 'fa-cog',
        'leave' => 'fa-calendar-times',
        'default' => 'fa-bell'
    ];
    return $icons[$type] ?? $icons['default'];
}

// Get color based on type
function getTypeColor($type) {
    $colors = [
        'attendance' => '#3b82f6',
        'payroll' => '#22c55e',
        'system' => '#6b7280',
        'leave' => '#f97316',
        'default' => '#9ca3af'
    ];
    return $colors[$type] ?? $colors['default'];
}

ob_start();
?>

<style>
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .notifications-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .notifications-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-mark-all {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .btn-mark-all:hover {
        background: var(--accent-color);
        color: #000;
        border-color: var(--accent-color);
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 12px;
    }

    .filter-tab {
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--text-secondary);
        transition: all 0.3s;
        text-decoration: none;
    }

    .filter-tab:hover {
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    .filter-tab.active {
        background: var(--accent-color);
        color: #000;
    }

    .filter-tab .badge {
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 6px;
    }

    .search-filter {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--accent-color);
    }

    .type-filter {
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 0.9rem;
        cursor: pointer;
    }

    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .notification-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .notification-card:hover {
        border-color: var(--accent-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .notification-card.unread {
        background: rgba(37, 99, 235, 0.05);
        border-left: 3px solid var(--accent-color);
    }

    .notification-icon-large {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-content-large {
        flex: 1;
        min-width: 0;
    }

    .notification-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 4px;
    }

    .notification-title-large {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .notification-time-large {
        font-size: 0.8rem;
        color: var(--text-secondary);
        flex-shrink: 0;
    }

    .notification-message-large {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .notification-meta {
        display: flex;
        gap: 12px;
        align-items: center;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .notification-type-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .notification-actions-row {
        display: flex;
        gap: 12px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .notification-card:hover .notification-actions-row {
        opacity: 1;
    }

    .btn-mark-read {
        background: none;
        border: none;
        color: var(--accent-color);
        cursor: pointer;
        font-size: 0.8rem;
        padding: 0;
    }

    .btn-mark-read:hover {
        text-decoration: underline;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 32px;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .pagination a {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .pagination a:hover {
        border-color: var(--accent-color);
    }

    .pagination span {
        background: var(--accent-color);
        color: #000;
    }

    @media (max-width: 768px) {
        .notification-actions-row {
            opacity: 1;
        }

        .notification-header-row {
            flex-direction: column;
        }
    }
</style>

<div class="notifications-header">
    <h1 class="notifications-title">Notifications</h1>
    <div class="notifications-actions">
        <button class="btn-mark-all" onclick="markAllAsRead()">
            <i class="fas fa-check-double"></i> Mark all as read
        </button>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="?" class="filter-tab <?= empty($filters['is_read']) && empty($filters['type']) ? 'active' : '' ?>">
        All
    </a>
    <a href="?is_read=0" class="filter-tab <?= isset($filters['is_read']) && $filters['is_read'] === 0 ? 'active' : '' ?>">
        Unread
        <?php if ($unread_count > 0): ?>
            <span class="badge"><?= $unread_count ?></span>
        <?php endif; ?>
    </a>
    <a href="?is_read=1" class="filter-tab <?= isset($filters['is_read']) && $filters['is_read'] === 1 ? 'active' : '' ?>">
        Read
    </a>
</div>

<!-- Search and Type Filter -->
<form method="GET" class="search-filter">
    <input type="hidden" name="is_read" value="<?= isset($filters['is_read']) ? $filters['is_read'] : '' ?>">
    <input type="text" name="search" class="search-input" placeholder="Search notifications..." value="<?= isset($filters['search']) ? htmlspecialchars($filters['search']) : '' ?>">
    <select name="type" class="type-filter" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="attendance" <?= isset($filters['type']) && $filters['type'] === 'attendance' ? 'selected' : '' ?>>Attendance</option>
        <option value="payroll" <?= isset($filters['type']) && $filters['type'] === 'payroll' ? 'selected' : '' ?>>Payroll</option>
        <option value="system" <?= isset($filters['type']) && $filters['type'] === 'system' ? 'selected' : '' ?>>System</option>
    </select>
    <button type="submit" class="btn-mark-all">
        <i class="fas fa-search"></i> Search
    </button>
</form>

<!-- Notifications List -->
<div class="notifications-list">
    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h3>No notifications yet</h3>
            <p>When you receive notifications, they will appear here.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-card <?= $notification['is_read'] ? 'read' : 'unread' ?>" onclick="markAsReadAndNavigate(<?= $notification['id'] ?>, '<?= $notification['link'] ?>')">
                <div class="notification-icon-large" style="background: <?= getTypeColor($notification['type']) ?>20; color: <?= getTypeColor($notification['type']) ?>">
                    <i class="fas <?= getTypeIcon($notification['type']) ?>"></i>
                </div>
                <div class="notification-content-large">
                    <div class="notification-header-row">
                        <div class="notification-title-large"><?= htmlspecialchars($notification['title']) ?></div>
                        <div class="notification-time-large"><?= getRelativeTime($notification['created_at']) ?></div>
                    </div>
                    <div class="notification-message-large"><?= htmlspecialchars($notification['message']) ?></div>
                    <div class="notification-meta">
                        <span class="notification-type-badge" style="background: <?= getTypeColor($notification['type']) ?>20; color: <?= getTypeColor($notification['type']) ?>">
                            <?= ucfirst($notification['type']) ?>
                        </span>
                        <?php if (!$notification['is_read']): ?>
                            <span style="color: #ef4444; font-size: 0.7rem;">● Unread</span>
                        <?php endif; ?>
                        <?php if (!$notification['is_read']): ?>
                            <span class="notification-actions-row">
                                <button class="btn-mark-read" onclick="event.stopPropagation(); markAsRead(<?= $notification['id'] ?>)">
                                    Mark as read
                                </button>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?= isset($filters['is_read']) ? '&is_read=' . $filters['is_read'] : '' ?><?= isset($filters['type']) ? '&type=' . $filters['type'] : '' ?><?= isset($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
                <span><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?><?= isset($filters['is_read']) ? '&is_read=' . $filters['is_read'] : '' ?><?= isset($filters['type']) ? '&type=' . $filters['type'] : '' ?><?= isset($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?><?= isset($filters['is_read']) ? '&is_read=' . $filters['is_read'] : '' ?><?= isset($filters['type']) ? '&type=' . $filters['type'] : '' ?><?= isset($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    const baseUrl = '<?= dirname($_SERVER['SCRIPT_NAME']) ?>';

    function markAsRead(id) {
        fetch(baseUrl + '/api/notifications/mark-read/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAsReadAndNavigate(id, link) {
        fetch(baseUrl + '/api/notifications/mark-read/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && link) {
                window.location.href = baseUrl + link;
            } else if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAllAsRead() {
        fetch(baseUrl + '/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
