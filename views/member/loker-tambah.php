<?php
/**
 * FORSAKDA 27 - Form Tambah Info Lowongan Kerja (Role Anggota & Admin)
 * Fitur 3: Bisa menambah info lowongan pekerjaan
 */

$pageTitle = 'Pasang Info Lowongan Kerja - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$user = current_user();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Bursa Kerja Santri
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Pasang Info Lowongan Pekerjaan
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Bagikan peluang kerja atau rekrutmen usaha Anda untuk menjangkau rekan santri & alumni FORSAKDA 27.
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition hover:bg-slate-200 flex items-center gap-1.5">
                <i class="fa-solid fa-folder-open"></i> Loker Saya
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-sm max-w-4xl">
            <form method="POST" action="<?php echo BASE_URL; ?>/actions/job_process.php" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="create">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Posisi / Judul Pekerjaan *
                        </label>
                        <input type="text" name="posisi" required placeholder="Contoh: Web Developer, Guru Tahfidz, Admin Keuangan" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Perusahaan / Lembaga / Usaha *
                        </label>
                        <input type="text" name="perusahaan" required placeholder="Contoh: PT Berkah Santri Digital, Pesantren X" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lokasi Kerja / Penempatan *
                        </label>
                        <input type="text" name="lokasi" required placeholder="Contoh: Jakarta Selatan, Surabaya, Remote (WFA)" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tipe Pekerjaan *
                        </label>
                        <select name="tipe_pekerjaan" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Internship/Magang">Internship / Magang</option>
                            <option value="Remote">Remote</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Rentang Gaji Minimum (Opsional - Rp)
                        </label>
                        <input type="number" name="gaji_min" placeholder="Contoh: 4000000" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Rentang Gaji Maksimum (Opsional - Rp)
                        </label>
                        <input type="number" name="gaji_max" placeholder="Contoh: 7000000" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Pekerjaan & Tanggung Jawab *
                    </label>
                    <textarea name="deskripsi" rows="4" required placeholder="Jelaskan deskripsi umum pekerjaan, tugas harian, budaya kerja..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Persyaratan & Kualifikasi
                    </label>
                    <textarea name="kualifikasi" rows="4" placeholder="- Pendidikan minimal SMA/MA/Pondok Pesantren&#10;- Menguasai skill X&#10;- Jujur, disiplin, berakhlak baik" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kontak / Link Cara Melamar *
                    </label>
                    <input type="text" name="kontak_lamaran" required placeholder="Contoh: hrd@perusahaan.com atau WhatsApp 081234567890" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Logo Perusahaan / Pamflet Loker (Opsional, JPG/PNG Maks 2MB)
                    </label>
                    <input type="file" name="logo_perusahaan" accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-500">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <a href="<?php echo BASE_URL; ?>/views/member/dashboard.php" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-extrabold rounded-2xl shadow-lg shadow-amber-600/30 text-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Publikasikan Info Loker</span>
                    </button>
                </div>
            </form>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
