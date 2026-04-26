<?php
require_once __DIR__ . '/../config/db.php';

$queries = [
    "ALTER TABLE roq ADD COLUMN IF NOT EXISTS author_id INT DEFAULT NULL",
    "ALTER TABLE roq MODIFY COLUMN status ENUM('pending', 'published', 'closed', 'awarded') DEFAULT 'pending'",
];

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "OK: $q\n";
    } catch (PDOException $e) {
        echo "SKIP/ERROR: " . $e->getMessage() . "\n";
    }
}

// Try to add FK only if it doesn't exist
try {
    $pdo->exec("ALTER TABLE roq ADD CONSTRAINT fk_roq_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL");
    echo "OK: FK added.\n";
} catch (PDOException $e) {
    echo "SKIP FK (may already exist): " . $e->getMessage() . "\n";
}

echo "Migration complete.\n";
