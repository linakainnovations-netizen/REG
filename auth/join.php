<?php
/**
 * Public Member Registration (Join the Parish Portal)
 * Leader activation (invitation codes) lives at `register`.
 */
$pageTitle = "Join the Parish Portal";
include_once 'includes/header.php';

try {
    $groups = $pdo->query("SELECT id, name FROM groups ORDER BY name")->fetchAll();
} catch (PDOException $e) { $groups = []; }
?>
<div class="auth-page">
    <!-- Visual Half -->
    <div class="auth-visual" style="background-image: url('<?php echo BASE_URL; ?>assets/images/other/signup.jpg');">
        <div style="position: absolute; bottom: 4rem; left: 4rem; z-index: 10; color: white;">
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;">Join the <br>Community.</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 400px;">Create your parish account to join ministries, volunteer and stay updated.</p>
        </div>
    </div>

    <!-- Form Half -->
    <div class="auth-form-container">
        <div class="auth-card">
            <div class="mb-4">
                <a href="<?php echo BASE_URL; ?>home" class="logo mb-4" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo/original_logo.jpeg" alt="St. Charles Lwanga Logo" style="height: 40px; width: auto;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">St. Charles Lwanga</span>
                </a>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">Create Account</h2>
                <p style="color: #64748b; font-weight: 500;">Members get a dashboard, ministry signup and live updates.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>api/auth" method="POST">
                <input type="hidden" name="action" value="member_register">
                <div class="grid grid-cols-2" style="gap: 1rem;">
                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">FULL NAME</label>
                        <input type="text" name="full_name" placeholder="Mary Zulu" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>
                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">USERNAME</label>
                        <input type="text" name="username" placeholder="maryz" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>
                </div>
                <div class="grid grid-cols-2" style="gap: 1rem;">
                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">EMAIL (optional)</label>
                        <input type="email" name="email" placeholder="you@example.com" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    </div>
                    <div class="mb-4">
                        <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">PHONE</label>
                        <input type="text" name="phone" placeholder="0975..." style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">PARISH GROUP (optional)</label>
                    <select name="group_id" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <option value="">— None yet —</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">PASSWORD</label>
                    <input type="password" name="password" placeholder="At least 8 characters" minlength="8" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700;">
                    Create My Account <i class="fas fa-check-circle ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p style="color: #64748b;">Already have an account? <a href="<?php echo BASE_URL; ?>login" style="color: var(--primary-color); font-weight: 800;">Sign In</a></p>
                <p style="color: #64748b; font-size: 0.85rem;">Parish leader with an invitation code? <a href="<?php echo BASE_URL; ?>register" style="color: var(--primary-color); font-weight: 700;">Activate here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
