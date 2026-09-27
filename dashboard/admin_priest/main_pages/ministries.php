<?php
require_once __DIR__ . '/../../../config/db.php';
$msg = null;
if (isset($_GET['approve'])) {
    $pdo->prepare("UPDATE ministry_members SET status='approved' WHERE id = ?")->execute([intval($_GET['approve'])]);
    header("Location: ministries"); exit();
}
if (isset($_GET['reject'])) {
    $pdo->prepare("UPDATE ministry_members SET status='rejected' WHERE id = ?")->execute([intval($_GET['reject'])]);
    header("Location: ministries"); exit();
}
try {
    $ministries = $pdo->query("SELECT * FROM ministries WHERE status='active' ORDER BY name")->fetchAll();
    $pending = $pdo->query("SELECT mm.*, m.name AS ministry_name FROM ministry_members mm JOIN ministries m ON mm.ministry_id = m.id WHERE mm.status='pending' ORDER BY mm.created_at DESC LIMIT 50")->fetchAll();
    $counts = $pdo->query("SELECT ministry_id, COUNT(*) c FROM ministry_members WHERE status='approved' GROUP BY ministry_id")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) { $ministries = null; $pending = []; $counts = []; }
?>
<h1>Ministries & Volunteers</h1>
<p class="text-muted">Approve join requests. Catalogue is extensible — add rows to <code>ministries</code> table, no code change.</p>
<?php if ($ministries === null): ?>
<div class="card p-4">Run <code>database_public_cms_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>
<div class="grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
<?php foreach ($ministries as $m): ?>
<div class="card p-4"><strong><?php echo htmlspecialchars($m['name']); ?></strong><br>
<small class="text-muted"><?php echo ($counts[$m['id']] ?? 0); ?> volunteers · <?php echo htmlspecialchars($m['meeting_info'] ?? ''); ?></small></div>
<?php endforeach; ?>
</div>
<h2 class="mt-4 mb-4">Pending join requests (<?php echo count($pending); ?>)</h2>
<?php if (empty($pending)): ?><div class="card p-4 text-muted">No pending requests.</div><?php endif; ?>
<?php foreach ($pending as $p): ?>
<div class="card p-4 mb-2 flex" style="justify-content: space-between; align-items: center;">
<span><strong><?php echo htmlspecialchars($p['full_name']); ?></strong> → <?php echo htmlspecialchars($p['ministry_name']); ?><br>
<small class="text-muted"><?php echo htmlspecialchars($p['phone']); ?> · <?php echo date('M j', strtotime($p['created_at'])); ?><?php echo $p['note'] ? ' · ' . htmlspecialchars($p['note']) : ''; ?></small></span>
<span><a href="ministries?approve=<?php echo $p['id']; ?>" class="btn btn-primary" style="padding:0.4rem 0.8rem;">Approve</a>
<a href="ministries?reject=<?php echo $p['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Reject</a></span>
</div>
<?php endforeach; endif; ?>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
