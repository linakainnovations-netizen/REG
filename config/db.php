<?php
/**
 * Database Configuration
 * St. Paul Chipata Portal
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'st_paul_parish_portal_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL configuration for root-relative paths
define('BASE_URL', '/St._Paul_Chipata_Portal/');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // In production, log error and show generic message
    die("Database Connection Failed: " . $e->getMessage());
}

// Function to get the PDO instance
function getDB() {
    global $pdo;
    return $pdo;
}
?>
