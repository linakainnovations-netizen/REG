<?php
/**
 * Dashboard Section Separator
 * St. Paul Chipata Portal
 */
?>
<div class="separator" style="margin: 2.5rem 0; display: flex; align-items: center; gap: 1rem;">
    <div style="flex-grow: 1; height: 1px; background: linear-gradient(to right, transparent, var(--border-color), transparent);"></div>
    <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap;">
        <?php echo isset($separatorTitle) ? htmlspecialchars($separatorTitle) : ''; ?>
    </div>
    <div style="flex-grow: 1; height: 1px; background: linear-gradient(to right, transparent, var(--border-color), transparent);"></div>
</div>
