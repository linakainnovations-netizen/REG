-- St. Paul Chipata Portal Database Schema
-- Database: st_paul_parish_portal_db

CREATE DATABASE IF NOT EXISTS st_paul_parish_portal_db;
USE st_paul_parish_portal_db;

-- Roles Table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_level INT NOT NULL
);

INSERT INTO roles (role_name, role_level) VALUES 
('Super Admin', 1),
('Parish Priest', 2),
('Parish Council Chairperson', 3),
('Parish Council Secretary', 3),
('Parish Council Treasurer', 3),
('Youth Office Chairperson', 4),
('Youth Office Secretary', 4),
('Youth Office Treasurer', 4),
('Lay Group Leader', 5),
('SCC Leader', 5),
('Choir Leader', 5),
('Member', 10);

-- Groups Table
CREATE TABLE IF NOT EXISTS groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('lay_group', 'youth', 'elder', 'scc', 'choir') NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    member_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    custom_id VARCHAR(8) UNIQUE NOT NULL, -- Automated 8-char ID (4 letters + 4 numbers)
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    group_id INT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
);

-- Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT,
    language VARCHAR(10) DEFAULT 'en',
    category ENUM('general', 'youth', 'liturgy', 'finance') DEFAULT 'general',
    status ENUM('pending', 'youth_approved', 'council_approved', 'priest_approved', 'published', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Finance Table
CREATE TABLE IF NOT EXISTS finance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('offertory', 'tithe', 'donation', 'contribution') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    user_id INT,
    transaction_date DATE NOT NULL,
    description TEXT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tasks (Duties) Table
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_name VARCHAR(100) NOT NULL,
    group_id INT,
    assigned_date DATE NOT NULL,
    description TEXT,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
);

-- ROQ (Request for Quotation) Table
CREATE TABLE IF NOT EXISTS roq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    deadline DATE,
    status ENUM('open', 'closed', 'awarded') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ROQ Offers Table
CREATE TABLE IF NOT EXISTS roq_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roq_id INT,
    user_id INT,
    offer_details TEXT NOT NULL,
    quoted_price DECIMAL(10, 2),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (roq_id) REFERENCES roq(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Pledges Table
CREATE TABLE IF NOT EXISTS pledges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    paid_amount DECIMAL(10, 2) DEFAULT 0,
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'fulfilled', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
