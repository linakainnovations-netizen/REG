<?php
$pageTitle = "Community Pledges";
include_once 'includes/header.php';

// Fetch summary stats
$stmt = $pdo->query("SELECT SUM(total_amount) as target, SUM(paid_amount) as achieved, COUNT(*) as count FROM pledges WHERE status = 'active' OR status = 'fulfilled'");
$stats = $stmt->fetch();

$target = $stats['target'] ?? 0;
$achieved = $stats['achieved'] ?? 0;
$percentage = ($target > 0) ? round(($achieved / $target) * 100) : 0;

// Fetch recent pledges (Autonomous display)
$stmt = $pdo->query("SELECT p.*, u.full_name FROM pledges p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 10");
$recentPledges = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: var(--accent-color); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Building Our Future Together</h1>
        <p style="opacity: 0.9;">Track our collective progress towards Parish goals and community projects.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <!-- Progress Tracking -->
    <div class="card mb-4" style="border: none; box-shadow: var(--shadow-lg); padding: 3rem;">
        <div class="text-center mb-4">
            <h2 style="font-weight: 800; color: var(--text-main);">Current Goal Progress</h2>
            <p class="text-muted">Yearly Development Fund 2026</p>
        </div>
        
        <div style="background: #e5e7eb; height: 30px; border-radius: 15px; overflow: hidden; margin: 2rem 0; position: relative;">
            <div style="background: var(--accent-color); width: <?php echo $percentage; ?>%; height: 100%; transition: width 1s ease-in-out;"></div>
            <span style="position: absolute; right: 10px; top: 3px; font-weight: 800; color: <?php echo $percentage > 90 ? 'white' : 'var(--text-main)'; ?>;"><?php echo $percentage; ?>%</span>
        </div>

        <div class="grid grid-cols-3 text-center">
            <div>
                <p class="text-muted mb-1">Total Pledged</p>
                <h3 style="color: var(--primary-color);">K <?php echo number_format($target, 2); ?></h3>
            </div>
            <div>
                <p class="text-muted mb-1">Total Collected</p>
                <h3 style="color: var(--accent-color);">K <?php echo number_format($achieved, 2); ?></h3>
            </div>
            <div>
                <p class="text-muted mb-1">Active Pledgers</p>
                <h3 style="color: var(--secondary-color);"><?php echo $stats['count']; ?> Members</h3>
            </div>
        </div>
    </div>

    <!-- Recent Recognition (Autonomous) -->
    <div class="page-header mt-4">
        <h2>Recent Contributions</h2>
        <p class="text-muted">Celebrating our members' commitment (Autonomous Display).</p>
    </div>
    
    <div class="grid grid-cols-3">
        <?php foreach ($recentPledges as $pledge): ?>
            <div class="card" style="border: 1px dashed var(--border-color); background: #f9fafb;">
                <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 700; color: var(--text-main);">
                        <?php 
                        if ($pledge['is_anonymous']) {
                            echo "Anonymous Member";
                        } else {
                            // Display only initials or partial name for autonomy
                            $names = explode(' ', $pledge['full_name']);
                            echo $names[0] . ' ' . (isset($names[1]) ? substr($names[1], 0, 1) . '.' : '');
                        }
                        ?>
                    </span>
                    <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('M Y', strtotime($pledge['created_at'])); ?></span>
                </div>
                <div style="background: white; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid var(--border-color); text-align: center;">
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin: 0;">Pledge Amount</p>
                    <h4 style="color: var(--primary-color);">K <?php echo number_format($pledge['total_amount'], 2); ?></h4>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="login" class="btn btn-primary" style="padding: 1rem 3rem;">Sign In to Make a Pledge</a>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
