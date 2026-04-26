<?php
/**
 * Authentication Logic
 * St. Paul Chipata Portal
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_helpers.php';

/**
 * Handle Forgot Password Request
 */
function handleForgotPasswordRequest($pdo) {
    $email = trim($_POST['email']);
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save token
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expiry]);

        // Send Email (Assuming MailManager is configured)
        require_once __DIR__ . '/mail_manager.php';
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/St._Paul_Chipata_Portal/reset_password?token=" . $token;
        $body = "<h2>Password Reset</h2><p>You requested a password reset. Click below to continue:</p><a href='$resetLink'>Reset My Password</a><p>This link expires in 1 hour.</p>";
        MailManager::send($email, "Password Reset Request", $body);

        $_SESSION['success'] = "If that email is registered, a reset link has been sent.";
    } else {
        // Obfuscate for security - same message even if email not found
        $_SESSION['success'] = "If that email is registered, a reset link has been sent.";
    }
    header("Location: ../forgot_password");
    exit();
}

/**
 * Handle Reset Password Submission
 */
function handleResetPasswordSubmit($pdo) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../reset_password?token=" . $token);
        exit();
    }

    // Validate Token
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $request = $stmt->fetch();

    if ($request) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Update User
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashed, $request['email']]);

        // Delete Token
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$request['email']]);

        $_SESSION['success'] = "Password updated successfully. You can now log in.";
        header("Location: ../login");
    } else {
        $_SESSION['error'] = "Invalid or expired token.";
        header("Location: ../forgot_password");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($pdo);
        break;
    case 'verify_activation_code':
        handleVerifyActivationCode($pdo);
        break;
    case 'finalize_activation':
        handleFinalizeActivation($pdo);
        break;
    case 'forgot_password_request':
        handleForgotPasswordRequest($pdo);
        break;
    case 'reset_password_submit':
        handleResetPasswordSubmit($pdo);
        break;
    default:
        $_SESSION['error'] = "Invalid action.";
        header('Location: ../login');
        exit;
}

function handleLogin($pdo) {
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT u.*, r.role_name, r.role_level FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? OR u.email = ?");
    $stmt->execute([$login_id, $login_id]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        // Success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['role_level'] = $user['role_level'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['profile_image'] = $user['profile_image'];

        header('Location: ../dashboard');
        exit;
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header('Location: ../login');
        exit;
    }
}


/**
 * Handle Activation Code Verification
 */
function handleVerifyActivationCode($pdo) {
    $code = trim($_POST['activation_code']);
    $stmt = $pdo->prepare("SELECT * FROM invitations WHERE activation_code = ? AND expires_at > NOW()");
    $stmt->execute([$code]);
    $invitation = $stmt->fetch();

    if ($invitation) {
        $_SESSION['activation_id'] = $invitation['id'];
        $_SESSION['activation_email'] = $invitation['email'];
        header('Location: ../register?step=final');
    } else {
        $_SESSION['error'] = "Invalid or expired activation code.";
        header('Location: ../register');
    }
    exit;
}

/**
 * Handle Final Activation (User Account Creation)
 */
function handleFinalizeActivation($pdo) {
    if (!isset($_SESSION['activation_id'])) {
        header('Location: ../register');
        exit;
    }

    $id = $_SESSION['activation_id'];
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Fetch invitation details
    $stmt = $pdo->prepare("SELECT * FROM invitations WHERE id = ?");
    $stmt->execute([$id]);
    $inv = $stmt->fetch();

    if ($inv) {
        $customId = generateCustomId($pdo);
        $stmt = $pdo->prepare("INSERT INTO users (custom_id, full_name, email, username, password, role_id, group_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customId, $full_name, $inv['email'], $username, $password, $inv['role_id'], $inv['group_id']]);

        // Cleanup
        $stmt = $pdo->prepare("DELETE FROM invitations WHERE id = ?");
        $stmt->execute([$id]);
        unset($_SESSION['activation_id'], $_SESSION['activation_email']);

        $_SESSION['success'] = "Account created successfully! Please log in.";
        header('Location: ../login');
    } else {
        $_SESSION['error'] = "Activation session expired.";
        header('Location: ../register');
    }
    exit;
}
?>
