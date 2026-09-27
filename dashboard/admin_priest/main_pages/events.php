<?php
require_once __DIR__ . '/../../../config/db.php';
$msg = null;

// Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['event_date'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description'] ?? '');
    $venue = trim($_POST['venue'] ?? 'Parish Church');
    $date = $_POST['event_date'];
    $time = trim($_POST['event_time'] ?? '');
    $cat = $_POST['category'] ?? 'other';
    if ($title && $date) {
        try {
            $stmt = $pdo->prepare("INSERT INTO parish_events (title, description, venue, event_date, event_time, category, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $desc, $venue, $date, $time ?: null, $cat, $_SESSION['user_id'] ?? null]);
            $msg = "Event published to public Events page.";
        } catch (PDOException $e) { $msg = "Save failed (run database_public_cms_updates.sql first)."; }
    } else { $msg = "Title + date required."; }
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM parish_events WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: events"); exit();
}
if (isset($_GET['cancel'])) {
    $pdo->prepare("UPDATE parish_events SET status='cancelled' WHERE id = ?")->execute([intval($_GET['cancel'])]);
    header("Location: events"); exit();
}
try { $rows = $pdo->query("SELECT * FROM parish_events ORDER BY event_date DESC LIMIT 50")->fetchAll(); }
catch (PDOException $e) { $rows = null; }
?>
<h1>Parish Events</h1>
<p class="text-muted">Publish Masses, meetings, fundraisers. Shows instantly on public Events page + homepage.</p>
<?php if ($msg): ?><div class="card p-4 mb-4"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($rows === null): ?>
<div class="card p-4">Run <code>database_public_cms_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>
<div class="card p-4 mb-4">
<form method="POST" class="grid" style="grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
<input type="text" name="title" placeholder="e.g. Easter Fundraising Dinner" required class="form-control">
<input type="date" name="event_date" required class="form-control">
<input type="text" name="event_time" placeholder="Time e.g. 17:00" class="form-control">
<input type="text" name="venue" placeholder="Venue (default Parish Church)" class="form-control">
<select name="category" class="form-control">
<option value="mass">Mass</option><option value="meeting">Meeting</option><option value="fundraiser">Fundraiser</option><option value="feast">Feast day</option><option value="youth">Youth</option><option value="choir">Choir</option><option value="scc">SCC</option><option value="other" selected>Other</option>
</select>
<input type="text" name="description" placeholder="Short description" class="form-control">
<button class="btn btn-primary">Publish Event</button>
</form>
</div>
<?php foreach ($rows as $r): ?>
<div class="card p-4 mb-2 flex" style="justify-content: space-between; align-items: center;">
<span><strong><?php echo htmlspecialchars($r['title']); ?></strong><br>
<small class="text-muted"><?php echo htmlspecialchars($r['event_date'] . ' ' . ($r['event_time'] ?? '') . ' · ' . $r['venue'] . ' · ' . $r['status']); ?></small></span>
<span>
<?php if ($r['status'] !== 'cancelled'): ?><a href="events?cancel=<?php echo $r['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Cancel</a><?php endif; ?>
<a href="events?delete=<?php echo $r['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" onclick="return confirm('Delete?')">Delete</a>
</span>
</div>
<?php endforeach; endif; ?>
