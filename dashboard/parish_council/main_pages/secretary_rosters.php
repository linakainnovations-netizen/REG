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
    $task_name = trim($_POST['task_name']);
    $group_id = $_POST['group_id'];
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
                        <th style="padding: 1rem; text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roster as $r): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 700; color: var(--primary-color);">
                                <?php echo date('D, M j, Y', strtotime($r['assigned_date'])); ?>
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($r['task_name']); ?></td>
                            <td style="padding: 1rem;">
                                <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($r['group_name'] ?? 'Not Assigned'); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <span style="color: #3b82f6; font-size: 0.85rem;"><i class="fas fa-clock mr-1"></i> Upcoming</span>
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
        <form method="POST">
            <div class="mb-4">
                <label class="block font-bold mb-2">Duty Name</label>
                <input type="text" name="task_name" class="form-control" placeholder="e.g. Sunday Morning Singing" style="width: 100%;" required>
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
