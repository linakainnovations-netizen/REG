<?php
/**
 * Priest/Admin Dashboard Overview
 * St. Paul Chipata Portal
 */

// 1. Fetch Group Distribution Data
$groupStmt = $pdo->query("SELECT type, COUNT(*) as count FROM groups GROUP BY type");
$groupData = $groupStmt->fetchAll(PDO::FETCH_ASSOC);
$groupLabels = array_column($groupData, 'type');
$groupCounts = array_column($groupData, 'count');

// 2. Fetch Monthly Collections Data (Current Year)
$financeStmt = $pdo->query("
    SELECT MONTHNAME(transaction_date) as month, SUM(amount) as total 
    FROM finance 
    WHERE YEAR(transaction_date) = YEAR(CURDATE()) 
    GROUP BY MONTH(transaction_date) 
    ORDER BY MONTH(transaction_date)
");
$financeData = $financeStmt->fetchAll(PDO::FETCH_ASSOC);
$financeLabels = array_column($financeData, 'month');
$financeTotals = array_column($financeData, 'total');

// 3. Fetch Recent Activities
$activityStmt = $pdo->query("
    (SELECT 'Announcement' as type, title as description, created_at FROM announcements)
    UNION
    (SELECT 'Finance' as type, CONCAT(type, ': K', amount) as description, created_at FROM finance)
    UNION
    (SELECT 'User' as type, CONCAT('New User: ', full_name) as description, created_at FROM users)
    ORDER BY created_at DESC LIMIT 10
");
$activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="overview-content">
    <!-- Top Stats Cards -->
    <div class="stats-grid mb-4">
        <div class="card overview-card" style="background: #fdf2f8;">
            <i class="fas fa-user-shield fa-2x" style="color: #be185d;"></i>
            <h3>Management</h3>
            <p>Review registrations.</p>
            <a href="users" class="btn btn-outline" style="width: 100%;">Users</a>
        </div>

        <div class="card overview-card" style="background: #ecfdf5;">
            <i class="fas fa-check-double fa-2x" style="color: #059669;"></i>
            <h3>Approvals</h3>
            <p>Pending publications.</p>
            <a href="announcements" class="btn btn-outline" style="width: 100%;">Verify</a>
        </div>

        <div class="card overview-card" style="background: #eff6ff;">
            <i class="fas fa-hands-helping fa-2x" style="color: #2563eb;"></i>
            <h3>Ministries</h3>
            <p>Volunteers & join requests.</p>
            <a href="ministries" class="btn btn-outline" style="width: 100%;">Ministries</a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-container mb-4">
        <div class="card chart-card">
            <h3>Groups Distribution</h3>
            <div class="chart-wrapper">
                <canvas id="groupsPieChart"></canvas>
            </div>
        </div>
        <div class="card chart-card">
            <h3>Revenue (<?php echo date('Y'); ?>)</h3>
            <div class="chart-wrapper">
                <canvas id="revenueBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card activity-card">
        <h3>Recent Activity Feed</h3>
        <div class="table-responsive">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $act): ?>
                        <tr>
                            <td><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($act['type']); ?>"><?php echo $act['type']; ?></span></td>
                            <td><?php echo htmlspecialchars($act['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Groups Pie Chart
    const groupsCtx = document.getElementById('groupsPieChart').getContext('2d');
    new Chart(groupsCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($groupLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($groupCounts); ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });

    // Revenue Bar Chart
    const revenueCtx = document.getElementById('revenueBarChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($financeLabels); ?>,
            datasets: [{
                label: 'Monthly Collections (K)',
                data: <?php echo json_encode($financeTotals); ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
