<nav class="navbar">
    <?php $path = $path ?? ''; ?>
    <div class="nav-backdrop" id="nav-backdrop"></div>
    <div class="container navbar-content">
        <a href="<?php echo BASE_URL; ?>home" class="logo" style="display: flex; align-items: center; gap: 0.6rem; text-decoration: none;">
            <img src="<?php echo BASE_URL; ?>assets\images\logo\original_logo.jpeg" alt="St. Charles Lwanga Logo" style="height: 40px; width: auto; object-fit: contain;">
            <div style="display: flex; flex-direction: column; line-height: 1.1;">
                <span style="font-weight: 800; font-size: 1.25rem; letter-spacing: 0.5px;">
                    <span style="color: #ef4444;">ST CHARLES</span> <span style="color: #3b82f6;">LWANGA</span>
                </span>
                <span style="font-size: 0.75rem; font-weight: 600; color: #ef4444; letter-spacing: 0.5px; text-transform: uppercase;">REGIMENT PARISH</span>
            </div>
        </a>

        <div class="nav-links" id="nav-links">
            <div class="sheet-head">
                <div class="sheet-handle"></div>
                <button class="sheet-close" id="sheet-close" aria-label="Close menu"><i class="fas fa-times"></i></button>
            </div>

            <div class="menu-group">
                <p class="menu-label">Parish</p>
                <a href="<?php echo BASE_URL; ?>home" class="nav-link <?php echo ($path == 'home' || $path == '') ? 'active' : ''; ?>"><i class="m-ico fas fa-home"></i>Home</a>
                <a href="<?php echo BASE_URL; ?>announcements" class="nav-link <?php echo ($path == 'announcements') ? 'active' : ''; ?>"><i class="m-ico fas fa-bullhorn"></i>Announcements</a>
                <a href="<?php echo BASE_URL; ?>live-updates" class="nav-link <?php echo ($path == 'live-updates') ? 'active' : ''; ?>"><i class="m-ico fas fa-bolt"></i>Updates</a>
                <a href="<?php echo BASE_URL; ?>events" class="nav-link <?php echo ($path == 'events') ? 'active' : ''; ?>"><i class="m-ico fas fa-calendar-alt"></i>Events</a>
            </div>

            <div class="menu-group">
                <p class="menu-label">Worship &amp; Giving</p>
                <a href="<?php echo BASE_URL; ?>media" class="nav-link <?php echo ($path == 'media') ? 'active' : ''; ?>"><i class="m-ico fas fa-broadcast-tower"></i>Live Mass</a>
                <a href="<?php echo BASE_URL; ?>bulletins" class="nav-link <?php echo ($path == 'bulletins') ? 'active' : ''; ?>"><i class="m-ico fas fa-newspaper"></i>Bulletins</a>
                <a href="<?php echo BASE_URL; ?>giving" class="nav-link <?php echo ($path == 'giving') ? 'active' : ''; ?>"><i class="m-ico fas fa-hand-holding-heart"></i>Give</a>
            </div>

            <div class="menu-group">
                <p class="menu-label">Ministries &amp; More</p>
                <div class="dropdown">
                    <a href="#" id="ministries-trigger" class="nav-link <?php echo in_array($path, ['groups', 'ministries', 'youth', 'singing_cycle', 'rosters', 'event_request']) ? 'active' : ''; ?>">
                        <i class="m-ico fas fa-users"></i>Ministries <i class="fas fa-chevron-down chev" style="font-size: 0.7rem;"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?php echo BASE_URL; ?>ministries">Ministries Hub</a>
                        <a href="<?php echo BASE_URL; ?>groups">Parish Groups</a>
                        <a href="<?php echo BASE_URL; ?>youth">Youth Ministry</a>
                        <a href="<?php echo BASE_URL; ?>singing_cycle">Singing Cycle</a>
                        <a href="<?php echo BASE_URL; ?>rosters">Service Rosters</a>
                        <a href="<?php echo BASE_URL; ?>event_request">Request Event Notice</a>
                    </div>
                </div>

                <div class="dropdown">
                    <a href="#" id="finance-trigger" class="nav-link <?php echo in_array($path, ['offertory', 'pledges', 'sunday_collection', 'giving']) ? 'active' : ''; ?>">
                        <i class="m-ico fas fa-chart-pie"></i>Finance <i class="fas fa-chevron-down chev" style="font-size: 0.7rem;"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="<?php echo BASE_URL; ?>offertory">Offertory Log</a>
                        <a href="<?php echo BASE_URL; ?>pledges">Pledges</a>
                        <a href="<?php echo BASE_URL; ?>sunday_collection">Yearly Collection</a>
                        <a href="<?php echo BASE_URL; ?>giving">Give Online</a>
                    </div>
                </div>

                <a href="<?php echo BASE_URL; ?>contact" class="nav-link <?php echo ($path == 'contact') ? 'active' : ''; ?>"><i class="m-ico fas fa-envelope"></i>Contact</a>
            </div>

            <div class="menu-account">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>dashboard" class="btn btn-primary">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>logout" class="nav-link"><i class="m-ico fas fa-sign-out-alt"></i>Log Out</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login" class="nav-link <?php echo ($path == 'login') ? 'active' : ''; ?>"><i class="m-ico fas fa-sign-in-alt"></i>Login</a>
                    <a href="<?php echo BASE_URL; ?>join" class="btn btn-primary">Join Us</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="mobile-toggle" style="display: block; cursor: pointer; font-size: 1.5rem; color: var(--primary-color); z-index: 1001; position: relative;">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<style>
/* Hide toggle on desktop */
@media (min-width: 769px) {
    .mobile-toggle { display: none !important; }
}
</style>

<style>
/* Dropdown Styles */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 180px;
    box-shadow: var(--shadow-lg);
    border-radius: 0.5rem;
    padding: 0.5rem 0;
    z-index: 100;
    top: 100%;
}

.dropdown-content a {
    color: var(--text-main);
    padding: 0.75rem 1rem;
    text-decoration: none;
    display: block;
    font-size: 0.9rem;
    font-weight: 500;
}

.dropdown-content a:hover {
    background-color: var(--bg-main);
    color: var(--primary-color);
}

/* Desktop: open on hover + flatten menu wrappers. Mobile uses tap (.active) — see style.css */
@media (min-width: 769px) {
    .dropdown:hover .dropdown-content {
        display: block;
    }
    .menu-group, .menu-account {
        display: contents;
    }
    .menu-label, .m-ico {
        display: none;
    }
}
</style>
