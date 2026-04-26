<?php
$pageTitle = "Offertory Transparency Log";
include_once 'includes/header.php';

// Fetch weekly offertory records
$stmt = $pdo->query("SELECT * FROM finance WHERE type = 'offertory' ORDER BY transaction_date DESC LIMIT 20");
$offertories = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: #0369a1; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Weekly Offertory Log</h1>
        <p style="opacity: 0.9;">A transparent record of our weekly gifts as a community.</p>
    </div>
</section>

<div class="container mt-4 mb-4" style="max-width: 800px;">
    <div class="card" style="padding: 0; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: #f1f5f9; color: var(--text-main); font-weight: 700;">
                <tr>
                    <th style="padding: 1.25rem;">Service Date</th>
                    <th style="padding: 1.25rem;">Category</th>
                    <th style="padding: 1.25rem; text-align: right;">Amount Collected</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offertories as $off): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1.25rem; font-weight: 500;">
                            <i class="far fa-calendar-alt text-muted mr-2"></i>
                            <?php echo date('D, F j, Y', strtotime($off['transaction_date'])); ?>
                        </td>
                        <td style="padding: 1.25rem;">
                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                Weekly Offertory
                            </span>
                        </td>
                        <td style="padding: 1.25rem; text-align: right; font-weight: 700; color: var(--primary-color);">
                            K <?php echo number_format($off['amount'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($offertories)): ?>
                    <tr>
                        <td colspan="3" style="padding: 4rem; text-align: center;" class="text-muted">
                            <i class="fas fa-database fa-3x mb-4 opacity-20"></i>
                            <p>No offertory records documented yet.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-4" style="background: #fffbeb; border-radius: 0.5rem; border: 1px solid #fef3c7;">
        <p style="margin: 0; font-size: 0.875rem; color: #92400e;">
            <i class="fas fa-shield-alt mr-2"></i> This log is updated weekly by the Parish Treasurer. For detailed yearly summaries, visit the <a href="sunday-collection" style="font-weight: 700; text-decoration: underline;">Yearly Collection Overview</a>.
        </p>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
