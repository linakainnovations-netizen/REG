<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../backend/giving_gateway.php';

if (isset($_GET['verify'])) {
    GivingGateway::markVerified($pdo, intval($_GET['verify']), $_SESSION['user_id'] ?? null);
    header("Location: giving"); exit();
}
if (isset($_GET['reject'])) {
    $pdo->prepare("UPDATE giving_transactions SET status='rejected' WHERE id=?")->execute([intval($_GET['reject'])]);
    header("Location: giving"); exit();
}
try {
    // tolerate installs before migration (provider column may not exist yet)
    $rows = $pdo->query("SELECT * FROM giving_transactions ORDER BY created_at DESC LIMIT 50")->fetchAll();
} catch (PDOException $e) { $rows = null; }
$pending = is_array($rows) ? count(array_filter($rows, fn($r) => ($r['status'] ?? '') === 'pending')) : 0;
$verifiedTotal = 0;
if (is_array($rows)) { foreach ($rows as $r) { if (($r['status'] ?? '') === 'verified') $verifiedTotal += (float)$r['amount']; } }
?>
<h1>Online Giving Verification</h1>
<p class="text-muted">Manual MoMo: confirm Txn ID on your phone, then Verify (auto-posts to Finance). Flutterwave/DPO intents verify automatically on callback — configure keys in Settings.</p>
<div class="grid mb-4" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
<div class="card p-4"><h3><?php echo $pending; ?></h3><small class="text-muted">Pending verification</small></div>
<div class="card p-4"><h3>K<?php echo number_format($verifiedTotal, 2); ?></h3><small class="text-muted">Verified (this view)</small></div>
</div>
<?php if ($rows === null): ?><div class="card p-4">Run <code>database_regiment_updates.sql</code> first.</div>
<?php else: ?>
<div class="card p-0"><div class="table-responsive"><table style="width:100%; border-collapse: collapse;">
<thead><tr style="background:#f1f5f9;"><th style="padding:1rem; text-align:left;">Date</th><th style="padding:1rem; text-align:left;">Giver</th><th style="padding:1rem; text-align:left;">Amount</th><th style="padding:1rem; text-align:left;">Channel / Txn</th><th style="padding:1rem; text-align:left;">Status</th><th style="padding:1rem; text-align:left;">Action</th></tr></thead>
<tbody><?php foreach ($rows as $r): ?>
<tr style="border-top:1px solid #e2e8f0;">
<td style="padding:1rem;"><?php echo date('M j H:i', strtotime($r['created_at'])); ?></td>
<td style="padding:1rem;"><strong><?php echo htmlspecialchars($r['giver_name']); ?></strong><br><small><?php echo htmlspecialchars($r['giver_phone']); ?> · <?php echo htmlspecialchars($r['network'] ?? ''); ?> · <?php echo htmlspecialchars($r['type']); ?></small></td>
<td style="padding:1rem;"><strong>K<?php echo number_format((float)$r['amount'], 2); ?></strong></td>
<td style="padding:1rem;"><small><?php echo htmlspecialchars($r['provider'] ?? 'momo_manual'); ?></small><br><code><?php echo htmlspecialchars($r['txn_id']); ?></code></td>
<td style="padding:1rem;"><?php echo htmlspecialchars($r['status']); ?></td>
<td style="padding:1rem; white-space:nowrap;"><?php if ($r['status']==='pending'): ?><a href="giving?verify=<?php echo $r['id']; ?>" class="btn btn-primary" style="padding:0.4rem 0.8rem;">Verify</a> <a href="giving?reject=<?php echo $r['id']; ?>" class="btn btn-outline" style="padding:0.4rem 0.8rem;">Reject</a><?php endif; ?></td>
</tr><?php endforeach; ?></tbody></table></div></div>
<?php endif; ?>
