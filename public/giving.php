<?php
$pageTitle = "Online Giving";
include_once 'includes/header.php';
require_once 'config/church_settings.php';
require_once 'backend/giving_gateway.php';

function siteVal(PDO $pdo, string $k, string $fb): string {
    try { $s = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?"); $s->execute([$k]); $v = $s->fetchColumn(); return ($v === false || $v === '') ? $fb : (string)$v; }
    catch (Throwable $e) { return $fb; }
}
$mtn = defined('MOMO_MTN_NUMBER') ? MOMO_MTN_NUMBER : '0975255734';
$airtel = defined('MOMO_AIRTEL_NUMBER') ? MOMO_AIRTEL_NUMBER : '0975255734';
try { $mtn = siteVal($pdo, 'momo_mtn', $mtn); $airtel = siteVal($pdo, 'momo_airtel', $airtel); } catch (Throwable $e) {}
$fwOn = GivingGateway::isEnabled($pdo, 'flutterwave');
$dpoOn = GivingGateway::isEnabled($pdo, 'dpo');

$success = $error = null;
$fwLink = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['giver_name'] ?? '');
    $phone = trim($_POST['giver_phone'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $type = $_POST['type'] ?? 'offertory';
    $network = $_POST['network'] ?? 'MTN';
    $method = $_POST['pay_method'] ?? 'momo_manual';
    $txn = trim($_POST['txn_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if (!$name || !$phone || $amount <= 0) {
        $error = "Please fill name, phone and a valid amount.";
    } elseif ($method === 'momo_manual' && !$txn) {
        $error = "Please include your MoMo Transaction ID from the SMS.";
    } elseif ($method === 'flutterwave' && !$fwOn) {
        $error = "Card/MoMo checkout is not enabled yet — please use manual MoMo below.";
    } else {
        try {
            if ($method === 'flutterwave') {
                $id = GivingGateway::recordIntent($pdo, ['giver_name' => $name, 'giver_phone' => $phone, 'amount' => $amount, 'type' => $type, 'network' => $network, 'provider' => 'flutterwave', 'provider_ref' => null]);
                $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                $cb = $base . rtrim(dirname($_SERVER['REQUEST_URI'] ?? '/'), '/') . '/giving_callback?ref=' . $id;
                $r = GivingGateway::flutterwaveCheckout($pdo, ['giver_name' => $name, 'giver_phone' => $phone, 'amount' => $amount, 'type' => $type, 'email' => $email], $cb);
                if ($r['ok']) {
                    $pdo->prepare("UPDATE giving_transactions SET provider_ref = ? WHERE id = ?")->execute([$r['tx_ref'], $id]);
                    $fwLink = $r['link'];
                    $success = "Checkout created — tap Pay Now to complete on Flutterwave.";
                } else { $error = $r['error']; }
            } else {
                GivingGateway::recordIntent($pdo, ['giver_name' => $name, 'giver_phone' => $phone, 'amount' => $amount, 'type' => $type, 'network' => $network, 'txn_id' => $txn, 'provider' => 'momo_manual']);
                $success = "Thank you, $name! Your K" . number_format($amount, 2) . " ($type via $network, Txn: $txn) was recorded. The treasurer will verify it shortly.";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false || $e->getCode() == 23000) $error = "That transaction ID was already submitted. Please check your MoMo SMS.";
            elseif (strpos($e->getMessage(), "doesn't exist") !== false) $error = "Giving table not set up yet. Please run database_regiment_updates.sql in phpMyAdmin.";
            else $error = "Submission failed. Please try again.";
        }
    }
}
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1><i class="fas fa-hand-holding-heart mr-2"></i> Give Online</h1>
        <p style="opacity: 0.9; max-width: 620px; margin: 0 auto;">Manual MoMo works today (zero fees). <?php echo $fwOn ? 'Card / MoMo checkout via Flutterwave is ON.' : 'Card checkout activates automatically once parish adds Flutterwave keys.' ?><?php echo $dpoOn ? ' DPO Pay also enabled.' : ''; ?></p>
    </div>
</section>
<div class="container mt-4 mb-4" style="max-width: 700px;">
    <div class="card mb-4" style="padding: 1.5rem; background: #ecfdf5; border: 1px solid #a7f3d0;">
        <h3 class="mb-4">Step 1 — Send MoMo (manual, always available)</h3>
        <p><span class="badge" style="background: #FFCC00; color: #000; padding: 0.4rem 0.8rem;">MTN MoMo: <?php echo htmlspecialchars($mtn); ?></span></p>
        <p><span class="badge" style="background: #ED1C24; color: #fff; padding: 0.4rem 0.8rem;">Airtel Money: <?php echo htmlspecialchars($airtel); ?></span></p>
        <p class="text-muted" style="font-size: 0.875rem;">Dial *303# (MTN) or *778# (Airtel) → Send Money → copy the Transaction ID from the SMS.</p>
    </div>
    <div class="card" style="padding: 1.5rem;">
        <h3 class="mb-4">Step 2 — Submit / Pay</h3>
        <?php if ($success): ?><div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?><?php if ($fwLink): ?><br><a href="<?php echo htmlspecialchars($fwLink); ?>" class="btn btn-primary mt-4" style="display:inline-block; padding: 0.85rem 2rem;">Pay Now on Flutterwave</a><?php endif; ?></div><?php endif; ?>
        <?php if ($error): ?><div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <input type="text" name="giver_name" placeholder="Full name" required style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <input type="text" name="giver_phone" placeholder="Phone e.g. 0975..." required style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <input type="number" name="amount" placeholder="Amount (K)" min="1" step="0.01" required style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <input type="email" name="email" placeholder="Email (for card receipts, optional)" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <select name="type" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <option value="offertory">Offertory</option><option value="tithe">Tithe</option><option value="donation">Donation</option><option value="thanksgiving">Thanksgiving</option><option value="pledge">Pledge payment</option>
                </select>
                <select name="network" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <option>MTN</option><option>Airtel</option><option>Zamtel</option><option>other</option>
                </select>
                <select name="pay_method" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; grid-column: 1 / -1;">
                    <option value="momo_manual">Manual MoMo — I already sent, verify my Txn ID (Recommended)</option>
                    <option value="flutterwave" <?php echo !$fwOn ? 'disabled' : ''; ?>>Pay now via Flutterwave <?php echo !$fwOn ? '(not enabled yet)' : '(Card / MoMo)'; ?></option>
                </select>
                <input type="text" name="txn_id" placeholder="MoMo Transaction ID (required for manual)" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; grid-column: 1 / -1;">
            </div>
            <button type="submit" class="btn btn-primary mt-4" style="width: 100%; padding: 1rem;">Submit Giving</button>
        </form>
        <p class="text-muted mt-4" style="font-size: 0.8rem;">Manual verify = treasurer confirms Txn ID then it posts to Finance. Flutterwave = auto-verify on callback. DPO wiring uses the same intent table — add token in Dashboard → Settings.</p>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
