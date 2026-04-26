<?php
/**
 * ROQ Procurement Handler
 * Admin/Priest Dashboard - St. Paul Chipata Portal
 * Located: dashboard/admin_priest/backend_pages/roq_handler.php
 */
require_once __DIR__ . '/../../../includes/session_manager.php';
SessionManager::start();
require_once __DIR__ . '/../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

$action     = $_POST['action'] ?? '';
$user_id    = $_SESSION['user_id'];
$role_level = $_SESSION['role_level'];

switch ($action) {
    case 'create_roq':
        handleCreateROQ($pdo, $user_id, $role_level);
        break;
    case 'approve_roq':
        handleApproveROQ($pdo, $role_level);
        break;
    case 'delete_roq':
        handleDeleteROQ($pdo, $role_level);
        break;
    case 'accept_submission':
        handleAcceptSubmission($pdo, $role_level);
        break;
    case 'reject_submission':
        handleRejectSubmission($pdo, $role_level);
        break;
    default:
        $_SESSION['error'] = "Invalid action.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
}

/**
 * Create a new ROQ
 * Priest/Admin -> auto-published | Others -> pending
 */
function handleCreateROQ($pdo, $user_id, $role_level) {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline    = $_POST['deadline'];

    if (empty($title) || empty($description) || empty($deadline)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $status = ($role_level <= 2) ? 'published' : 'pending';

    try {
        $stmt = $pdo->prepare("INSERT INTO roq (title, description, deadline, author_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $deadline, $user_id, $status]);

        $_SESSION['success'] = ($status === 'published')
            ? "ROQ published to the public portal successfully!"
            : "ROQ submitted. Awaiting Priest approval.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to create ROQ: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Approve and Publish a pending ROQ (Priest/Admin Only)
 */
function handleApproveROQ($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized. Only the Parish Priest can approve requests.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare("UPDATE roq SET status = 'published' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ROQ approved and published to the public portal.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to approve ROQ: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Reject/Delete an ROQ (Priest/Admin Only)
 */
function handleDeleteROQ($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized. Only the Parish Priest can delete requests.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM roq WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ROQ rejected and removed.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete ROQ: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Accept and Award a submission
 */
function handleAcceptSubmission($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $submission_id = intval($_POST['submission_id']);
    $roq_id = intval($_POST['roq_id']);

    try {
        $pdo->beginTransaction();

        // Mark this submission as accepted
        $stmt = $pdo->prepare("UPDATE roq_submissions SET status = 'accepted' WHERE id = ?");
        $stmt->execute([$submission_id]);

        // Mark ROQ as awarded
        $stmt = $pdo->prepare("UPDATE roq SET status = 'awarded' WHERE id = ?");
        $stmt->execute([$roq_id]);

        $pdo->commit();
        $_SESSION['success'] = "Offer accepted and procurement awarded!";
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['error'] = "Failed to accept offer: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/index.php?page=roq_submissions&id=' . $roq_id);
    exit;
}

/**
 * Reject a submission
 */
function handleRejectSubmission($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $submission_id = intval($_POST['submission_id']);
    $roq_id = intval($_POST['roq_id']);

    try {
        $stmt = $pdo->prepare("UPDATE roq_submissions SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$submission_id]);
        $_SESSION['success'] = "Offer rejected.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to reject offer.";
    }

    header('Location: ' . BASE_URL . 'dashboard/index.php?page=roq_submissions&id=' . $roq_id);
    exit;
}
