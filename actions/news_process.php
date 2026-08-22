<?php
/**
 * FORSAKDA 27 - News & Article Controller
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

require_role(['admin']); // Hanya admin yang dapat mengelola berita resmi

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user = current_user();
$pdo = Database::getConnection();

function create_slug(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'berita-' . time());
}

// ==========================================
// 1. CREATE NEWS
// ==========================================
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/berita-tambah.php');
        exit;
    }

    $judul     = sanitize_input($_POST['judul'] ?? '');
    $kategori  = sanitize_input($_POST['kategori'] ?? 'Berita');
    $ringkasan = sanitize_input($_POST['ringkasan'] ?? '');
    $konten    = trim($_POST['konten'] ?? '');
    $status    = sanitize_input($_POST['status'] ?? 'published');
    $isPinned  = isset($_POST['is_pinned']) ? 1 : 0;

    if (empty($judul) || empty($konten)) {
        set_flash('error', 'Judul dan konten berita wajib diisi.');
        header('Location: ' . BASE_URL . '/views/admin/berita-tambah.php');
        exit;
    }

    $slug = create_slug($judul);
    // Pastikan slug unik
    $stmt = $pdo->prepare("SELECT id FROM news WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    if ($stmt->fetch()) {
        $slug .= '-' . time();
    }

    // Upload Cover Gambar jika ada
    $gambarPath = null;
    if (!empty($_FILES['gambar']['name'])) {
        $uploadRes = upload_image($_FILES['gambar'], 'berita');
        if ($uploadRes['success']) {
            $gambarPath = $uploadRes['filepath'];
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO news (user_id, judul, slug, kategori, ringkasan, konten, gambar, status, is_pinned, views, created_at)
            VALUES (:uid, :judul, :slug, :kategori, :ringkasan, :konten, :gambar, :status, :pinned, 0, NOW())
        ");
        $stmt->execute([
            ':uid'       => $user['id'],
            ':judul'     => $judul,
            ':slug'      => $slug,
            ':kategori'  => $kategori,
            ':ringkasan' => $ringkasan,
            ':konten'    => $konten,
            ':gambar'    => $gambarPath,
            ':status'    => $status,
            ':pinned'    => $isPinned
        ]);

        $newNewsId = (int)$pdo->lastInsertId();
        log_activity($user['id'], 'CREATE_NEWS', "Membuat berita baru: $judul (ID: $newNewsId)");
        set_flash('success', 'Berita berhasil diterbitkan!');
        header('Location: ' . BASE_URL . '/views/admin/berita.php');
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyimpan berita: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/admin/berita-tambah.php');
        exit;
    }
}

// ==========================================
// 2. EDIT NEWS
// ==========================================
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/berita.php');
        exit;
    }

    $id        = (int)($_POST['id'] ?? 0);
    $judul     = sanitize_input($_POST['judul'] ?? '');
    $kategori  = sanitize_input($_POST['kategori'] ?? 'Berita');
    $ringkasan = sanitize_input($_POST['ringkasan'] ?? '');
    $konten    = trim($_POST['konten'] ?? '');
    $status    = sanitize_input($_POST['status'] ?? 'published');
    $isPinned  = isset($_POST['is_pinned']) ? 1 : 0;

    // Fetch existing news
    $stmtOld = $pdo->prepare("SELECT gambar FROM news WHERE id = :id LIMIT 1");
    $stmtOld->execute([':id' => $id]);
    $oldNews = $stmtOld->fetch();

    $gambarUpdate = "";
    $params = [
        ':judul'     => $judul,
        ':kategori'  => $kategori,
        ':ringkasan' => $ringkasan,
        ':konten'    => $konten,
        ':status'    => $status,
        ':pinned'    => $isPinned,
        ':id'        => $id
    ];

    if (!empty($_FILES['gambar']['name'])) {
        $uploadRes = upload_image($_FILES['gambar'], 'berita');
        if ($uploadRes['success']) {
            $gambarUpdate = ", gambar = :gambar";
            $params[':gambar'] = $uploadRes['filepath'];

            // Clean old image file
            if (!empty($oldNews['gambar'])) {
                $oldImgPath = ROOT_PATH . '/' . $oldNews['gambar'];
                if (file_exists($oldImgPath)) {
                    @unlink($oldImgPath);
                }
            }
        }
    }

    try {
        $sql = "UPDATE news SET judul = :judul, kategori = :kategori, ringkasan = :ringkasan, konten = :konten, status = :status, is_pinned = :pinned $gambarUpdate WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity($user['id'], 'EDIT_NEWS', "Mengubah berita ID: $id ($judul)");
        set_flash('success', 'Berita berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/views/admin/berita.php');
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui berita: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/admin/berita.php');
        exit;
    }
}

// ==========================================
// 3. TOGGLE STATUS (Published / Draft)
// ==========================================
if ($action === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);
    try {
        $stmt = $pdo->prepare("SELECT status, judul FROM news WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            $newStatus = ($row['status'] === 'published') ? 'draft' : 'published';
            $up = $pdo->prepare("UPDATE news SET status = :st WHERE id = :id");
            $up->execute([':st' => $newStatus, ':id' => $id]);

            log_activity($user['id'], 'STATUS_NEWS', "Mengubah status berita ID: $id menjadi $newStatus");
            set_flash('success', "Status berita '" . $row['judul'] . "' diubah menjadi: " . ucfirst($newStatus));
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal mengubah status berita: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/berita.php');
    exit;
}

// ==========================================
// 4. TOGGLE PIN
// ==========================================
if ($action === 'toggle_pin') {
    $id = (int)($_GET['id'] ?? 0);
    try {
        $stmt = $pdo->prepare("SELECT is_pinned, judul FROM news WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if ($row) {
            $newPinned = $row['is_pinned'] ? 0 : 1;
            $up = $pdo->prepare("UPDATE news SET is_pinned = :p WHERE id = :id");
            $up->execute([':p' => $newPinned, ':id' => $id]);

            log_activity($user['id'], 'PIN_NEWS', "Mengubah status pin berita ID: $id");
            set_flash('success', $newPinned ? "Berita '" . $row['judul'] . "' berhasil disematkan (Pin)." : "Sematkan berita '" . $row['judul'] . "' dilepas.");
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal mengubah pin berita: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/berita.php');
    exit;
}

// ==========================================
// 5. DELETE NEWS
// ==========================================
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    try {
        // Fetch image path
        $stmt = $pdo->prepare("SELECT judul, gambar FROM news WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $targetNews = $stmt->fetch();

        if ($targetNews) {
            // Delete image file from disk
            if (!empty($targetNews['gambar'])) {
                $imgFile = ROOT_PATH . '/' . $targetNews['gambar'];
                if (file_exists($imgFile)) {
                    @unlink($imgFile);
                }
            }

            $del = $pdo->prepare("DELETE FROM news WHERE id = :id");
            $del->execute([':id' => $id]);

            log_activity($user['id'], 'DELETE_NEWS', "Menghapus berita ID: $id (" . $targetNews['judul'] . ")");
            set_flash('success', 'Berita berhasil dihapus.');
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus berita: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/berita.php');
    exit;
}

header('Location: ' . BASE_URL . '/views/admin/berita.php');
exit;
