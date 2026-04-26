<?php
/**
 * Global Session Manager
 * St. Paul Chipata Portal
 */

class SessionManager {
    /**
     * Start session if not already started
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure cookie settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            
            session_start();

            // Run handover cleanup on every new session start (or periodic check)
            self::cleanupExpiredAdmins();
        }
    }

    /**
     * Automated Cleanup for Leadership Handovers
     * Deletes transitional admins after their 30-day grace period.
     */
    private static function cleanupExpiredAdmins() {
        global $pdo; // Assumes $pdo is available or needs to be included
        if (!isset($pdo)) {
            require_once __DIR__ . '/../config/db.php';
        }

        try {
            // Delete transitional admins whose expiry date has passed
            $stmt = $pdo->prepare("DELETE FROM users WHERE is_transitional = 1 AND expiry_date <= CURDATE()");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Handover Cleanup Error: " . $e->getMessage());
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login or redirect
     */
    public static function requireLogin($loginPath = 'login') {
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = "Access denied. Please sign in to continue.";
            header("Location: $loginPath");
            exit;
        }
    }

    /**
     * Require specific role level
     */
    public static function requireRole($maxLevel, $redirectPath = 'dashboard') {
        if (!isset($_SESSION['role_level']) || $_SESSION['role_level'] > $maxLevel) {
            $_SESSION['error'] = "You do not have permission to view that page.";
            header("Location: $redirectPath");
            exit;
        }
    }

    /**
     * Destroy session and logout
     */
    public static function logout($redirectPath = 'home') {
        self::start();
        
        // Unset all variables
        $_SESSION = array();

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Final destroy
        session_destroy();
        
        header("Location: $redirectPath");
        exit;
    }

    /**
     * Get current user data
     */
    public static function getUser() {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['full_name'] ?? 'Guest',
            'role' => $_SESSION['role_name'] ?? 'Guest',
            'level' => $_SESSION['role_level'] ?? 99,
            'portal_id' => $_SESSION['username'] ?? null
        ];
    }
}
?>
