<?php
/**
 * Premium Role-Specific Sidebar: Admin/Priest
 * St. Paul Chipata Portal
 */
?>
<aside class="dashboard-sidebar-premium">
    <div class="sidebar-brand">
        <div class="brand-icon" style="background: transparent; box-shadow: none;">
            <img src="../assets/images/logo/original_logo.jpeg" alt="Regiment Logo" style="height: 48px; width: auto;">
        </div>
        <div class="brand-text">
            <span>St. Charles Lwanga</span>
            <small>Choirs Portal</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Management</div>
        <a href="dashboard" class="nav-item active">
            <i class="fas fa-th-large"></i> <span>Overview</span>
        </a>
        <a href="users" class="nav-item">
            <i class="fas fa-user-friends"></i> <span>User Directory</span>
        </a>
        <a href="groups" class="nav-item">
            <i class="fas fa-hands-helping"></i> <span>Parish Groups</span>
        </a>
        <a href="announcements" class="nav-item">
            <i class="fas fa-megaphone"></i> <span>Announcements</span>
        </a>
        
        <div class="nav-section">Finance & ROQ</div>
        <a href="finance" class="nav-item">
            <i class="fas fa-chart-pie"></i> <span>Collections</span>
        </a>
        <a href="roq" class="nav-item">
            <i class="fas fa-file-invoice-dollar"></i> <span>Procurement</span>
        </a>

        <div class="nav-section">System</div>
        <a href="handover" class="nav-item">
            <i class="fas fa-handshake"></i> <span>Handover</span>
        </a>
        <a href="settings" class="nav-item">
            <i class="fas fa-cog"></i> <span>Portal Settings</span>
        </a>
        <a href="../logout" class="nav-item logout-item">
            <i class="fas fa-sign-out-alt"></i> <span>Log Out</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <p>V 1.2.0 Stable</p>
    </div>
</aside>

<style>
:root {
    --sidebar-bg: #0f172a;
    --sidebar-color: #94a3b8;
    --sidebar-active-bg: #1e293b;
    --sidebar-active-color: #f8fafc;
    --sidebar-accent: #3b82f6;
}

.dashboard-sidebar-premium {
    width: 280px;
    background: var(--sidebar-bg);
    color: var(--sidebar-color);
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: sticky;
    top: 0;
    padding: 1.5rem;
    box-shadow: 10px 0 30px rgba(0,0,0,0.1);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.brand-icon {
    width: 40px;
    height: 40px;
    background: var(--sidebar-accent);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}

.brand-text span {
    display: block;
    font-size: 1.25rem;
    font-weight: 800;
    color: white;
    line-height: 1;
}

.brand-text small {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    opacity: 0.6;
}

.sidebar-nav {
    flex-grow: 1;
    margin-top: 2rem;
}

.nav-section {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin: 1.5rem 0 0.75rem 0.75rem;
    opacity: 0.4;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1rem;
    border-radius: 0.75rem;
    color: var(--sidebar-color);
    font-weight: 500;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 0.25rem;
}

.nav-item i {
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

.nav-item:hover {
    background: var(--sidebar-active-bg);
    color: white;
    transform: translateX(5px);
}

.nav-item.active {
    background: var(--sidebar-accent);
    color: white;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
}

.logout-item:hover {
    background: #ef4444;
    color: white;
}

.sidebar-footer {
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.05);
    font-size: 0.75rem;
    text-align: center;
    opacity: 0.5;
}
</style>
