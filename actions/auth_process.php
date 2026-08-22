<?php
/**
 * FORSAKDA 27 - Authentication & Profile Controller
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/auth.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ==========================================
// 1. LOGIN ACTION
// ==========================================
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid. Silakan coba kembali.');
        header('Location: ' . BASE_URL . '/views/public/login.php');
        exit;
    }

    $nama     = sanitize_input($_POST['nama'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($password)) {
        set_flash('error', 'Nama/Email dan password wajib diisi.');
        header('Location: ' . BASE_URL . '/views/public/login.php');
        exit;
    }

    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE nama = :n OR email = :e LIMIT 1");
        $stmt->execute([':n' => $nama, ':e' => $nama]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Cek Status Akun
            if ($user['status'] === 'pending') {
                set_flash('warning', 'Pendaftaran Anda sedang menunggu verifikasi oleh Pengurus / Admin Pusat FORSAKDA 27.');
                header('Location: ' . BASE_URL . '/views/public/login.php');
                exit;
            }

            if ($user['status'] === 'suspended') {
                set_flash('error', 'Akun Anda telah dinonaktifkan oleh administrator.');
                header('Location: ' . BASE_URL . '/views/public/login.php');
                exit;
            }

            // Mencegah Session Fixation
            session_regenerate_id(true);

            // Simpan Session User
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['user_nama']     = $user['nama'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['user_role']     = $user['role'];
            $_SESSION['user_status']   = $user['status'];
            $_SESSION['user_foto']     = $user['foto'];
            $_SESSION['user_angkatan'] = $user['angkatan'];
            $_SESSION['user_no_hp']    = $user['no_hp'];
            $_SESSION['user_domisili'] = $user['domisili'];
            $_SESSION['user_profesi']  = $user['profesi'];

            log_activity($user['id'], 'LOGIN', 'Pengguna berhasil masuk ke sistem.');
            set_flash('success', 'Ahlan wa Sahlan, ' . $user['nama'] . '! Selamat datang di FORSAKDA 27.');

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/views/member/dashboard.php');
            }
            exit;
        } else {
            set_flash('error', 'Nama atau password yang Anda masukkan salah.');
            header('Location: ' . BASE_URL . '/views/public/login.php');
            exit;
        }
    } catch (Exception $e) {
        set_flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/public/login.php');
        exit;
    }
}

// ==========================================
// 2. REGISTER ACTION (Pendaftaran Santri)
// ==========================================
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid. Silakan ulangi pendaftaran.');
        header('Location: ' . BASE_URL . '/views/public/register.php');
        exit;
    }

    $nama     = sanitize_input($_POST['nama'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($password)) {
        set_flash('error', 'Nama dan password wajib diisi.');
        header('Location: ' . BASE_URL . '/views/public/register.php');
        exit;
    }

    if (strlen($password) < 6) {
        set_flash('error', 'Password minimal 6 karakter demi keamanan akun.');
        header('Location: ' . BASE_URL . '/views/public/register.php');
        exit;
    }

    try {
        $pdo = Database::getConnection();

        // Cek apakah nama sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE nama = :nama LIMIT 1");
        $stmt->execute([':nama' => $nama]);
        if ($stmt->fetch()) {
            set_flash('error', 'Nama tersebut sudah terdaftar. Silakan gunakan nama lain atau langsung login.');
            header('Location: ' . BASE_URL . '/views/public/register.php');
            exit;
        }

        // Hash password dengan bcrypt
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Simpan data pendaftaran anggota baru
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, password, role, status, angkatan, created_at)
            VALUES (:nama, :pass, 'anggota', 'active', '27', NOW())
        ");
        $stmt->execute([
            ':nama' => $nama,
            ':pass' => $passwordHash,
        ]);

        $newUserId = (int)$pdo->lastInsertId();
        log_activity($newUserId, 'REGISTER', "Pendaftaran anggota baru: $nama");

        set_flash('success', 'Alhamdulillah! Pendaftaran berhasil. Silakan masuk menggunakan nama dan password Anda.');
        header('Location: ' . BASE_URL . '/views/public/login.php');
        exit;

    } catch (Exception $e) {
        set_flash('error', 'Gagal mendaftar: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/public/register.php');
        exit;
    }
}

// ==========================================
// 3. LOGOUT ACTION
// ==========================================
if ($action === 'logout') {
    $uid = $_SESSION['user_id'] ?? null;
    if ($uid) {
        log_activity($uid, 'LOGOUT', 'Pengguna keluar dari sistem.');
    }
    
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header('Location: ' . BASE_URL . '/views/public/login.php');
    exit;
}

// ==========================================
// 4. UPDATE PROFIL
// ==========================================
if ($action === 'update_profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $nama     = sanitize_input($_POST['nama'] ?? '');
    $no_hp    = sanitize_input($_POST['no_hp'] ?? '');
    $domisili = sanitize_input($_POST['domisili'] ?? '');
    $profesi  = sanitize_input($_POST['profesi'] ?? '');
    $bio      = sanitize_input($_POST['bio'] ?? '');

    try {
        $pdo = Database::getConnection();

        // Handle Avatar Update
        $fotoUpdateSql = "";
        $params = [
            ':nama'     => $nama,
            ':no_hp'    => $no_hp,
            ':domisili' => $domisili,
            ':profesi'  => $profesi,
            ':bio'      => $bio,
            ':uid'      => $userId
        ];

        if (!empty($_FILES['foto']['name'])) {
            $uploadRes = upload_image($_FILES['foto'], 'avatars');
            if ($uploadRes['success']) {
                $fotoUpdateSql = ", foto = :foto";
                $params[':foto'] = $uploadRes['filepath'];
                $_SESSION['user_foto'] = $uploadRes['filepath'];
            } else {
                set_flash('error', 'Gagal mengunggah foto profil: ' . ($uploadRes['message'] ?? 'Terjadi kesalahan.'));
                header('Location: ' . BASE_URL . '/views/member/profil.php');
                exit;
            }
        }

        $sql = "UPDATE users SET nama = :nama, no_hp = :no_hp, domisili = :domisili, profesi = :profesi, bio = :bio $fotoUpdateSql WHERE id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['user_nama']     = $nama;
        $_SESSION['user_no_hp']    = $no_hp;
        $_SESSION['user_domisili'] = $domisili;
        $_SESSION['user_profesi']  = $profesi;

        log_activity($userId, 'UPDATE_PROFILE', 'Pengguna memperbarui profil pribadi.');
        set_flash('success', 'Profil Anda berhasil diperbarui.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;

    } catch (Exception $e) {
        set_flash('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }
}

// ==========================================
// 5. CHANGE PASSWORD
// ==========================================
if ($action === 'change_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    if (!verify_csrf()) {
        set_flash('error', 'Token CSRF tidak valid.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $oldPass = $_POST['old_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($oldPass) || empty($newPass)) {
        set_flash('error', 'Password lama dan password baru wajib diisi.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }

    if (strlen($newPass) < 6) {
        set_flash('error', 'Password baru minimal 6 karakter.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }

    if ($newPass !== $confirmPass) {
        set_flash('error', 'Konfirmasi password baru tidak cocok.');
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }

    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :uid LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $curr = $stmt->fetch();

        if ($curr && password_verify($oldPass, $curr['password'])) {
            $newHash = password_hash($newPass, PASSWORD_BCRYPT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = :p WHERE id = :uid");
            $updateStmt->execute([':p' => $newHash, ':uid' => $userId]);

            log_activity($userId, 'CHANGE_PASSWORD', 'Pengguna berhasil mengganti kata sandi.');
            set_flash('success', 'Kata sandi Anda berhasil diperbarui!');
        } else {
            set_flash('error', 'Kata sandi saat ini (lama) tidak sesuai.');
        }

        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/member/profil.php');
        exit;
    }
}

header('Location: ' . BASE_URL . '/index.php');
exit;
