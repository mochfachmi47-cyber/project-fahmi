<?php
/**
 * FORSAKDA 27 - User Management Controller (Admin CRUD)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

require_role(['admin']); // Hanya Admin yang memiliki akses

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user = current_user();
$pdo = Database::getConnection();

// ==========================================
// 1. CREATE USER (Tambah Pengguna Baru)
// ==========================================
if ($action === 'create_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    $nama      = sanitize_input($_POST['nama'] ?? '');
    $email     = sanitize_input($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = sanitize_input($_POST['role'] ?? 'anggota');
    $status    = sanitize_input($_POST['status'] ?? 'active');
    $angkatan  = sanitize_input($_POST['angkatan'] ?? '27');
    $no_hp     = sanitize_input($_POST['no_hp'] ?? '');
    $domisili  = sanitize_input($_POST['domisili'] ?? '');
    $profesi   = sanitize_input($_POST['profesi'] ?? '');
    $bio       = sanitize_input($_POST['bio'] ?? '');

    if (empty($nama) || empty($password)) {
        set_flash('error', 'Nama dan Password wajib diisi.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    if (!empty($email)) {
        // Cek duplikasi email
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $chk->execute([':email' => $email]);
        if ($chk->fetch()) {
            set_flash('error', 'Alamat email tersebut sudah digunakan akun lain.');
            header('Location: ' . BASE_URL . '/views/admin/users.php');
            exit;
        }
    } else {
        $email = null;
    }

    if (!in_array($role, ['admin', 'anggota', 'publik'], true)) $role = 'anggota';
    if (!in_array($status, ['active', 'pending', 'suspended'], true)) $status = 'active';

    $hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, email, password, role, status, angkatan, no_hp, domisili, profesi, bio, created_at)
            VALUES (:nama, :email, :pass, :role, :status, :angkatan, :no_hp, :domisili, :profesi, :bio, NOW())
        ");
        $stmt->execute([
            ':nama'     => $nama,
            ':email'    => $email,
            ':pass'     => $hash,
            ':role'     => $role,
            ':status'   => $status,
            ':angkatan' => $angkatan,
            ':no_hp'    => $no_hp,
            ':domisili' => $domisili,
            ':profesi'  => $profesi,
            ':bio'      => $bio
        ]);

        $newId = (int)$pdo->lastInsertId();
        log_activity($user['id'], 'CREATE_USER', "Membuat akun baru: $nama (ID: $newId, Role: $role)");
        set_flash('success', "Akun baru untuk '$nama' berhasil dibuat.");
    } catch (Exception $e) {
        set_flash('error', 'Gagal membuat pengguna: ' . $e->getMessage());
    }

    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 2. EDIT USER (Update Biodata & Role Pengguna)
// ==========================================
if ($action === 'edit_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    $id        = (int)($_POST['id'] ?? 0);
    $nama      = sanitize_input($_POST['nama'] ?? '');
    $email     = sanitize_input($_POST['email'] ?? '');
    $role      = sanitize_input($_POST['role'] ?? 'anggota');
    $status    = sanitize_input($_POST['status'] ?? 'active');
    $angkatan  = sanitize_input($_POST['angkatan'] ?? '27');
    $no_hp     = sanitize_input($_POST['no_hp'] ?? '');
    $domisili  = sanitize_input($_POST['domisili'] ?? '');
    $profesi   = sanitize_input($_POST['profesi'] ?? '');
    $bio       = sanitize_input($_POST['bio'] ?? '');
    $newPass   = $_POST['new_password'] ?? '';

    if ($id <= 0 || empty($nama)) {
        set_flash('error', 'Data pengguna tidak valid atau nama kosong.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    // Proteksi self-demotion
    if ($id === $user['id']) {
        $role = 'admin'; // Admin tidak boleh mencopot rolenya sendiri di sini
        $status = 'active'; // Admin tidak boleh men-suspend dirinya sendiri
    }

    if (!empty($email)) {
        // Cek duplikasi email ke user lain
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $chk->execute([':email' => $email, ':id' => $id]);
        if ($chk->fetch()) {
            set_flash('error', 'Alamat email tersebut sudah digunakan oleh akun lain.');
            header('Location: ' . BASE_URL . '/views/admin/users.php');
            exit;
        }
    } else {
        $email = null;
    }

    try {
        $passSql = "";
        $params = [
            ':nama'     => $nama,
            ':email'    => $email,
            ':role'     => $role,
            ':status'   => $status,
            ':angkatan' => $angkatan,
            ':no_hp'    => $no_hp,
            ':domisili' => $domisili,
            ':profesi'  => $profesi,
            ':bio'      => $bio,
            ':id'       => $id
        ];

        if (!empty($newPass)) {
            $passSql = ", password = :pass";
            $params[':pass'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $sql = "UPDATE users SET nama = :nama, email = :email, role = :role, status = :status, angkatan = :angkatan, no_hp = :no_hp, domisili = :domisili, profesi = :profesi, bio = :bio $passSql WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_activity($user['id'], 'EDIT_USER', "Memperbarui akun pengguna: $nama (ID: $id)");
        set_flash('success', "Data pengguna '$nama' berhasil diperbarui.");
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
    }

    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 3. APPROVE USER
// ==========================================
if ($action === 'approve') {
    $id = (int)($_GET['id'] ?? 0);
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        log_activity($user['id'], 'APPROVE_USER', "Menyetujui akun anggota ID: $id");
        set_flash('success', 'Akun anggota berhasil disetujui & diaktifkan.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menyetujui akun: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 4. TOGGLE STATUS (Active / Suspended)
// ==========================================
if ($action === 'toggle_status') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id === $user['id']) {
        set_flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT status, nama FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $target = $stmt->fetch();

        if ($target) {
            $newStatus = ($target['status'] === 'active') ? 'suspended' : 'active';
            $up = $pdo->prepare("UPDATE users SET status = :st WHERE id = :id");
            $up->execute([':st' => $newStatus, ':id' => $id]);

            log_activity($user['id'], 'STATUS_USER', "Mengubah status user ID: $id (" . $target['nama'] . ") menjadi $newStatus");
            set_flash('success', 'Status akun ' . $target['nama'] . ' berhasil diubah menjadi: ' . ucfirst($newStatus));
        }
    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 5. CHANGE ROLE
// ==========================================
if ($action === 'change_role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    $role = sanitize_input($_POST['role'] ?? 'anggota');

    if ($id === $user['id'] && $role !== 'admin') {
        set_flash('error', 'Anda tidak dapat mencopot hak akses Admin dari diri Anda sendiri.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    if (!in_array($role, ['admin', 'anggota', 'publik'], true)) {
        set_flash('error', 'Role tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET role = :r WHERE id = :id");
        $stmt->execute([':r' => $role, ':id' => $id]);

        log_activity($user['id'], 'ROLE_USER', "Mengubah role user ID: $id menjadi $role");
        set_flash('success', 'Role pengguna berhasil diperbarui menjadi: ' . ucfirst($role));
    } catch (Exception $e) {
        set_flash('error', 'Gagal mengubah role: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 6. RESET PASSWORD
// ==========================================
if ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    $newPass = trim($_POST['new_password'] ?? 'santriforsakda27');

    if (empty($newPass)) {
        $newPass = 'santriforsakda27';
    }

    try {
        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
        $stmt->execute([':p' => $hash, ':id' => $id]);

        log_activity($user['id'], 'RESET_PASSWORD', "Mereset password user ID: $id");
        set_flash('success', "Password akun (ID: $id) berhasil direset ke: $newPass");
    } catch (Exception $e) {
        set_flash('error', 'Gagal mereset password: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

// ==========================================
// 7. DELETE USER (Safe Cascade Deletion)
// ==========================================
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id === $user['id']) {
        set_flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Ambil data user
        $stmt = $pdo->prepare("SELECT nama, foto FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $targetUser = $stmt->fetch();

        if ($targetUser) {
            // Reassign or delete related data
            // Reassign news to current admin
            $pdo->prepare("UPDATE news SET user_id = :admin_id WHERE user_id = :uid")->execute([
                ':admin_id' => $user['id'],
                ':uid'      => $id
            ]);

            // Reassign jobs to current admin
            $pdo->prepare("UPDATE job_vacancies SET user_id = :admin_id WHERE user_id = :uid")->execute([
                ':admin_id' => $user['id'],
                ':uid'      => $id
            ]);

            // Delete messages
            $pdo->prepare("DELETE FROM messages WHERE user_id = :uid")->execute([':uid' => $id]);

            // Delete logs
            $pdo->prepare("DELETE FROM activity_logs WHERE user_id = :uid")->execute([':uid' => $id]);

            // Delete user
            $pdo->prepare("DELETE FROM users WHERE id = :uid")->execute([':uid' => $id]);

            // Delete avatar file if exists
            if (!empty($targetUser['foto'])) {
                $fotoFile = ROOT_PATH . '/' . $targetUser['foto'];
                if (file_exists($fotoFile)) {
                    @unlink($fotoFile);
                }
            }

            $pdo->commit();

            log_activity($user['id'], 'DELETE_USER', "Menghapus akun pengguna: " . $targetUser['nama'] . " (ID: $id)");
            set_flash('success', "Akun pengguna '" . $targetUser['nama'] . "' berhasil dihapus secara bersih dari sistem.");
        } else {
            $pdo->rollBack();
            set_flash('error', 'Pengguna tidak ditemukan.');
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        set_flash('error', 'Gagal menghapus user: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/views/admin/users.php');
    exit;
}

header('Location: ' . BASE_URL . '/views/admin/users.php');
exit;
