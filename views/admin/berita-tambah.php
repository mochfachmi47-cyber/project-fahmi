<?php
/**
 * FORSAKDA 27 - Form Tambah Berita (Admin)
 */

$pageTitle = 'Tulis Berita Baru - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto max-w-4xl">
        
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Publikasi Warta
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Tulis Berita / Artikel Baru
                </h1>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                Kembali ke Daftar Berita
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="<?php echo BASE_URL; ?>/actions/news_process.php" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="create">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Judul Berita / Artikel *
                    </label>
                    <input type="text" name="judul" required placeholder="Tulis judul berita yang menarik dan informatif..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Berita *
                        </label>
                        <select name="kategori" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                            <option value="Kegiatan">Kegiatan Pesantren / Reuni</option>
                            <option value="Opini">Opini & Kajian Santri</option>
                            <option value="Sosial">Sosial & Wakaf</option>
                            <option value="Alumni">Kabar Alumni</option>
                            <option value="Pesantren">Informasi Almamater</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Status Publikasi
                        </label>
                        <select name="status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                            <option value="published">Langsung Tayang (Published)</option>
                            <option value="draft">Simpan sebagai Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ringkasan Singkat (Lead Paragraph)
                    </label>
                    <textarea name="ringkasan" rows="2" placeholder="Ringkasan 1-2 kalimat untuk preview di beranda..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Isi Konten Berita Lengkap * (Mendukung HTML & Paragraf)
                    </label>
                    <textarea name="konten" rows="8" required placeholder="Tuliskan isi berita secara lengkap..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100 font-sans"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Gambar Sampul (Cover Berita, JPG/PNG Maks 2MB)
                    </label>
                    <input type="file" name="gambar" accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_pinned" name="is_pinned" value="1" class="w-4 h-4 text-brand-600 rounded">
                    <label for="is_pinned" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        Sematkan (Pin) berita ini di posisi teratas
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-2xl text-xs transition shadow-md shadow-brand-600/30">
                        Terbitkan Berita
                    </button>
                </div>
            </form>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
