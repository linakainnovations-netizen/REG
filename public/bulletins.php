<?php
$pageTitle = "Sunday Bulletins";
include_once 'includes/header.php';
try {
    $bulletins = $pdo->query("SELECT * FROM bulletins WHERE status = 'published' ORDER BY sunday_date DESC, created_at DESC LIMIT 30")->fetchAll();
} catch (PDOException $e) { $bulletins = null; }
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1><i class="fas fa-newspaper mr-2"></i> Sunday Bulletins</h1>
        <p style="opacity: 0.9;">Weekly readings, announcements and rosters — tap to download the PDF.</p>
    </div>
</section>
<div class="container mt-4 mb-4" style="max-width: 900px;">
    <?php if ($bulletins === null): ?>
        <div class="card text-center" style="padding: 3rem;">
            <p class="font-weight-bold">Bulletins module not installed yet</p>
            <p class="text-muted">Run <code>database_regiment_updates.sql</code> in phpMyAdmin, then upload PDFs from Dashboard → Bulletins.</p>
        </div>
    <?php elseif (empty($bulletins)): ?>
        <div class="card text-center" style="padding: 3rem;">
            <i class="fas fa-inbox fa-3x mb-4 text-muted"></i>
            <p class="text-muted">No bulletins published yet. Check back on Friday.</p>
        </div>
    <?php else: ?>
        <div class="card p-0"><div class="table-responsive"><table style="width:100%; border-collapse: collapse;">
            <thead><tr style="background: #f5f3ff; text-align: left;">
                <th style="padding: 1rem;">Bulletin</th>
                <th style="padding: 1rem;">Sunday</th>
                <th style="padding: 1rem; text-align: right;">PDF</th>
            </tr></thead>
            <tbody>
            <?php foreach ($bulletins as $b): ?>
            <tr style="border-top: 1px solid #ede9fe;">
                <td style="padding: 1rem;">
                    <div style="display: flex; gap: 0.85rem; align-items: center;">
                        <div style="width: 40px; min-width: 40px; height: 40px; border-radius: 0.6rem; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;"><i class="fas fa-file-pdf"></i></div>
                        <div><strong><?php echo htmlspecialchars($b['title']); ?></strong><?php echo !empty($b['description']) ? '<br><small class="text-muted">' . htmlspecialchars(substr($b['description'], 0, 100)) . '</small>' : ''; ?></div>
                    </div>
                </td>
                <td style="padding: 1rem; white-space: nowrap;" class="text-muted"><?php echo $b['sunday_date'] ? date('D, M j, Y', strtotime($b['sunday_date'])) : date('M j, Y', strtotime($b['created_at'])); ?></td>
                <td style="padding: 1rem; text-align: right;"><a href="<?php echo BASE_URL; ?>download?type=bulletin&id=<?php echo $b['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-download mr-2"></i>Download</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div></div>
        <p class="text-muted mt-4" style="font-size: 0.8rem;">PDFs download straight to your phone or computer — nothing plays in the browser.</p>
    <?php endif; ?>
</div>
<?php include_once 'includes/footer.php'; ?>
