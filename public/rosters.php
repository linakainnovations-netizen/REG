<?php
$pageTitle = "Parish Rosters & Programs";
include_once 'includes/header.php';

// Fetch Rosters from Tasks table
$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE t.task_name LIKE '%sweeping%' AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$sweeping = $stmt->fetchAll();

$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE (t.task_name LIKE '%Sunday%' OR t.task_name LIKE '%Reading%') AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$sunday = $stmt->fetchAll();

$stmt = $pdo->query("SELECT t.* FROM tasks t WHERE t.task_name LIKE '%Mass%' AND t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$masses = $stmt->fetchAll();

function dutyTable(array $rows, string $accent, string $emptyMsg, bool $showGroup = true): void {
    if (empty($rows)) { echo '<p class="text-muted" style="padding: 1rem 0;">' . htmlspecialchars($emptyMsg) . '</p>'; return; }
    echo '<div class="table-responsive"><table style="width:100%; border-collapse: collapse;">';
    echo '<thead><tr style="background: ' . $accent . '14; text-align: left;"><th style="padding: 0.85rem;">Duty</th>' . ($showGroup ? '<th style="padding: 0.85rem;">Group</th>' : '') . '<th style="padding: 0.85rem;">Date</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr style="border-top: 1px solid #e2e8f0;">';
        echo '<td style="padding: 0.85rem; font-weight: 600;">' . htmlspecialchars($r['task_name']) . '</td>';
        if ($showGroup) echo '<td style="padding: 0.85rem;" class="text-muted">' . htmlspecialchars($r['group_name'] ?? '—') . '</td>';
        echo '<td style="padding: 0.85rem; white-space: nowrap; font-weight: 700; color: var(--primary-color);">' . date('D, M j', strtotime($r['assigned_date'])) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
?>

<section class="hero-premium" style="background: #065f46; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Liturgy & Service Rosters</h1>
        <p style="opacity: 0.9;">Sweeping rotas, Sunday reading cycle and Mass programs — published by the Parish Council.</p>
        <a href="<?php echo BASE_URL; ?>download?type=roster" class="btn mt-4" style="background: white; color: #065f46; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.5rem;"><i class="fas fa-file-pdf mr-2"></i>Download Full Roster (PDF)</a>
    </div>
</section>

<div class="container mt-4 mb-4">
    <div class="grid grid-cols-3" style="align-items: flex-start;">
        <div class="card" style="border-top: 4px solid #10b981;">
            <h3 class="mb-4"><i class="fas fa-broom mr-2"></i> Sweeping Roster</h3>
            <?php dutyTable($sweeping, '#10b981', 'No upcoming sweeping duties.'); ?>
        </div>
        <div class="card" style="border-top: 4px solid #3b82f6;">
            <h3 class="mb-4"><i class="fas fa-book-open mr-2"></i> Sunday Reading Cycle</h3>
            <?php dutyTable($sunday, '#3b82f6', 'No Sunday rosters published yet.'); ?>
        </div>
        <div class="card" style="border-top: 4px solid #8b5cf6;">
            <h3 class="mb-4"><i class="fas fa-church mr-2"></i> Mass Programs</h3>
            <?php dutyTable($masses, '#8b5cf6', 'No mass programs scheduled for this week.', false); ?>
        </div>
    </div>

    <div class="mt-4 p-4 text-center card" style="background: #fdf2f8; border: 1px dashed #f472b6;">
        <h3 style="color: #be185d;">Have a Private Event?</h3>
        <p class="mb-4">Members can request the Choir or SCC participation for Weddings, Funerals, and Memorials.</p>
        <a href="event_request" class="btn" style="background: #be185d; color: white; padding: 0.75rem 2rem; font-weight: 700;">Submit Event Request</a>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
