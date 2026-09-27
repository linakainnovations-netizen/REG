USE st_paul_parish_portal_db;

-- Bulletins (Sunday bulletins / newsletters)
CREATE TABLE IF NOT EXISTS bulletins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    sunday_date DATE,
    status ENUM('draft','published') DEFAULT 'published',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Online giving: manual MoMo verification queue
CREATE TABLE IF NOT EXISTS giving_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    giver_name VARCHAR(100) NOT NULL,
    giver_phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('offertory','tithe','donation','thanksgiving','pledge') DEFAULT 'offertory',
    network ENUM('MTN','Airtel','Zamtel','other') DEFAULT 'MTN',
    txn_id VARCHAR(100) NOT NULL,
    status ENUM('pending','verified','rejected') DEFAULT 'pending',
    verified_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_txn (txn_id),
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Simple site settings (FB/YT links editable without code)
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('fb_page_url', 'https://www.facebook.com/profile.php?id=100067832423652'),
('yt_channel_url', 'https://www.youtube.com/'),
('yt_channel_id', ''),
('momo_mtn', '0975255734'),
('momo_airtel', '0975255734');
