<?php
$pageTitle = "Parish Rosters & Programs";
include_once 'includes/header.php';

// Fetch Rosters from Tasks table
// 1. Sweeping Roster
$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE t.task_name LIKE '%sweeping%' AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$sweeping = $stmt->fetchAll();

// 2. Sunday Roster (Readings, Offertory, etc.)
$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE t.task_name LIKE '%Sunday%' OR t.task_name LIKE '%Reading%' AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$sunday = $stmt->fetchAll();

// 3. Weekly Mass Programs
$stmt = $pdo->query("SELECT t.* FROM tasks t WHERE t.task_name LIKE '%Mass%' AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$masses = $stmt->fetchAll();
?>

<section class="hero-premium" style="background: #065f46; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Liturgy & Service Rosters</h1>
        <p style="opacity: 0.9;">Stay updated with group duties for sweeping, singing, and Sunday mass programs.</p>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid grid-cols-3" style="align-items: flex-start;">
        <!-- Sweeping Roster -->
        <div>
            <div class="card" style="border-top: 4px solid #10b981;">
                <h3 class="mb-4"><i class="fas fa-broom mr-2"></i> Sweeping Roster</h3>
                <?php foreach ($sweeping as $s): ?>
                    <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                        <p style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($s['group_name']); ?></p>
                        <p class="text-muted" style="font-size: 0.85rem;"><?php echo date('D, M j, Y', strtotime($s['assigned_date'])); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($sweeping)): ?> <p class="text-muted mt-2">No upcoming sweeping duties.</p> <?php endif; ?>
            </div>
        </div>

        <!-- Sunday Roster -->
        <div>
            <div class="card" style="border-top: 4px solid #3b82f6;">
                <h3 class="mb-4"><i class="fas fa-book-open mr-2"></i> Sunday Liturgy</h3>
                <?php foreach ($sunday as $sun): ?>
                    <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                        <p style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($sun['task_name']); ?></p>
                        <p class="text-muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars($sun['group_name']); ?> | <?php echo date('M j', strtotime($sun['assigned_date'])); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($sunday)): ?> <p class="text-muted mt-2">No Sunday rosters published yet.</p> <?php endif; ?>
            </div>
        </div>

        <!-- Weekly Mass Programs -->
        <div>
            <div class="card" style="border-top: 4px solid #8b5cf6;">
                <h3 class="mb-4"><i class="fas fa-church mr-2"></i> Mass Programs</h3>
                <?php foreach ($masses as $m): ?>
                    <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                        <p style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($m['task_name']); ?></p>
                        <p class="text-muted" style="font-size: 0.85rem;"><?php echo date('l, M j', strtotime($m['assigned_date'])); ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($masses)): ?> <p class="text-muted mt-2">No mass programs scheduled for this week.</p> <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Event Request CTA -->
    <div class="mt-4 p-4 text-center card" style="background: #fdf2f8; border: 1px dashed #f472b6;">
        <h3 style="color: #be185d;">Have a Private Event?</h3>
        <p class="mb-4">Members can request the Choir or SCC participation for Weddings, Funerals, and Memorials.</p>
        <a href="event_request" class="btn" style="background: #be185d; color: white; padding: 0.75rem 2rem; font-weight: 700;">Submit Event Request</a>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
