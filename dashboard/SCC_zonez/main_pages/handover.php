<?php
/**
 * Leadership Handover Module
 * Initiates the transition from current leader to a successor.
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../backend/auth_helpers.php'; // For generateCustomId

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newLeaderName = trim($_POST['full_name']);
    $newLeaderEmail = trim($_POST['email']);
    $newLeaderPhone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($newLeaderName) && !empty($newLeaderEmail)) {
        try {
            $pdo->beginTransaction();

            // 1. Mark current user as Transistional
            $expiryDate = date('Y-m-d', strtotime('+30 days'));
            $currentUserId = $_SESSION['user_id'];
            $currentRoleId = $_SESSION['role_id'];
            
            $stmt = $pdo->prepare("UPDATE users SET is_transitional = 1, expiry_date = ?, original_role_id = ? WHERE id = ?");
            $stmt->execute([$expiryDate, $currentRoleId, $currentUserId]);

            // 2. Create the New Leader
            $customId = generateCustomId($pdo);
            $username = strtolower(explode(' ', $newLeaderName)[0]) . rand(10, 99);
            
            $stmt = $pdo->prepare("INSERT INTO users (custom_id, full_name, email, username, password, role_id, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customId, $newLeaderName, $newLeaderEmail, $username, $password, $currentRoleId, $newLeaderPhone]);

            $pdo->commit();
            $success = "Handover initiated! Your successor ($newLeaderName) has been created. Your account will automatically expire on $expiryDate.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Handover failed: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<div class="handover-container">
    <div class="page-header mb-4">
        <h1>Leadership Handover</h1>
        <p class="text-muted">Initiate the transition to your successor. Once confirmed, you will remain an admin for 30 days before your account is automatically removed.</p>
    </div>

    <?php if ($success): ?>
        <div class="card" style="background: #ecfdf5; border-color: #10b981; color: #065f46; padding: 2rem; text-align: center;">
            <i class="fas fa-handshake fa-4x mb-4"></i>
            <h2>Success!</h2>
            <p><?php echo $success; ?></p>
        </div>
    <?php else: ?>
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div class="card">
                <h3 class="mb-4">Invite Successor</h3>
                <?php if ($error): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="block font-bold mb-2">New Leader's Full Name</label>
                        <input type="text" name="full_name" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold mb-2">Email Address</label>
                        <input type="email" name="email" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold mb-2">Phone Number</label>
                        <input type="text" name="phone" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold mb-2">Temporary Password</label>
                        <input type="password" name="password" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-weight: 700;">
                        Confirm Handover & Create Successor
                    </button>
                </form>
            </div>

            <div class="card" style="background: #f8fafc;">
                <h3>Handover Policy</h3>
                <ul style="padding-left: 1.5rem; margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                    <li><strong>Smooth Transition:</strong> Both the outgoing and incoming leaders will have full admin access for 30 days.</li>
                    <li><strong>Data Integrity:</strong> All previous contributions (posts, financial records) remain attributed to the outgoing leader.</li>
                    <li><strong>Automatic Removal:</strong> Exactly 30 days after this action, the outgoing leader's account will be permanently removed.</li>
                    <li><strong>Verification:</strong> Ensure the recipient's email is correct as they will need it for account recovery.</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
