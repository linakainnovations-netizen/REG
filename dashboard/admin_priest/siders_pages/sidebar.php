<?php
/**
 * Premium Role-Specific Sidebar: Admin/Priest
 * St. Paul Chipata Portal
 */
?>
<aside class="dashboard-sidebar-premium">
    <a href="overview" class="sidebar-brand">
        <div class="brand-icon">
            <img src="<?php echo BASE_URL; ?>assets/images/other/main_logo.png" alt="St. Paul Logo">
        </div>
        <div class="brand-text">
            <span>St. Paul</span>
            <small>Priest Portal</small>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="nav-section">Management</div>
        <a href="overview" class="nav-item <?php echo $subPath == 'overview' ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> <span>Overview</span>
        </a>
        <a href="users" class="nav-item <?php echo $subPath == 'users' || $subPath == 'invite_leader' ? 'active' : ''; ?>">
            <i class="fas fa-user-friends"></i> <span>User Directory</span>
        </a>
        <a href="groups" class="nav-item <?php echo $subPath == 'groups' ? 'active' : ''; ?>">
            <i class="fas fa-hands-helping"></i> <span>Parish Groups</span>
        </a>
        <a href="announcements" class="nav-item <?php echo $subPath == 'announcements' ? 'active' : ''; ?>">
            <i class="fas fa-megaphone"></i> <span>Announcements</span>
        </a>
        
        <div class="nav-section">Finance & ROQ</div>
        <a href="finance" class="nav-item <?php echo $subPath == 'finance' ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i> <span>Collections</span>
        </a>
        <a href="roq" class="nav-item <?php echo $subPath == 'roq' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice-dollar"></i> <span>Procurement</span>
        </a>

        <div class="nav-section">System</div>
        <a href="profile" class="nav-item <?php echo $subPath == 'profile' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i> <span>My Profile</span>
        </a>
        <a href="handover" class="nav-item <?php echo $subPath == 'handover' ? 'active' : ''; ?>">
            <i class="fas fa-handshake"></i> <span>Handover</span>
        </a>
        <a href="settings" class="nav-item <?php echo $subPath == 'settings' ? 'active' : ''; ?>">
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

