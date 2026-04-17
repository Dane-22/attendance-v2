<?php
$title = 'Branch Device Login';
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
ob_start();
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        background: #1e1e1e;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 40px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .login-header .logo {
        font-size: 3rem;
        color: #ffd700;
        margin-bottom: 16px;
    }

    .login-header h1 {
        color: #ffffff;
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .login-header p {
        color: #888;
        font-size: 0.9rem;
    }

    .branch-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 215, 0, 0.1);
        border: 2px solid #ffd700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
    }

    .branch-icon i {
        font-size: 2.5rem;
        color: #ffd700;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: #ccc;
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 14px 16px;
        background: #2a2a2a;
        border: 1px solid #444;
        border-radius: 8px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #ffd700;
        background: #333;
    }

    .form-group select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffd700' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        padding-right: 40px;
    }

    .form-group select option {
        background: #2a2a2a;
        color: #fff;
    }

    .btn-login {
        width: 100%;
        padding: 16px;
        background: #ffd700;
        color: #000;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-login:hover {
        opacity: 0.9;
    }

    .error-message {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.875rem;
    }

    .back-link {
        text-align: center;
        margin-top: 24px;
    }

    .back-link a {
        color: #888;
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.2s;
    }

    .back-link a:hover {
        color: #ffd700;
    }

    @media (max-width: 480px) {
        .login-container {
            margin: 20px;
            padding: 32px 24px;
        }
    }
</style>

<div class="login-container">
    <div class="login-header">
        <div class="branch-icon">
            <i class="fas fa-building"></i>
        </div>
        <h1>Branch Device Login</h1>
        <p>Select your branch and sign in</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="error-message">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $baseUrl ?>/branch/login">
        <div class="form-group">
            <label for="branch_code">Select Branch</label>
            <select id="branch_code" name="branch_code" required>
                <option value="">-- Choose Branch --</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= htmlspecialchars($branch['branch_code']) ?>">
                    <?= htmlspecialchars($branch['branch_name']) ?> (<?= htmlspecialchars($branch['branch_code']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter password">
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In to Branch
        </button>
    </form>

    <div class="back-link">
        <a href="<?= $baseUrl ?>/login"><i class="fas fa-arrow-left"></i> Back to Admin Login</a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/auth.php'; ?>
