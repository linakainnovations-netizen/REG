<?php
/**
 * User Directory & Management
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

// Fetch all users with their roles and groups
$sql = "SELECT u.*, r.role_name, g.name as group_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id 
        LEFT JOIN groups g ON u.group_id = g.id 
        ORDER BY u.created_at DESC";
$users = $pdo->query($sql)->fetchAll();
?>

<div class="user-directory-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>User Directory</h1>
            <p class="text-muted">Manage all parish members, leaders, and administration staff.</p>
        </div>
        <div>
            <a href="invite_leader" class="btn btn-primary">
                <i class="fas fa-user-plus mr-2"></i> Invite New Leader
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-4 mb-4" style="gap: 1.5rem;">
        <div class="card p-4" style="border-left: 4px solid #3b82f6;">
            <small class="text-muted text-uppercase font-weight-bold">Total Users</small>
            <h2 class="mb-0"><?php echo count($users); ?></h2>
        </div>
        <div class="card p-4" style="border-left: 4px solid #10b981;">
            <small class="text-muted text-uppercase font-weight-bold">Active Leaders</small>
            <h2 class="mb-0"><?php 
                echo count(array_filter($users, function($u) { return $u['role_id'] < 10; })); 
            ?></h2>
        </div>
        <div class="card p-4" style="border-left: 4px solid #f59e0b;">
            <small class="text-muted text-uppercase font-weight-bold">Parish Groups</small>
            <h2 class="mb-0">12</h2> <!-- This can be dynamic later -->
        </div>
        <div class="card p-4" style="border-left: 4px solid #ef4444;">
            <small class="text-muted text-uppercase font-weight-bold">Pending Requests</small>
            <h2 class="mb-0">5</h2>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="p-4 text-left">User</th>
                            <th class="p-4 text-left">Role</th>
                            <th class="p-4 text-left">Parish Group</th>
                            <th class="p-4 text-left">Phone</th>
                            <th class="p-4 text-left">Joined</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle mr-3" style="background: <?php echo $user['role_id'] <= 2 ? '#1e3a8a' : '#e2e8f0'; ?>; color: <?php echo $user['role_id'] <= 2 ? 'white' : '#64748b'; ?>;">
                                            <?php 
                                            $names = explode(' ', $user['full_name']);
                                            echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                            ?>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($user['custom_id']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="badge <?php 
                                        echo $user['role_id'] <= 2 ? 'badge-priest' : ($user['role_id'] <= 5 ? 'badge-leader' : 'badge-member'); 
                                    ?>">
                                        <?php echo htmlspecialchars($user['role_name']); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-muted">
                                    <?php echo $user['group_name'] ? htmlspecialchars($user['group_name']) : '<i class="text-light">General Parish</i>'; ?>
                                </td>
                                <td class="p-4 text-muted">
                                    <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : '---'; ?>
                                </td>
                                <td class="p-4 text-muted">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="p-4 text-right">
                                    <button class="btn btn-icon" title="View Profile"><i class="fas fa-eye text-primary"></i></button>
                                    <button class="btn btn-icon" title="Edit User"><i class="fas fa-edit text-muted"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

