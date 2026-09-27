<?php
/**
 * File storage helper — NOTHING is stored inside the web project.
 * Uploads live in STORAGE_PATH (outside the document root, set in .env),
 * and are served to browsers via download.php with force-download headers.
 */
require_once __DIR__ . '/env_loader.php';

function storageDir(string $sub = ''): string
{
    loadEnv(dirname(__DIR__));
    $base = env('STORAGE_PATH', '');
    if ($base === '') {
        // Default: sibling of the web root (e.g. C:\xampp\parish_storage)
        $base = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'parish_storage';
    }
    $dir = rtrim($base, '/\\') . DIRECTORY_SEPARATOR . 'bulletins';
    if ($sub !== '') $dir .= DIRECTORY_SEPARATOR . trim($sub, '/\\');
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    return $dir;
}

/** Move an uploaded PDF into outside-root storage. Returns stored filename or null. */
function storePdfUpload(array $file): ?string
{
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) return null;
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $mime = $file['type'] ?? '';
    if ($ext !== 'pdf' && $mime !== 'application/pdf') return null;
    $fn = date('Ymd_His_') . bin2hex(random_bytes(4)) . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], storageDir() . DIRECTORY_SEPARATOR . $fn)) return null;
    return $fn;
}

/** Resolve a DB file_path to an absolute file. Supports new (filename) + legacy (assets/...) rows. */
function resolveStoredFile(string $filePath): ?string
{
    $filePath = ltrim($filePath, '/');
    // Legacy rows stored inside the project
    if (strpos($filePath, 'assets/') === 0) {
        $abs = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        if (is_file($abs)) return $abs;
        // Maybe already migrated: try basename in storage
        $filePath = basename($filePath);
    }
    $abs = storageDir() . DIRECTORY_SEPARATOR . basename($filePath);
    return is_file($abs) ? $abs : null;
}

function deleteStoredFile(string $filePath): void
{
    $abs = resolveStoredFile($filePath);
    if ($abs) @unlink($abs);
}

/** Stream a file as a browser download (never renders inline, never exposes path). */
function streamDownload(string $absPath, string $downloadName): void
{
    if (!is_file($absPath)) {
        http_response_code(404);
        echo "File not found.";
        exit;
    }
    // Clean any prior output so the PDF isn't corrupted
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadName) . '"');
    header('Content-Length: ' . filesize($absPath));
    header('Cache-Control: private, must-revalidate');
    readfile($absPath);
    exit;
}
