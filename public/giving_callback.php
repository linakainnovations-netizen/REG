<?php
// Flutterwave callback: ?path=giving_callback&ref=<intent id>&transaction_id=<fw id>&tx_ref=<ref>&status=successful
$pageTitle = "Giving Receipt";
include_once 'includes/header.php';
require_once 'backend/giving_gateway.php';
$msg = $err = null;
$ref = intval($_GET['ref'] ?? 0);
$fwTxId = $_GET['transaction_id'] ?? null;
$status = $_GET['status'] ?? '';
if ($ref && $fwTxId && $status === 'successful') {
    $v = GivingGateway::flutterwaveVerify($pdo, $fwTxId);
    if ($v['ok']) {
        $ok = GivingGateway::markVerified($pdo, $ref, null);
        $msg = $ok ? "Payment confirmed (K" . number_format((float)$v['amount'], 2) . " " . htmlspecialchars($v['currency']) . "). Thank you — receipt recorded." : "Payment was already recorded. Thank you!";
    } else { $err = "We could not verify this payment yet. If money left your account, contact the treasurer with ref #$ref."; }
} elseif ($ref) {
    $err = "Payment not completed (status: " . htmlspecialchars($status ?: 'cancelled') . "). No money was recorded for ref #$ref.";
} else { $err = "Invalid callback."; }
?>
<div class="container mt-4 mb-4" style="max-width: 640px;">
<div class="card text-center" style="padding: 3rem;">
<?php if ($msg): ?><i class="fas fa-check-circle fa-3x mb-4" style="color:#059669;"></i><h2>God bless you!</h2><p><?php echo $msg; ?></p>
<?php else: ?><i class="fas fa-exclamation-circle fa-3x mb-4" style="color:#dc2626;"></i><h2>Payment issue</h2><p class="text-muted"><?php echo $err; ?></p><?php endif; ?>
<a href="giving" class="btn btn-primary mt-4">Back to Giving</a>
</div>
</div>
<?php include_once 'includes/footer.php'; ?>
