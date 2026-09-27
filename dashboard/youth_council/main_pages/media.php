<?php
/**
 * Media & Livestream — Youth Council owns the parish live media.
 * Links + featured videos feed the public Live page instantly.
 */
require_once __DIR__ . '/../../../config/db.php';
$msg = null;

function getSetting(PDO $pdo, string $k, string $d = ''): string {
    try { $s = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?"); $s->execute([$k]); $v = $s->fetchColumn(); return $v !== false ? (string)$v : $d; }
    catch (Throwable $e) { return $d; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['media_form'])) {
    $keys = ['fb_page_url','yt_channel_url','yt_channel_id','yt_live_video_id','fb_video_url','mass_times'];
    try {
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($keys as $k) { $stmt->execute([$k, trim($_POST[$k] ?? '')]); }
        $msg = "Live page updated — changes show instantly on the public Live page.";
    } catch (PDOException $e) { $msg = "Save failed (run database_regiment_updates.sql + database_public_cms_updates.sql first)."; }
}

$s = [];
foreach (['fb_page_url','yt_channel_url','yt_channel_id','yt_live_video_id','fb_video_url','mass_times'] as $k) {
    $s[$k] = getSetting($pdo, $k);
}
?>
<h1><i class="fas fa-broadcast-tower mr-2"></i> Media & Livestream</h1>
<p class="text-muted">Youth media team: link the parish Facebook + YouTube, feature a test/replay video, keep Mass times fresh. The public <a href="<?php echo BASE_URL; ?>media" target="_blank">Live page</a> updates instantly.</p>
<?php if ($msg): ?><div class="card p-4 mb-4"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="POST">
<input type="hidden" name="media_form" value="1">
<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
<div class="card p-4">
<h3 class="mb-4"><i class="fab fa-facebook mr-2" style="color: #1877F2;"></i> Facebook</h3>
<label class="form-label">Parish Facebook Page URL</label>
<input type="url" name="fb_page_url" value="<?php echo htmlspecialchars($s['fb_page_url']); ?>" class="form-control mb-4" style="width:100%;" placeholder="https://www.facebook.com/YourParish">
<label class="form-label">Feature a video (optional full video URL)</label>
<input type="url" name="fb_video_url" value="<?php echo htmlspecialchars($s['fb_video_url']); ?>" class="form-control" style="width:100%;" placeholder="https://www.facebook.com/.../videos/...">
<p class="text-muted mt-4" style="font-size: 0.8rem;">Go live from the Facebook app → the page links out. Paste one video link above to embed it for testing/replays. Video must be public.</p>
</div>
<div class="card p-4">
<h3 class="mb-4"><i class="fab fa-youtube mr-2" style="color: #FF0000;"></i> YouTube</h3>
<label class="form-label">Channel URL</label>
<input type="url" name="yt_channel_url" value="<?php echo htmlspecialchars($s['yt_channel_url']); ?>" class="form-control mb-4" style="width:100%;">
<label class="form-label">Channel ID (auto-live embed)</label>
<input type="text" name="yt_channel_id" value="<?php echo htmlspecialchars($s['yt_channel_id']); ?>" class="form-control mb-4" style="width:100%;" placeholder="UCxxxxxxxxxxxxxxxx">
<label class="form-label">Feature a video (optional Video ID)</label>
<input type="text" name="yt_live_video_id" value="<?php echo htmlspecialchars($s['yt_live_video_id']); ?>" class="form-control" style="width:100%;" placeholder="e.g. dQw4w9WgXcQ">
<p class="text-muted mt-4" style="font-size: 0.8rem;">With the Channel ID set, the Live page auto-plays whenever the channel is live.</p>
</div>
</div>
<div class="card p-4 mt-4">
<h3 class="mb-4"><i class="fas fa-clock mr-2"></i> Mass times line</h3>
<input type="text" name="mass_times" value="<?php echo htmlspecialchars($s['mass_times']); ?>" class="form-control" style="width:100%;" placeholder="Sun 06:30, 09:00, 11:00 | Sat 17:00">
</div>
<button class="btn btn-primary mt-4" style="padding: 0.85rem 2.5rem;">Save Media Settings</button>
</form>
<div class="card p-4 mt-4" style="background: #fffbeb; border: 1px solid #fef3c7;">
<strong>Going live checklist:</strong>
<span class="text-muted" style="font-size: 0.875rem;">1) Phone charged + data bundle &nbsp; 2) Facebook app → Page → Live (or YouTube app → + → Go live) &nbsp; 3) Good lighting near the altar &nbsp; 4) Open the site Live page on a second phone to confirm.</span>
</div>
<style>.form-label { font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.4rem; } .form-control { padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; } @media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
