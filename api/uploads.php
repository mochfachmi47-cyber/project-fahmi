<?php

/**
 * FORSAKDA 27 - Serve Uploaded Files (Vercel /tmp fallback)
 * Vercel filesystem is read-only except /tmp, so uploads are stored there.
 * This endpoint securely serves files from /tmp/uploads/.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/security.php';

$requested = $_GET['file'] ?? '';
$requested = ltrim($requested, '/');

if (empty($requested)) {
    http_response_code(404);
    exit('File not found');
}

$baseUploadDir = rtrim(UPLOAD_PATH, '/');

// Normalize path: database stores "uploads/avatars/xxx.jpg" but UPLOAD_PATH already points to uploads/
if (strpos($requested, 'uploads/') === 0) {
    $requested = substr($requested, 8);
}

$filePath = realpath($baseUploadDir . '/' . $requested);

if (!$filePath || strpos($filePath, $baseUploadDir) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml',
    'pdf' => 'application/pdf'
];

$mime = $mimeTypes[$extension] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=86400');
header('X-Content-Type-Options: nosniff');

readfile($filePath);
exit;
