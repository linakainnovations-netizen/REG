<?php
/**
 * Youth Council Dashboard Overview
 */
?>
<div class="grid grid-cols-3">
    <div class="card" style="border-top: 4px solid var(--primary-light);">
        <i class="fas fa-bullhorn fa-2x mb-2" style="color: var(--primary-light);"></i>
        <h3>Youth News</h3>
        <p class="text-muted">Review and approve youth-focused content.</p>
        <a href="announcements/youth" class="btn btn-outline mt-4" style="width: 100%;">Approve: 2</a>
    </div>

    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <i class="fas fa-walking fa-2x mb-2" style="color: var(--accent-color);"></i>
        <h3>Activity Planner</h3>
        <p class="text-muted">Organize youth events and registrations.</p>
        <a href="events/youth" class="btn btn-outline mt-4" style="width: 100%;">Create Event</a>
    </div>

    <div class="card" style="border-top: 4px solid var(--secondary-color);">
        <i class="fas fa-search-dollar fa-2x mb-2" style="color: var(--secondary-color);"></i>
        <h3>Fundraising</h3>
        <p class="text-muted">Track youth group contributions and pledges.</p>
        <a href="finance/youth" class="btn btn-outline mt-4" style="width: 100%;">View Funds</a>
    </div>
</div>
