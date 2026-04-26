<?php
/**
 * Parish Council Dashboard Overview
 */
?>
<div class="grid grid-cols-3">
    <div class="card">
        <i class="fas fa-file-contract fa-2x mb-2" style="color: var(--primary-color);"></i>
        <h3>Council News</h3>
        <p class="text-muted">Draft and verify general announcements.</p>
        <a href="announcements/council" class="btn btn-outline mt-4" style="width: 100%;">Verify News</a>
    </div>

    <div class="card">
        <i class="fas fa-calendar-check fa-2x mb-2" style="color: var(--secondary-color);"></i>
        <h3>Liturgy Planner</h3>
        <p class="text-muted">Coordinate group duties and schedules.</p>
        <a href="tasks/manage" class="btn btn-outline mt-4" style="width: 100%;">Setup Schedule</a>
    </div>

    <div class="card">
        <i class="fas fa-hand-holding-usd fa-2x mb-2" style="color: var(--accent-color);"></i>
        <h3>Treasury</h3>
        <p class="text-muted">Monitor offertories and group contributions.</p>
        <a href="finance/council" class="btn btn-outline mt-4" style="width: 100%;">Finance Logs</a>
    </div>
</div>
