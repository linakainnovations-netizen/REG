USE st_paul_parish_portal_db;

-- ============================================================
-- Public CMS + Extensible Member/Ministry architecture
-- Run once in phpMyAdmin. Safe: all CREATE IF NOT EXISTS.
-- ============================================================

-- 1) Parish events (Masses, fundraisers, meetings, feast days)
-- Distinct from public_requests (funeral/wedding notices needing verification)
CREATE TABLE IF NOT EXISTS parish_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    venue VARCHAR(255) DEFAULT 'Parish Church',
    event_date DATE NOT NULL,
    event_time VARCHAR(20) DEFAULT NULL,
    category ENUM('mass','meeting','fundraiser','feast','youth','choir','scc','other') DEFAULT 'other',
    cover_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','published','cancelled') DEFAULT 'published',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_date (event_date),
    INDEX idx_status (status)
);

-- 2) Live updates: short posts from leaders to cut Sunday verbal announcements
-- Priest, Parish Council, Youth Office, Lay Groups, Choirs, SCCs can post.
CREATE TABLE IF NOT EXISTS live_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    office VARCHAR(50) NOT NULL DEFAULT 'parish_council',
    message VARCHAR(500) NOT NULL,
    audience ENUM('all','youth','choirs','scc','lay_groups','leaders') DEFAULT 'all',
    author_id INT,
    is_pinned TINYINT(1) DEFAULT 0,
    status ENUM('published','hidden') DEFAULT 'published',
    expires_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status_created (status, created_at),
    INDEX idx_office (office)
);

-- 3) Ministries catalogue (extensible: add rows, no code change)
CREATE TABLE IF NOT EXISTS ministries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    leader_name VARCHAR(150) DEFAULT NULL,
    meeting_info VARCHAR(255) DEFAULT NULL,
    needs_volunteers TINYINT(1) DEFAULT 1,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO ministries (name, slug, description, meeting_info) VALUES
('Lectors & Readers', 'lectors', 'Proclaim the Word at Mass. Training provided.', 'Saturdays 10:00, Church'),
('Choir Ministry', 'choir', 'Lead worship through music across all Masses.', 'Per choir schedule'),
('Youth Ministry', 'youth', 'Faith, fellowship and service for young parishioners.', 'Sundays 14:00, Hall'),
('Altar Servers', 'altar-servers', 'Serve at the altar. Open to confirmed youth.', 'Saturdays 09:00'),
('Caritas & Outreach', 'caritas', 'Serve the poor, sick and elderly in our zone.', 'First Saturday monthly'),
('Media & Livestream', 'media', 'Cameras, sound, Facebook/YouTube live, bulletins.', 'Fridays 17:00'),
('Catechists (RCIA)', 'catechists', 'Prepare adults and children for sacraments.', 'Wednesdays 17:30'),
('Ushers & Welcomers', 'ushers', 'Welcome, seating, offertory and order at Mass.', 'Sundays 07:00');

-- 4) Ministry membership / volunteer signups
CREATE TABLE IF NOT EXISTS ministry_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ministry_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    user_id INT DEFAULT NULL,
    note VARCHAR(500) DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ministry_status (ministry_id, status)
);

-- 5) Volunteer slots for events (e.g. need 10 ushers for Easter Mass)
CREATE TABLE IF NOT EXISTS volunteer_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT DEFAULT NULL,
    ministry_id INT DEFAULT NULL,
    role_needed VARCHAR(150) NOT NULL,
    slots_total INT NOT NULL DEFAULT 5,
    slots_taken INT NOT NULL DEFAULT 0,
    slot_date DATE DEFAULT NULL,
    status ENUM('open','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES parish_events(id) ON DELETE CASCADE,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS volunteer_signups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    user_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (slot_id) REFERENCES volunteer_slots(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uniq_slot_phone (slot_id, phone)
);

-- 6) Giving: add gateway tracking to existing manual table (no rebuild)
SET @has_provider := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'giving_transactions' AND COLUMN_NAME = 'provider');
SET @sql := IF(@has_provider = 0,
    'ALTER TABLE giving_transactions ADD COLUMN provider VARCHAR(30) DEFAULT ''momo_manual'', ADD COLUMN provider_ref VARCHAR(150) DEFAULT NULL, ADD COLUMN currency VARCHAR(10) DEFAULT ''ZMW''',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7) Site settings keys for gateways + live
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('flutterwave_pub_key', ''),
('flutterwave_secret_key', ''),
('flutterwave_enabled', '0'),
('dpo_company_token', ''),
('dpo_enabled', '0'),
('mass_times', 'Sun 06:30, 09:00, 11:00 | Sat 17:00 | Daily 06:00, 17:30'),
('office_hours', 'Mon-Fri 08:00-16:00, Sat 08:00-12:00'),
('live_updates_enabled', '1');
