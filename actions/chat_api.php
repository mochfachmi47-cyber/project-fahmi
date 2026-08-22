<?php
/**
 * FORSAKDA 27 - Live Chat JSON API Endpoint
 * Digunakan untuk polling dan pengiriman pesan real-time sesama anggota santri
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

// Validasi Login (Hanya untuk Anggota & Admin)
if (!is_logged_in() || (!has_role('anggota') && !has_role('admin'))) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Anda harus login untuk menggunakan ruang chatting santri.'
    ]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user = current_user();

try {
    $pdo = Database::getConnection();

    // ==========================================
    // GET MESSAGES
    // ==========================================
    if ($action === 'get_messages') {
        $lastId = (int)($_GET['last_id'] ?? 0);
        $room = sanitize_input($_GET['room'] ?? 'general');

        $stmt = $pdo->prepare("
            SELECT m.id, m.user_id, m.pesan, m.created_at, u.nama, u.role, u.foto
            FROM messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.room = :room AND m.id > :last_id
            ORDER BY m.id ASC
            LIMIT 50
        ");
        $stmt->execute([
            ':room'    => $room,
            ':last_id' => $lastId
        ]);

        $rows = $stmt->fetchAll();
        $messages = [];

        foreach ($rows as $row) {
            $messages[] = [
                'id'             => (int)$row['id'],
                'user_id'        => (int)$row['user_id'],
                'nama'           => $row['nama'],
                'role'           => $row['role'],
                'foto'           => $row['foto'],
                'pesan'          => $row['pesan'],
                'time_formatted' => format_date_id($row['created_at'], true),
                'created_at'     => $row['created_at']
            ];
        }

        echo json_encode([
            'status'   => 'success',
            'messages' => $messages,
            'count'    => count($messages)
        ]);
        exit;
    }

    // ==========================================
    // SEND MESSAGE
    // ==========================================
    if ($action === 'send_message' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Token CSRF tidak valid.'
            ]);
            exit;
        }

        $pesan = trim($_POST['pesan'] ?? '');
        $room  = sanitize_input($_POST['room'] ?? 'general');

        if (empty($pesan)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pesan tidak boleh kosong.'
            ]);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO messages (user_id, room, pesan, created_at)
            VALUES (:uid, :room, :pesan, NOW())
        ");
        $stmt->execute([
            ':uid'   => $user['id'],
            ':room'  => $room,
            ':pesan' => $pesan
        ]);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Pesan berhasil dikirim.',
            'id'      => (int)$pdo->lastInsertId()
        ]);
        exit;
    }

    echo json_encode([
        'status'  => 'error',
        'message' => 'Aksi tidak dikenali.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
