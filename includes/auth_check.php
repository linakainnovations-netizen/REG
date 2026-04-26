<?php
/**
 * Auth Check Utility
 */
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page.";
    header('Location: login'); // Assuming 'login' route via index.php
    exit;
}

// Helper to check role level (lower is more powerful)
function checkMinRole($minLevel) {
    if ($_SESSION['role_level'] > $minLevel) {
        $_SESSION['error'] = "You do not have permission to access that section.";
        header('Location: dashboard');
        exit;
    }
}
?>
