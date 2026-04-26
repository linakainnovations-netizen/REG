<?php
$pageTitle = "Request Parish Event Post";
include_once 'includes/header.php';
require_once 'backend/mail_manager.php';

$success = false;
$error = false;

// Fetch groups for the dropdown
$stmt = $pdo->query("SELECT id, name FROM groups ORDER BY name");
$groups = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $title = trim($_POST['title']);
    $email = trim($_POST['email']);
    $group_id = $_POST['group_id'];
    $description = trim($_POST['description']);
    
    if (!empty($title) && !empty($email) && !empty($description)) {
        try {
            $token = bin2hex(random_bytes(32));
            
            $stmt = $pdo->prepare("INSERT INTO public_requests (type, title, description, requested_by_email, requested_group_id, leader_token) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$type, $title, $description, $email, $group_id, $token]);
            
            // Trigger Email to Leader (Mock leader email for now, in real life we fetch leader of $group_id)
            // For now, let's assume we send to a default admin if leader not found
            MailManager::sendApprovalRequest('council-leader@stpaulchipata.org', $title, $token);
            
            $success = "Request submitted! Verification email sent to the Group Leader.";
        } catch (PDOException $e) {
            $error = "Submission failed. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<section class="hero-premium" style="background: #1e40af; color: white; padding: 4rem 0;">
    <div class="container text-center">
        <h1>Submit Event Notice</h1>
        <p style="opacity: 0.9;">Funerals, Memorials, Weddings, and more. Submissions require Leader verification.</p>
    </div>
</section>

<div class="container mt-4 mb-4" style="max-width: 650px;">
    <div class="card">
        <?php if ($success): ?>
            <div class="text-center" style="padding: 3rem;">
                <i class="fas fa-paper-plane fa-4x mb-4" style="color: var(--primary-color);"></i>
                <h2>Request Received</h2>
                <p><?php echo $success; ?></p>
                <a href="home" class="btn btn-primary mt-4">Return Home</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="event-request" method="POST">
                <div class="grid grid-cols-2">
                    <div class="mb-4">
                        <label class="font-bold mb-2 block">Event Type</label>
                        <select name="type" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                            <option value="funeral">Funeral Notice</option>
                            <option value="memorial">Memorial Service</option>
                            <option value="wedding">Wedding Banns</option>
                            <option value="event">Group Event</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="font-bold mb-2 block">Related Group</label>
                        <select name="group_id" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                            <?php foreach ($groups as $g): ?>
                                <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="font-bold mb-2 block">Event Title</label>
                    <input type="text" name="title" placeholder="E.g. Funeral Notice: Late John Doe" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>

                <div class="mb-4">
                    <label class="font-bold mb-2 block">Your Contact Email</label>
                    <input type="email" name="email" placeholder="To receive status updates" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>

                <div class="mb-4">
                    <label class="font-bold mb-2 block">Event Details / Description</label>
                    <textarea name="description" rows="5" placeholder="Include dates, times, and venue..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required></textarea>
                </div>

                <div class="p-4 mb-4" style="background: #f1f5f9; border-radius: 0.5rem; font-size: 0.85rem;">
                    <i class="fas fa-shield-alt mr-2 text-primary"></i> 
                    To prevent false postings, your request must be digitally approved by the chosen Group Leader before it appearing on the public portal.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: 700;">Submit Request</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
