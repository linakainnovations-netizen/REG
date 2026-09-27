<?php
/**
 * Secretary Roster Planner
 * Allows secretaries to schedule and publish community duties (Singing, Sweeping, Liturgy).
 */
require_once __DIR__ . '/../../../config/db.php';

$success = false;
$error = false;

// Handle Adding Roster Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_roster'])) {
    $duty_type = $_POST['duty_type'] ?? 'other';
    $detail = trim($_POST['task_detail'] ?? '');
    $prefixes = [
        'sweeping' => 'Sweeping',
        'reading' => 'Sunday Reading',
        'mass' => 'Mass Program',
        'singing' => 'Sunday Singing',
        'offertory' => 'Sunday Offertory',
        'other' => '',
    ];
    $prefix = $prefixes[$duty_type] ?? '';
    $task_name = trim($prefix . ($prefix && $detail ? ' — ' . $detail : $detail));
    if ($task_name === '') $task_name = trim($_POST['task_name'] ?? '');
    $group_id = $_POST['group_id'] ?: null;
    $assigned_date = $_POST['assigned_date'];
    $description = trim($_POST['description']);

    if (!empty($task_name) && !empty($assigned_date)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (task_name, group_id, assigned_date, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$task_name, $group_id, $assigned_date, $description]);
            $success = "Roster updated: '$task_name' scheduled for $assigned_date.";
        } catch (PDOException $e) {
            $error = "Error scheduling task: " . $e->getMessage();
        }
    }
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([intval($_GET['delete'])]);
    header("Location: secretary_rosters"); exit();
}
if (isset($_GET['done'])) {
    $pdo->prepare("UPDATE tasks SET status='completed' WHERE id = ?")->execute([intval($_GET['done'])]);
    header("Location: secretary_rosters"); exit();
}

// Fetch Groups for Dropdown
$groups = $pdo->query("SELECT id, name FROM groups ORDER BY name")->fetchAll();

// Fetch Upcoming Roster
$stmt = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC");
$roster = $stmt->fetchAll();
?>

<div class="roster-planner">
    <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Liturgy & Service Roster Planner</h1>
            <p class="text-muted">Coordinate group responsibilities for singing cycles and weekly church duties.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addRosterModal').style.display='flex'">
            <i class="fas fa-calendar-plus mr-2"></i> Schedule Duty
        </button>
    </div>

    <?php if ($success): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1">
        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Assigned Date</th>
                        <th style="padding: 1rem; text-align: left;">Duty / Task Name</th>
                        <th style="padding: 1rem; text-align: left;">Assigned Group</th>
                        <th style="padding: 1rem; text-align: right;">Status / Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roster as $r): ?>
                        <tr style="border-bottom: 1px solid var(--border-color); <?php echo ($r['status'] ?? '') === 'completed' ? 'opacity: 0.6;' : ''; ?>">
                            <td style="padding: 1rem; font-weight: 700; color: var(--primary-color);">
                                <?php echo date('D, M j, Y', strtotime($r['assigned_date'])); ?>
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($r['task_name']); ?></td>
                            <td style="padding: 1rem;">
                                <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($r['group_name'] ?? 'Not Assigned'); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: right; white-space: nowrap;">
                                <?php if (($r['status'] ?? 'scheduled') === 'completed'): ?>
                                    <span style="color: #16a34a; font-size: 0.85rem;"><i class="fas fa-check-circle mr-1"></i> Done</span>
                                <?php else: ?>
                                    <a href="secretary_rosters?done=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.7rem;" title="Mark completed"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="secretary_rosters?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.7rem;" title="Delete" onclick="return confirm('Delete this duty?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="addRosterModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <h2 class="mb-4">Schedule Parish Duty</h2>
        <p class="text-muted mb-4" style="font-size: 0.85rem;">Pick a duty type — it files the entry under Sweeping, Sunday Reading or Mass on the public Rosters page automatically.</p>
        <form method="POST">
            <div class="grid grid-cols-2">
                <div class="mb-4">
                    <label class="block font-bold mb-2">Duty Type</label>
                    <select name="duty_type" class="form-control" style="width: 100%;">
                        <option value="sweeping">Sweeping Rota</option>
                        <option value="reading">Sunday Reading Cycle</option>
                        <option value="mass">Mass Program</option>
                        <option value="singing">Sunday Singing</option>
                        <option value="offertory">Sunday Offertory</option>
                        <option value="other">Other / Custom</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2">Detail (group / readers)</label>
                    <input type="text" name="task_detail" class="form-control" placeholder="e.g. St. Anne Zone, or Mwila + Chanda" style="width: 100%;">
                </div>
            </div>
            <div class="grid grid-cols-2">
                <div class="mb-4">
                    <label class="block font-bold mb-2">Assigned Group</label>
                    <select name="group_id" class="form-control" style="width: 100%;">
                        <option value="">None (Individual/Guest)</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block font-bold mb-2">Date</label>
                    <input type="date" name="assigned_date" class="form-control" style="width: 100%;" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Additional Instructions</label>
                <textarea name="description" class="form-control" style="width: 100%;" rows="3"></textarea>
            </div>
            <div class="flex" style="justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addRosterModal').style.display='none'">Cancel</button>
                <button type="submit" name="add_roster" class="btn btn-primary">Publish to Roster</button>
            </div>
        </form>
    </div>
</div>
