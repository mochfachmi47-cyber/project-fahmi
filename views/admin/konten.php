<?php
/**
 * FORSAKDA 27 - Manajemen Konten Publik (Admin CRUD Konten)
 * Mengelola Visi, Misi, Tujuan Web, Sejarah, dan Sambutan
 */

$pageTitle = 'Kelola Konten Publik - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$pdo = Database::getConnection();

// Ambil konten Visi & Misi
$stmt1 = $pdo->prepare("SELECT content FROM site_content WHERE key_name = 'visi_misi' LIMIT 1");
$stmt1->execute();
$row1 = $stmt1->fetch();
$visiMisi = $row1 ? json_decode($row1['content'], true) : [];

// Ambil konten Tujuan Web & Sejarah
$stmt2 = $pdo->prepare("SELECT content FROM site_content WHERE key_name = 'tujuan_web' LIMIT 1");
$stmt2->execute();
$row2 = $stmt2->fetch();
$tujuanData = $row2 ? json_decode($row2['content'], true) : [];

$visi = $visiMisi['visi'] ?? '';
$misiText = implode("\n", $visiMisi['misi'] ?? []);
$nilaiText = implode("\n", $visiMisi['nilai_utama'] ?? []);

$tujuanText = implode("\n", $tujuanData['tujuan'] ?? []);
$sejarah = $tujuanData['sejarah'] ?? '';
$sambutan = $tujuanData['sambutan_ketua'] ?? '';
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto max-w-5xl">
        
        <div>
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Pengaturan Informasi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                Kelola Konten Publik (Visi, Misi, Tujuan & Sejarah)
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                Perbarui teks visi misi dan penjelasan tujuan website yang tampil di halaman depan untuk publik.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8">
            
            <!-- 1. Form Visi & Misi -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fa-solid fa-compass text-brand-600"></i> Konten Visi, Misi & Nilai Utama
                </h3>

                <form method="POST" action="<?php echo BASE_URL; ?>/actions/content_process.php" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="update_visi_misi">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pernyataan Visi Utama</label>
                        <textarea name="visi" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($visi); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Poin-Poin Misi (1 Baris = 1 Poin Misi)
                        </label>
                        <textarea name="misi" rows="5" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($misiText); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Nilai-Nilai Dasar / Core Values (1 Baris = 1 Nilai)
                        </label>
                        <textarea name="nilai_utama" rows="4" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($nilaiText); ?></textarea>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition shadow-sm">
                            Simpan Perubahan Visi & Misi
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Form Tujuan Web & Sejarah -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fa-solid fa-laptop-code text-teal-600"></i> Konten Tujuan Web, Sejarah & Sambutan
                </h3>

                <form method="POST" action="<?php echo BASE_URL; ?>/actions/content_process.php" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="update_tujuan_sejarah">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Poin-Poin Tujuan Pembuatan Website (1 Baris = 1 Poin)
                        </label>
                        <textarea name="tujuan" rows="5" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($tujuanText); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sejarah Singkat Angkatan 27</label>
                        <textarea name="sejarah" rows="4" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($sejarah); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sambutan Ketua FORSAKDA 27</label>
                        <textarea name="sambutan_ketua" rows="3" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"><?php echo e($sambutan); ?></textarea>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl text-xs transition shadow-sm">
                            Simpan Perubahan Tujuan & Sejarah
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
