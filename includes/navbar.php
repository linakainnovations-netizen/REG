<nav class="navbar">
    <div class="container navbar-content">
        <a href="home" class="logo" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <img src="assets/images/other/main_logo.png" alt="St. Paul Logo" style="height: 48px; width: auto; object-fit: contain; margin-bottom: 2px;">
            <div style="display: flex; flex-direction: column; line-height: 1.1;">
                <span style="font-weight: 800; font-size: 1.25rem; letter-spacing: 0.5px;">
                    <span style="color: #ef4444;">SAINT</span> <span style="color: #3b82f6;">PAUL</span>
                </span>
                <span style="font-size: 0.75rem; font-weight: 600; color: #ef4444; letter-spacing: 0.5px; text-transform: uppercase;">CHIPATA COMPOUND</span>
            </div>
        </a>
        
        <div class="nav-links">
            <a href="home" class="nav-link <?php echo ($path == 'home' || $path == '') ? 'active' : ''; ?>">Home</a>
            
            <div class="dropdown">
                <a href="#" id="ministries-trigger" class="nav-link <?php echo in_array($path, ['groups', 'youth', 'singing_cycle']) ? 'active' : ''; ?>">
                    Ministries <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="ministries">Ministries Hub</a>
                    <a href="groups">Parish Groups</a>
                    <a href="youth">Youth Ministry</a>
                    <a href="singing_cycle">Singing Cycle</a>
                    <a href="rosters">Service Rosters</a>
                    <a href="event_request">Request Event Notice</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" id="finance-trigger" class="nav-link <?php echo in_array($path, ['offertory', 'pledges', 'sunday_collection']) ? 'active' : ''; ?>">
                    Finance <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="offertory">Offertory Log</a>
                    <a href="pledges">Pledges</a>
                    <a href="sunday_collection">Yearly Collection</a>
                </div>
            </div>

            <a href="announcements" class="nav-link <?php echo ($path == 'announcements') ? 'active' : ''; ?>">Announcements</a>
            <a href="roq" class="nav-link <?php echo ($path == 'roq') ? 'active' : ''; ?>">ROQ</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard" class="btn btn-primary">Dashboard</a>
                <a href="logout" class="nav-link"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login" class="nav-link">Login</a>
                <a href="register" class="btn btn-primary">Join Us</a>
            <?php endif; ?>
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

.dropdown:hover .dropdown-content {
    display: block;
}
</style>
