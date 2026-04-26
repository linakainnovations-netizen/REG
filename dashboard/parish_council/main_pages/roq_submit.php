<?php
/**
 * ROQ Submission Page for Members/Vendors
 * St. Paul Chipata Portal
 */
require_once __DIR__ . '/../../../config/db.php';

$roq_id = $_GET['id'] ?? null;

if (!$roq_id) {
    echo "<div class='alert alert-danger'>Invalid Request.</div>";
    exit;
}

// Fetch ROQ details
$stmt = $pdo->prepare("SELECT * FROM roq WHERE id = ?");
$stmt->execute([$roq_id]);
$roq = $stmt->fetch();

if (!$roq || $roq['status'] !== 'published') {
    echo "<div class='alert alert-danger'>This procurement request is no longer active.</div>";
    exit;
}

// Check if already submitted
$stmt = $pdo->prepare("SELECT id FROM roq_submissions WHERE roq_id = ? AND user_id = ?");
$stmt->execute([$roq_id, $_SESSION['user_id']]);
$existing = $stmt->fetch();

$deadline_ts = strtotime($roq['deadline'] . ' 23:59:59');
$is_expired = time() > $deadline_ts;
?>

<div class="container py-4">
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>roq" class="text-primary mb-3 d-inline-block"><i class="fas fa-arrow-left mr-2"></i> Back to Official Notices</a>
        <h1>Submit Formal Quotation</h1>
        <p class="text-muted">You are submitting a formal offer for: <strong><?php echo htmlspecialchars($roq['title']); ?></strong></p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <?php if ($existing): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i> You have already submitted an offer for this request. Multiple submissions are not allowed.
                        </div>
                    <?php elseif ($is_expired): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i> The deadline for this request has passed (<?php echo date('M d, Y', $deadline_ts); ?>). Submissions are closed.
                        </div>
                    <?php else: ?>
                        <form action="<?php echo BASE_URL; ?>backend/roq_handler.php" method="POST">
                            <input type="hidden" name="action" value="submit_quote">
                            <input type="hidden" name="roq_id" value="<?php echo $roq_id; ?>">

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Total Quoted Amount (ZMW)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">K</span>
                                    </div>
                                    <input type="number" step="0.01" name="quoted_amount" class="form-control form-control-lg" required placeholder="0.00">
                                </div>
                                <small class="text-muted">Enter the total cost including delivery and installation if applicable.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label font-weight-bold">Proposal / Offer Details</label>
                                <textarea name="proposal_text" class="form-control" rows="10" required placeholder="Provide a detailed breakdown of your offer, including specifications, timelines, and warranty information..."></textarea>
                            </div>

                            <div class="alert alert-warning small">
                                <i class="fas fa-shield-alt mr-2"></i> By submitting, you confirm that all information provided is accurate and you agree to the Parish procurement terms.
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                Submit Formal Quote <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Request Summary</h5>
                    <div class="mb-3">
                        <small class="text-muted d-block">Deadline</small>
                        <span class="text-danger font-weight-bold"><?php echo date('M d, Y', strtotime($roq['deadline'])); ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Requirements</small>
                        <div class="small" style="max-height: 200px; overflow-y: auto;">
                            <?php echo nl2br(htmlspecialchars($roq['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
