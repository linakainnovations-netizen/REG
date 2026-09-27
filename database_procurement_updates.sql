USE st_paul_parish_portal_db;

-- ============================================================
-- Procurement ownership: Parish Council
-- Run once in phpMyAdmin. Safe: guards + CREATE IF NOT EXISTS.
-- ============================================================

-- roq needs an author (code already inserts author_id)
SET @has_author := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'roq' AND COLUMN_NAME = 'author_id');
SET @sql := IF(@has_author = 0,
    'ALTER TABLE roq ADD COLUMN author_id INT DEFAULT NULL, ADD FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Vendor/member quotes on published ROQs
CREATE TABLE IF NOT EXISTS roq_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roq_id INT NOT NULL,
    user_id INT NOT NULL,
    quoted_amount DECIMAL(10,2) NOT NULL,
    proposal_text TEXT NOT NULL,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (roq_id) REFERENCES roq(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_roq (roq_id)
);
