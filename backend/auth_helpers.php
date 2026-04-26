<?php
/**
 * Authentication Helpers
 * Reusable functional utilities for user management.
 */

/**
 * Generate a unique Custom ID for new users (STP-XXXX)
 */
if (!function_exists('generateCustomId')) {
    function generateCustomId($pdo) {
        do {
            $id = "STP-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $pdo->prepare("SELECT id FROM users WHERE custom_id = ?");
            $stmt->execute([$id]);
        } while ($stmt->fetch());
        return $id;
    }
}
