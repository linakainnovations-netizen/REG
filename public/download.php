<?php
// File downloads: ?path=download&type=bulletin&id=NN  → bulletin PDF (browser download)
//                 ?path=download&type=roster          → duty roster PDF generated on the fly
$pageTitle = "Download";
$type = $_GET['type'] ?? '';

if ($type === 'roster') {
    include_once 'includes/session_manager.php';
    SessionManager::start();
    require_once 'config/db.php';
    require_once 'backend/pdf_generator.php';
    try {
        $tasks = $pdo->query("SELECT t.*, g.name as group_name FROM tasks t LEFT JOIN groups g ON t.group_id = g.id WHERE t.assigned_date >= CURDATE() ORDER BY t.assigned_date ASC LIMIT 100")->fetchAll();
    } catch (PDOException $e) { $tasks = []; }
    PDFGenerator::streamRoster($tasks, 'Parish Duty Roster');
}

if ($type === 'bulletin') {
    require_once 'includes/session_manager.php';
    SessionManager::start();
    require_once 'config/db.php';
    require_once 'backend/file_storage.php';
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM bulletins WHERE id = ? AND status = 'published'");
    $stmt->execute([$id]);
    $b = $stmt->fetch();
    if (!$b) { http_response_code(404); echo "Bulletin not found."; exit; }
    $abs = resolveStoredFile($b['file_path']);
    if (!$abs) { http_response_code(404); echo "File missing. Contact the parish office."; exit; }
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $b['title']) . '.pdf';
    streamDownload($abs, $name);
}

http_response_code(404);
echo "Invalid download.";
