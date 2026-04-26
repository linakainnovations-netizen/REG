<?php
$pageTitle = "Request for Quotation (ROQ)";
include_once 'includes/header.php';

// Fetch published ROQs with author info
$stmt = $pdo->query("
    SELECT r.*, ro.role_name as author_role
    FROM roq r 
    LEFT JOIN users u ON r.author_id = u.id
    LEFT JOIN roles ro ON u.role_id = ro.id
    WHERE r.status = 'published' 
    ORDER BY r.deadline ASC
");
$roqs = $stmt->fetchAll();
?>

<div class="official-notice-page py-5" style="background-color: #f1f5f9; min-height: 100vh;">
    <div class="container">
        <!-- Official Notice Paper -->
        <div class="card p-5 mb-5 shadow-lg mx-auto" style="max-width: 850px; background: white; border: 1px solid #e2e8f0; border-radius: 0;">
            
            <!-- Official Header -->
            <div class="notice-header text-center mb-5">
                <img src="<?php echo BASE_URL; ?>assets/images/other/main_logo.png" alt="Parish Logo" style="height: 100px; margin-bottom: 1.5rem;">
                <h1 style="font-family: 'Times New Roman', Times, serif; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.5rem;">St. Paul's Parish - Chipata</h1>
                <p class="mb-0 text-muted" style="text-transform: uppercase; font-size: 0.9rem; font-weight: 700;">Catholic Diocese of Chipata, Zambia</p>
                <div style="width: 100%; height: 3px; background: #1a202c; margin-top: 1.5rem; margin-bottom: 3rem;"></div>
            </div>

            <!-- Page Title -->
            <div class="text-center mb-5">
                <h2 style="font-family: 'Inter', sans-serif; font-weight: 800; background: #1a202c; color: white; display: inline-block; padding: 0.5rem 2rem; border-radius: 4px;">OFFICIAL REQUEST FOR QUOTATION</h2>
            </div>

            <?php if (empty($roqs)): ?>
                <div class="text-center py-5">
                    <p class="text-muted italic">There are no active procurement requests at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($roqs as $item): ?>
                    <div class="roq-document mb-5 p-4" style="border: 1px solid #cbd5e1; position: relative;">
                        <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                            <div>
                                <span class="text-muted small">REF: STP/ROQ/2026/<?php echo str_pad($item['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                <h3 style="margin-top: 0.5rem; color: #1e293b; font-weight: 800;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            </div>
                            <div class="text-right">
                                <span class="badge" style="background: #ef4444; color: white; padding: 0.5rem 1rem;">DEADLINE: <?php echo date('M j, Y', strtotime($item['deadline'])); ?></span>
                            </div>
                        </div>

                        <div class="notice-content" style="line-height: 1.8; color: #334155; font-size: 1.05rem;">
                            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0" style="font-weight: 700; color: #1e293b;">Requested by: <?php echo htmlspecialchars($item['author_role'] ?? 'Parish Priest'); ?></p>
                                <small class="text-muted">Issued Date: <?php echo date('D, M j, Y', strtotime($item['created_at'])); ?></small>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="dashboard?page=roq_submit&id=<?php echo $item['id']; ?>" class="btn btn-dark" style="border-radius: 0;">Submit Formal Offer</a>
                            <?php else: ?>
                                <a href="login" class="btn btn-outline-dark" style="border-radius: 0; border-style: dashed;">Login to Submit Offer</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Footer Text -->
            <div class="mt-5 pt-5 text-center text-muted" style="font-size: 0.85rem; border-top: 1px dashed #cbd5e1;">
                <p>One Faith, One People, One Portal.</p>
                <p class="mb-0">&copy; <?php echo date('Y'); ?> St. Paul's Parish Portal. All procurement follows administrative guidelines.</p>
            </div>
        </div>

        <div class="notice-meta text-center text-muted" style="font-size: 0.9rem;">
            <p><i class="fas fa-lock mr-2"></i> This is an official notice generated through the St. Paul Parish Digital Portal.</p>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>

<?php include_once 'includes/footer.php'; ?>
