<?php
$pageTitle = "Parish News & Announcements";
include_once 'includes/header.php';

// Fetch announced
$stmt = $pdo->query("SELECT a.*, u.full_name as author, r.role_name FROM announcements a 
                    LEFT JOIN users u ON a.author_id = u.id 
                    LEFT JOIN roles r ON u.role_id = r.id
                    WHERE a.status = 'published' 
                    ORDER BY a.created_at DESC");
$announcements = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: #b45309; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Stay Informed</h1>
        <p style="opacity: 0.9;">The official voice of St. Charles Lwanga Regiment. Verified news from our Parish leadership.</p>
    </div>
</section>

<!-- Poster Banner -->
<section style="background: #fdf2f8; color: #be185d; padding: 1.5rem 0; border-bottom: 1px solid #fbcfe8;">
    <div class="container flex" style="justify-content: space-between; align-items: center;">
        <p style="font-weight: 600; margin: 0;"><i class="fas fa-edit mr-2"></i> Only authorized leaders (Priests, Council, Group Leaders) can post announcements.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login" class="btn btn-primary" style="background: #be185d; border: none; font-size: 0.875rem;">Login to Submit News</a>
        <?php else: ?>
            <a href="dashboard/announcements/create" class="btn btn-primary" style="background: #be185d; border: none; font-size: 0.875rem;">Submit New Post</a>
        <?php endif; ?>
    </div>
</section>

<div class="container mt-4 mb-4" style="max-width: 900px;">
    <?php if (empty($announcements)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <i class="fas fa-newspaper fa-3x mb-4 text-muted"></i>
            <h3>No news yet.</h3>
            <p class="text-muted">Stay tuned for official updates from the Parish Council.</p>
        </div>
    <?php else: ?>
        <?php foreach ($announcements as $ann): ?>
            <article class="card mb-4" style="border: none; box-shadow: var(--shadow-md); border-radius: 1rem; overflow: hidden; display: flex; flex-direction: column;">
                <div style="padding: 2.5rem;">
                    <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span class="badge" style="background: <?php 
                            echo $ann['category'] == 'youth' ? '#dbeafe' : 
                                ($ann['category'] == 'finance' ? '#fef3c7' : '#f1f5f9'); 
                        ?>; color: <?php 
                            echo $ann['category'] == 'youth' ? '#1e40af' : 
                                ($ann['category'] == 'finance' ? '#92400e' : '#475569'); 
                        ?>; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.4rem 0.8rem; border-radius: 2rem;">
                            <?php echo htmlspecialchars($ann['category']); ?>
                        </span>
                        <time class="text-muted" style="font-size: 0.875rem;">
                            <i class="far fa-calendar-alt mr-1"></i> <?php echo date('F j, Y', strtotime($ann['created_at'])); ?>
                        </time>
                    </div>
                    
                    <h2 style="font-size: 1.75rem; color: var(--text-main); line-height: 1.3; margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars($ann['title']); ?>
                    </h2>
                    
                    <div class="announcement-content" style="font-size: 1.1rem; line-height: 1.7; color: #4b5563; margin-bottom: 2rem;">
                        <?php echo nl2br(htmlspecialchars($ann['content'])); ?>
                    </div>
                    
                    <div class="author-info" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color);">
                            <?php echo substr($ann['author'] ?? 'A', 0, 1); ?>
                        </div>
                        <div>
                            <p style="margin: 0; font-weight: 700; color: var(--text-main); font-size: 0.95rem;"><?php echo htmlspecialchars($ann['author'] ?? 'Parish Administrator'); ?></p>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;"><?php echo htmlspecialchars($ann['role_name'] ?? 'Admin'); ?></p>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
