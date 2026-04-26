<?php
/**
 * Secretary Group Management
 * Allows secretaries to add and manage Lay Groups and Choirs.
 */
require_once __DIR__ . '/../../../config/db.php';

$success = false;
$error = false;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO groups (name, type, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $type, $description]);
            $success = "New group '$name' added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding group: " . $e->getMessage();
        }
    } else {
        $error = "Group name is required.";
    }
}

// Fetch existing groups
$stmt = $pdo->query("SELECT * FROM groups ORDER BY type, name");
$allGroups = $stmt->fetchAll();
?>

<div class="secretary-groups-container">
    <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>Group & Choir Management</h1>
            <p class="text-muted">Register new Lay Groups, Choirs, and SCCs to the Parish Directory.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addGroupModal').style.display='flex'">
            <i class="fas fa-plus mr-2"></i> Add New Group
        </button>
    </div>

    <?php if ($success): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
                <tr>
                    <th style="padding: 1rem; text-align: left;">Group Name</th>
                    <th style="padding: 1rem; text-align: left;">Category</th>
                    <th style="padding: 1rem; text-align: left;">Members</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                    <th style="padding: 1rem; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allGroups as $g): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 1rem;">
                            <div style="font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($g['name']); ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo substr($g['description'], 0, 50); ?>...</div>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="background: #eff6ff; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                <?php echo str_replace('_', ' ', $g['type']); ?>
                            </span>
                        </td>
                        <td style="padding: 1rem; font-weight: 600;"><?php echo $g['member_count']; ?></td>
                        <td style="padding: 1rem;">
                            <span style="color: #10b981;"><i class="fas fa-circle" style="font-size: 0.6rem; vertical-align: middle;"></i> Active</span>
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <button style="background: none; border: none; color: var(--text-muted); cursor: pointer;" title="Edit Group"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Group Modal (Minimal Example) -->
<div id="addGroupModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <h2 class="mb-4">Register Group</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block font-bold mb-2">Group Name</label>
                <input type="text" name="name" class="form-control" style="width: 100%;" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Category</label>
                <select name="type" class="form-control" style="width: 100%;">
                    <option value="lay_group">Lay Group</option>
                    <option value="choir">Choir</option>
                    <option value="scc">Small Christian Community (SCC)</option>
                    <option value="youth">Youth Group</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-2">Description</label>
                <textarea name="description" class="form-control" style="width: 100%;" rows="3"></textarea>
            </div>
            <div class="flex" style="justify-content: flex-end; gap: 1rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addGroupModal').style.display='none'">Cancel</button>
                <button type="submit" name="add_group" class="btn btn-primary">Save Group</button>
            </div>
        </form>
    </div>
</div>
