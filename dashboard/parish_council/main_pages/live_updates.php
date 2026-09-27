<?php
/**
 * Live Updates — Parish Council
 * Short notices that cut down long Sunday announcements.
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../backend/live_updates.php';
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $office = $_POST['office'] ?? 'parish_council';
    $audience = $_POST['audience'] ?? 'all';
    $pinned = isset($_POST['is_pinned']) ? 1 : 0;
    $expires = trim($_POST['expires_at'] ?? '');
    if ($message && array_key_exists($office, LiveUpdates::offices())) {
        try {
            $stmt = $pdo->prepare("INSERT INTO live_updates (office, message, audience, author_id, is_pinned, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$office, $message, $audience, $_SESSION['user_id'] ?? null, $pinned, $expires ?: null]);
            $msg = "Live update posted — visible on Live Updates page + homepage instantly.";
        } catch (PDOException $e) { $msg = "Save failed (run database_public_cms_updates.sql first)."; }
    } else { $msg = "Message required."; }
}
if (isset($_GET['hide'])) {
    $pdo->prepare("UPDATE live_updates SET status='hidden' WHERE id = ?")->execute([intval($_GET['hide'])]);
    header("Location: live_updates"); exit();
}
if (isset($_GET['show'])) {
    $pdo->prepare("UPDATE live_updates SET status='published' WHERE id = ?")->execute([intval($_GET['show'])]);
    header("Location: live_updates"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM live_updates WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: live_updates"); exit();
}
try {
    $rows = $pdo->query("SELECT l.*, u.full_name AS author_name FROM live_updates l LEFT JOIN users u ON l.author_id = u.id ORDER BY l.created_at DESC LIMIT 50")->fetchAll();
} catch (PDOException $e) { $rows = null; }
$offices = LiveUpdates::offices();
?>
<h1>Live Updates</h1>
<p class="text-muted">Short council notices — parishioners check the live feed instead of long Sunday announcements.</p>
<?php if ($msg): ?><div class="card p-4 mb-4"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($rows === null): ?>
<div class="card p-4">Run <code>database_public_cms_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>
<div class="card p-4 mb-4">
<form method="POST" class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
<textarea name="message" maxlength="500" rows="2" placeholder="e.g. Parish meeting Saturday 10:00 in the Hall — all SCC chairs attend" required class="form-control" style="grid-column: 1 / -1;"></textarea>
<select name="office" class="form-control"><?php foreach ($offices as $k => $v): ?><option value="<?php echo $k; ?>" <?php echo $k === 'parish_council' ? 'selected' : ''; ?>><?php echo htmlspecialchars($v); ?></option><?php endforeach; ?></select>
<select name="audience" class="form-control"><option value="all">Everyone</option><option value="youth">Youth only</option><option value="choirs">Choirs</option><option value="scc">SCC</option><option value="lay_groups">Lay groups</option><option value="leaders">Leaders only</option></select>
<label style="font-size:0.85rem;"><input type="checkbox" name="is_pinned"> Pin to top</label>
<input type="datetime-local" name="expires_at" class="form-control" title="Optional expiry">
<button class="btn btn-primary" style="grid-column: 1 / -1;">Post Live Update</button>
</form>
</div>
<?php foreach ($rows as $r): ?>
<div class="card p-4 mb-2 flex" style="justify-content: space-between; align-items: center; <?php echo $r['is_pinned'] ? 'border-left: 4px solid #eab308;' : ''; ?>">
<span><strong><?php echo htmlspecialchars($offices[$r['office']] ?? $r['office']); ?></strong> · <small class="text-muted"><?php echo date('M j H:i', strtotime($r['created_at'])); ?> · <?php echo htmlspecialchars($r['status']); ?></small><br>
<?php echo htmlspecialchars($r['message']); ?></span>
<span style="white-space: nowrap;">
<?php if ($r['status'] === 'published'): ?><a href="live_updates?hide=<?php echo $r['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Hide</a>
<?php else: ?><a href="live_updates?show=<?php echo $r['id']; ?>" class="btn btn-primary" style="padding:0.4rem 0.8rem;">Show</a><?php endif; ?>
<a href="live_updates?delete=<?php echo $r['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" onclick="return confirm('Delete?')">Delete</a>
</span>
</div>
<?php endforeach; endif; ?>
