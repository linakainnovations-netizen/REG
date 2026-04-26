<?php
$pageTitle = "Parish Ministries & Resources Hub";
include_once 'includes/header.php';
?>

<section class="hero-premium" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Explore St. Paul Chipata</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Access all public services, financial reports, liturgy schedules, and community news from one central location.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid grid-cols-3">
        <!-- Community & Liturgy -->
        <div class="card" style="border-top: 4px solid var(--primary-color);">
            <h3 class="mb-4">Community & Liturgy</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="groups" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-users-cog text-primary"></i> <span>Parish Groups Directory</span>
                </a>
                <a href="singing_cycle" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-music text-primary"></i> <span>Singing Cycle</span>
                </a>
                <a href="rosters" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-list-check text-primary"></i> <span>Service & Sweeping Rosters</span>
                </a>
                <a href="youth" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-child text-primary"></i> <span>Youth Ministry</span>
                </a>
            </div>
        </div>

        <!-- Financial Transparency -->
        <div class="card" style="border-top: 4px solid var(--accent-color);">
            <h3 class="mb-4">Financial Transparency</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="offertory" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-hand-holding-usd" style="color: var(--accent-color);"></i> <span>Weekly Offertory Log</span>
                </a>
                <a href="pledges" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-heart" style="color: var(--accent-color);"></i> <span>Community Pledges</span>
                </a>
                <a href="sunday_collection" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-calendar-alt" style="color: var(--accent-color);"></i> <span>Yearly Collection Progress</span>
                </a>
            </div>
        </div>

        <!-- News & Procurement -->
        <div class="card" style="border-top: 4px solid var(--secondary-color);">
            <h3 class="mb-4">News & Engagement</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="announcements" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-bullhorn" style="color: var(--secondary-color);"></i> <span>Parish Announcements</span>
                </a>
                <a href="submit_announcement" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-plus-circle" style="color: var(--secondary-color);"></i> <span>Submit News for Review</span>
                </a>
                <a href="roq" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-file-invoice-dollar" style="color: var(--secondary-color);"></i> <span>Active ROQ Requests</span>
                </a>
                <a href="event_request" class="flex nav-link" style="align-items: center; gap: 1rem; color: var(--text-main);">
                    <i class="fas fa-calendar-plus" style="color: var(--secondary-color);"></i> <span>Request Private Event Post</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Join CTA -->
    <div class="card mt-4 text-center" style="background: #f1f5f9; border: none; padding: 3rem;">
        <h2 class="mb-2">Are you a Parish Leader?</h2>
        <p class="text-muted mb-4">Access your specialized dashboard to manage liturgy, finance, and community communications.</p>
        <div class="flex" style="justify-content: center; gap: 1rem;">
            <a href="login" class="btn btn-primary" style="padding: 0.75rem 2.5rem;">Leader Sign In</a>
            <a href="register" class="btn btn-outline" style="padding: 0.75rem 2.5rem;">Join the Community</a>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
