<?php
/**
 * Parish Finance Overview
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

// Fetch summary stats
$today = date('Y-m-d');
$firstOfMonth = date('Y-m-01');

// Total Offertory current month
$stmt = $pdo->prepare("SELECT SUM(amount) FROM finance WHERE type = 'offertory' AND transaction_date >= ?");
$stmt->execute([$firstOfMonth]);
$monthlyOffertory = $stmt->fetchColumn() ?? 0;

// Recent Transactions
$recentTransactions = $pdo->query("SELECT f.*, u.full_name FROM finance f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.transaction_date DESC LIMIT 10")->fetchAll();
?>

<div class="finance-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Collections & Finance</h1>
            <p class="text-muted">Monitor parish revenue and contributions for the current period.</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline mr-2"><i class="fas fa-file-pdf mr-2"></i> Export Report</button>
            <button class="btn btn-primary" onclick="alert('Opening Contribution Entry...')"><i class="fas fa-plus mr-2"></i> Record Entry</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 mb-4" style="gap: 1.5rem;">
        <div class="card p-4 border-0 shadow-sm finance-summary-card">
            <div class="text-muted small font-weight-bold mb-2">MONTHLY OFFERTORY</div>
            <h2 class="text-primary">K<?php echo number_format($monthlyOffertory, 2); ?></h2>
        </div>
        <div class="card p-4 border-0 shadow-sm finance-summary-card">
            <div class="text-muted small font-weight-bold mb-2">TITHES (MTD)</div>
            <h2 class="text-success">K4,850.00</h2> <!-- Placeholder -->
        </div>
        <div class="card p-4 border-0 shadow-sm finance-summary-card">
            <div class="text-muted small font-weight-bold mb-2">ACTIVE PLEDGES</div>
            <h2 class="text-warning">K12,400.00</h2> <!-- Placeholder -->
        </div>
        <div class="card p-4 border-0 shadow-sm finance-summary-card">
            <div class="text-muted small font-weight-bold mb-2">REVENUE GOAL</div>
            <div class="progress-container">
                <div class="progress-bar" style="width: 65%;"></div>
            </div>
            <small class="text-muted">65% of K20,000</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4">
            <h3 class="mb-0">Recent Transactions</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="p-4">Type</th>
                            <th class="p-4">Contributor</th>
                            <th class="p-4 text-right">Amount</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $t): ?>
                            <tr>
                                <td class="p-4">
                                    <span class="type-indicator type-<?php echo $t['type']; ?>"></span>
                                    <?php echo ucfirst($t['type']); ?>
                                </td>
                                <td class="p-4">
                                    <?php echo $t['is_anonymous'] ? '<i>Anonymous</i>' : htmlspecialchars($t['full_name']); ?>
                                </td>
                                <td class="p-4 text-right font-weight-bold">
                                    K<?php echo number_format($t['amount'], 2); ?>
                                </td>
                                <td class="p-4 text-muted">
                                    <?php echo date('M d, Y', strtotime($t['transaction_date'])); ?>
                                </td>
                                <td class="p-4 text-muted small">
                                    <?php echo htmlspecialchars($t['description']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentTransactions)): ?>
                            <tr><td colspan="5" class="p-5 text-center text-muted">No recent transactions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
