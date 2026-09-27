<?php
$pageTitle = "Youth Ministry Portal";
include_once 'includes/header.php';

// Fetch youth announcements
$stmt = $pdo->query("SELECT * FROM announcements WHERE category = 'youth' AND status = 'published' ORDER BY created_at DESC LIMIT 5");
$youthNews = $stmt->fetchAll();

// Fetch youth groups
$stmt = $pdo->query("SELECT * FROM groups WHERE type = 'youth' ORDER BY member_count DESC");
$youthGroups = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 5rem 0;">
    <div class="container text-center">
        <h1>St. Charles Lwanga Youth Ministry</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Empowering the next generation through faith, community, and service.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid" style="grid-template-columns: 2fr 1.2fr; gap: 2rem;">
        <!-- Youth News -->
        <div>
            <div class="page-header">
                <h2>Latest Youth News</h2>
            </div>
            <?php foreach ($youthNews as $news): ?>
                <div class="card mb-4" style="border: 1px solid var(--border-color); border-radius: 0.75rem;">
                    <div class="flex" style="justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-color); text-transform: uppercase;">Update</span>
                        <span class="text-muted" style="font-size: 0.8rem;"><?php echo date('M j, Y', strtotime($news['created_at'])); ?></span>
                    </div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-main);"><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p style="color: var(--text-muted); line-height: 1.6;"><?php echo nl2br(htmlspecialchars(substr($news['content'], 0, 200))); ?>...</p>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($youthNews)): ?>
                <div class="card text-center" style="padding: 3rem;">
                    <p class="text-muted">No specific youth news at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Youth Groups & Leadership -->
        <div>
            <div class="page-header">
                <h2>Youth Groups</h2>
            </div>
            <?php foreach ($youthGroups as $group): ?>
                <div class="card mb-2" style="padding: 1rem; background: #ecfdf5; border: none;">
                    <div class="flex" style="justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: #065f46;"><?php echo htmlspecialchars($group['name']); ?></span>
                        <span class="badge" style="background: white; color: #059669;"><?php echo $group['member_count']; ?> members</span>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="card mt-4" style="background: var(--primary-color); color: white; border: none;">
                <h3>Get Involved</h3>
                <p style="opacity: 0.8; font-size: 0.9rem; margin: 1rem 0;">Are you a youth member at St. Charles Lwanga Regiment? Join our digital community to stay updated on conferences, liturgies, and outings.</p>
                <a href="register" class="btn" style="background: white; color: var(--primary-color); font-weight: 700; width: 100%;">Create Account</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
