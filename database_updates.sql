USE st_paul_parish_portal_db;

-- Communication Messages Table
CREATE TABLE IF NOT EXISTS group_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_group_id INT, -- NULL if global
    receiver_role_id INT,  -- NULL if global
    subject VARCHAR(255),
    message TEXT NOT NULL,
    attachment_path VARCHAR(255), -- For PDFs sent to leaders
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- PDF Document Tracking Table
CREATE TABLE IF NOT EXISTS generated_docs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    generated_by INT,
    assigned_group_id INT,
    doc_type ENUM('thanksgiving_list', 'duty_roster', 'financial_report', 'other') DEFAULT 'other',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_group_id) REFERENCES groups(id) ON DELETE CASCADE
);

-- Public Event Requests Table
CREATE TABLE IF NOT EXISTS public_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('funeral', 'memorial', 'wedding', 'event') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requested_by_email VARCHAR(100) NOT NULL,
    requested_group_id INT, -- To which group it belongs (e.g. Choir for singing)
    status ENUM('pending', 'verified', 'published', 'rejected') DEFAULT 'pending',
    leader_token VARCHAR(64) UNIQUE, -- For email verification link
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requested_group_id) REFERENCES groups(id) ON DELETE CASCADE
);

-- New columns for Roles/Groups if needed (already mostly covered)
-- Added extra Rosters to existing tasks or separate table?
-- Let's use tasks for Rosters (Sweeping, Singing, Sunday Roster).
