<?php
$pageTitle = "Sign In";
include_once 'includes/header.php';
?>

<div class="auth-page">
    <!-- Visual Half -->
    <div class="auth-visual" style="background-image: url('<?php echo BASE_URL; ?>assets/images/other/login.jpg');">
        <div style="position: absolute; bottom: 4rem; left: 4rem; z-index: 10; color: white;">
            <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;">Faith & <br>Technology</h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 400px;">Managing our community with transparency, tradition, and digital innovation.</p>
        </div>
    </div>

    <!-- Form Half -->
    <div class="auth-form-container">
        <div class="auth-card">
            <div class="mb-4">
                <a href="../home" class="logo mb-4" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="assets/images/other/main_logo.png" alt="Regiment Logo" style="height: 40px; width: auto;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">St. Charles Lwanga Regiment</span>
                </a>
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 2rem;">Welcome Back</h2>
                <p style="color: #64748b; font-weight: 500;">Please enter your credentials to access the portal.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="api/auth" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">USERNAME OR EMAIL</label>
                    <input type="text" name="login_id" placeholder="e.g. jdoe24" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem;" required>
                </div>

                <div class="mb-4">
                    <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; font-size: 0.875rem; color: #475569;">PASSWORD</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="passwordInput" style="width: 100%; padding: 0.85rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem;" required>
                        <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; transition: color 0.2s;"></i>
                    </div>
                </div>

                <script>
                    const togglePassword = document.querySelector('#togglePassword');
                    const password = document.querySelector('#passwordInput');

                    togglePassword.addEventListener('click', function (e) {
                        // toggle the type attribute
                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                        password.setAttribute('type', type);
                        // toggle the eye slash icon
                        this.classList.toggle('fa-eye-slash');
                        this.style.color = type === 'text' ? 'var(--primary-color)' : '#94a3b8';
                    });
                </script>

                <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #64748b; cursor: pointer;">
                        <input type="checkbox" name="remember" style="width: 1rem; height: 1rem;"> Remember me
                    </label>
                    <a href="forgot_password" style="font-size: 0.875rem; color: var(--primary-color); font-weight: 600;">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 700; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);">
                    Sign Into Portal <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p style="color: #64748b;">New leadre? <a href="../register" style="color: var(--primary-color); font-weight: 800;">Create Administrator Account</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
