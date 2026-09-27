<?php
/**
 * ROQ Procurement Handler — Parish Council owns procurement.
 * Chairperson / Secretary / Treasurer (role_level <= 3) may
 * create, approve, publish, award and reject.
 */
require_once __DIR__ . '/../../../includes/session_manager.php';
SessionManager::start();
require_once __DIR__ . '/../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

$action     = $_POST['action'] ?? '';
$user_id    = $_SESSION['user_id'] ?? null;
$role_level = $_SESSION['role_level'] ?? 99;

if ($role_level > 3) {
    $_SESSION['error'] = "Unauthorized. Procurement is managed by the Parish Council.";
    header('Location: ' . BASE_URL . 'dashboard/roq');
    exit;
}

switch ($action) {
    case 'create_roq':
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $deadline = $_POST['deadline'] ?? '';
        if (!$title || !$description || !$deadline) {
            $_SESSION['error'] = "All fields are required.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO roq (title, description, deadline, author_id, status) VALUES (?, ?, ?, ?, 'published')");
                $stmt->execute([$title, $description, $deadline, $user_id]);
                $_SESSION['success'] = "ROQ published to the public portal successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Failed to create ROQ.";
            }
        }
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;

    case 'approve_roq':
        $pdo->prepare("UPDATE roq SET status = 'published' WHERE id = ?")->execute([intval($_POST['id'] ?? 0)]);
        $_SESSION['success'] = "ROQ approved and published to the public portal.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;

    case 'delete_roq':
        $pdo->prepare("DELETE FROM roq WHERE id = ?")->execute([intval($_POST['id'] ?? 0)]);
        $_SESSION['success'] = "ROQ rejected and removed.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;

    case 'accept_submission':
        $sid = intval($_POST['submission_id'] ?? 0);
        $rid = intval($_POST['roq_id'] ?? 0);
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE roq_submissions SET status = 'accepted' WHERE id = ?")->execute([$sid]);
            $pdo->prepare("UPDATE roq SET status = 'awarded' WHERE id = ?")->execute([$rid]);
            $pdo->commit();
            $_SESSION['success'] = "Offer accepted and procurement awarded!";
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['error'] = "Failed to accept offer.";
        }
        header('Location: ' . BASE_URL . 'dashboard/index.php?page=roq_submissions&id=' . $rid);
        exit;

    case 'reject_submission':
        $sid = intval($_POST['submission_id'] ?? 0);
        $rid = intval($_POST['roq_id'] ?? 0);
        $pdo->prepare("UPDATE roq_submissions SET status = 'rejected' WHERE id = ?")->execute([$sid]);
        $_SESSION['success'] = "Offer rejected.";
        header('Location: ' . BASE_URL . 'dashboard/index.php?page=roq_submissions&id=' . $rid);
        exit;

    default:
        $_SESSION['error'] = "Invalid action.";
        header('Location: ' . BASE_URL . 'dashboard/roq');
        exit;
}
