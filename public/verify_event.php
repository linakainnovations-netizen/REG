<?php
$pageTitle = "Event Verification";
include_once 'includes/header.php';

$token = $_GET['token'] ?? '';
$verified = false;
$error = false;

if (!empty($token)) {
    // Check token in DB
    $stmt = $pdo->prepare("SELECT * FROM public_requests WHERE leader_token = ? AND status = 'pending'");
    $stmt->execute([$token]);
    $request = $stmt->fetch();

    if ($request) {
        // Appove the event
        $update = $pdo->prepare("UPDATE public_requests SET status = 'published', leader_token = NULL WHERE id = ?");
        $update->execute([$request['id']]);
        
        // Also create a dynamic announcement for it?
        $annTitle = ucfirst($request['type']) . ": " . $request['title'];
        $ann = $pdo->prepare("INSERT INTO announcements (title, content, category, status) VALUES (?, ?, 'general', 'published')");
        $ann->execute([$annTitle, $request['description']]);

        $verified = true;
    } else {
        $error = "Invalid or expired verification link.";
    }
} else {
    $error = "No verification token provided.";
}
?>

<div class="container mt-4 mb-4 text-center">
    <div class="card" style="max-width: 500px; margin: 4rem auto; padding: 3rem;">
        <?php if ($verified): ?>
            <i class="fas fa-check-circle fa-5x mb-4" style="color: #10b981;"></i>
            <h2 class="mb-2">Success!</h2>
            <p class="text-muted">The event has been verified and is now live on the Parish Portal.</p>
            <a href="announcements" class="btn btn-primary mt-4">View Public Announcements</a>
        <?php else: ?>
            <i class="fas fa-times-circle fa-5x mb-4" style="color: #ef4444;"></i>
            <h2 class="mb-2">Verification Failed</h2>
            <p class="text-muted"><?php echo $error; ?></p>
            <a href="home" class="btn btn-outline mt-4">Return Home</a>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
