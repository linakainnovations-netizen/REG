<?php
/**
 * Forgot Password Request Page
 * St. Paul Chipata Portal
 */
$pageTitle = "Forgot Password";
include_once 'includes/header.php';
?>

<div class="auth-page">
    <!-- Visual Half -->
    <div class="auth-visual" style="background-image: url('assets/images/other/main1.jpeg');">
        <div style="position: absolute; bottom: 4rem; left: 4rem; z-index: 10; color: white;">
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;">Never Locked <br>Out.</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 400px;">Recover your access securely through our automated token system.</p>
        </div>
    </div>

    <!-- Form Half -->
    <div class="auth-form-container">
        <div class="auth-card">
            <div class="mb-4">
                <a href="home" class="logo mb-4" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="assets/images/other/saint_logo.png" alt="St. Charles Lwanga Logo" style="height: 40px; width: auto;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">St. Charles Lwanga Regiment</span>
                </a>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">Reset Access</h2>
                <p style="color: #64748b; font-weight: 500;">Enter your registered email below to receive a reset link.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="api/auth" method="POST">
                <input type="hidden" name="action" value="forgot_password_request">
                
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">EMAIL ADDRESS</label>
                    <input type="email" name="email" placeholder="e.g. leader@stcharleslwangaregiment.org" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem;" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);">
                    Send Reset Link <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p style="color: #64748b;">Remembered your password? <a href="login" style="color: var(--primary-color); font-weight: 800;">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
