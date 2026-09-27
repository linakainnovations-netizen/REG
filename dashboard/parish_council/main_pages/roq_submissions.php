<?php
/**
 * ROQ Submissions Review — Parish Council
 */
require_once __DIR__ . '/../../../config/db.php';

$roq_id = intval($_GET['id'] ?? 0);
if (!$roq_id) { echo "<div class='card p-4'>Invalid request.</div>"; return; }

$stmt = $pdo->prepare("SELECT * FROM roq WHERE id = ?");
$stmt->execute([$roq_id]);
$roq = $stmt->fetch();
if (!$roq) { echo "<div class='card p-4'>ROQ not found.</div>"; return; }

try {
    $stmt = $pdo->prepare("SELECT s.*, u.full_name, u.email, u.phone FROM roq_submissions s JOIN users u ON s.user_id = u.id WHERE s.roq_id = ? ORDER BY s.created_at DESC");
    $stmt->execute([$roq_id]);
    $submissions = $stmt->fetchAll();
} catch (PDOException $e) { $submissions = null; }

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
$handler = BASE_URL . 'dashboard/parish_council/backend_pages/roq_handler.php';
?>
<div class="mb-4">
    <a href="roq" style="color: var(--primary-color); font-weight: 700;">&larr; Back to Procurement</a>
    <div class="flex mt-4" style="justify-content: space-between; align-items: center;">
        <div>
            <h1>Submissions Review</h1>
            <p class="text-muted">Offers for: <strong><?php echo htmlspecialchars($roq['title']); ?></strong></p>
        </div>
        <span class="badge" style="background: #f1f5f9;"><?php echo strtoupper($roq['status']); ?></span>
    </div>
</div>
<?php if ($success): ?><div class="card p-4 mb-4" style="background:#d1fae5; color:#065f46;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($submissions === null): ?>
<div class="card p-4">Run <code>database_procurement_updates.sql</code> in phpMyAdmin first.</div>
<?php else: ?>
<div class="card p-0"><div class="table-responsive"><table style="width:100%; border-collapse: collapse;">
<thead><tr style="background:#f1f5f9; text-align: left;"><th style="padding:1rem;">Vendor</th><th style="padding:1rem;">Quoted</th><th style="padding:1rem;">Date</th><th style="padding:1rem;">Status</th><th style="padding:1rem; text-align: right;">Action</th></tr></thead>
<tbody>
<?php if (empty($submissions)): ?>
<tr><td colspan="5" class="text-center p-4 text-muted">No offers yet.</td></tr>
<?php endif; ?>
<?php foreach ($submissions as $s): ?>
<tr style="border-top: 1px solid #e2e8f0;">
<td style="padding:1rem;"><strong><?php echo htmlspecialchars($s['full_name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($s['email']); ?> · <?php echo htmlspecialchars($s['phone']); ?></small><br><small><?php echo nl2br(htmlspecialchars(substr($s['proposal_text'], 0, 120))); ?></small></td>
<td style="padding:1rem; font-weight: 800;">K<?php echo number_format((float)$s['quoted_amount'], 2); ?></td>
<td style="padding:1rem;" class="text-muted"><?php echo date('M d, Y', strtotime($s['created_at'])); ?></td>
<td style="padding:1rem;"><span class="badge" style="background:#f1f5f9;"><?php echo strtoupper($s['status']); ?></span></td>
<td style="padding:1rem; text-align: right; white-space: nowrap;">
<?php if ($s['status'] === 'pending'): ?>
<form action="<?php echo $handler; ?>" method="POST" style="display:inline;"><input type="hidden" name="action" value="accept_submission"><input type="hidden" name="submission_id" value="<?php echo $s['id']; ?>"><input type="hidden" name="roq_id" value="<?php echo $roq_id; ?>"><button class="btn btn-primary btn-sm" onclick="return confirm('Award this contract?')">Award</button></form>
<form action="<?php echo $handler; ?>" method="POST" style="display:inline;"><input type="hidden" name="action" value="reject_submission"><input type="hidden" name="submission_id" value="<?php echo $s['id']; ?>"><input type="hidden" name="roq_id" value="<?php echo $roq_id; ?>"><button class="btn btn-outline btn-sm">Reject</button></form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php endif; ?>
