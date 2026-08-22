<?php
/**
 * FORSAKDA 27 - Job Vacancy (Loker) Controller
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user = current_user();
$pdo = Database::getConnection();

// ==========================================
// 1. CREATE JOB (Tambah Loker)
// ==========================================
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/member/loker-tambah.php');
        exit;
    }

    $posisi         = sanitize_input($_POST['posisi'] ?? '');
    $perusahaan     = sanitize_input($_POST['perusahaan'] ?? '');
    $lokasi         = sanitize_input($_POST['lokasi'] ?? '');
    $tipePekerjaan  = sanitize_input($_POST['tipe_pekerjaan'] ?? 'Full-time');
    $gajiMin        = (int)($_POST['gaji_min'] ?? 0);
    $gajiMax        = (int)($_POST['gaji_max'] ?? 0);
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $kualifikasi    = trim($_POST['kualifikasi'] ?? '');
    $kontakLamaran  = sanitize_input($_POST['kontak_lamaran'] ?? '');

    if (empty($posisi) || empty($perusahaan) || empty($lokasi) || empty($deskripsi) || empty($kontakLamaran)) {
        set_flash('error', 'Semua bidang bertanda bintang (*) wajib diisi.');
        header('Location: ' . BASE_URL . '/views/member/loker-tambah.php');
        exit;
    }

    // Upload Logo jika ada
    $logoPath = null;
    if (!empty($_FILES['logo_perusahaan']['name'])) {
        $uploadRes = upload_image($_FILES['logo_perusahaan'], 'loker');
        if ($uploadRes['success']) {
            $logoPath = $uploadRes['filepath'];
        }
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO job_vacancies (user_id, posisi, perusahaan, lokasi, tipe_pekerjaan, gaji_min, gaji_max, deskripsi, kualifikasi, kontak_lamaran, logo_perusahaan, status, created_at)
            VALUES (:uid, :posisi, :perusahaan, :lokasi, :tipe, :gmin, :gmax, :desk, :kual, :kontak, :logo, 'open', NOW())
        ");
        $stmt->execute([
            ':uid'        => $user['id'],
            ':posisi'     => $posisi,
            ':perusahaan' => $perusahaan,
            ':lokasi'     => $lokasi,
            ':tipe'       => $tipePekerjaan,
            ':gmin'       => $gajiMin,
            ':gmax'       => $gajiMax,
            ':desk'       => $deskripsi,
            ':kual'       => $kualifikasi,
            ':kontak'     => $kontakLamaran,
            ':logo'       => $logoPath
        ]);

        $newJobId = (int)$pdo->lastInsertId();
        log_activity($user['id'], 'CREATE_JOB', "Menambahkan lowongan pekerjaan: $posisi di $perusahaan (ID: $newJobId)");
        set_flash('success', 'Alhamdulillah! Info lowongan pekerjaan berhasil dipublikasikan.');

        if (has_role('admin')) {
            header('Location: ' . BASE_URL . '/views/admin/loker.php');
        } else {
            header('Location: ' . BASE_URL . '/views/member/loker-saya.php');
        }
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Gagal memposting loker: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/member/loker-tambah.php');
        exit;
    }
}

// ==========================================
// 2. EDIT JOB
// ==========================================
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
        exit;
    }

    $id             = (int)($_POST['id'] ?? 0);
    $posisi         = sanitize_input($_POST['posisi'] ?? '');
    $perusahaan     = sanitize_input($_POST['perusahaan'] ?? '');
    $lokasi         = sanitize_input($_POST['lokasi'] ?? '');
    $tipePekerjaan  = sanitize_input($_POST['tipe_pekerjaan'] ?? 'Full-time');
    $gajiMin        = (int)($_POST['gaji_min'] ?? 0);
    $gajiMax        = (int)($_POST['gaji_max'] ?? 0);
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $kualifikasi    = trim($_POST['kualifikasi'] ?? '');
    $kontakLamaran  = sanitize_input($_POST['kontak_lamaran'] ?? '');
    $status         = sanitize_input($_POST['status'] ?? 'open');

    // Cek kepemilikan atau role admin
    $stmt = $pdo->prepare("SELECT user_id, logo_perusahaan FROM job_vacancies WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $job = $stmt->fetch();

    if (!$job || (!has_role('admin') && (int)$job['user_id'] !== (int)$user['id'])) {
        set_flash('error', 'Anda tidak memiliki hak untuk mengedit info lowongan ini.');
        header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
        exit;
    }

    $logoUpdate = "";
    $params = [
        ':posisi'     => $posisi,
        ':perusahaan' => $perusahaan,
        ':lokasi'     => $lokasi,
        ':tipe'       => $tipePekerjaan,
        ':gmin'       => $gajiMin,
        ':gmax'       => $gajiMax,
        ':desk'       => $deskripsi,
        ':kual'       => $kualifikasi,
        ':kontak'     => $kontakLamaran,
        ':status'     => $status,
        ':id'         => $id
    ];

    if (!empty($_FILES['logo_perusahaan']['name'])) {
        $uploadRes = upload_image($_FILES['logo_perusahaan'], 'loker');
        if ($uploadRes['success']) {
            $logoUpdate = ", logo_perusahaan = :logo";
            $params[':logo'] = $uploadRes['filepath'];

            // Clean old logo
            if (!empty($job['logo_perusahaan'])) {
                $oldLogo = ROOT_PATH . '/' . $job['logo_perusahaan'];
                if (file_exists($oldLogo)) {
                    @unlink($oldLogo);
                }
            }
        }
    }

    try {
        $sql = "UPDATE job_vacancies SET posisi = :posisi, perusahaan = :perusahaan, lokasi = :lokasi, tipe_pekerjaan = :tipe, gaji_min = :gmin, gaji_max = :gmax, deskripsi = :desk, kualifikasi = :kual, kontak_lamaran = :kontak, status = :status $logoUpdate WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity($user['id'], 'EDIT_JOB', "Mengubah info lowongan ID: $id ($posisi di $perusahaan)");
        set_flash('success', 'Info lowongan pekerjaan berhasil diperbarui.');

        if (has_role('admin')) {
            header('Location: ' . BASE_URL . '/views/admin/loker.php');
        } else {
            header('Location: ' . BASE_URL . '/views/member/loker-saya.php');
        }
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui lowongan: ' . $e->getMessage());
        header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
        exit;
    }
}

// ==========================================
// 3. DELETE JOB
// ==========================================
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT user_id, posisi, logo_perusahaan FROM job_vacancies WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $job = $stmt->fetch();

    if (!$job || (!has_role('admin') && (int)$job['user_id'] !== (int)$user['id'])) {
        set_flash('error', 'Anda tidak berhak menghapus lowongan ini.');
        header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
        exit;
    }

    try {
        // Delete logo file
        if (!empty($job['logo_perusahaan'])) {
            $logoFile = ROOT_PATH . '/' . $job['logo_perusahaan'];
            if (file_exists($logoFile)) {
                @unlink($logoFile);
            }
        }

        $del = $pdo->prepare("DELETE FROM job_vacancies WHERE id = :id");
        $del->execute([':id' => $id]);

        log_activity($user['id'], 'DELETE_JOB', "Menghapus info lowongan ID: $id (" . $job['posisi'] . ")");
        set_flash('success', 'Info lowongan berhasil dihapus.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus lowongan: ' . $e->getMessage());
    }

    header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
    exit;
}

// ==========================================
// 4. TOGGLE STATUS (Open / Closed)
// ==========================================
if ($action === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT user_id, status, posisi FROM job_vacancies WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $job = $stmt->fetch();

    if ($job && (has_role('admin') || (int)$job['user_id'] === (int)$user['id'])) {
        $newStatus = ($job['status'] === 'open') ? 'closed' : 'open';
        $up = $pdo->prepare("UPDATE job_vacancies SET status = :st WHERE id = :id");
        $up->execute([':st' => $newStatus, ':id' => $id]);

        log_activity($user['id'], 'STATUS_JOB', "Mengubah status loker ID: $id (" . $job['posisi'] . ") menjadi $newStatus");
        set_flash('success', 'Status lowongan ' . $job['posisi'] . ' berhasil diubah menjadi: ' . strtoupper($newStatus));
    }

    header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
    exit;
}

header('Location: ' . BASE_URL . '/views/member/loker.php');
exit;
