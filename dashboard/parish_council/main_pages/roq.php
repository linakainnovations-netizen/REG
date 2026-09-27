<?php
/**
 * Procurement Oversight (ROQ) — Parish Council owns procurement.
 * Approve requests, publish to portal, review vendor offers, award.
 */
require_once __DIR__ . '/../../../config/db.php';

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$sql = "SELECT r.*, u.full_name as author_name, ro.role_name as author_role,
        (SELECT COUNT(*) FROM roq_submissions WHERE roq_id = r.id) as offer_count
        FROM roq r
        LEFT JOIN users u ON r.author_id = u.id
        LEFT JOIN roles ro ON u.role_id = ro.id
        ORDER BY r.created_at DESC";
try { $roqs = $pdo->query($sql)->fetchAll(); }
catch (PDOException $e) { $roqs = null; }

$pending = $active = [];
if (is_array($roqs)) {
    foreach ($roqs as $r) {
        if ($r['status'] === 'pending') $pending[] = $r;
        elseif ($r['status'] === 'published') $active[] = $r;
    }
}
$handler = BASE_URL . 'dashboard/parish_council/backend_pages/roq_handler.php';
?>
<div class="procurement-container">
    <div class="flex mb-4" style="justify-content: space-between; align-items: center;">
        <div>
            <h1>Procurement Oversight</h1>
            <p class="text-muted">Review requests, publish tenders, and award contracts. (Moved here from the Priest portal.)</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('roqModal').style.display='flex'">
            <i class="fas fa-plus mr-2"></i> New ROQ / Tender
        </button>
    </div>

    <?php if ($success): ?><div class="card p-4 mb-4" style="background:#d1fae5; color:#065f46;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($roqs === null): ?>
        <div class="card p-4">Run <code>database.sql</code> + <code>database_procurement_updates.sql</code> in phpMyAdmin first.</div>
    <?php else: ?>

    <?php if (!empty($pending)): ?>
        <h2 class="mb-4" style="color: #b45309;"><i class="fas fa-clock mr-2"></i> Pending Council Approval (<?php echo count($pending); ?>)</h2>
        <div class="grid grid-cols-2 mb-4" style="gap: 1.5rem;">
            <?php foreach ($pending as $r): ?>
                <div class="card p-4" style="border-left: 4px solid #f59e0b;">
                    <div class="flex" style="justify-content: space-between; align-items: start;">
                        <div>
                            <h3><?php echo htmlspecialchars($r['title']); ?></h3>
                            <small class="text-muted">By: <?php echo htmlspecialchars($r['author_name'] ?? '—'); ?> (<?php echo htmlspecialchars($r['author_role'] ?? '—'); ?>)</small>
                        </div>
                        <span class="badge" style="background: #fef3c7; color: #92400e;">PENDING</span>
                    </div>
                    <p class="text-muted mt-4" style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>
                    <div class="flex mt-4" style="gap: 0.5rem;">
                        <form action="<?php echo $handler; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="approve_roq">
                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                            <button class="btn btn-primary btn-sm">Approve & Publish</button>
                        </form>
                        <form action="<?php echo $handler; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_roq">
                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                            <button class="btn btn-outline btn-sm" onclick="return confirm('Reject and delete?')">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-4"><i class="fas fa-check-circle mr-2" style="color: #16a34a;"></i> Published Requests</h2>
    <div class="grid grid-cols-2" style="gap: 1.5rem;">
        <?php foreach ($active as $r): ?>
            <div class="card" style="padding: 0; overflow: hidden;">
                <div class="p-4" style="background: var(--primary-color); color: white;">
                    <div class="flex" style="justify-content: space-between; align-items: start;">
                        <div>
                            <h3 style="color: white;"><?php echo htmlspecialchars($r['title']); ?></h3>
                            <small style="opacity: 0.8;">By: <?php echo htmlspecialchars($r['author_role'] ?? 'Council'); ?></small>
                        </div>
                        <span class="badge" style="background: white; color: var(--primary-color);"><?php echo strtoupper($r['status']); ?></span>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-muted" style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars(substr($r['description'], 0, 200))); ?></p>
                    <div class="flex p-4 mt-4" style="background: #f8fafc; border-radius: 0.5rem; justify-content: space-between; align-items: center;">
                        <div><div style="font-weight: 800; color: var(--primary-color);"><?php echo $r['offer_count']; ?> Offers</div><small class="text-muted">Submissions received</small></div>
                        <div style="text-align: right;"><div style="font-weight: 700;">Deadline</div><small style="color: #dc2626;"><?php echo $r['deadline'] ? date('M d, Y', strtotime($r['deadline'])) : '—'; ?></small></div>
                    </div>
                    <div class="mt-4" style="text-align: right;">
                        <a href="roq_submissions?id=<?php echo $r['id']; ?>" class="btn btn-outline btn-sm">Review Submissions</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($active)): ?>
            <div class="card p-4 text-center text-muted" style="grid-column: 1 / -1;"><i class="fas fa-box-open fa-3x mb-4"></i><p>No active procurement requests.</p></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="roqModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card p-4" style="max-width: 540px; width: 100%;">
        <div class="flex mb-4" style="justify-content: space-between; align-items: center;">
            <h3>New ROQ / Tender</h3>
            <button class="btn btn-sm btn-outline" onclick="document.getElementById('roqModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo $handler; ?>" method="POST">
            <input type="hidden" name="action" value="create_roq">
            <div class="mb-4">
                <label class="form-label">Request title</label>
                <input type="text" name="title" class="form-control" style="width: 100%;" required placeholder="e.g. Painting of Council Office">
            </div>
            <div class="mb-4">
                <label class="form-label">Requirements / description</label>
                <textarea name="description" class="form-control" style="width: 100%;" rows="5" required></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label">Bidding deadline</label>
                <input type="date" name="deadline" class="form-control" style="width: 100%;" required min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
            </div>
            <button class="btn btn-primary" style="width: 100%; padding: 0.85rem;">Publish to Portal</button>
        </form>
    </div>
</div>
<style>.form-label { font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.4rem; } .form-control { padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; } @media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
