<?php
/**
 * My Profile Page
 * St. Paul Chipata Portal
 */
require_once __DIR__ . '/../../../config/db.php';

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-large">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?php echo BASE_URL; ?>dashboard/admin_priest/assets/images/uploads/<?php echo $user['profile_image']; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <?php echo substr($user['full_name'], 0, 1); ?>
            <?php endif; ?>
            <div class="avatar-overlay">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        <div class="profile-info-header">
            <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p><?php echo $user['email']; ?> • <?php echo $_SESSION['role_name']; ?></p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        <!-- Account Information -->
        <div class="card p-4">
            <h3 class="mb-4"><i class="fas fa-id-card mr-2 text-primary"></i> Account Details</h3>
            <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/profile_logic.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*" id="profileImageInput">
                    <small class="text-muted">Recommended: Square image, max 2MB (JPG, PNG, WebP)</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Username (Read-only)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="background: #f8fafc;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
            </form>
        </div>

        <!-- Password Reset -->
        <div class="card p-4">
            <h3 class="mb-4"><i class="fas fa-key mr-2 text-success"></i> Change Password</h3>
            <form action="<?php echo BASE_URL; ?>dashboard/admin_priest/backend_pages/profile_logic.php" method="POST">
                <input type="hidden" name="action" value="update_password">
                
                <div class="form-group password-field">
                    <label class="form-label">Current Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="current_password" class="form-control" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group password-field">
                    <label class="form-label">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="new_password" class="form-control" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group password-field">
                    <label class="form-label">Confirm New Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" class="form-control" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="background: #059669; width: 100%;">Update Password</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Optional: Click on avatar to trigger file input
    const avatar = document.querySelector('.profile-avatar-large');
    const fileInput = document.getElementById('profileImageInput');
    if (avatar && fileInput) {
        avatar.addEventListener('click', () => fileInput.click());
    }
});
</script>
