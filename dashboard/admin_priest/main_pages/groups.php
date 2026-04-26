<?php
/**
 * Parish Groups Management
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

// Fetch all groups with their types
$sql = "SELECT * FROM groups ORDER BY type, name ASC";
$groups = $pdo->query($sql)->fetchAll();

// Group by type for the UI
$groupedByTypes = [];
foreach ($groups as $g) {
    $groupedByTypes[$g['type']][] = $g;
}

$typeLabels = [
    'lay_group' => 'Lay Groups',
    'youth' => 'Youth Organizations',
    'elder' => 'Elders/Senior Councils',
    'scc' => 'Small Christian Communities (SCC)',
    'choir' => 'Parish Choirs'
];
?>

<div class="groups-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Parish Groups</h1>
            <p class="text-muted">High-level oversight of all organizations, choirs, and communities.</p>
        </div>
        <button class="btn btn-primary" onclick="alert('Opening Group Registry...')">
            <i class="fas fa-plus mr-2"></i> Register New Group
        </button>
    </div>

    <div class="grid grid-cols-2" style="gap: 2rem;">
        <?php foreach ($typeLabels as $type => $label): ?>
            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="group-section-title"><?php echo $label; ?></h3>
                    <span class="badge badge-primary"><?php echo count($groupedByTypes[$type] ?? []); ?> Total</span>
                </div>
                
                <div class="group-list">
                    <?php if (!isset($groupedByTypes[$type])): ?>
                        <p class="text-muted italic small">No groups registered in this category.</p>
                    <?php else: ?>
                        <?php foreach ($groupedByTypes[$type] as $g): ?>
                            <div class="group-item p-3 border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="group-logo-placeholder mr-3">
                                        <?php if ($g['logo']): ?>
                                            <img src="<?php echo $g['logo']; ?>" alt="Logo">
                                        <?php else: ?>
                                            <i class="fas fa-users"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold"><?php echo htmlspecialchars($g['name']); ?></div>
                                        <small class="text-muted"><?php echo $g['member_count']; ?> Members</small>
                                    </div>
                                </div>
                                <div class="group-actions">
                                    <button class="btn btn-icon btn-sm" title="View Metrics"><i class="fas fa-chart-bar text-muted"></i></button>
                                    <button class="btn btn-icon btn-sm" title="Leaders"><i class="fas fa-user-shield text-muted"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
