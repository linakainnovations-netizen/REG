<?php
/**
 * Group Leader Dashboard Overview (Lay Groups, Choirs, SCCs)
 */
?>
<div class="grid grid-cols-3">
    <div class="card">
        <i class="fas fa-users-cog fa-2x mb-2" style="color: var(--primary-color);"></i>
        <h3>My Members</h3>
        <p class="text-muted">Manage your group members and details.</p>
        <a href="group/members" class="btn btn-outline mt-4" style="width: 100%;">View List</a>
    </div>

    <div class="card">
        <i class="fas fa-tasks fa-2x mb-2" style="color: var(--accent-color);"></i>
        <h3>Assigned Duties</h3>
        <p class="text-muted">Check when your group is scheduled for duties.</p>
        <a href="tasks/group" class="btn btn-outline mt-4" style="width: 100%;">View Schedule</a>
    </div>

    <div class="card">
        <i class="fas fa-coins fa-2x mb-2" style="color: var(--secondary-color);"></i>
        <h3>Contributions</h3>
        <p class="text-muted">Track group-wide contributions and targets.</p>
        <a href="finance/group" class="btn btn-outline mt-4" style="width: 100%;">Pledge Tracker</a>
    </div>
</div>
