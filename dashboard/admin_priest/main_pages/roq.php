<?php
/**
 * Procurement Management (ROQ)
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

// Handle success/error messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Fetch ROQs with author info
$sql = "SELECT r.*, u.full_name as author_name, ro.role_name as author_role,
        (SELECT COUNT(*) FROM roq_offers WHERE roq_id = r.id) as offer_count 
        FROM roq r 
        LEFT JOIN users u ON r.author_id = u.id
        LEFT JOIN roles ro ON u.role_id = ro.id
        ORDER BY r.created_at DESC";
$roqs = $pdo->query($sql)->fetchAll();

$pendingRoqs = array_filter($roqs, fn($r) => $r['status'] === 'pending');
$activeRoqs = array_filter($roqs, fn($r) => $r['status'] === 'published');
?>

<div class="procurement-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Procurement Oversight</h1>
            <p class="text-muted">Review equipment requests, quotations, and award contracts.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('roqModal').style.display='flex'">
            <i class="fas fa-file-invoice mr-2"></i> New ROQ / Tender
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success mb-4"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Pending Approval Section -->
    <?php if (!empty($pendingRoqs)): ?>
        <h2 class="mb-3 text-warning"><i class="fas fa-clock mr-2"></i> Pending Approval</h2>
        <div class="grid grid-cols-2 mb-5" style="gap: 2rem;">
            <?php foreach ($pendingRoqs as $r): ?>
                <div class="card border-0 shadow-sm p-0 mb-4 overflow-hidden" style="border-left: 4px solid #f59e0b !important;">
                    <div class="p-4 bg-light">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-1 text-dark"><?php echo htmlspecialchars($r['title']); ?></h3>
                                <small class="text-muted">By: <?php echo htmlspecialchars($r['author_name']); ?> (<?php echo $r['author_role']; ?>)</small>
                            </div>
                            <span class="badge badge-warning">PENDING</span>
                        </div>
                        <p class="mt-3 text-muted small"><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>
                        <div class="mt-4 d-flex gap-2">
                            <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/roq_handler.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="approve_roq">
                                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Approve & Publish</button>
                            </form>
                            <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/roq_handler.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_roq">
                                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm text-danger" onclick="return confirm('Reject and Delete this request?')">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Published/Active Section -->
    <h2 class="mb-3"><i class="fas fa-check-circle mr-2 text-success"></i> Published Requests</h2>
    <div class="grid grid-cols-2" style="gap: 2rem;">
        <?php foreach ($activeRoqs as $r): ?>
            <div class="card border-0 shadow-sm p-0 mb-4 overflow-hidden">
                <div class="roq-card-header p-4 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="text-white mb-1"><?php echo htmlspecialchars($r['title']); ?></h3>
                            <small class="text-white opacity-75">By: <?php echo htmlspecialchars($r['author_role'] ?? 'Parish Priest'); ?></small>
                        </div>
                        <span class="badge badge-light"><?php echo strtoupper($r['status']); ?></span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4"><?php echo nl2br(htmlspecialchars($r['description'])); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <div>
                            <div class="font-weight-bold text-primary"><?php echo $r['offer_count']; ?> Offers</div>
                            <small class="text-muted">Submissions received</small>
                        </div>
                        <div class="text-right">
                            <div class="font-weight-bold">Deadline</div>
                            <small class="text-danger"><?php echo date('M d, Y', strtotime($r['deadline'])); ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white p-3 border-top d-flex justify-content-end gap-2">
                    <a href="?page=roq_submissions&id=<?php echo $r['id']; ?>" class="btn btn-outline btn-sm">Review Submissions</a>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($activeRoqs)): ?>
            <div class="col-span-2 card p-5 text-center text-muted">
                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                <h3>No active procurement requests.</h3>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- New ROQ Modal -->
<div id="roqModal" class="roq-modal-backdrop">
    <div class="roq-modal">
        <!-- Modal Header -->
        <div class="roq-modal-header">
            <div class="roq-modal-title">
                <div class="roq-modal-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h3>Compose New ROQ</h3>
                    <p>This will be published immediately to the public portal.</p>
                </div>
            </div>
            <button class="roq-modal-close" onclick="document.getElementById('roqModal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/roq_handler.php" method="POST" class="roq-modal-form">
            <input type="hidden" name="action" value="create_roq">

            <div class="roq-field">
                <label class="roq-label">
                    <i class="fas fa-heading"></i> Request Title
                </label>
                <input type="text" name="title" class="roq-input" required placeholder="e.g. Purchase of New Sound System for the Church">
                <span class="roq-hint">Be specific — this is the headline vendors will see.</span>
            </div>

            <div class="roq-field">
                <label class="roq-label">
                    <i class="fas fa-align-left"></i> Requirements & Description
                </label>
                <textarea name="description" class="roq-input roq-textarea" rows="6" required placeholder="Describe exact specifications, quantities, delivery expectations, and any technical requirements..."></textarea>
            </div>

            <div class="roq-field">
                <label class="roq-label">
                    <i class="fas fa-calendar-alt"></i> Bidding Deadline
                </label>
                <input type="date" name="deadline" class="roq-input" required min="<?php echo date('Y-m-d'); ?>">
                <span class="roq-hint">Vendors must submit their offers before this date.</span>
            </div>

            <!-- Footer -->
            <div class="roq-modal-footer">
                <button type="button" class="roq-btn-cancel" onclick="document.getElementById('roqModal').style.display='none'">
                    Cancel
                </button>
                <button type="submit" class="roq-btn-submit">
                    <i class="fas fa-paper-plane mr-2"></i> Publish to Portal
                </button>
            </div>
        </form>
    </div>
</div>

