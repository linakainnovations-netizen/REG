<?php
/**
 * Portal Settings
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';
?>

<div class="settings-container">
    <div class="mb-4">
        <h1>Portal Settings</h1>
        <p class="text-muted">Configure parish metadata, branding, and system-wide preferences.</p>
    </div>

    <div class="grid grid-cols-2" style="gap: 3rem;">
        <div class="card border-0 shadow-sm p-4">
            <h3 class="mb-4 d-flex align-items-center"><i class="fas fa-church mr-3 text-primary"></i> Parish Information</h3>
            <form id="parishSettingsForm">
                <div class="mb-4">
                    <label class="form-label">Parish Name</label>
                    <input type="text" class="form-control" value="St. Paul Parish - Chipata" style="width: 100%;">
                </div>
                <div class="mb-4">
                    <label class="form-label">Motto / Slogan</label>
                    <input type="text" class="form-control" value="Faith & Technology" style="width: 100%;">
                </div>
                <div class="mb-4">
                    <label class="form-label">Parish Priest Name</label>
                    <input type="text" class="form-control" value="Rev. Fr. Chongo" style="width: 100%;">
                </div>
                <button type="button" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <h3 class="mb-4 d-flex align-items-center"><i class="fas fa-shield-alt mr-3 text-success"></i> Security & Access</h3>
            <div class="setting-row d-flex justify-content-between align-items-center p-3 border-bottom">
                <div>
                    <div class="font-weight-bold">Two-Factor Authentication</div>
                    <small class="text-muted">Require OTP for all administrative logins.</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" checked>
                </div>
            </div>
            <div class="setting-row d-flex justify-content-between align-items-center p-3 border-bottom">
                <div>
                    <div class="font-weight-bold">Leader Registration</div>
                    <small class="text-muted">Allow leaders to invite other leaders.</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox">
                </div>
            </div>
            <div class="setting-row d-flex justify-content-between align-items-center p-3">
                <div>
                    <div class="font-weight-bold">Maintenance Mode</div>
                    <small class="text-muted">Temporarily disable public access during updates.</small>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="maintenanceToggle">
                </div>
            </div>
        </div>
    </div>
</div>

