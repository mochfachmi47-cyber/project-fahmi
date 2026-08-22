<?php
/**
 * FORSAKDA 27 - Manajemen Galeri Foto (Admin CRUD)
 */

$pageTitle = 'Kelola Galeri Foto - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
$stmt->execute();
$galleries = $stmt->fetchAll();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div>
            <span class="text-xs font-extrabold uppercase tracking-widest text-purple-600 bg-purple-50 px-3 py-1 rounded-full border border-purple-200">
                Media & Kenangan
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                Kelola Galeri Kegiatan & Reuni (CRUD)
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                Unggah foto dokumentasi silaturahmi akbar dan kegiatan pondok santri Forum Santri Kelas Dua Angkatan 27.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Upload Form (Left / Col 5) -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <i class="fa-solid fa-cloud-arrow-up text-purple-600"></i> Unggah Foto Baru
                </h3>

                <form method="POST" action="<?php echo BASE_URL; ?>/actions/content_process.php" enctype="multipart/form-data" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="add_gallery">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Caption Foto *</label>
                        <input type="text" name="judul" required placeholder="Contoh: Reuni Akbar 2026 di Pesantren" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori *</label>
                        <select name="kategori" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                            <option value="Kegiatan">Kegiatan Reuni</option>
                            <option value="Kenangan">Kenangan Masa Mondok</option>
                            <option value="Sosial">Bakti Sosial & Santunan</option>
                            <option value="Pesantren">Kunjungan Almamater</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="2" placeholder="Catatan kecil momen tersebut..." class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">File Foto (JPG/PNG Maks 2MB) *</label>
                        <input type="file" name="gambar" required accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs transition shadow-md shadow-purple-600/20">
                            Simpan ke Galeri
                        </button>
                    </div>
                </form>
            </div>

            <!-- Photos Grid (Right / Col 7) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Koleksi Galeri Saat Ini (<?php echo count($galleries); ?>)</h3>
                </div>

                <?php if (empty($galleries)): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200 dark:border-slate-800 text-center text-slate-400 text-xs">
                        Belum ada foto yang diunggah. Silakan gunakan formulir di samping untuk menambahkan foto.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($galleries as $g): ?>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:shadow-lg transition">
                                <div>
                                    <div class="h-36 bg-slate-800 overflow-hidden relative">
                                        <img src="<?php echo BASE_URL . '/' . e($g['gambar']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        <span class="absolute top-2 left-2 bg-slate-900/80 text-white text-[9px] font-bold px-2 py-0.5 rounded">
                                            <?php echo e($g['kategori']); ?>
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1"><?php echo e($g['judul']); ?></h4>
                                        <span class="text-[10px] text-slate-400"><?php echo format_date_id($g['created_at'], false); ?></span>
                                    </div>
                                </div>
                                <div class="p-3 pt-0 text-right">
                                    <a href="<?php echo BASE_URL; ?>/actions/content_process.php?action=delete_gallery&id=<?php echo $g['id']; ?>" onclick="return confirm('Hapus foto \'<?php echo addslashes($g['judul']); ?>\' dari galeri? Berkas gambar di server juga akan dihapus.')" class="px-3 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
