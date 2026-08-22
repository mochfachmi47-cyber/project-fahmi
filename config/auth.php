<?php
/**
 * FORSAKDA 27 - Authentication & Role-Based Access Control (RBAC)
 * Mengatur hak akses 3 Role: admin, anggota, publik
 */

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/database.php';

/**
 * Cek apakah user sedang login
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

/**
 * Ambil data user yang sedang login dari session / DB
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'        => (int)$_SESSION['user_id'],
        'nama'      => $_SESSION['user_nama'] ?? 'Pengguna',
        'email'     => $_SESSION['user_email'] ?? '',
        'role'      => $_SESSION['user_role'] ?? 'publik',
        'status'    => $_SESSION['user_status'] ?? 'active',
        'foto'      => $_SESSION['user_foto'] ?? '',
        'angkatan'  => $_SESSION['user_angkatan'] ?? '27',
        'no_hp'     => $_SESSION['user_no_hp'] ?? '',
        'domisili'  => $_SESSION['user_domisili'] ?? '',
        'profesi'   => $_SESSION['user_profesi'] ?? ''
    ];
}

/**
 * Cek apakah user memiliki salah satu dari role yang ditentukan
 * Contoh: has_role(['admin']) atau has_role(['admin', 'anggota'])
 */
function has_role($roles): bool {
    if (!is_logged_in()) return false;
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    $userRole = $_SESSION['user_role'] ?? '';
    return in_array($userRole, $roles, true);
}

/**
 * Middleware: Wajib login, redirect ke halaman login jika belum
 */
function require_login(string $redirectUrl = ''): void {
    if (!is_logged_in()) {
        $target = $redirectUrl ?: (BASE_URL . '/views/public/login.php');
        set_flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        header("Location: " . $target);
        exit;
    }
}

/**
 * Middleware: Wajib memiliki role tertentu
 */
function require_role($roles, string $redirectUrl = ''): void {
    require_login();

    if (!has_role($roles)) {
        set_flash('error', 'Akses ditolak! Anda tidak memiliki izin untuk membuka halaman tersebut.');
        $fallback = (has_role('admin')) ? (BASE_URL . '/views/admin/dashboard.php') : (BASE_URL . '/views/member/dashboard.php');
        header("Location: " . ($redirectUrl ?: $fallback));
        exit;
    }
}

/**
 * Set Flash Message (Notifikasi satu kali tayang)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type'    => $type, // 'success', 'error', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Ambil dan hapus Flash Message dari session
 */
function get_flash(): ?array {
    if (!empty($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Catat log aktivitas ke database
 */
function log_activity(?int $userId, string $action, string $details = ''): void {
    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (:uid, :act, :det, :ip, NOW())");
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt->execute([
            ':uid' => $userId,
            ':act' => $action,
            ':det' => $details,
            ':ip'  => $ip
        ]);
    } catch (Exception $e) {
        // Silent fail for logs
    }
}
