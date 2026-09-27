<?php
/**
 * Premium Role-Specific Header: Admin/Priest
 * St. Paul Chipata Portal
 */
?>
<header class="dashboard-header-premium">
    <div class="header-left">
        <img src="../assets/images/logo/original_logo.jpeg" alt="Logo" style="height: 45px; width: auto; margin-right: 1rem;">
        <button class="menu-toggle"><i class="fas fa-indent"></i></button>
        <div class="breadcrumb">
            <span class="text-muted">Dashboard</span>
            <i class="fas fa-chevron-right mx-2" style="font-size: 0.7rem;"></i>
            <span class="breadcrumb-active"><?php echo $pageTitle ?? 'Overview'; ?></span>
        </div>
    </div>

    <div class="header-right">
        <!-- Search Bar -->
        <div class="header-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search records...">
        </div>

        <!-- Notifications Indicator -->
        <div class="icon-btn notification-hub">
            <i class="far fa-bell"></i>
            <span class="pulse-dot"></span>
        </div>

        <!-- User Profile Dropdown -->
        <div class="user-profile-widget">
            <div class="user-info text-right">
                <p class="name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                <p class="role"><?php echo $_SESSION['role_name']; ?></p>
            </div>
            <div class="user-avatar-premium">
                <?php echo substr($_SESSION['full_name'], 0, 1); ?>
            </div>
        </div>
    </div>
</header>

<style>
.dashboard-header-premium {
    background: white;
    height: 80px;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    position: sticky;
    top: 0;
    z-index: 40;
}

.header-left, .header-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.menu-toggle {
    background: #f1f5f9;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    color: var(--primary-color);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.breadcrumb {
    font-size: 0.9rem;
    font-weight: 500;
}

.breadcrumb-active {
    font-weight: 700;
    color: var(--text-main);
}

.header-search {
    position: relative;
    width: 280px;
}

.header-search i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.header-search input {
    width: 100%;
    padding: 0.6rem 1rem 0.6rem 2.8rem;
    background: #f8fafc;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.header-search input:focus {
    background: white;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    outline: none;
}

.icon-btn {
    width: 44px;
    height: 44px;
    background: #f8fafc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: var(--text-main);
    cursor: pointer;
    position: relative;
    border: 1px solid var(--border-color);
}

.pulse-dot {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
}

.user-profile-widget {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-left: 1.5rem;
    border-left: 1px solid var(--border-color);
    cursor: pointer;
}

.user-info .name {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-main);
}

.user-info .role {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.user-avatar-premium {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}
</style>
