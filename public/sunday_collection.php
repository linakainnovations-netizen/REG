<?php
$pageTitle = "Yearly Collection Overview";
include_once 'includes/header.php';

// Fetch yearly summary (Mock data if dynamic not available)
$currentYear = date('Y');
$stmt = $pdo->prepare("SELECT MONTH(transaction_date) as month, SUM(amount) as total FROM finance WHERE YEAR(transaction_date) = ? GROUP BY MONTH(transaction_date)");
$stmt->execute([$currentYear]);
$monthlyData = $stmt->fetchAll();

// Treasurer Update (Mock for now)
$treasurerNote = "The collections for $currentYear have been instrumental in completing the church roof renovation. We are now focusing on the SCC community hall project.";
?>

<section class="hero-premium" style="background: #1e2937; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1><?php echo $currentYear; ?> Collection Transparency</h1>
        <p style="opacity: 0.9;">Tracking our yearly financial stewardship and project funding.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Collection Charts/Monthly -->
        <div>
            <div class="card" style="border: none; box-shadow: var(--shadow-md);">
                <h3 class="mb-4">Monthly Collection Breakdown</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php 
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $dataMap = [];
                    foreach ($monthlyData as $d) $dataMap[$d['month']] = $d['total'];
                    
                    $max = !empty($dataMap) ? max($dataMap) : 1000;

                    for ($i = 1; $i <= 12; $i++): 
                        $val = $dataMap[$i] ?? 0;
                        $width = ($val / $max) * 100;
                    ?>
                        <div class="flex" style="align-items: center; gap: 1rem;">
                            <span style="width: 40px; font-weight: 600; color: var(--text-muted);"><?php echo $months[$i-1]; ?></span>
                            <div style="flex-grow: 1; background: #f1f5f9; height: 12px; border-radius: 6px; overflow: hidden;">
                                <div style="background: var(--primary-color); width: <?php echo $width; ?>%; height: 100%; border-radius: 6px;"></div>
                            </div>
                            <span style="min-width: 80px; text-align: right; font-weight: 700;">K <?php echo number_format($val); ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Treasurer Note -->
        <div>
            <div class="card" style="background: var(--primary-color); color: white; border: none; box-shadow: var(--shadow-lg);">
                <h3 class="mb-4"><i class="fas fa-comment-dots"></i> Treasurer's Note</h3>
                <p style="font-size: 1.1rem; line-height: 1.6; opacity: 0.9; font-style: italic;">
                    "<?php echo $treasurerNote; ?>"
                </p>
                <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.2);">
                    <p style="margin: 0; font-weight: 700;">Parish Treasurer Office</p>
                    <p style="margin: 0; font-size: 0.875rem; opacity: 0.8;">Updated: <?php echo date('F Y'); ?></p>
                </div>
            </div>

            <div class="card mt-4" style="border: 1px dashed var(--border-color);">
                <h4>Public Accountability</h4>
                <p class="text-muted" style="font-size: 0.875rem;">All financial records are audited annually. Members can request detailed reports through the Parish Council Secretary via their dashboards.</p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
