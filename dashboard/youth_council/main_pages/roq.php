<?php
/**
 * Procurement Submission (ROQ)
 * St. Paul Chipata Portal
 */
require_once __DIR__ . '/../../../config/db.php';

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Fetch my sent ROQs
$stmt = $pdo->prepare("SELECT * FROM roq WHERE author_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$myRoqs = $stmt->fetchAll();
?>

<div class="roq-submission-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Request for Quotation</h1>
            <p class="text-muted">Initiate a procurement request for your group or council.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('roqModal').style.display='flex'">
            <i class="fas fa-plus mr-2"></i> Create New Request
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mt-4 mb-4"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card overflow-hidden">
        <h3 class="p-4 border-bottom bg-light">My Procurement Requests</h3>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Title</th>
                        <th>Date Submitted</th>
                        <th>Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myRoqs as $r): ?>
                        <tr>
                            <td class="font-weight-bold"><?php echo htmlspecialchars($r['title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($r['deadline'])); ?></td>
                            <td>
                                <?php if ($r['status'] === 'pending'): ?>
                                    <span class="badge badge-warning">Awaiting Approval</span>
                                <?php elseif ($r['status'] === 'published'): ?>
                                    <span class="badge badge-success">Published / Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo strtoupper($r['status']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($myRoqs)): ?>
                        <tr><td colspan="4" class="text-center p-5 text-muted">You haven't submitted any requests yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ROQ Submission Modal -->
<div id="roqModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card p-4" style="width: 500px; max-width: 95%;">
        <div class="d-flex justify-content-between mb-4">
            <h3>New ROQ Submission</h3>
            <button class="btn btn-sm" onclick="document.getElementById('roqModal').style.display='none'"><i class="fas fa-times"></i></button>
        </div>
        <form action="<?php echo BASE_URL; ?>backend/roq_handler.php" method="POST">
            <input type="hidden" name="action" value="create_roq">
            <div class="mb-3">
                <label class="form-label">Request Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Painting of Council Office">
            </div>
            <div class="mb-3">
                <label class="form-label">Full Requirements / Description</label>
                <textarea name="description" class="form-control" rows="6" required placeholder="Outline exactly what is needed and specify any technical requirements..."></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label">Deadline for Bidding</label>
                <input type="date" name="deadline" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                <small class="text-muted">Must be at least 3 days from now.</small>
            </div>
            <button type="submit" class="btn btn-primary w-100 p-2">Submit for Priest Approval</button>
        </form>
    </div>
</div>
