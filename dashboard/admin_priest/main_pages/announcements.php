<?php
/**
 * Announcements Management
 * St. Paul Chipata Portal - Priest Dashboard
 */
require_once __DIR__ . '/../../../config/db.php';

// Fetch announcements with author info
$sql = "SELECT a.*, u.full_name as author_name 
        FROM announcements a 
        LEFT JOIN users u ON a.author_id = u.id 
        ORDER BY a.created_at DESC";
$announcements = $pdo->query($sql)->fetchAll();
?>

<div class="announcements-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Parish Announcements</h1>
            <p class="text-muted">Review, approve, and manage all parish communications.</p>
        </div>
        <button class="btn btn-primary" id="newAnnouncementBtn">
            <i class="fas fa-plus mr-2"></i> Create Global Announcement
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 mb-4" style="gap: 1.5rem;">
        <div class="card p-4 bg-white shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-blue-light text-blue mr-3"><i class="fas fa-paper-plane"></i></div>
                <div>
                    <h3 class="mb-0"><?php echo count($announcements); ?></h3>
                    <small class="text-muted">Total Posted</small>
                </div>
            </div>
        </div>
        <div class="card p-4 bg-white shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-yellow-light text-yellow mr-3"><i class="fas fa-clock"></i></div>
                <div>
                    <h3 class="mb-0"><?php echo count(array_filter($announcements, function($a) { return $a['status'] === 'pending'; })); ?></h3>
                    <small class="text-muted">Awaiting Approval</small>
                </div>
            </div>
        </div>
        <div class="card p-4 bg-white shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-green-light text-green mr-3"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3 class="mb-0">12</h3>
                    <small class="text-muted">Active in Chinyanja</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="p-4">Announcement</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Author</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td class="p-4">
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($a['title']); ?></div>
                                    <small class="text-muted"><?php echo substr(strip_tags($a['content']), 0, 60); ?>...</small>
                                </td>
                                <td class="p-4">
                                    <span class="badge badge-outline"><?php echo ucfirst($a['category']); ?></span>
                                </td>
                                <td class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-small mr-2"><?php echo substr($a['author_name'], 0, 1); ?></div>
                                        <span><?php echo htmlspecialchars($a['author_name']); ?></span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <?php 
                                    $statusClass = [
                                        'pending' => 'status-pending',
                                        'published' => 'status-published',
                                        'rejected' => 'status-rejected'
                                    ][$a['status']] ?? 'status-pending';
                                    ?>
                                    <span class="status-pill <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($a['status']); ?>
                                    </span>
                                </td>
                                <td class="p-4 text-muted">
                                    <?php echo date('M d, H:i', strtotime($a['created_at'])); ?>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($a['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-success mr-1" title="Approve"><i class="fas fa-check"></i></button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-light" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
