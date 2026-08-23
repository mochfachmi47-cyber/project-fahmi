<?php

/**
 * FORSAKDA 27 - Vercel Front Controller / Router
 * Menangani routing permintaan ke file PHP yang sesuai
 */

require_once __DIR__ . '/../config/app.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = str_replace('\\', '/', $requestUri);
$requestUri = ltrim($requestUri, '/');

// Security: prevent directory traversal
$requestUri = preg_replace('#\.\./#', '', $requestUri);

// Build allowed routes map
$allowedPaths = [
    '' => __DIR__ . '/../index.php',
    'index.php' => __DIR__ . '/../index.php',
];

// Scan view directories
$viewDirs = ['views/public', 'views/member', 'views/admin'];
foreach ($viewDirs as $dir) {
    $fullDir = __DIR__ . '/../' . $dir;
    if (is_dir($fullDir)) {
        foreach (glob($fullDir . '/*.php') as $file) {
            $name = basename($file);
            $allowedPaths[$dir . '/' . $name] = $file;
        }
    }
}

// Allow database setup
$setupFile = __DIR__ . '/../database/setup.php';
if (file_exists($setupFile)) {
    $allowedPaths['database/setup.php'] = $setupFile;
}

// Route request
if (isset($allowedPaths[$requestUri]) && file_exists($allowedPaths[$requestUri])) {
    require $allowedPaths[$requestUri];
    exit;
}

// Default fallback
require __DIR__ . '/../index.php';
