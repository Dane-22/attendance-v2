<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - " : "" ?>Attendance System</title>
    <link rel="stylesheet" href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/assets/css/universal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --text-primary: #333333;
            --text-secondary: #6b7280;
            --accent-color: #2563eb;
            --sidebar-width: 260px;
            --header-height: 70px;
            --border-color: #e5e7eb;
        }

        [data-theme="dark"] {
            --bg-primary: #000000;
            --bg-secondary: #1A1A1A;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-color: #FFD700;
            --border-color: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }

        /* Layout Container */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .nav-menu {
            padding: 20px 0;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-color), #FFA500);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-weight: bold;
        }

        .sidebar-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover,
        .nav-item.active {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), transparent);
            color: var(--accent-color);
            border-left-color: var(--accent-color);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .nav-badge {
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 20px;
        }

        .has-submenu {
            cursor: pointer;
        }

        .submenu-icon {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.3s;
        }

        .submenu-icon.open {
            transform: rotate(180deg);
        }

        .submenu {
            padding-left: 20px;
            display: none;
        }

        .submenu-item {
            display: block;
            padding: 10px 20px 10px 52px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            border-left: 2px solid transparent;
            transition: all 0.2s;
        }

        .submenu-item:hover {
            color: var(--accent-color);
            border-left-color: var(--accent-color);
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent);
        }

        .submenu-item.active {
            color: var(--accent-color);
            border-left-color: var(--accent-color);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            height: var(--header-height);
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Dark Mode Toggle */
        .theme-toggle {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 6px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            color: var(--accent-color);
        }

        /* Notification Bell */
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .notification-btn:hover {
            background: var(--bg-primary);
            color: var(--accent-color);
        }

        .notification-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .profile-btn:hover {
            background: var(--bg-primary);
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-color), #FFA500);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .profile-name {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 180px;
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: var(--bg-primary);
            color: var(--accent-color);
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 24px;
        }

        .container {
            width: 100%;
            max-width: 100%;
        }

        /* Card Styles - Theme Aware */
        .card {
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .card h2, .card h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        /* Table Styles - Theme Aware */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        td {
            color: var(--text-primary);
        }

        tr:hover {
            background-color: var(--bg-primary);
        }

        /* Footer */
        .footer {
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 20px 24px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .hamburger {
                display: block;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .profile-name {
                display: none;
            }

            .theme-toggle span {
                display: none;
            }

            .content-area {
                padding: 16px;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.show {
            display: block;
        }

        @media (min-width: 769px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <span class="sidebar-title">JAJR Company</span>
                    <small style="color: var(--text-secondary); font-size: 0.75rem; display: block;">Owned by Arcadius</small>
                </div>
            </div>
            <?php $baseUrl = dirname($_SERVER['SCRIPT_NAME']); ?>
            <nav class="nav-menu">
                <a href="<?= $baseUrl ?>/dashboard" class="nav-item <?= ($_SERVER["REQUEST_URI"] == "/" || strpos($_SERVER["REQUEST_URI"], "/dashboard") !== false) ? "active" : "" ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= $baseUrl ?>/attendance" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/attendance") !== false ? "active" : "" ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Site Attendance</span>
                </a>
                <a href="<?= $baseUrl ?>/notifications" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/notifications") !== false ? "active" : "" ?>">
                    <i class="fas fa-bell"></i>
                    <span>Notification</span>
                    <span class="nav-badge">12</span>
                </a>
                <a href="<?= $baseUrl ?>/employee" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/employee") !== false ? "active" : "" ?>">
                    <i class="fas fa-users"></i>
                    <span>Employee List</span>
                </a>
                <a href="<?= $baseUrl ?>/branches" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/branches") !== false ? "active" : "" ?>">
                    <i class="fas fa-building"></i>
                    <span>Branches</span>
                </a>
                <a href="<?= $baseUrl ?>/documents" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/documents") !== false ? "active" : "" ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
                <a href="<?= $baseUrl ?>/activity-logs" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/activity-logs") !== false ? "active" : "" ?>">
                    <i class="fas fa-list-alt"></i>
                    <span>Activity Logs</span>
                </a>
                <a href="<?= $baseUrl ?>/attendance-audit" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/attendance-audit") !== false ? "active" : "" ?>">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Attendance Audit</span>
                </a>
                <div class="nav-item has-submenu <?= strpos($_SERVER["REQUEST_URI"], "/finance") !== false ? "active" : "" ?>" onclick="toggleSubmenu('financeSubmenu')">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Finance</span>
                    <i class="fas fa-chevron-down submenu-icon" id="financeIcon"></i>
                </div>
                <div class="submenu" id="financeSubmenu" style="display: <?= strpos($_SERVER["REQUEST_URI"], "/finance") !== false ? 'block' : 'none' ?>;">
                    <a href="<?= $baseUrl ?>/finance/payroll" class="submenu-item <?= strpos($_SERVER["REQUEST_URI"], "/finance/payroll") !== false ? 'active' : '' ?>">Payroll</a>
                    <a href="<?= $baseUrl ?>/finance/overtime" class="submenu-item <?= strpos($_SERVER["REQUEST_URI"], "/finance/overtime") !== false ? 'active' : '' ?>">Overtime</a>
                    <a href="<?= $baseUrl ?>/finance/billing" class="submenu-item <?= strpos($_SERVER["REQUEST_URI"], "/finance/billing") !== false ? 'active' : '' ?>">Billing</a>
                    <a href="<?= $baseUrl ?>/finance/cash-advance" class="submenu-item <?= strpos($_SERVER["REQUEST_URI"], "/finance/cash-advance") !== false ? 'active' : '' ?>">Cash Advance</a>
                </div>
                <a href="<?= $baseUrl ?>/procurement" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/procurement") !== false ? "active" : "" ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Procurement</span>
                </a>
                <a href="<?= $baseUrl ?>/settings" class="nav-item <?= strpos($_SERVER["REQUEST_URI"], "/settings") !== false ? "active" : "" ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="<?= $baseUrl ?>/logout" class="nav-item" style="border-top: 1px solid var(--border-color); margin-top: auto;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?= isset($title) ? $title : "Dashboard" ?></h1>
                </div>
                <div class="header-right">
                    <!-- Date Display -->
                    <span class="header-date"><?= date('F j, Y H:i') ?></span>

                    <!-- Dark Mode Toggle -->
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Theme">
                        <i class="fas fa-sun" id="lightIcon"></i>
                        <i class="fas fa-moon hidden" id="darkIcon"></i>
                        <span>Theme</span>
                    </button>

                    <!-- Notification Bell -->
                    <button class="notification-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown">
                        <button class="profile-btn" onclick="toggleDropdown()">
                            <div class="profile-avatar">
                                <?= isset($_SESSION["admin_name"]) ? strtoupper(substr($_SESSION["admin_name"], 0, 1)) : "A" ?>
                            </div>
                            <span class="profile-name"><?= isset($_SESSION["admin_name"]) ? $_SESSION["admin_name"] : "Admin" ?></span>
                            <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 0.8rem;"></i>
                        </button>
                        <div class="dropdown-menu" id="profileDropdown">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                            <div style="border-top: 1px solid var(--border-color); margin: 4px 0;"></div>
                            <a href="<?= $baseUrl ?>/logout" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <div class="container">
                    <?php if (isset($_SESSION["success"])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION["success"]; unset($_SESSION["success"]); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION["error"])): ?>
                        <div class="alert alert-error">
                            <?= $_SESSION["error"]; unset($_SESSION["error"]); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?= $content ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <p>&copy; 2026 JAJR Attendance System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <script>
        // Toggle Sidebar (Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebarOverlay");
            sidebar.classList.toggle("open");
            overlay.classList.toggle("show");
        }

        // Toggle Profile Dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById("profileDropdown");
            dropdown.classList.toggle("show");
        }

        // Close dropdown when clicking outside
        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("profileDropdown");
            const profileBtn = event.target.closest(".profile-btn");
            if (!profileBtn && dropdown.classList.contains("show")) {
                dropdown.classList.remove("show");
            }
        });

        // Toggle Submenu
        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            const icon = document.getElementById(submenuId.replace('Submenu', 'Icon'));
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                if (icon) icon.classList.add('open');
            } else {
                submenu.style.display = 'none';
                if (icon) icon.classList.remove('open');
            }
        }

        // Dark Mode Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute("data-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";
            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            document.cookie = "theme=" + newTheme + ";path=/;max-age=" + (60*60*24*365);
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const lightIcon = document.getElementById("lightIcon");
            const darkIcon = document.getElementById("darkIcon");
            if (theme === "dark") {
                lightIcon.classList.add("hidden");
                darkIcon.classList.remove("hidden");
            } else {
                lightIcon.classList.remove("hidden");
                darkIcon.classList.add("hidden");
            }
        }

        // Load saved theme from localStorage (sync with cookie)
        const savedTheme = localStorage.getItem("theme") || "light";
        document.documentElement.setAttribute("data-theme", savedTheme);
        document.cookie = "theme=" + savedTheme + ";path=/;max-age=" + (60*60*24*365);
        updateThemeIcon(savedTheme);
    </script>
</body>
</html>
