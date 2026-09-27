<?php
/**
 * Announcements Management — Parish Council
 * Council publishes directly to the public Announcements page.
 */
require_once __DIR__ . '/../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $cat = $_POST['category'] ?? 'general';
    if ($title && $content) {
        $pdo->prepare("INSERT INTO announcements (title, content, author_id, category, status) VALUES (?, ?, ?, ?, 'published')")
            ->execute([$title, $content, $_SESSION['user_id'] ?? null, $cat]);
        header("Location: announcements"); exit();
    }
}
if (isset($_GET['publish'])) {
    $pdo->prepare("UPDATE announcements SET status='published' WHERE id = ?")->execute([intval($_GET['publish'])]);
    header("Location: announcements"); exit();
}
if (isset($_GET['reject'])) {
    $pdo->prepare("UPDATE announcements SET status='rejected' WHERE id = ?")->execute([intval($_GET['reject'])]);
    header("Location: announcements"); exit();
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM announcements WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: announcements"); exit();
}

$sql = "SELECT a.*, u.full_name as author_name FROM announcements a LEFT JOIN users u ON a.author_id = u.id ORDER BY a.created_at DESC";
$announcements = $pdo->query($sql)->fetchAll();
$pending = count(array_filter($announcements, fn($a) => $a['status'] === 'pending'));
?>
<div class="announcements-container">
    <div class="flex mb-4" style="justify-content: space-between; align-items: center;">
        <div><h1>Parish Announcements</h1><p class="text-muted">Publish council notices. Published items show on the public Announcements page instantly.</p></div>
    </div>
    <div class="card p-4 mb-4">
        <h3 class="mb-4">Create announcement</h3>
        <form method="POST" class="grid" style="grid-template-columns: 2fr 1fr; gap: 1rem;">
            <input type="text" name="title" placeholder="Title" required class="form-control">
            <select name="category" class="form-control"><option value="general">General</option><option value="youth">Youth</option><option value="liturgy">Liturgy</option><option value="finance">Finance</option></select>
            <textarea name="content" rows="3" placeholder="Announcement body..." required class="form-control" style="grid-column: 1 / -1;"></textarea>
            <button class="btn btn-primary" style="grid-column: 1 / -1;">Publish Now</button>
        </form>
    </div>
    <div class="grid mb-4" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="card p-4"><h3><?php echo count($announcements); ?></h3><small class="text-muted">Total Posted</small></div>
        <div class="card p-4"><h3><?php echo $pending; ?></h3><small class="text-muted">Awaiting Approval</small></div>
    </div>
    <div class="card"><div class="table-responsive"><table class="table table-hover mb-0">
        <thead class="bg-light"><tr><th class="p-4">Announcement</th><th class="p-4">Author</th><th class="p-4">Status</th><th class="p-4">Date</th><th class="p-4 text-right">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($announcements as $a): ?>
        <tr>
            <td class="p-4"><div style="font-weight:700;"><?php echo htmlspecialchars($a['title']); ?></div><small class="text-muted"><?php echo htmlspecialchars(substr($a['content'], 0, 80)); ?>...</small></td>
            <td class="p-4"><?php echo htmlspecialchars($a['author_name'] ?? '—'); ?></td>
            <td class="p-4"><span class="badge" style="background:#f1f5f9;"><?php echo htmlspecialchars($a['status']); ?></span></td>
            <td class="p-4 text-muted"><?php echo date('M d, H:i', strtotime($a['created_at'])); ?></td>
            <td class="p-4 text-right" style="white-space: nowrap;">
                <?php if ($a['status'] !== 'published'): ?><a href="announcements?publish=<?php echo $a['id']; ?>" class="btn btn-sm btn-success" title="Publish"><i class="fas fa-check"></i></a><?php endif; ?>
                <?php if ($a['status'] === 'pending'): ?><a href="announcements?reject=<?php echo $a['id']; ?>" class="btn btn-sm btn-light" title="Reject"><i class="fas fa-times"></i></a><?php endif; ?>
                <a href="announcements?delete=<?php echo $a['id']; ?>" class="btn btn-sm btn-light text-danger" title="Delete" onclick="return confirm('Delete?')"><i class="fas fa-trash-alt"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div></div>
</div>
