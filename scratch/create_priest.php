<?php
/**
 * Setup Script: Create Parish Priest Account
 * This is a one-time script to ensure at least one Priest exists.
 */
require_once __DIR__ . '/../config/db.php';

$username = 'priest';
$password = 'Password123'; // Default password
$fullName = 'Rev. Fr. Parish Priest';
$email = 'priest@stpaulchipata.org';
$role_id = 2; // Assuming 2 based on database.sql seed

// Check if Priest role exists and get its ID just in case
$stmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'Parish Priest'");
$stmt->execute();
$role = $stmt->fetch();
if ($role) {
    $role_id = $role['id'];
} else {
    echo "Error: 'Parish Priest' role not found in database.\n";
    exit;
}

// Check if user already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "Account already exists for username '$username'.\n";
    exit;
}

// Generate Custom ID (e.g. PRIE0001)
$custom_id = 'PRIE' . rand(1000, 9999);

// Hash Password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (custom_id, full_name, email, username, password, role_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$custom_id, $fullName, $email, $username, $hashedPassword, $role_id]);
    echo "SUCCESS: Parish Priest account created!\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
} catch (PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
?>
