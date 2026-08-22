<?php
/**
 * FORSAKDA 27 - Pengaturan Profil & Keamanan Akun
 */

$pageTitle = 'Profil Saya - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_login();
$user = current_user();
$pdo = Database::getConnection();

// Fetch latest user details from DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :uid LIMIT 1");
$stmt->execute([':uid' => $user['id']]);
$userData = $stmt->fetch();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto max-w-4xl">
        
        <div>
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Pengaturan Akun
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                Profil & Keamanan Akun
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                Perbarui biodata santri dan jaga keamanan kata sandi Anda.
            </p>
        </div>

        <!-- 1. Biodata Profile Form -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-user-pen text-brand-600"></i> Biodata Pribadi
            </h3>

            <form method="POST" action="<?php echo BASE_URL; ?>/actions/auth_process.php" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="update_profile">

                <div class="flex flex-col sm:flex-row items-center gap-6 pb-4">
                    <img id="avatarPreview" src="<?php echo !empty($userData['foto']) ? BASE_URL . '/' . e($userData['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($userData['nama']) . '&background=059669&color=fff'; ?>" 
                         alt="<?php echo e($userData['nama']); ?>" 
                         class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-500/40 shadow-md">
                    
                    <div class="space-y-1.5 text-center sm:text-left">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Ganti Foto Profil</label>
                        <input type="file" name="foto" accept="image/png, image/jpeg, image/webp" data-preview-target="avatarPreview" class="image-preview-input text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500">
                        <p class="text-[10px] text-slate-400">Maksimal 2MB, Format JPG/PNG/WEBP.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap & Gelar *</label>
                        <input type="text" name="nama" value="<?php echo e($userData['nama']); ?>" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Email (Permanen)</label>
                        <input type="email" value="<?php echo e($userData['email']); ?>" disabled class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Angkatan</label>
                        <input type="text" value="27" disabled class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-brand-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" value="<?php echo e($userData['no_hp']); ?>" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Domisili / Kota</label>
                        <input type="text" name="domisili" value="<?php echo e($userData['domisili']); ?>" placeholder="Jakarta, Surabaya..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Profesi / Aktivitas</label>
                    <input type="text" name="profesi" value="<?php echo e($userData['profesi']); ?>" placeholder="Software Engineer, Guru, Wirausaha..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bio Singkat</label>
                    <textarea name="bio" rows="2" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($userData['bio']); ?></textarea>
                </div>

                <div class="pt-3 text-right">
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Change Password Form -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-key text-amber-600"></i> Ganti Kata Sandi (Password)
            </h3>

            <form method="POST" action="<?php echo BASE_URL; ?>/actions/auth_process.php" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="change_password">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Saat Ini (Lama) *</label>
                    <input type="password" name="old_password" required placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Baru (Min. 6 Karakter) *</label>
                        <input type="password" name="new_password" required placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Kata Sandi Baru *</label>
                        <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="pt-3 text-right">
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition">
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
