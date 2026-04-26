<?php
$pageTitle = "Parish Ministries & Groups";
include_once 'includes/header.php';

// Fetch all groups
$stmt = $pdo->query("SELECT * FROM groups ORDER BY type, name");
$groups = $stmt->fetchAll();

// Group by type
$grouped = [];
foreach ($groups as $group) {
    $grouped[$group['type']][] = $group;
}

$typeLabels = [
    'lay_group' => 'Lay Apostles & Devotions',
    'youth' => 'Youth Ministries',
    'elder' => 'Elder & Senior Councils',
    'scc' => 'Small Christian Communities (SCC)',
    'choir' => 'Liturgical Choirs'
];
?>

<section class="hero-premium" style="background: var(--primary-color); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Connect with Your Community</h1>
        <p style="opacity: 0.9; max-width: 600px; margin: 0 auto;">Discover the heartbeat of St. Paul Chipata. Filter through our ministries and find your place of service and communion.</p>
    </div>
</section>

<!-- Call to Action Banner (If not logged in) -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section style="background: var(--secondary-color); color: white; padding: 1.5rem 0;">
    <div class="container flex flex-responsive" style="justify-content: space-between; align-items: center;">
        <p style="font-weight: 600; margin: 0;"><i class="fas fa-info-circle mr-2"></i> Sign in to your portal account to join groups and track your membership.</p>
        <a href="login" class="btn" style="background: white; color: var(--secondary-color); font-weight: 700; border-radius: 0.375rem; padding: 0.5rem 1.5rem;">Sign In Now</a>
    </div>
</section>
<?php endif; ?>

<div class="container mt-4 mb-4">
    <?php if (empty($groups)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <i class="fas fa-search fa-3x mb-4 text-muted"></i>
            <h3>No groups found.</h3>
            <p class="text-muted">The Parish structure is currently being updated. Please check back soon.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped as $type => $typeGroups): ?>
            <div class="page-header mt-4" style="border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">
                <h2 style="color: var(--primary-color);"><?php echo $typeLabels[$type] ?? ucfirst($type); ?></h2>
                <p class="text-muted"><?php echo count($typeGroups); ?> active ministries in this category.</p>
            </div>
            <div class="grid grid-cols-3 mb-4">
                <?php foreach ($typeGroups as $group): ?>
                    <div class="card group-card" style="display: flex; flex-direction: column;">
                        <div class="flex" style="justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <h3 style="color: var(--text-main);"><?php echo htmlspecialchars($group['name']); ?></h3>
                            <span class="badge" style="background: var(--bg-main); color: var(--text-muted); font-size: 0.75rem; font-weight: 700;">
                                <i class="fas fa-users"></i> <?php echo $group['member_count']; ?>
                            </span>
                        </div>
                        <p class="text-muted flex-grow" style="margin-bottom: 1.5rem; font-size: 0.95rem;"><?php echo htmlspecialchars($group['description']); ?></p>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="dashboard/groups/join?id=<?php echo $group['id']; ?>" class="btn btn-primary" style="width: 100%;">Join Ministry</a>
                        <?php else: ?>
                            <a href="login" class="btn btn-outline" style="width: 100%; border-style: dashed; border-color: var(--primary-light);">Log in to Join</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>

<style>
.group-card {
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}
.group-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}
</style>
