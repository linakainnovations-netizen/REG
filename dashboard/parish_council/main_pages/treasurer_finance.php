<?php
/**
 * Treasurer Financial Console
 * Central hub for tracking parish contributions and generating PDF reports.
 */
require_once __DIR__ . '/../../../config/db.php';

$success = false;
$error = false;

// Handle Adding Contribution (Quick Entry)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_finance'])) {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = trim($_POST['description']);
    $date = $_POST['transaction_date'];

    if ($amount > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO finance (type, amount, description, transaction_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$type, $amount, $description, $date]);
            $success = "Financial record added: K" . number_format($amount, 2);
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}

// Stats for Treasurer
$totalOffertory = $pdo->query("SELECT SUM(amount) FROM finance WHERE type='offertory'")->fetchColumn() ?: 0;
$totalTithes = $pdo->query("SELECT SUM(amount) FROM finance WHERE type='tithe'")->fetchColumn() ?: 0;
$totalPledges = $pdo->query("SELECT SUM(total_amount) FROM pledges")->fetchColumn() ?: 0;

// Fetch Recent Logs
$stmt = $pdo->query("SELECT * FROM finance ORDER BY transaction_date DESC LIMIT 10");
$recentFinance = $stmt->fetchAll();
?>

<div class="treasurer-console">
    <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h1>Financial Management Console</h1>
            <p class="text-muted">High-precision tracking of Parish funds, tithes, and community contributions.</p>
        </div>
        <div class="flex" style="gap: 1rem;">
            <button class="btn btn-outline"><i class="fas fa-file-pdf mr-2"></i> Export Monthly Report</button>
            <button class="btn btn-primary" onclick="document.getElementById('addFinanceModal').style.display='flex'">
                <i class="fas fa-plus mr-2"></i> Record Entry
            </button>
        </div>
    </div>

    <!-- Quick Stats Widgets -->
    <div class="grid grid-cols-3 mb-4">
        <div class="card" style="border-left: 4px solid var(--primary-color);">
            <p class="text-muted font-bold text-xs uppercase" style="letter-spacing: 1px;">Total Offertory</p>
            <h2 style="font-size: 2rem; color: var(--text-main);">K<?php echo number_format($totalOffertory, 2); ?></h2>
        </div>
        <div class="card" style="border-left: 4px solid var(--secondary-color);">
            <p class="text-muted font-bold text-xs uppercase" style="letter-spacing: 1px;">Total Tithes</p>
            <h2 style="font-size: 2rem; color: var(--text-main);">K<?php echo number_format($totalTithes, 2); ?></h2>
        </div>
        <div class="card" style="border-left: 4px solid var(--accent-color);">
            <p class="text-muted font-bold text-xs uppercase" style="letter-spacing: 1px;">Total Pledges</p>
            <h2 style="font-size: 2rem; color: var(--text-main);">K<?php echo number_format($totalPledges, 2); ?></h2>
        </div>
    </div>

    <?php if ($success): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="p-4 border-bottom" style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.1rem;">Recent Transactions</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; font-size: 0.75rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 1rem;">DATE</th>
                    <th style="padding: 1rem;">TYPE</th>
                    <th style="padding: 1rem;">DESCRIPTION</th>
                    <th style="padding: 1rem; text-align: right;">AMOUNT (ZMW)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentFinance as $f): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem; color: var(--text-muted);"><?php echo date('M j, Y', strtotime($f['transaction_date'])); ?></td>
                        <td style="padding: 1rem;">
                            <span style="font-weight: 700; text-transform: uppercase; font-size: 0.7rem;"><?php echo $f['type']; ?></span>
                        </td>
                        <td style="padding: 1rem;"><?php echo htmlspecialchars($f['description']); ?></td>
                        <td style="padding: 1rem; text-align: right; font-weight: 800; color: var(--primary-color);">
                            K<?php echo number_format($f['amount'], 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="addFinanceModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <h2 class="mb-4">Record Contribution</h2>
        <form method="POST">
            <div class="grid grid-cols-2">
                <div class="mb-4">
                    <label class="block font-bold mb-2">Category</label>
                    <select name="type" class="form-control" style="width: 100%;">
                        <option value="offertory">Offertory</option>
                        <option value="tithe">Tithe</option>
                        <option value="donation">Donation</option>
                        <option value="contribution">Other Contribution</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2">Amount (K)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" style="width: 100%;" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Transaction Date</label>
                <input type="date" name="transaction_date" class="form-control" style="width: 100%;" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Remarks / Details</label>
                <textarea name="description" class="form-control" style="width: 100%;" rows="3" placeholder="e.g. Sunday Morning Mass Offertory"></textarea>
            </div>
            <div class="flex" style="justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addFinanceModal').style.display='none'">Cancel</button>
                <button type="submit" name="add_finance" class="btn btn-primary">Confirm Entry</button>
            </div>
        </form>
    </div>
</div>
