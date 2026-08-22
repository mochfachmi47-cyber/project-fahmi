<?php
/**
 * FORSAKDA 27 - Form Edit Lowongan Kerja (Admin & Anggota)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/auth.php';

require_role(['anggota', 'admin']);
$user = current_user();
$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM job_vacancies WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$job = $stmt->fetch();

if (!$job || (!has_role('admin') && (int)$job['user_id'] !== (int)$user['id'])) {
    set_flash('error', 'Info lowongan tidak ditemukan atau Anda tidak berhak mengeditnya.');
    header('Location: ' . (has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'));
    exit;
}

$pageTitle = 'Edit Loker - ' . $job['posisi'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto max-w-4xl">
        
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Bursa Kerja & Karir
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Edit Lowongan Kerja
                </h1>
            </div>
            <a href="<?php echo has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'; ?>" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                Kembali
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="<?php echo BASE_URL; ?>/actions/job_process.php" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $job['id']; ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Posisi / Judul Pekerjaan *
                        </label>
                        <input type="text" name="posisi" value="<?php echo e($job['posisi']); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Perusahaan / Lembaga *
                        </label>
                        <input type="text" name="perusahaan" value="<?php echo e($job['perusahaan']); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lokasi Kerja *
                        </label>
                        <input type="text" name="lokasi" value="<?php echo e($job['lokasi']); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tipe Pekerjaan *
                        </label>
                        <select name="tipe_pekerjaan" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                            <option value="Full-time" <?php echo ($job['tipe_pekerjaan'] === 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                            <option value="Part-time" <?php echo ($job['tipe_pekerjaan'] === 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                            <option value="Freelance" <?php echo ($job['tipe_pekerjaan'] === 'Freelance') ? 'selected' : ''; ?>>Freelance</option>
                            <option value="Internship/Magang" <?php echo ($job['tipe_pekerjaan'] === 'Internship/Magang') ? 'selected' : ''; ?>>Internship/Magang</option>
                            <option value="Remote" <?php echo ($job['tipe_pekerjaan'] === 'Remote') ? 'selected' : ''; ?>>Remote / Dari Rumah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Status Lowongan *
                        </label>
                        <select name="status" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                            <option value="open" <?php echo ($job['status'] === 'open') ? 'selected' : ''; ?>>Dibuka (Open)</option>
                            <option value="closed" <?php echo ($job['status'] === 'closed') ? 'selected' : ''; ?>>Ditutup (Closed)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Estimasi Gaji Minimal (IDR)
                        </label>
                        <input type="number" name="gaji_min" value="<?php echo e($job['gaji_min']); ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Estimasi Gaji Maksimal (IDR)
                        </label>
                        <input type="number" name="gaji_max" value="<?php echo e($job['gaji_max']); ?>" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Pekerjaan & Tanggung Jawab *
                    </label>
                    <textarea name="deskripsi" rows="5" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100"><?php echo e($job['deskripsi']); ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kualifikasi & Persyaratan Pelamar
                    </label>
                    <textarea name="kualifikasi" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100"><?php echo e($job['kualifikasi']); ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kontak & Instruksi Lamaran * (Email HRD / No WhatsApp)
                    </label>
                    <input type="text" name="kontak_lamaran" value="<?php echo e($job['kontak_lamaran']); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-800 dark:text-slate-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ganti Logo Perusahaan (Opsional, JPG/PNG Maks 2MB)
                    </label>
                    <?php if (!empty($job['logo_perusahaan'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL . '/' . e($job['logo_perusahaan']); ?>" class="w-20 h-20 object-cover rounded-xl border">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo_perusahaan" accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-500">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <a href="<?php echo has_role('admin') ? BASE_URL . '/views/admin/loker.php' : BASE_URL . '/views/member/loker-saya.php'; ?>" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-2xl text-xs transition shadow-md shadow-amber-600/30">
                        Simpan Perubahan Loker
                    </button>
                </div>
            </form>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
