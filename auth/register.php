<?php
/**
 * Invited Leader Registration: Multi-Stage Activation
 * St. Paul Chipata Portal
 */
$pageTitle = "Activate Admin Account";
include_once 'includes/header.php';

$step = isset($_SESSION['activation_id']) ? 2 : 1;
$invitation = null;

if ($step === 2) {
    $stmt = $pdo->prepare("SELECT i.*, r.role_name FROM invitations i JOIN roles r ON i.role_id = r.id WHERE i.id = ?");
    $stmt->execute([$_SESSION['activation_id']]);
    $invitation = $stmt->fetch();
    
    if (!$invitation) {
        unset($_SESSION['activation_id']);
        $step = 1;
    }
}
?>

<div class="auth-page">
    <!-- Visual Half -->
    <div class="auth-visual" style="background-image: url('assets/images/other/signup.jpg');">
        <div style="position: absolute; bottom: 4rem; left: 4rem; z-index: 10; color: white;">
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;">Leadership <br>Activation.</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 400px;">Setting up your secure administrative access to the St. Charles Lwanga Regiment Portal.</p>
        </div>
    </div>

    <!-- Form Half -->
    <div class="auth-form-container">
        <div class="auth-card">
            <div class="mb-4">
                <a href="home" class="logo mb-4" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="assets/images/logo/original_logo.jpeg" alt="Regiment Logo" style="height: 40px; width: auto;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">St. Charles Lwanga Regiment</span>
                </a>
                
                <?php if ($step === 1): ?>
                    <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">Verify Code</h2>
                    <p style="color: #64748b; font-weight: 500;">Enter the activation code sent to your email by the Parish leadership.</p>
                <?php else: ?>
                    <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">Setup Profile</h2>
                    <p style="color: #64748b; font-weight: 500;">Code verified! You are activating as: <br><strong style="color: var(--primary-color);"><?php echo htmlspecialchars($invitation['role_name']); ?></strong></p>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <!-- Step 1: Code Verification -->
                <form action="api/auth" method="POST">
                    <input type="hidden" name="action" value="verify_activation_code">
                    
                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">6-DIGIT ACTIVATION CODE</label>
                        <input type="text" name="code" placeholder="e.g. 123456" maxlength="6" style="width: 100%; padding: 1rem; border: 2px solid var(--border-color); border-radius: 0.5rem; font-size: 1.5rem; text-align: center; letter-spacing: 0.5em; font-weight: 800;" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700;">
                        Verify Activation <i class="fas fa-shield-alt ml-2"></i>
                    </button>
                </form>
            <?php else: ?>
                <!-- Step 2: Final Registration -->
                <form action="api/auth" method="POST">
                    <input type="hidden" name="action" value="finalize_activation">
                    <input type="hidden" name="invitation_id" value="<?php echo $invitation['id']; ?>">
                    
                    <div class="grid grid-cols-2" style="gap: 1rem;">
                        <div class="mb-4">
                            <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">FULL NAME</label>
                            <input type="text" name="full_name" placeholder="John Doe" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                        </div>
                        <div class="mb-4">
                            <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">USERNAME</label>
                            <input type="text" name="username" placeholder="jdoe24" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">EMAIL ADDRESS (Confirmed)</label>
                        <input type="email" value="<?php echo htmlspecialchars($invitation['email']); ?>" disabled style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: #f1f5f9; color: #64748b;">
                    </div>

                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">PASSWORD</label>
                        <input type="password" name="password" placeholder="At least 8 characters" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700;">
                        Create My Account <i class="fas fa-check-circle ml-2"></i>
                    </button>
                </form>
            <?php endif; ?>

            <div class="text-center mt-4">
                <p style="color: #64748b;">Already have an account? <a href="login" style="color: var(--primary-color); font-weight: 800;">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
