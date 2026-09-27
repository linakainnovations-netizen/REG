<?php
/**
 * Council Settings — Parish Council
 * Livestream links, Mass times and MoMo numbers.
 * (Payment gateway keys stay with the Priest/Admin.)
 */
require_once __DIR__ . '/../../../config/db.php';
$msg = null;

function getSetting(PDO $pdo, string $k, string $d = ''): string {
    try { $s = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?"); $s->execute([$k]); $v = $s->fetchColumn(); return $v !== false ? (string)$v : $d; }
    catch (Throwable $e) { return $d; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings_form'])) {
    $keys = ['fb_page_url','yt_channel_url','yt_channel_id','yt_live_video_id','fb_video_url','momo_mtn','momo_airtel','mass_times','office_hours'];
    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($keys as $k) {
            $stmt->execute([$k, trim($_POST[$k] ?? '')]);
        }
        $msg = "Settings saved — public site updates instantly.";
    } catch (PDOException $e) { $msg = "Save failed (run database_regiment_updates.sql + database_public_cms_updates.sql first)."; }
}

$s = [];
foreach (['fb_page_url','yt_channel_url','yt_channel_id','yt_live_video_id','fb_video_url','momo_mtn','momo_airtel','mass_times','office_hours'] as $k) {
    $s[$k] = getSetting($pdo, $k);
}
?>
<h1>Council Settings</h1>
<p class="text-muted">Livestream links, Mass times and MoMo numbers. No code edits needed.</p>
<?php if ($msg): ?><div class="card p-4 mb-4"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="POST">
<input type="hidden" name="settings_form" value="1">
<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
<div class="card p-4">
<h3 class="mb-4"><i class="fas fa-broadcast-tower mr-2"></i> Livestream + Mass times</h3>
<label class="form-label">Facebook Page URL</label><input type="url" name="fb_page_url" value="<?php echo htmlspecialchars($s['fb_page_url']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">YouTube Channel URL</label><input type="url" name="yt_channel_url" value="<?php echo htmlspecialchars($s['yt_channel_url']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">YouTube Channel ID (enables auto-live embed)</label><input type="text" name="yt_channel_id" value="<?php echo htmlspecialchars($s['yt_channel_id']); ?>" placeholder="UCxxxxxxxxxxxxxxxx" class="form-control mb-4" style="width:100%;">
<label class="form-label">Test / Feature a YouTube video (optional Video ID)</label><input type="text" name="yt_live_video_id" value="<?php echo htmlspecialchars($s['yt_live_video_id']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">Test / Feature a Facebook video (optional full URL)</label><input type="url" name="fb_video_url" value="<?php echo htmlspecialchars($s['fb_video_url']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">Mass times (shown on homepage)</label><input type="text" name="mass_times" value="<?php echo htmlspecialchars($s['mass_times']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">Office hours</label><input type="text" name="office_hours" value="<?php echo htmlspecialchars($s['office_hours']); ?>" class="form-control" style="width:100%;">
</div>
<div class="card p-4">
<h3 class="mb-4"><i class="fas fa-hand-holding-heart mr-2"></i> Giving numbers</h3>
<label class="form-label">MTN MoMo number</label><input type="text" name="momo_mtn" value="<?php echo htmlspecialchars($s['momo_mtn']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">Airtel Money number</label><input type="text" name="momo_airtel" value="<?php echo htmlspecialchars($s['momo_airtel']); ?>" class="form-control" style="width:100%;">
<p class="text-muted mt-4" style="font-size:0.8rem;">Gateway API keys (Flutterwave/DPO) are managed by the Priest/Admin in .env.</p>
</div>
</div>
<button class="btn btn-primary mt-4" style="padding: 0.85rem 2.5rem;">Save Settings</button>
</form>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } } .form-label { font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.4rem; } .form-control { padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; }</style>
