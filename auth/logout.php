<?php
/**
 * Logout Page
 * Uses SessionManager for secure cleanup
 */
require_once '../includes/session_manager.php';
SessionManager::logout('../home');
?>
