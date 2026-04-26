<?php
$pageTitle = "Liturgy Singing Cycle";
include_once 'includes/header.php';

// Fetch upcoming tasks related to singing
$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t 
                    LEFT JOIN groups g ON t.group_id = g.id 
                    WHERE t.task_name LIKE '%singing%' OR t.task_name LIKE '%choir%'
                    AND t.assigned_date >= CURDATE()
                    ORDER BY t.assigned_date ASC LIMIT 15");
$cycle = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/other/choir.webp'); background-size: cover; background-position: center; color: white; padding: 6rem 0;">
    <div class="container text-center">
        <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem;">Liturgy Singing Cycle</h1>
        <p style="opacity: 0.9; font-size: 1.25rem;">Weekly rotation for choirs and group-led singing schedules.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="page-header text-center">
        <h2>Upcoming Choirs in Rotation</h2>
        <p class="text-muted">Stay tuned with which group is leading the liturgy this week.</p>
    </div>

    <div class="grid grid-cols-3">
        <?php foreach ($cycle as $index => $item): ?>
            <div class="card" style="border: none; box-shadow: var(--shadow-md); position: relative; overflow: hidden; <?php echo $index === 0 ? 'border: 2px solid var(--primary-color);' : ''; ?>">
                <?php if ($index === 0): ?>
                    <div style="position: absolute; top: 0; right: 0; background: var(--primary-color); color: white; padding: 0.25rem 1rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                        Next Service
                    </div>
                <?php endif; ?>
                
                <div class="text-center">
                    <div style="font-size: 0.875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
                        <?php echo date('l, F j', strtotime($item['assigned_date'])); ?>
                    </div>
                    <h3 style="color: var(--primary-color); font-size: 1.5rem; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($item['group_name'] ?? 'Guest Choir'); ?>
                    </h3>
                    <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.375rem; font-size: 0.875rem; color: #475569;">
                        <?php echo htmlspecialchars($item['task_name']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($cycle)): ?>
            <div class="card text-center" style="grid-column: span 3; padding: 4rem;">
                <p class="text-muted">The singing cycle for the next period is currently being finalized.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4 p-4 text-center">
        <p class="text-muted">Group leaders can update their rotation status and membership through the <a href="login" style="color: var(--primary-color); font-weight: 700;">Leader Dashboard</a>.</p>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
