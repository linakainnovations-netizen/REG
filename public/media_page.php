<?php
$pageTitle = "Live Media & Streams";
include_once 'includes/header.php';
require_once 'config/church_settings.php';

function mediaSetting(PDO $pdo, string $key, string $fallback = ''): string {
    try {
        $s = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $s->execute([$key]);
        $v = $s->fetchColumn();
        return ($v === false || $v === '') ? $fallback : (string)$v;
    } catch (Throwable $e) { return $fallback; }
}
$fbPage = mediaSetting($pdo, 'fb_page_url', defined('FB_PAGE_URL') ? FB_PAGE_URL : 'https://www.facebook.com/');
$ytChannel = mediaSetting($pdo, 'yt_channel_url', defined('YT_CHANNEL_URL') ? YT_CHANNEL_URL : 'https://www.youtube.com/');
$ytChannelId = mediaSetting($pdo, 'yt_channel_id', defined('YT_CHANNEL_ID') ? YT_CHANNEL_ID : '');
// Optional: paste ONE specific video to feature/test (replay or test live)
$ytVideoId = mediaSetting($pdo, 'yt_live_video_id', '');
$fbVideoUrl = mediaSetting($pdo, 'fb_video_url', '');
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #0f172a 0%, #3b82f6 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1><i class="fas fa-broadcast-tower mr-2"></i> Parish Live Media</h1>
        <p style="opacity: 0.9; max-width: 640px; margin: 0 auto;">Watch Mass and parish events live on Facebook and YouTube. No account needed. When we are not live, catch up with recent recordings below.</p>
        <div class="mt-4 flex" style="gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo htmlspecialchars($fbPage); ?>" target="_blank" class="btn" style="background: #1877F2; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 700;"><i class="fab fa-facebook-f mr-2"></i> Facebook Page</a>
            <a href="<?php echo htmlspecialchars($ytChannel); ?>" target="_blank" class="btn" style="background: #FF0000; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 700;"><i class="fab fa-youtube mr-2"></i> YouTube Channel</a>
        </div>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div class="card" style="padding: 1.5rem;">
            <h3 class="mb-4"><i class="fab fa-youtube mr-2" style="color: #FF0000;"></i> YouTube Live</h3>
            <?php if ($ytVideoId): ?>
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 0.5rem;">
                    <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($ytVideoId); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
                <p class="text-muted mt-4" style="font-size: 0.875rem;">Featured video set by admin (Dashboard → Settings). Clear it to return to auto-live.</p>
            <?php elseif (!empty($ytChannelId)): ?>
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 0.5rem;">
                    <iframe src="https://www.youtube.com/embed/live_stream?channel=<?php echo htmlspecialchars($ytChannelId); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
                <p class="text-muted mt-4" style="font-size: 0.875rem;">If live now, it plays automatically. Otherwise YouTube shows recent streams.</p>
            <?php else: ?>
                <div style="background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 0.5rem; padding: 3rem 1rem; text-align: center;">
                    <i class="fab fa-youtube fa-3x mb-4" style="color: #94a3b8;"></i>
                    <p class="font-weight-bold">YouTube channel not linked yet</p>
                    <p class="text-muted" style="font-size: 0.875rem;">Paste the channel ID in Dashboard → Settings → Livestream, or a test Video ID below it.</p>
                    <a href="<?php echo htmlspecialchars($ytChannel); ?>" target="_blank" class="btn btn-outline mt-4">Open YouTube</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h3 class="mb-4"><i class="fab fa-facebook mr-2" style="color: #1877F2;"></i> Facebook Live</h3>
            <?php if ($fbVideoUrl): ?>
                <div style="border-radius: 0.5rem; overflow: hidden;">
                    <iframe src="https://www.facebook.com/plugins/video.php?href=<?php echo urlencode($fbVideoUrl); ?>&show_text=false&width=560" style="width: 100%; height: 315px; border: 0; overflow: hidden;" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
                <div class="mt-4 text-center">
                    <a href="<?php echo htmlspecialchars($fbPage); ?>" target="_blank" class="btn btn-outline">Open Facebook Page</a>
                </div>
            <?php else: ?>
            <div style="background: #f1f5f9; border-radius: 0.5rem; padding: 2rem 1rem; text-align: center;">
                <i class="fab fa-facebook fa-3x mb-4" style="color: #1877F2;"></i>
                <p class="font-weight-bold">Watch live on our Facebook Page</p>
                <p class="text-muted" style="font-size: 0.875rem;">Media team goes live from the Facebook app on phone or camera. Parishioners tap below — no video is stored on this portal, Facebook hosts it. Tip: paste a specific live/replay video link in Settings to embed it here for testing.</p>
                <a href="<?php echo htmlspecialchars($fbPage); ?>" target="_blank" class="btn btn-primary mt-4"><i class="fas fa-video mr-2"></i> Watch on Facebook</a>
            </div>
            <?php endif; ?>
            <div class="mt-4 p-4" style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 0.5rem; font-size: 0.875rem; color: #92400e;">
                <strong>For media team:</strong> open the Facebook Page → Create → Live video → Go Live. Same for YouTube app → + → Go live. Nothing to upload here.
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
