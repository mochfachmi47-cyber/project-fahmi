<?php
/**
 * FORSAKDA 27 - Content & Gallery Management Controller (Admin & Anggota)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

require_login(); // Wajib login

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user = current_user();
$pdo = Database::getConnection();

// ==========================================
// 1. UPDATE VISI & MISI (Admin Only)
// ==========================================
if ($action === 'update_visi_misi' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['admin']);

    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/konten.php');
        exit;
    }

    $visi = trim($_POST['visi'] ?? '');
    $misiRaw = trim($_POST['misi'] ?? '');
    $nilaiRaw = trim($_POST['nilai_utama'] ?? '');

    $misiArr = array_values(array_filter(array_map('trim', explode("\n", $misiRaw))));
    $nilaiArr = array_values(array_filter(array_map('trim', explode("\n", $nilaiRaw))));

    $contentJson = json_encode([
        'visi'        => $visi,
        'misi'        => $misiArr,
        'nilai_utama' => $nilaiArr
    ], JSON_UNESCAPED_UNICODE);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO site_content (key_name, title, content, updated_at)
            VALUES ('visi_misi', 'Visi & Misi FORSAKDA 27', :content, NOW())
            ON DUPLICATE KEY UPDATE content = :content, updated_at = NOW()
        ");
        $stmt->execute([':content' => $contentJson]);

        log_activity($user['id'], 'UPDATE_CONTENT', 'Memperbarui Visi & Misi Organisasi');
        set_flash('success', 'Visi, Misi & Nilai Utama FORSAKDA 27 berhasil diperbarui.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui konten: ' . $e->getMessage());
    }

    header('Location: ' . BASE_URL . '/views/admin/konten.php');
    exit;
}

// ==========================================
// 2. UPDATE TUJUAN & SEJARAH (Admin Only)
// ==========================================
if ($action === 'update_tujuan_sejarah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['admin']);

    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/konten.php');
        exit;
    }

    $tujuanRaw = trim($_POST['tujuan'] ?? '');
    $sejarah = trim($_POST['sejarah'] ?? '');
    $sambutan = trim($_POST['sambutan_ketua'] ?? '');

    $tujuanArr = array_values(array_filter(array_map('trim', explode("\n", $tujuanRaw))));

    $contentJson = json_encode([
        'tujuan'         => $tujuanArr,
        'sejarah'        => $sejarah,
        'sambutan_ketua' => $sambutan
    ], JSON_UNESCAPED_UNICODE);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO site_content (key_name, title, content, updated_at)
            VALUES ('tujuan_web', 'Tujuan Pembuatan Website FORSAKDA 27', :content, NOW())
            ON DUPLICATE KEY UPDATE content = :content, updated_at = NOW()
        ");
        $stmt->execute([':content' => $contentJson]);

        log_activity($user['id'], 'UPDATE_CONTENT', 'Memperbarui Tujuan Web, Sejarah & Sambutan');
        set_flash('success', 'Tujuan Web, Sejarah & Sambutan Ketua FORSAKDA 27 berhasil diperbarui.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui konten: ' . $e->getMessage());
    }

    header('Location: ' . BASE_URL . '/views/admin/konten.php');
    exit;
}

// ==========================================
// 3. TAMBAH FOTO GALERI (Admin & Anggota)
// ==========================================
if ($action === 'add_gallery' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['admin', 'anggota']);

    $redirectUrl = has_role('admin') ? BASE_URL . '/views/admin/galeri.php' : BASE_URL . '/views/member/galeri.php';

    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . $redirectUrl);
        exit;
    }

    $judul     = sanitize_input($_POST['judul'] ?? '');
    $kategori  = sanitize_input($_POST['kategori'] ?? 'Kegiatan');
    $deskripsi = sanitize_input($_POST['deskripsi'] ?? '');

    if (empty($judul) || empty($_FILES['gambar']['name'])) {
        set_flash('error', 'Judul foto dan file gambar wajib diisi.');
        header('Location: ' . $redirectUrl);
        exit;
    }

    $uploadRes = upload_image($_FILES['gambar'], 'galeri');
    if (!$uploadRes['success']) {
        set_flash('error', $uploadRes['message']);
        header('Location: ' . $redirectUrl);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO gallery (user_id, judul, kategori, gambar, deskripsi, created_at)
            VALUES (:uid, :judul, :kategori, :gambar, :deskripsi, NOW())
        ");
        $stmt->execute([
            ':uid'       => $user['id'],
            ':judul'     => $judul,
            ':kategori'  => $kategori,
            ':gambar'    => $uploadRes['filepath'],
            ':deskripsi' => $deskripsi
        ]);

        $newGalId = (int)$pdo->lastInsertId();
        log_activity($user['id'], 'ADD_GALLERY', "Menambahkan foto galeri: $judul (ID: $newGalId)");
        set_flash('success', 'Alhamdulillah! Foto kenangan/kegiatan berhasil diunggah ke galeri.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyimpan foto: ' . $e->getMessage());
    }

    header('Location: ' . $redirectUrl);
    exit;
}

// ==========================================
// 4. HAPUS FOTO GALERI (Admin & Pemilik Foto)
// ==========================================
if ($action === 'delete_gallery') {
    require_role(['admin', 'anggota']);

    $id = (int)($_GET['id'] ?? 0);
    $redirectUrl = has_role('admin') ? BASE_URL . '/views/admin/galeri.php' : BASE_URL . '/views/member/galeri.php';

    try {
        // Fetch image file path
        $stmt = $pdo->prepare("SELECT user_id, judul, gambar FROM gallery WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $target = $stmt->fetch();

        if ($target) {
            // Check ownership or admin role
            if (!has_role('admin') && (int)$target['user_id'] !== (int)$user['id']) {
                set_flash('error', 'Anda tidak memiliki hak untuk menghapus foto ini.');
                header('Location: ' . $redirectUrl);
                exit;
            }

            // Delete file from disk
            if (!empty($target['gambar'])) {
                $filePath = ROOT_PATH . '/' . $target['gambar'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $del = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
            $del->execute([':id' => $id]);

            log_activity($user['id'], 'DELETE_GALLERY', "Menghapus foto galeri ID: $id (" . $target['judul'] . ")");
            set_flash('success', 'Foto berhasil dihapus dari galeri.');
        } else {
            set_flash('error', 'Foto tidak ditemukan.');
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus foto: ' . $e->getMessage());
    }

    header('Location: ' . $redirectUrl);
    exit;
}

header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/dashboard.php' : BASE_URL . '/views/member/dashboard.php'));
exit;
