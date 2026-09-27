<?php
/**
 * Invite New Leader
 * Automated Activation Code Generation & Email Delivery
 */
require_once __DIR__ . '/../../../config/db.php';

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_invitation'])) {
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'];
    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : null;
    
    // Generate 6-digit Numeric Code
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiry = date('Y-m-d H:i:s', strtotime('+48 hours'));

    try {
        $stmt = $pdo->prepare("INSERT INTO invitations (email, activation_code, role_id, group_id, expires_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$email, $code, $role_id, $group_id, $expiry]);

        // Trigger Email
        require_once __DIR__ . '/../../../backend/mail_manager.php';
        $subject = "Parish Leadership Invitation - St. Charles Lwanga Regiment";
        $body = "
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
                <h2 style='color: #1e3a8a;'>You are Invited!</h2>
                <p>You have been formally invited to join the St. Charles Lwanga Regiment leadership portal.</p>
                <div style='background: #f1f5f9; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                    <p style='font-size: 0.875rem; color: #64748b; margin-bottom: 5px;'>YOUR ACTIVATION CODE</p>
                    <h1 style='font-size: 2.5rem; letter-spacing: 10px; margin: 0; color: #1e3a8a;'>$code</h1>
                </div>
                <p>Click the link below and enter your code to create your administrator account:</p>
                <a href='http://localhost/St_Charles_Lwanga_Regiment_Portal/register' style='display: inline-block; background: #1e3a8a; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Activate Account</a>
                <p style='font-size: 0.8rem; color: #94a3b8; margin-top: 20px;'>This code expires in 48 hours.</p>
            </div>
        ";
        MailManager::send($email, $subject, $body);
        $success = "Invitation sent successfully to $email!";
    } catch (PDOException $e) {
        $error = "Failed to send invitation: " . $e->getMessage();
    }
}

// Fetch Roles for Selection (Only level 1-5 admins)
$roles = $pdo->query("SELECT id, role_name FROM roles WHERE role_level <= 5 ORDER BY role_level ASC")->fetchAll();
$groups = $pdo->query("SELECT id, name FROM groups ORDER BY name ASC")->fetchAll();
?>

<div class="invite-leader-container">
    <div class="mb-4">
        <h1>Invite New Administrator</h1>
        <p class="text-muted">Generate a secure activation code and invite a new leader to the portal.</p>
    </div>

    <?php if ($success): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border-left: 5px solid #10b981;">
            <h4 style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle mr-2"></i> Success!</h4>
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem; border-left: 5px solid #ef4444;">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2" style="gap: 3rem;">
        <div class="card p-5">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label font-weight-bold">Leader's Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. j.banda@outlook.com" style="width: 100%;" required>
                </div>

                <div class="mb-4">
                    <label class="form-label font-weight-bold">Assigned Role</label>
                    <select name="role_id" class="form-control" style="width: 100%;" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo $r['role_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label font-weight-bold">Parish Group (If applicable)</label>
                    <select name="group_id" class="form-control" style="width: 100%;">
                        <option value="">Full Parish Admin (No specific group)</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo $g['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="send_invitation" class="btn btn-primary" style="width: 100%; padding: 1rem; margin-top: 1rem;">
                    Generate & Send Activation <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </form>
        </div>

        <div>
            <div class="card p-4" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <h3 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Registration Security</h3>
                <ul class="text-muted small">
                    <li class="mb-2">Administrators <strong>cannot</strong> register freely; they must be invited.</li>
                    <li class="mb-2">Codes are unique and expire after <strong>48 hours</strong>.</li>
                    <li class="mb-2">The email contains a secure link that pre-fills their designation.</li>
                    <li>Once used, the code is immediately deactivated.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
