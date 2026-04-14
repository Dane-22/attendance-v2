<?php
$title = 'Site Attendance';
$projects = [
    ['name' => 'BCDA - Admin', 'description' => 'Deploy employees to this project for attendance.'],
    ['name' => 'BCDA - CCA', 'description' => 'Deploy employees to this project for attendance.'],
    ['name' => 'BCDA - CCTV', 'description' => 'Deploy employees to this project for attendance.'],
    ['name' => 'BCDA - Control Tower', 'description' => 'Deploy employees to this project for attendance.'],
    ['name' => 'BCDA - Fence', 'description' => 'Deploy employees to this project for attendance.'],
    ['name' => 'BCDA - Fire Station', 'description' => 'Deploy employees to this project for attendance.'],
];

ob_start();
?>

<style>
    .welcome-banner {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }

    .welcome-banner h1 {
        color: var(--accent-color);
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--accent-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary {
        background: var(--accent-color);
        color: #000000;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    .search-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        width: 300px;
        font-size: 0.875rem;
    }

    .search-box::placeholder {
        color: var(--text-secondary);
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .project-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        transition: border-color 0.2s;
    }

    .project-card:hover {
        border-color: var(--accent-color);
    }

    .project-card.selected {
        border-color: var(--accent-color);
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.05), var(--bg-secondary));
    }

    .project-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ef4444;
        color: white;
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-name {
        font-weight: 600;
        color: var(--accent-color);
        font-size: 0.95rem;
        margin-bottom: 8px;
    }

    .project-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        line-height: 1.4;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stat-value {
        color: var(--accent-color);
        font-size: 2rem;
        font-weight: 700;
        font-family: monospace;
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }

    .filter-tab {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-tab.available {
        background: var(--accent-color);
        color: #000000;
    }

    .filter-tab:not(.available) {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .filter-tab:not(.available):hover {
        color: var(--text-primary);
    }

    .employee-search-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .employee-search-row .search-box {
        flex: 1;
    }

    .btn-undo {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .employee-placeholder {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        color: var(--text-secondary);
    }

    .employee-placeholder i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .quick-tips {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin-top: 24px;
    }

    .quick-tips h3 {
        color: var(--accent-color);
        font-size: 1rem;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tips-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    .tip-item {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .tip-item strong {
        color: var(--accent-color);
    }

    .pagination {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .pagination-btn.active {
        background: var(--accent-color);
        color: #000000;
        border-color: var(--accent-color);
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .tips-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .tips-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .search-box {
            width: 100%;
        }
        .section-header {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <h1>Welcome! Please select a project to start!</h1>
</div>

<!-- Projects Section -->
<div class="section-header">
    <span class="section-title">SELECT DEPLOYMENT PROJECT</span>
    <button class="btn-primary">
        <i class="fas fa-plus"></i> Add Project
    </button>
</div>

<div class="search-box" style="margin-bottom: 16px; width: 100%;">
    <input type="text" placeholder="Search projects..." style="background: transparent; border: none; color: var(--text-primary); width: 100%; outline: none;">
</div>

<div class="projects-grid">
    <?php foreach ($projects as $index => $project): ?>
    <div class="project-card <?= $index === 0 ? 'selected' : '' ?>">
        <button class="project-close">×</button>
        <div class="project-name"><?= htmlspecialchars($project['name']) ?></div>
        <div class="project-desc"><?= htmlspecialchars($project['description']) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
    <div class="pagination">
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn">2</button>
        <button class="pagination-btn">3</button>
        <button class="pagination-btn" style="width: auto; padding: 0 12px;">...</button>
        <button class="pagination-btn" style="width: auto; padding: 0 12px;">Next <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></button>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">TOTAL WORKERS</div>
        <div class="stat-value">--</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">PRESENT</div>
        <div class="stat-value">--</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">ABSENT</div>
        <div class="stat-value">--</div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <button class="filter-tab available">Available</button>
    <button class="filter-tab">Summary</button>
    <button class="filter-tab">Present</button>
    <button class="filter-tab">Absent</button>
</div>

<!-- Employee Search -->
<div class="employee-search-row">
    <input type="text" class="search-box" placeholder="Search employees by name or ID...">
    <button class="btn-undo">
        <i class="fas fa-undo"></i> Undo
    </button>
</div>

<!-- Employee Placeholder -->
<div class="employee-placeholder">
    <i class="fas fa-users"></i>
    <p>Please select a deployment project to view all available employees</p>
</div>

<!-- Quick Tips -->
<div class="quick-tips">
    <h3><i class="fas fa-lightbulb"></i> Quick Tips</h3>
    <div class="tips-grid">
        <div class="tip-item">
            <strong>Select a Project:</strong> You must select a deployment project first to view and manage its employees.
        </div>
        <div class="tip-item">
            <strong>Marking Attendance:</strong> Use the <strong>Time In</strong> and <strong>Mark Absent</strong> buttons to record daily attendance.
        </div>
        <div class="tip-item">
            <strong>Search:</strong> You can search for specific employees within the selected project by name or ID.
        </div>
        <div class="tip-item">
            <strong>Filters:</strong> Use the status pills (Available, Present, etc.) to quickly organize your view.
        </div>
        <div class="tip-item">
            <strong>Undo:</strong> If you make a mistake, look for the <strong>"Undo"</strong> button in the right side of the employee search.
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
