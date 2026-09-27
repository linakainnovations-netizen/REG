<?php
$pageTitle = "Parish Events";
include_once 'includes/header.php';
try {
    $parishEvents = $pdo->query("SELECT * FROM parish_events WHERE status = 'published' AND event_date >= CURDATE() - INTERVAL 7 DAY ORDER BY event_date ASC LIMIT 30")->fetchAll();
} catch (PDOException $e) { $parishEvents = null; }
try {
    $notices = $pdo->query("SELECT * FROM public_requests WHERE status IN ('verified','published') ORDER BY created_at DESC LIMIT 20")->fetchAll();
} catch (PDOException $e) { $notices = []; }
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Parish Events</h1>
        <p style="opacity: 0.9;">Masses, fundraisers, meetings — plus verified funeral / wedding notices.</p>
        <a href="event_request" class="btn mt-4" style="background: white; color: #1e40af; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.5rem;">+ Request Event Notice</a>
    </div>
</section>
<div class="container mt-4 mb-4" style="max-width: 900px;">
    <?php if ($parishEvents === null): ?>
        <div class="card text-center" style="padding: 2rem;"><p>Events module not installed yet — run <code>database_public_cms_updates.sql</code>, then publish from Dashboard → Events.</p></div>
    <?php elseif (!empty($parishEvents)): ?>
        <h2 class="mb-4">Upcoming</h2>
        <div class="card p-0 mb-4"><div class="table-responsive"><table style="width:100%; border-collapse: collapse;">
            <thead><tr style="background: #eff6ff; text-align: left;">
                <th style="padding: 1rem;">Event</th>
                <th style="padding: 1rem;">Date & Time</th>
                <th style="padding: 1rem;">Venue</th>
            </tr></thead>
            <tbody>
            <?php foreach ($parishEvents as $e): ?>
            <tr style="border-top: 1px solid #dbeafe;">
                <td style="padding: 1rem;"><span class="badge" style="background: #dbeafe; color: #1e40af; font-size: 0.7rem; text-transform: uppercase;"><?php echo htmlspecialchars($e['category']); ?></span><br>
                <strong><?php echo htmlspecialchars($e['title']); ?></strong><?php echo !empty($e['description']) ? '<br><small class="text-muted">' . nl2br(htmlspecialchars(substr($e['description'], 0, 120))) . '</small>' : ''; ?></td>
                <td style="padding: 1rem; white-space: nowrap; font-weight: 700; color: #1e40af;"><?php echo date('D, M j, Y', strtotime($e['event_date'])); ?><?php echo $e['event_time'] ? '<br><small class="text-muted">' . htmlspecialchars($e['event_time']) . '</small>' : ''; ?></td>
                <td style="padding: 1rem;" class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($e['venue']); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div></div>
    <?php endif; ?>
    <?php if (!empty($notices)): ?>
        <h2 class="mb-4">Notices (funerals, weddings, memorials)</h2>
        <div class="card p-0"><div class="table-responsive"><table style="width:100%; border-collapse: collapse;">
            <thead><tr style="background: #f8fafc; text-align: left;">
                <th style="padding: 1rem;">Notice</th>
                <th style="padding: 1rem;">Type</th>
                <th style="padding: 1rem;">Posted</th>
            </tr></thead>
            <tbody>
            <?php foreach ($notices as $n): ?>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($n['title']); ?></strong><br><small class="text-muted"><?php echo nl2br(htmlspecialchars(substr($n['description'], 0, 140))); ?></small></td>
                <td style="padding: 1rem;"><span class="badge" style="background: #f1f5f9; font-size: 0.7rem; text-transform: uppercase;"><?php echo htmlspecialchars($n['type']); ?></span></td>
                <td style="padding: 1rem; white-space: nowrap;" class="text-muted"><?php echo date('M j, Y', strtotime($n['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div></div>
    <?php endif; ?>
    <?php if (($parishEvents === [] || $parishEvents === null) && empty($notices)): ?>
        <div class="card text-center" style="padding: 3rem;"><p class="text-muted">No events published yet.</p></div>
    <?php endif; ?>
</div>
<?php include_once 'includes/footer.php'; ?>
