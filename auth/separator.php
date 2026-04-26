<?php
/**
 * Login Separator (Traffic Controller)
 * Redirects users to their specific dashboard path based on role.
 */

require_once '../includes/session_manager.php';
SessionManager::start();
SessionManager::requireLogin('../login');

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

// Log the routing event (optional)
// error_log("Routing User ID {$_SESSION['user_id']} to /$roleFolder");

// Redirect to the role-specific landing page
header("Location: ../dashboard"); // dashboard/index.php will now handle the nested inclusion
exit;
?>
