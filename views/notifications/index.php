<?php
$title = 'Notifications';
ob_start();
?>

<style>
    .coming-soon-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
    }

    .coming-soon-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--accent-color), #FFA500);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 32px;
    }

    .coming-soon-icon i {
        font-size: 3rem;
        color: #000000;
    }

    .coming-soon-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
    }

    .coming-soon-text {
        color: var(--text-secondary);
        font-size: 1.1rem;
        max-width: 500px;
        line-height: 1.6;
    }
</style>

<div class="coming-soon-container">
    <div class="coming-soon-icon">
        <i class="fas fa-bell"></i>
    </div>
    <h1 class="coming-soon-title">Notifications</h1>
    <p class="coming-soon-text">This feature is coming soon. You'll be able to view and manage all your notifications here.</p>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
