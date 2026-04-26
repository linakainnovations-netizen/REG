<?php
/**
 * Profile Management Logic
 * St. Paul Chipata Portal
 */
require_once __DIR__ . '/../includes/session_manager.php';
SessionManager::start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/profile');
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        handleUpdateProfile($pdo, $user_id);
        break;
    case 'update_password':
        handleUpdatePassword($pdo, $user_id);
        break;
    default:
        $_SESSION['error'] = "Invalid action.";
        header('Location: ' . BASE_URL . 'dashboard/profile');
        exit;
}

/**
 * Handle Profile Information Update
 */
function handleUpdateProfile($pdo, $user_id) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $profile_image = null;

    if (empty($full_name) || empty($email)) {
        $_SESSION['error'] = "Name and Email are required.";
        header('Location: ' . BASE_URL . 'dashboard/profile');
        exit;
    }

    // Handle File Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and WebP are allowed.";
            header('Location: ' . BASE_URL . 'dashboard/profile');
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "File is too large. Maximum size is 2MB.";
            header('Location: ' . BASE_URL . 'dashboard/profile');
            exit;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = "user_" . $user_id . "_" . time() . "." . $ext;
        $uploadDir = __DIR__ . '/../assets/images/profiles/';
        
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            $profile_image = $fileName;
            $_SESSION['profile_image'] = $fileName; // Update session
        } else {
            $_SESSION['error'] = "Failed to upload image.";
        }
    }

    try {
        if ($profile_image) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_image = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $profile_image, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }

        $_SESSION['full_name'] = $full_name; // Update session
        $_SESSION['success'] = "Profile updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to update profile: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/profile');
    exit;
}

/**
 * Handle Password Update
 */
function handleUpdatePassword($pdo, $user_id) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All password fields are required.";
        header('Location: ' . BASE_URL . 'dashboard/profile');
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
        header('Location: ' . BASE_URL . 'dashboard/profile');
        exit;
    }

    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            $_SESSION['success'] = "Password changed successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to change password.";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect.";
    }

    header('Location: ../dashboard/profile');
    exit;
}
