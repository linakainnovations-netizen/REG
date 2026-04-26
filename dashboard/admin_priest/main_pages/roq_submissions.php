<?php
/**
 * ROQ Submissions Review Page
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

$roq_id = $_GET['id'] ?? null;

if (!$roq_id) {
    echo "<div class='alert alert-danger'>Invalid Request. ROQ ID missing.</div>";
    exit;
}

// Fetch ROQ details
$stmt = $pdo->prepare("SELECT * FROM roq WHERE id = ?");
$stmt->execute([$roq_id]);
$roq = $stmt->fetch();

if (!$roq) {
    echo "<div class='alert alert-danger'>ROQ not found.</div>";
    exit;
}

// Fetch Submissions
$stmt = $pdo->prepare("
    SELECT s.*, u.full_name, u.email, u.phone 
    FROM roq_submissions s
    JOIN users u ON s.user_id = u.id
    WHERE s.roq_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$roq_id]);
$submissions = $stmt->fetchAll();

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="procurement-container">
    <div class="mb-4">
        <a href="?page=roq" class="text-primary mb-3 d-inline-block"><i class="fas fa-arrow-left mr-2"></i> Back to Procurement</a>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-1">Submissions Review</h1>
                <p class="text-muted">Viewing offers for: <strong><?php echo htmlspecialchars($roq['title']); ?></strong></p>
            </div>
            <div class="text-right">
                <span class="badge <?php echo $roq['status'] === 'published' ? 'badge-success' : 'badge-secondary'; ?>">
                    <?php echo strtoupper($roq['status']); ?>
                </span>
                <p class="small text-muted mt-1">Deadline: <?php echo date('M d, Y', strtotime($roq['deadline'])); ?></p>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mb-4"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">Vendor/Member</th>
                        <th class="border-0">Quoted Amount</th>
                        <th class="border-0">Submission Date</th>
                        <th class="border-0">Status</th>
                        <th class="border-0 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p>No submissions received for this request yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm mr-3 bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e0e7ff;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($s['full_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($s['email']); ?> | <?php echo htmlspecialchars($s['phone']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark">K <?php echo number_format($s['quoted_amount'], 2); ?></span>
                                </td>
                                <td class="align-middle text-muted small">
                                    <?php echo date('M d, Y H:i', strtotime($s['created_at'])); ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-<?php 
                                        echo $s['status'] === 'accepted' ? 'success' : ($s['status'] === 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo strtoupper($s['status']); ?>
                                    </span>
                                </td>
                                <td class="align-middle text-right">
                                    <button class="btn btn-primary btn-sm" onclick="viewSubmission(<?php echo htmlspecialchars(json_encode($s)); ?>)">View Details</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Submission Detail Modal -->
<div id="submissionModal" class="roq-modal-backdrop" style="display:none;">
    <div class="roq-modal" style="max-width: 700px;">
        <div class="roq-modal-header">
            <div class="roq-modal-title">
                <div class="roq-modal-icon"><i class="fas fa-file-alt"></i></div>
                <div>
                    <h3 id="modal_vendor_name">Vendor Proposal</h3>
                    <p id="modal_submission_date">Submitted on ...</p>
                </div>
            </div>
            <button class="roq-modal-close" onclick="document.getElementById('submissionModal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-4">
            <div class="row mb-4">
                <div class="col-6">
                    <label class="small text-muted text-uppercase font-weight-bold">Quoted Price</label>
                    <h2 class="text-primary" id="modal_price">K 0.00</h2>
                </div>
                <div class="col-6 text-right">
                    <label class="small text-muted text-uppercase font-weight-bold">Contact Info</label>
                    <p class="mb-0" id="modal_contact">email@example.com</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="small text-muted text-uppercase font-weight-bold">Proposal / Offer Details</label>
                <div id="modal_proposal" class="p-3 bg-light rounded" style="white-space: pre-line; max-height: 300px; overflow-y: auto;">
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <div>
                    <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/roq_handler.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="reject_submission">
                        <input type="hidden" name="submission_id" id="modal_reject_id">
                        <input type="hidden" name="roq_id" value="<?php echo $roq_id; ?>">
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Reject this offer?')">Reject Offer</button>
                    </form>
                </div>
                <div>
                    <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/roq_handler.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="accept_submission">
                        <input type="hidden" name="submission_id" id="modal_accept_id">
                        <input type="hidden" name="roq_id" value="<?php echo $roq_id; ?>">
                        <button type="submit" class="btn btn-success px-4" onclick="return confirm('Accept this offer? This will notify the vendor.')">Accept & Award</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewSubmission(data) {
    document.getElementById('modal_vendor_name').innerText = data.full_name;
    document.getElementById('modal_submission_date').innerText = 'Submitted on ' + new Date(data.created_at).toLocaleDateString();
    document.getElementById('modal_price').innerText = 'K ' + parseFloat(data.quoted_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('modal_contact').innerText = data.email + '\n' + data.phone;
    document.getElementById('modal_proposal').innerText = data.proposal_text;
    
    document.getElementById('modal_reject_id').value = data.id;
    document.getElementById('modal_accept_id').value = data.id;
    
    document.getElementById('submissionModal').style.display = 'flex';
}
</script>
