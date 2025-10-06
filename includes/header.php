<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - <?php echo htmlspecialchars($pageTitle ?? 'Dashboard'); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <div class="header-content">
                <div class="logo-section">
                    <i class="fas fa-tasks logo-icon"></i>
                    <h1 class="logo-text">TaskFlow</h1>
                    <span class="version-badge">v3.0</span>
                </div>

                <nav class="main-nav">
                    <a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="analytics.php" class="nav-link <?php echo $current_page === 'analytics.php' ? 'active' : '';?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </a>
                </nav>

                <div class="header-actions">
                    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="user-widget">
                        <i class="fas fa-user-circle user-avatar"></i>
                        <span class="username">Developer</span>
                    </div>
                </div>
            </div>
        </header>
    <main class="main-content">
        <!-- Flash messages container -->
        <div id="flashMessages" class="flash-messages"></div>