<?php
/**
 * Reset Password Page
 * St. Paul Chipata Portal
 */
$token = $_GET['token'] ?? '';
$pageTitle = "Reset Password";
include_once 'includes/header.php';

// If no token, redirect
if (empty($token)) {
    $_SESSION['error'] = "Invalid access. No token provided.";
    header("Location: login");
    exit();
}
?>

<div class="auth-page">
    <!-- Visual Half -->
    <div class="auth-visual" style="background-image: url('assets/images/other/login.jpg');">
        <div style="position: absolute; bottom: 4rem; left: 4rem; z-index: 10; color: white;">
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;">Secure <br>Reset.</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 400px;">Please choose a strong new password to protect your leadership access.</p>
        </div>
    </div>

    <!-- Form Half -->
    <div class="auth-form-container">
        <div class="auth-card">
            <div class="mb-4">
                <a href="home" class="logo mb-4" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="assets/images/other/main_logo.png" alt="Regiment Logo" style="height: 40px; width: auto;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">St. Charles Lwanga Regiment</span>
                </a>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">New Password</h2>
                <p style="color: #64748b; font-weight: 500;">Your token has been verified. Enter a new password below.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="api/auth" method="POST">
                <input type="hidden" name="action" value="reset_password_submit">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">NEW PASSWORD</label>
                    <input type="password" name="password" placeholder="At least 8 characters" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem;" required>
                </div>

                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">CONFIRM PASSWORD</label>
                    <input type="password" name="confirm_password" placeholder="Repeat new password" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem;" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); background-color: var(--accent-color);">
                    Update Password <i class="fas fa-check-circle ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
