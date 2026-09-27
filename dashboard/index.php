<?php
/**
 * Dashboard Router
 * Handles dynamic role-based content loading with nested architecture.
 */
require_once '../includes/session_manager.php';
SessionManager::start();
SessionManager::requireLogin('../login');
require_once '../config/db.php';

// Map role levels to folder names
$roleFolders = [
    1 => 'admin_priest',
    2 => 'admin_priest',
    3 => 'parish_council',
    4 => 'youth_council',
    5 => 'lay_groups',
    6 => 'choirs',
    7 => 'SCC_zonez',
    10 => 'members'
];

$roleFolder = $roleFolders[$_SESSION['role_level']] ?? 'members';
$basePath = __DIR__ . '/' . $roleFolder;

// Get the specific dashboard sub-page (e.g. overview, handover, users)
$subPath = $_GET['page'] ?? 'overview';
$roleSpecificPath = "$roleFolder/main_pages/$subPath.php";
$pageTitle = ucfirst(str_replace('_', ' ', $subPath));

// Check if role-specific structure exists
$hasStructuredRole = file_exists($basePath . '/main_pages/overview.php');

if ($hasStructuredRole) {
    if (!file_exists($basePath . "/main_pages/$subPath.php")) {
        $subPath = 'overview'; // Fallback
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?> | St. Charles Lwanga Regiment Portal</title>
        <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/other/saint_logo.png">
        <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/other/saint_logo.png">
        
        <!-- Global/Base Styles -->
        <?php if (file_exists($basePath . '/assets/main/dashboard-base.css')): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/main/dashboard-base.css">
        <?php else: ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
        <?php endif; ?>
        <!-- Role Specific Styles -->
        <?php if (file_exists($basePath . '/assets/main/head-side.css')): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/main/head-side.css">
        <?php endif; ?>
        <?php if (file_exists($basePath . '/assets/main/main.css')): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/main/main.css">
        <?php endif; ?>

        <!-- Page Specific Styles -->
        <?php if (file_exists($basePath . "/assets/pages_styles/$subPath.css")): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/pages_styles/<?php echo $subPath; ?>.css">
        <?php endif; ?>
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts -->
        <!-- Charts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="dashboard-body">
        
        <div class="dashboard-wrapper">
            <!-- Role Specific Sidebar -->
            <?php 
            if (file_exists($basePath . '/siders_pages/sidebar.php')) {
                include_once $roleFolder . '/siders_pages/sidebar.php';
            }
            ?>

            <main class="dashboard-main">
                <!-- Role Specific Header -->
                <?php 
                if (file_exists($basePath . '/siders_pages/header.php')) {
                    include_once $roleFolder . '/siders_pages/header.php';
                }
                ?>

                <div class="content-inner">
                    <?php include_once $roleFolder . "/main_pages/$subPath.php"; ?>
                </div>
            </main>
        </div>

        <!-- Scripts -->
        <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
        <!-- Layout Scripts -->
        <?php if (file_exists($basePath . '/assets/main/head-side.js')): ?>
            <script src="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/main/head-side.js"></script>
        <?php endif; ?>
        <?php if (file_exists($basePath . '/assets/main/main.js')): ?>
            <script src="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/main/main.js"></script>
        <?php endif; ?>

        <!-- Page Specific Scripts -->
        <?php if (file_exists($basePath . "/assets/pages_scripts/$subPath.js")): ?>
            <script src="<?php echo BASE_URL; ?>dashboard/<?php echo $roleFolder; ?>/assets/pages_scripts/<?php echo $subPath; ?>.js"></script>
        <?php endif; ?>
    </body>
    </html>
    <?php
} else {
    // Fallback to generic layout
    $pageTitle = "Dashboard Overview";
    include_once '../includes/header.php';
    ?>
    <div class="container mt-4 mb-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
        <div class="card">
            <p>Your dashboard is being set up. Please contact the administrator.</p>
        </div>
    </div>
    <?php
    include_once '../includes/footer.php';
}
?>
