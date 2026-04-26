<?php
/**
 * Main Router
 * St. Paul Chipata Portal
 */

require_once 'includes/session_manager.php';
SessionManager::start();
require_once 'config/db.php';

// Get the requested path
$path = isset($_GET['path']) ? $_GET['path'] : 'home';
$path = rtrim($path, '/');

// Simple routing logic
switch ($path) {
    case 'home':
    case '':
        require_once 'public/home.php';
        break;
    
    case 'login':
        require_once 'auth/login.php';
        break;

    case 'forgot_password':
        require_once 'auth/forgot_password.php';
        break;

    case 'reset_password':
        require_once 'auth/reset_password.php';
        break;

    case 'register':
        require_once 'auth/register.php';
        break;
        
    case 'logout':
        require_once 'auth/logout.php';
        break;
        
    case 'groups':
        require_once 'public/groups.php';
        break;
        
    case 'announcements':
        require_once 'public/announcements.php';
        break;
        
    case 'dashboard':
        require_once 'dashboard/index.php';
        break;
        
    case 'roq':
        require_once 'public/roq.php';
        break;

    case 'ministries':
        require_once 'public/ministries.php';
        break;
 
    case 'pledges':
        require_once 'public/pledges.php';
        break;
 
    case 'offertory':
         require_once 'public/offertory.php';
         break;
 
    case 'sunday_collection':
        require_once 'public/sunday_collection.php';
        break;
 
    case 'singing_cycle':
        require_once 'public/singing_cycle.php';
        break;
 
    case 'youth':
        require_once 'public/youth.php';
        break;
 
    case 'submit_announcement':
        require_once 'public/submit_announcement.php';
        break;
 
     case 'rosters':
        require_once 'public/rosters.php';
        break;
 
    case 'verify_event':
        require_once 'public/verify_event.php';
        break;
 
    case 'event_request':
        require_once 'public/event_request.php';
        break;

    case 'api/auth':
        require_once 'backend/auth_logic.php';
        break;

    default:
        // Check if it's a sub-path for dashboard or modules
        if (strpos($path, 'dashboard/') === 0) {
            require_once 'dashboard/index.php';
        } else {
            // 404 Page (Simple placeholder for now)
            http_response_code(404);
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for ($path) does not exist.</p>";
        }
        break;
}
?>
