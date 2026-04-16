<?php
$title = 'Branches';
ob_start();
?>

<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .btn-add {
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

    .btn-add:hover {
        opacity: 0.9;
    }

    .search-section {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .search-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        width: 100%;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-box input {
        background: transparent;
        border: none;
        color: var(--text-primary);
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }

    .search-box i {
        color: var(--text-secondary);
    }

    .section-title {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
    }

    .branch-list {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .branch-card {
        display: grid;
        grid-template-columns: auto 1fr auto auto auto;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s;
    }

    .branch-card:last-child {
        border-bottom: none;
    }

    .branch-card:hover {
        background: rgba(255, 215, 0, 0.05);
    }

    .branch-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--accent-color);
        color: #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .branch-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .branch-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .branch-address {
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .branch-code {
        color: var(--accent-color);
        font-size: 0.875rem;
        font-weight: 600;
        font-family: monospace;
        padding: 4px 12px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 6px;
    }

    .branch-status {
        color: #22c55e;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .branch-status.inactive {
        color: #ef4444;
    }

    .branch-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        background: var(--bg-primary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--accent-color);
        color: #000000;
    }

    @media (max-width: 768px) {
        .branch-card {
            grid-template-columns: auto 1fr auto;
            gap: 12px;
        }
        .branch-code, .branch-status {
            display: none;
        }
    }
</style>

<div class="page-header">
    <div class="page-header-row">
        <div>
            <div class="page-subtitle">Manage branch locations</div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 8px;">
                Total Branches: <strong style="color: var(--accent-color);"><?= $totalBranches ?></strong>
            </div>
        </div>
        <button class="btn-add" onclick="openAddBranchModal()">
            <i class="fas fa-plus"></i> Add Branch
        </button>
    </div>
</div>

<!-- Search Section -->
<div class="search-section">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search branches by name or code...">
    </div>
</div>

<!-- Branch List -->
<div class="section-title">Existing Branches</div>

<div class="branch-list">
    <?php foreach ($branches as $branch): ?>
    <div class="branch-card">
        <div class="branch-avatar">
            <?= htmlspecialchars($branch['branch_code']) ?>
        </div>
        <div class="branch-info">
            <div class="branch-name"><?= htmlspecialchars($branch['branch_name']) ?></div>
            <?php if (!empty($branch['address'])): ?>
            <div class="branch-address"><?= htmlspecialchars($branch['address']) ?></div>
            <?php endif; ?>
        </div>
        <div class="branch-code"><?= htmlspecialchars($branch['branch_code']) ?></div>
        <div class="branch-status <?= $branch['status'] === 'Inactive' ? 'inactive' : '' ?>">
            <?= htmlspecialchars($branch['status']) ?>
        </div>
        <div class="branch-actions">
            <button class="action-btn" title="Edit" onclick="editBranch(<?= $branch['id'] ?>)"><i class="fas fa-edit"></i></button>
            <button class="action-btn" title="Delete" onclick="deleteBranch(<?= $branch['id'] ?>)"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    function openAddBranchModal() {
        alert('Add Branch modal coming soon');
    }

    function editBranch(id) {
        window.location.href = '/branches/edit/' + id;
    }

    function deleteBranch(id) {
        if (confirm('Are you sure you want to delete this branch?')) {
            window.location.href = '/branches/delete/' + id;
        }
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
