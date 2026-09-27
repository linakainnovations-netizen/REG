<?php
$pageTitle = "Live Updates from Parish Leaders";
include_once 'includes/header.php';
require_once 'backend/live_updates.php';
$updates = LiveUpdates::latest($pdo, 30);
$offices = LiveUpdates::offices();
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1><i class="fas fa-bolt mr-2"></i> Live Parish Updates</h1>
        <p style="opacity: 0.92; max-width: 640px; margin: 0 auto;">Short notices from the Priest, Council, Youth Office and ministries — check here instead of waiting through long Sunday announcements.</p>
        <p class="mt-4" style="font-size: 0.85rem; opacity: 0.85;">Auto-refreshes every 60 seconds · <a href="media" style="color: white; font-weight: 700;">Watch live Mass →</a></p>
    </div>
</section>
<div class="container mt-4 mb-4" style="max-width: 760px;" id="live-feed">
    <?php if (empty($updates)): ?>
        <div class="card text-center" style="padding: 3rem;"><p class="text-muted">No live updates yet. Leaders post here during the week.</p></div>
    <?php else: foreach ($updates as $u): ?>
        <div class="card mb-2" style="padding: 1.25rem 1.5rem; <?php echo $u['is_pinned'] ? 'border-left: 4px solid #eab308;' : ''; ?>">
            <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem; text-transform: uppercase;"><?php echo htmlspecialchars($offices[$u['office']] ?? $u['office']); ?><?php echo $u['is_pinned'] ? ' · Pinned' : ''; ?></span>
                <small class="text-muted"><?php echo date('D M j, H:i', strtotime($u['created_at'])); ?></small>
            </div>
            <p style="margin: 0; font-size: 1.05rem;"><?php echo nl2br(htmlspecialchars($u['message'])); ?></p>
            <?php if (!empty($u['author_name'])): ?><small class="text-muted">— <?php echo htmlspecialchars($u['author_name']); ?></small><?php endif; ?>
        </div>
    <?php endforeach; endif; ?>
</div>
<script>setTimeout(function(){ location.reload(); }, 60000);</script>
<?php include_once 'includes/footer.php'; ?>
