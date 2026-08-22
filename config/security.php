<?php
/**
 * FORSAKDA 27 - Security & Helper Functions
 * Standar Keamanan Web: CSRF, XSS, Sanitasi, Session Hardening, Secure File Upload
 */

// Inisialisasi Sesi Aman (Hardened Session Settings)
if (session_status() === PHP_SESSION_NONE) {
    // Pastikan cookie session aman
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Aktifkan secure flag jika https
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

/**
 * Escape string untuk pencegahan XSS (Cross-Site Scripting)
 */
function e(?string $data): string {
    if ($data === null) return '';
    return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate atau ambil CSRF Token dari sesi
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render input field hidden untuk CSRF token di dalam form
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Verifikasi CSRF Token dari request POST
 */
function verify_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
    }
    return true;
}

/**
 * Sanitasi string input dasar
 */
function sanitize_input(?string $data): string {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    return strip_tags($data);
}

/**
 * Format tanggal dalam bahasa Indonesia
 */
function format_date_id(string $datetime, bool $withTime = true): string {
    if (empty($datetime)) return '-';
    $timestamp = strtotime($datetime);
    if (!$timestamp) return $datetime;

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $d = date('j', $timestamp);
    $m = $bulan[(int)date('n', $timestamp)];
    $y = date('Y', $timestamp);

    if ($withTime) {
        $time = date('H:i', $timestamp);
        return "$d $m $y, $time WIB";
    }
    return "$d $m $y";
}

/**
 * Format relative time (misal: 5 menit lalu, 1 jam lalu)
 */
function time_ago(string $datetime): string {
    $timestamp = strtotime($datetime);
    if (!$timestamp) return $datetime;

    $difference = time() - $timestamp;
    if ($difference < 60) {
        return 'Baru saja';
    } elseif ($difference < 3600) {
        return floor($difference / 60) . ' menit lalu';
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . ' jam lalu';
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . ' hari lalu';
    } else {
        return format_date_id($datetime, false);
    }
}

/**
 * Format Rupiah
 */
function format_rupiah($number): string {
    if (!is_numeric($number) || $number == 0) return 'Gaji Dirahasiakan / Nego';
    return 'Rp ' . number_format($number, 0, ',', '.');
}

/**
 * Helper validasi & upload file gambar secara aman
 */
function upload_image(array $file, string $targetFolder = 'avatars', int $maxSize = 2097152): array {
    // 2MB default max size
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Parameter file tidak valid.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'message' => 'Tidak ada file yang diunggah.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'message' => 'Ukuran file terlalu besar (Maksimal 2MB).'];
            default:
                return ['success' => false, 'message' => 'Gagal mengunggah file. Kode error: ' . $file['error']];
        }
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Ukuran file melebihi batas 2MB.'];
    }

    // Whitelist ekstensi & MIME Type
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        return ['success' => false, 'message' => 'Ekstensi file tidak diizinkan. Gunakan JPG, PNG, atau WEBP.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMimes, true)) {
        return ['success' => false, 'message' => 'Format file tidak valid (MIME Mismatch).'];
    }

    // Generate nama file acak untuk mencegah Directory Traversal & Name Collision
    $newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
    $destinationDir = UPLOAD_PATH . '/' . trim($targetFolder, '/');

    if (!file_exists($destinationDir)) {
        @mkdir($destinationDir, 0755, true);
    }

    $destinationPath = $destinationDir . '/' . $newFileName;
    if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
        return ['success' => false, 'message' => 'Gagal menyimpan file ke direktori server.'];
    }

    return [
        'success' => true,
        'filename' => $newFileName,
        'filepath' => 'uploads/' . trim($targetFolder, '/') . '/' . $newFileName
    ];
}
