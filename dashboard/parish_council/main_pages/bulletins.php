<?php
/**
 * Sunday Bulletins — Parish Council
 * Upload weekly PDF bulletins to the public Bulletins page.
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../backend/file_storage.php';
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bulletin_pdf'])) {
    $title = trim($_POST['title'] ?? '');
    $sunday = $_POST['sunday_date'] ?? null;
    // Stored OUTSIDE the web project; parishioners download via browser.
    if ($title && is_uploaded_file($_FILES['bulletin_pdf']['tmp_name'])) {
        $fn = storePdfUpload($_FILES['bulletin_pdf']);
        if ($fn) {
            $stmt = $pdo->prepare("INSERT INTO bulletins (title, sunday_date, file_path, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $sunday ?: null, $fn, $_SESSION['user_id'] ?? null]);
            $msg = "Bulletin published.";
        } else $msg = "Upload failed — only PDF files are allowed.";
    } else $msg = "Title + PDF required.";
}
if (isset($_GET['delete'])) {
    $row = $pdo->prepare("SELECT file_path FROM bulletins WHERE id = ?");
    $row->execute([intval($_GET['delete'])]);
    if ($f = $row->fetch()) { deleteStoredFile($f['file_path']); }
    $pdo->prepare("DELETE FROM bulletins WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: bulletins"); exit();
}
try { $rows = $pdo->query("SELECT * FROM bulletins ORDER BY created_at DESC LIMIT 20")->fetchAll(); }
catch (PDOException $e) { $rows = null; }
?>
<h1>Sunday Bulletins</h1>
<p class="text-muted">Upload weekly PDF bulletins. They appear instantly on the public Bulletins page.</p>
<?php if ($msg): ?><div class="card p-4 mb-4"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($rows === null): ?>
<div class="card p-4">Run <code>database_regiment_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>
<div class="card p-4 mb-4">
<form method="POST" enctype="multipart/form-data" class="grid" style="grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
<input type="text" name="title" placeholder="e.g. 28th Sunday Bulletin - Jan 2026" required class="form-control">
<input type="date" name="sunday_date" class="form-control">
<input type="file" name="bulletin_pdf" accept="application/pdf" required class="form-control">
<button class="btn btn-primary">Publish</button>
</form>
</div>
<?php foreach ($rows as $b): ?>
<div class="card p-4 mb-2 flex" style="justify-content: space-between; align-items: center;">
<span><strong><?php echo htmlspecialchars($b['title']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($b['file_path']); ?></small></span>
<span style="white-space: nowrap;">
<a href="<?php echo BASE_URL . 'download?type=bulletin&id=' . $b['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Download PDF</a>
<a href="bulletins?delete=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;" onclick="return confirm('Delete bulletin + file?')">Delete</a>
</span>
</div>
<?php endforeach; endif; ?>
