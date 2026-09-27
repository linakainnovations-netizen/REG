<?php
$pageTitle = "Ministries & Volunteers";
include_once 'includes/header.php';

$success = $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ministry_id'], $_POST['full_name'], $_POST['phone'])) {
    $mid = intval($_POST['ministry_id']);
    $name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $note = trim($_POST['note'] ?? '');
    if ($mid && $name && $phone) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ministry_members (ministry_id, full_name, phone, user_id, note) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$mid, $name, $phone, $_SESSION['user_id'] ?? null, $note ?: null]);
            $success = "Thank you, $name! Your request was sent to the ministry leader for approval.";
        } catch (PDOException $e) { $error = "Submission failed. " . (strpos($e->getMessage(), "doesn't exist") !== false ? "Run database_public_cms_updates.sql first." : "Please try again."); }
    } else { $error = "Name, phone and ministry are required."; }
}
try { $ministries = $pdo->query("SELECT * FROM ministries WHERE status='active' ORDER BY name")->fetchAll(); }
catch (PDOException $e) { $ministries = null; }
$joinId = intval($_GET['join'] ?? 0);
?>
<section class="hero-premium" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Ministries & Volunteers</h1>
        <p style="opacity: 0.9; max-width: 620px; margin: 0 auto;">Pick a ministry, tap Join, and the leader approves you. No account needed — members can also join from their dashboard.</p>
        <div class="mt-4 flex" style="gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="live-updates" class="btn" style="background: white; color: #1e3a8a; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.5rem;"><i class="fas fa-bolt mr-2"></i> Live Updates</a>
            <a href="events" class="btn" style="background: rgba(255,255,255,0.15); color: white; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.3);">Events</a>
        </div>
    </div>
</section>
<div class="container mt-4 mb-4">
    <?php if ($success): ?><div class="card p-4 mb-4" style="background:#d1fae5; color:#065f46;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="card p-4 mb-4" style="background:#fee2e2; color:#991b1b;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($ministries === null): ?>
        <div class="card text-center" style="padding: 3rem;"><p>Ministries module not installed yet — run <code>database_public_cms_updates.sql</code>.</p></div>
    <?php elseif (empty($ministries)): ?>
        <div class="card text-center" style="padding: 3rem;"><p class="text-muted">No ministries listed yet.</p></div>
    <?php else: ?>
        <div class="grid grid-cols-3">
        <?php foreach ($ministries as $m): ?>
            <div class="card" style="padding: 1.5rem; border-top: 4px solid var(--primary-color);">
                <h3><?php echo htmlspecialchars($m['name']); ?></h3>
                <p class="text-muted" style="font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars(substr($m['description'] ?? '', 0, 140))); ?></p>
                <?php if (!empty($m['meeting_info'])): ?><small class="text-muted"><i class="fas fa-clock mr-1"></i><?php echo htmlspecialchars($m['meeting_info']); ?></small><br><?php endif; ?>
                <?php if (!empty($m['leader_name'])): ?><small class="text-muted"><i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($m['leader_name']); ?></small><?php endif; ?>
                <a href="ministries?join=<?php echo $m['id']; ?>#join-form" class="btn btn-primary mt-4" style="width: 100%;">Join Ministry</a>
            </div>
        <?php endforeach; ?>
        </div>
        <div class="card mt-4" id="join-form" style="padding: 2rem; max-width: 640px; margin: 2rem auto 0;">
            <h3 class="mb-4">Volunteer signup</h3>
            <form method="POST" class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <select name="ministry_id" required style="grid-column: 1 / -1; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <?php foreach ($ministries as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo $joinId === (int)$m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['name']); ?></option><?php endforeach; ?>
                </select>
                <input type="text" name="full_name" placeholder="Full name" required style="padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <input type="text" name="phone" placeholder="Phone e.g. 0975..." required style="padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <input type="text" name="note" placeholder="Anything to tell the leader? (optional)" style="grid-column: 1 / -1; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                <button class="btn btn-primary" style="grid-column: 1 / -1; padding: 1rem;">Send Join Request</button>
            </form>
            <p class="text-muted mt-4" style="font-size: 0.8rem;">Leaders approve from Dashboard → Ministries. Approved volunteers show in rosters.</p>
        </div>
    <?php endif; ?>
    <div class="card mt-4 text-center" style="background: #f1f5f9; border: none; padding: 3rem;">
        <h2 class="mb-2">Are you a Parish Leader?</h2>
        <p class="text-muted mb-4">Access your specialized dashboard to manage liturgy, finance, and community communications.</p>
        <div class="flex" style="justify-content: center; gap: 1rem;">
            <a href="login" class="btn btn-primary" style="padding: 0.75rem 2.5rem;">Leader Sign In</a>
            <a href="register" class="btn btn-outline" style="padding: 0.75rem 2.5rem;">Join the Community</a>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>
<style>@media (max-width: 768px) { .grid { grid-template-columns: 1fr !important; } }</style>
