<?php
/**
 * FORSAKDA 27 - Konfigurasi Aplikasi
 * Forum Silaturahmi Alumni dan Santri KDA Angkatan 27
 */

// Error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Set timezone Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Konstanta Aplikasi
if (!defined('APP_NAME')) define('APP_NAME', 'FORSAKDA 27');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'Forum Santri Kelas Dua 27');
if (!defined('APP_VERSION')) define('APP_VERSION', '1.0.0');

// Base URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Script directory relative to document root
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', $scriptDir);
// Jika script dipanggil dari subfolder seperti /actions atau /views, kita cari root project-nya
$basePath = preg_replace('/(\/(actions|views|config|includes|database).*)$/', '', $scriptDir);
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}

if (!defined('BASE_URL')) {
    define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/..'));
}

// Upload directory
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', ROOT_PATH . '/uploads');
}

// Ensure upload folders exist
$uploadFolders = [
    UPLOAD_PATH,
    UPLOAD_PATH . '/avatars',
    UPLOAD_PATH . '/berita',
    UPLOAD_PATH . '/loker',
    UPLOAD_PATH . '/galeri'
];
foreach ($uploadFolders as $folder) {
    if (!file_exists($folder)) {
        @mkdir($folder, 0755, true);
    }
}
