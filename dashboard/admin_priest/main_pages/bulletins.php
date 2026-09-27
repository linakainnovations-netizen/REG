<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../backend/file_storage.php';
$msg = null;
$msgErr = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bulletin_pdf'])) {
    $title = trim($_POST['title'] ?? '');
    $sunday = $_POST['sunday_date'] ?? null;
    $desc = trim($_POST['description'] ?? '');
    $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    if ($title && is_uploaded_file($_FILES['bulletin_pdf']['tmp_name'])) {
        // Stored OUTSIDE the web project; parishioners download via browser.
        $fn = storePdfUpload($_FILES['bulletin_pdf']);
        if ($fn) {
            $stmt = $pdo->prepare("INSERT INTO bulletins (title, description, sunday_date, file_path, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $desc ?: null, $sunday ?: null, $fn, $status, $_SESSION['user_id'] ?? null]);
            $msg = $status === 'published' ? "Bulletin published to the public page." : "Draft saved (not visible publicly).";
        } else { $msg = "Upload failed — only PDF files are allowed."; $msgErr = true; }
    } else { $msg = "Title + PDF required."; $msgErr = true; }
}
if (isset($_GET['delete'])) {
    $row = $pdo->prepare("SELECT file_path FROM bulletins WHERE id = ?");
    $row->execute([intval($_GET['delete'])]);
    if ($f = $row->fetch()) { deleteStoredFile($f['file_path']); }
    $pdo->prepare("DELETE FROM bulletins WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: bulletins"); exit();
}
if (isset($_GET['publish'])) {
    $pdo->prepare("UPDATE bulletins SET status='published' WHERE id = ?")->execute([intval($_GET['publish'])]);
    header("Location: bulletins"); exit();
}
if (isset($_GET['unpublish'])) {
    $pdo->prepare("UPDATE bulletins SET status='draft' WHERE id = ?")->execute([intval($_GET['unpublish'])]);
    header("Location: bulletins"); exit();
}
try { $rows = $pdo->query("SELECT b.*, u.full_name AS author_name FROM bulletins b LEFT JOIN users u ON b.created_by = u.id ORDER BY b.sunday_date DESC, b.created_at DESC LIMIT 30")->fetchAll(); }
catch (PDOException $e) { $rows = null; }

$total = is_array($rows) ? count($rows) : 0;
$published = is_array($rows) ? count(array_filter($rows, fn($r) => ($r['status'] ?? '') === 'published')) : 0;
$latest = (is_array($rows) && $rows) ? ($rows[0]['sunday_date'] ?: date('Y-m-d', strtotime($rows[0]['created_at']))) : null;
?>
<h1>Sunday Bulletins</h1>
<p class="text-muted">Upload the weekly PDF — it appears instantly on the public Bulletins page for parishioners to download.</p>

<?php if ($msg): ?>
<div class="card p-4 mb-4" style="<?php echo $msgErr ? 'background:#fee2e2; color:#991b1b;' : 'background:#d1fae5; color:#065f46;'; ?>"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<?php if ($rows === null): ?>
<div class="card p-4">Run <code>database_regiment_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>

<div class="grid mb-4" style="grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
    <div class="card p-4" style="display: flex; gap: 1rem; align-items: center;">
        <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-newspaper"></i></div>
        <div><h3 style="margin: 0;"><?php echo $total; ?></h3><small class="text-muted">Total bulletins</small></div>
    </div>
    <div class="card p-4" style="display: flex; gap: 1rem; align-items: center;">
        <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-check-circle"></i></div>
        <div><h3 style="margin: 0;"><?php echo $published; ?></h3><small class="text-muted">Published live</small></div>
    </div>
    <div class="card p-4" style="display: flex; gap: 1rem; align-items: center;">
        <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: #eff6ff; color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-calendar-alt"></i></div>
        <div><h3 style="margin: 0; font-size: 1.1rem;"><?php echo $latest ? date('M j, Y', strtotime($latest)) : '—'; ?></h3><small class="text-muted">Latest Sunday</small></div>
    </div>
</div>

<div class="card p-4 mb-4" style="border-top: 4px solid #7c3aed;">
    <h3 class="mb-4"><i class="fas fa-upload mr-2" style="color: #7c3aed;"></i>Publish new bulletin</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="grid" style="grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
            <div>
                <label class="form-label">Title *</label>
                <input type="text" name="title" placeholder="e.g. 2nd Sunday of Advent — 7 Dec 2026" required class="form-control" style="width: 100%;">
            </div>
            <div>
                <label class="form-label">Sunday date</label>
                <input type="date" name="sunday_date" class="form-control" style="width: 100%;">
            </div>
            <div>
                <label class="form-label">Visibility</label>
                <select name="status" class="form-control" style="width: 100%;"><option value="published">Publish now</option><option value="draft">Save as draft</option></select>
            </div>
        </div>
        <div class="mt-4">
            <label class="form-label">Short description (shown under the title)</label>
            <input type="text" name="description" placeholder="e.g. Readings, notices and Christmas programme inside" class="form-control" style="width: 100%;">
        </div>
        <div class="mt-4 flex" style="gap: 1rem; align-items: center; flex-wrap: wrap;">
            <label class="btn btn-outline" style="cursor: pointer; margin: 0;"><i class="fas fa-file-pdf mr-2"></i>Choose PDF<input type="file" name="bulletin_pdf" accept="application/pdf" required style="display: none;" onchange="document.getElementById('pdf-name').textContent = this.files[0] ? this.files[0].name : 'No file chosen';"></label>
            <small class="text-muted" id="pdf-name">No file chosen</small>
            <button class="btn btn-primary" style="margin-left: auto; padding: 0.75rem 2rem;">Publish Bulletin</button>
        </div>
    </form>
</div>

<h3 class="mb-4">All bulletins (<?php echo $total; ?>)</h3>
<?php if (empty($rows)): ?>
    <div class="card text-center" style="padding: 3rem;"><i class="fas fa-inbox fa-3x mb-4 text-muted"></i><p class="text-muted">No bulletins yet — publish the first one above.</p></div>
<?php endif; ?>
<?php foreach ($rows as $b):
    $full = __DIR__ . '/../../../' . $b['file_path'];
    $size = is_file($full) ? round(filesize($full) / 1048576, 1) . ' MB' : 'missing file';
    $isPub = ($b['status'] ?? 'published') === 'published';
?>
<div class="card p-4 mb-2 flex" style="gap: 1rem; align-items: center; <?php echo $isPub ? '' : 'opacity: 0.75; border-style: dashed;'; ?>">
    <div style="width: 46px; min-width: 46px; height: 46px; border-radius: 0.75rem; background: <?php echo $isPub ? '#f5f3ff; color: #7c3aed;' : '#f1f5f9; color: #94a3b8;'; ?> display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas fa-file-pdf"></i></div>
    <span style="flex: 1;">
        <strong><?php echo htmlspecialchars($b['title']); ?></strong>
        <span class="badge" style="background: <?php echo $isPub ? '#dcfce7; color: #166534;' : '#f1f5f9; color: #64748b;'; ?> font-size: 0.7rem; margin-left: 0.5rem;"><?php echo $isPub ? 'LIVE' : 'DRAFT'; ?></span><br>
        <small class="text-muted"><?php echo $b['sunday_date'] ? date('D, M j, Y', strtotime($b['sunday_date'])) : date('M j, Y', strtotime($b['created_at'])); ?> · <?php echo $size; ?><?php echo !empty($b['author_name']) ? ' · by ' . htmlspecialchars($b['author_name']) : ''; ?><?php echo !empty($b['description']) ? '<br>' . htmlspecialchars(substr($b['description'], 0, 100)) : ''; ?></small>
    </span>
    <span style="white-space: nowrap;">
        <a href="<?php echo BASE_URL . 'download?type=bulletin&id=' . $b['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" title="Download PDF"><i class="fas fa-download"></i></a>
        <?php if ($isPub): ?><a href="bulletins?unpublish=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" title="Hide from public"><i class="fas fa-eye-slash"></i></a>
        <?php else: ?><a href="bulletins?publish=<?php echo $b['id']; ?>" class="btn btn-primary" style="padding:0.4rem 0.8rem;" title="Publish"><i class="fas fa-check"></i></a><?php endif; ?>
        <a href="bulletins?delete=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" title="Delete" onclick="return confirm('Delete this bulletin and its PDF file?')"><i class="fas fa-trash-alt"></i></a>
    </span>
</div>
<?php endforeach; endif; ?>
<style>
.form-label { font-weight: 700; font-size: 0.8rem; display: block; margin-bottom: 0.4rem; color: #475569; }
.form-control { padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; }
@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }
</style>
