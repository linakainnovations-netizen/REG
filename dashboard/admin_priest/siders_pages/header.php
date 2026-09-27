<?php
/**
 * Premium Role-Specific Header: Admin/Priest
 * St. Paul Chipata Portal
 */
?>
<header class="dashboard-header-premium">
    <div class="header-left">
        <a href="overview" style="display: flex; align-items: center; text-decoration: none;">
            <img src="<?php echo BASE_URL; ?>assets/images/logo/original_logo.jpeg" alt="Logo" style="height: 45px; width: auto; margin-right: 1rem;">
        </a>
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

        <!-- User Profile Link -->
        <a href="profile" class="profile-link-wrapper" style="text-decoration: none; color: inherit;">
            <div class="user-profile-widget">
                <div class="user-info text-right">
                    <p class="name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <p class="role"><?php echo $_SESSION['role_name']; ?></p>
                </div>
                <div class="user-avatar-premium">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?php echo BASE_URL; ?>dashboard/admin_priest/assets/images/uploads/<?php echo $_SESSION['profile_image']; ?>" alt="Profile">
                    <?php else: ?>
                        <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
</header>

