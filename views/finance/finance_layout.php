<?php
// Finance submenu layout
$financeMenu = [
    ['label' => 'Payroll', 'icon' => 'fa-money-check-alt', 'url' => '/jajr-v2/finance/payroll', 'id' => 'payroll'],
    ['label' => 'Overtime', 'icon' => 'fa-clock', 'url' => '/jajr-v2/finance/overtime', 'id' => 'overtime'],
    ['label' => 'Cash Advance', 'icon' => 'fa-hand-holding-usd', 'url' => '/jajr-v2/finance/cash-advance', 'id' => 'cash-advance'],
    ['label' => 'Billing', 'icon' => 'fa-file-invoice-dollar', 'url' => '/jajr-v2/finance/billing', 'id' => 'billing'],
];
?>

<style>
    .finance-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 24px;
    }

    .finance-sidebar {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        height: fit-content;
    }

    .finance-menu-title {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding: 0 12px;
    }

    .finance-menu {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .finance-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .finance-menu-item:hover {
        background: rgba(255, 215, 0, 0.1);
        color: var(--accent-color);
    }

    .finance-menu-item.active {
        background: var(--accent-color);
        color: #000000;
        font-weight: 500;
    }

    .finance-menu-item i {
        width: 20px;
        text-align: center;
    }

    .finance-content {
        min-width: 0;
    }

    @media (max-width: 768px) {
        .finance-layout {
            grid-template-columns: 1fr;
        }
        .finance-sidebar {
            order: -1;
        }
        .finance-menu {
            flex-direction: row;
            flex-wrap: wrap;
        }
        .finance-menu-item {
            flex: 1;
            min-width: 140px;
            justify-content: center;
        }
    }
</style>

<div class="finance-layout">
    <aside class="finance-sidebar">
        <div class="finance-menu-title">Finance Menu</div>
        <nav class="finance-menu">
            <?php foreach ($financeMenu as $item): ?>
            <a href="<?= $item['url'] ?>" class="finance-menu-item <?= isset($currentPage) && $currentPage === $item['id'] ? 'active' : '' ?>">
                <i class="fas <?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <div class="finance-content">
        <?= $content ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/main.php'; ?>
