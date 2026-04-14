<?php
$title = 'Payroll';
$currentPage = 'payroll';
ob_start();
?>

<style>
    .coming-soon-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 50vh;
        text-align: center;
    }

    .coming-soon-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--accent-color), #FFA500);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
    }

    .coming-soon-icon i {
        font-size: 2.5rem;
        color: #000000;
    }

    .coming-soon-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .coming-soon-text {
        color: var(--text-secondary);
        font-size: 1rem;
        max-width: 400px;
        line-height: 1.6;
    }
</style>

<div class="coming-soon-container">
    <div class="coming-soon-icon">
        <i class="fas fa-money-check-alt"></i>
    </div>
    <h1 class="coming-soon-title">Payroll</h1>
    <p class="coming-soon-text">This feature is coming soon. You'll be able to manage employee salaries, process payroll, and generate payslips here.</p>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/finance_layout.php'; ?>
