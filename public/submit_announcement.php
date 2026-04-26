<?php
$pageTitle = "Submit Sunday Announcement";
include_once 'includes/header.php';

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'] ?? 'general';
    $author_name = trim($_POST['author_name']); // For public submission tracking

    if (!empty($title) && !empty($content)) {
        try {
            // If logged in, use user_id, otherwise null (or specific public author ID)
            $author_id = $_SESSION['user_id'] ?? null;
            
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, author_id, category, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$title, $content, $author_id, $category]);
            $success = "Your announcement has been submitted for review. Thank you!";
        } catch (PDOException $e) {
            $error = "Submission failed. Please try again later.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<section class="hero-premium" style="background: #be185d; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Submit Sunday Announcement</h1>
        <p style="opacity: 0.9;">Help us share important news with the Parish community.</p>
    </div>
</section>

<div class="container mt-4 mb-4" style="max-width: 600px;">
    <div class="card">
        <?php if ($success): ?>
            <div class="text-center" style="padding: 2rem;">
                <i class="fas fa-check-circle fa-4x mb-4" style="color: var(--accent-color);"></i>
                <h2>Thank You!</h2>
                <p><?php echo $success; ?></p>
                <a href="announcements" class="btn btn-primary mt-4">Back to News</a>
            </div>
        <?php else: ?>
            <h2 class="mb-4">Announcement Details</h2>
            
            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="submit-announcement" method="POST">
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Your Name / Group</label>
                    <input type="text" name="author_name" placeholder="E.g. St. Jude SCC" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;" required>
                </div>

                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Announcement Title</label>
                    <input type="text" name="title" placeholder="Brief subject" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;" required>
                </div>

                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Category</label>
                    <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                        <option value="general">General Parish</option>
                        <option value="youth">Youth Focused</option>
                        <option value="liturgy">Liturgy / Church</option>
                        <option value="finance">Finance / Appeal</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Announcement Content</label>
                    <textarea name="content" rows="6" placeholder="Full details of the announcement..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;" required></textarea>
                </div>

                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;" class="text-muted">
                    <i class="fas fa-info-circle mr-2"></i> Submissions are reviewed by the Parish Council or Youth Office before they appear on the public board.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Submit for Review</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
