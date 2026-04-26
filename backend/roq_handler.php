<?php
/**
 * ROQ (Procurement) Handler
 * Handles the creation, approval, and management of Request for Quotations.
 */
require_once __DIR__ . '/../includes/session_manager.php';
SessionManager::start();
require_once __DIR__ . '/../config/db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];
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
    case 'submit_quote':
        handleSubmitQuote($pdo, $user_id);
        break;
    default:
        $_SESSION['error'] = "Invalid action.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
}

/**
 * Create a new ROQ
 */
function handleCreateROQ($pdo, $user_id, $role_level) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'];
    
    // Status: Priest/Admin (1,2) auto-publish, others stay pending
    $status = ($role_level <= 2) ? 'published' : 'pending';

    try {
        $stmt = $pdo->prepare("INSERT INTO roq (title, description, deadline, author_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $deadline, $user_id, $status]);
        
        $_SESSION['success'] = ($status === 'published') ? "ROQ published successfully!" : "ROQ submitted for approval.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to create ROQ: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Approve and Publish an ROQ (Priest/Admin Only)
 */
function handleApproveROQ($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized action.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("UPDATE roq SET status = 'published' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ROQ approved and published successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to approve ROQ.";
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Delete an ROQ
 */
function handleDeleteROQ($pdo, $role_level) {
    if ($role_level > 2) {
        $_SESSION['error'] = "Unauthorized action.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
    }

    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM roq WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ROQ deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete ROQ.";
    }

    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

/**
 * Handle Quote Submission from Public Portal
 */
function handleSubmitQuote($pdo, $user_id) {
    $roq_id = intval($_POST['roq_id']);
    $amount = floatval($_POST['quoted_amount']);
    $proposal = trim($_POST['proposal_text']);

    // Check deadline and status
    $stmt = $pdo->prepare("SELECT deadline, status FROM roq WHERE id = ?");
    $stmt->execute([$roq_id]);
    $roq = $stmt->fetch();

    if (!$roq) {
        $_SESSION['error'] = "Request not found.";
        header('Location: ' . BASE_URL . 'roq');
        exit;
    }

    if ($roq['status'] !== 'published') {
        $_SESSION['error'] = "This request is no longer accepting offers (Status: " . $roq['status'] . ").";
        header('Location: ' . BASE_URL . 'roq');
        exit;
    }

    // Strict Deadline Check
    $deadline_ts = strtotime($roq['deadline'] . ' 23:59:59');
    if (time() > $deadline_ts) {
        $_SESSION['error'] = "The deadline for this request (" . date('M d, Y', $deadline_ts) . ") has passed. No further quotes are being accepted.";
        header('Location: ' . BASE_URL . 'roq');
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO roq_submissions (roq_id, user_id, quoted_amount, proposal_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$roq_id, $user_id, $amount, $proposal]);
        $_SESSION['success'] = "Your formal quote has been submitted successfully! The Parish administration will review it.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to submit quote: " . $e->getMessage();
    }

    header('Location: ' . BASE_URL . 'roq');
    exit;
}
