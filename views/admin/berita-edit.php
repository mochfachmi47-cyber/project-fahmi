<?php
/**
 * FORSAKDA 27 - Form Edit Berita (Admin)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/auth.php';

require_role(['admin']);
$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$news = $stmt->fetch();

if (!$news) {
    header('Location: ' . BASE_URL . '/views/admin/berita.php');
    exit;
}

$pageTitle = 'Edit Berita - ' . $news['judul'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto max-w-4xl">
        
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Edit Warta
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Edit Berita / Artikel
                </h1>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                Kembali ke Daftar Berita
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="<?php echo BASE_URL; ?>/actions/news_process.php" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $news['id']; ?>">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Judul Berita / Artikel *
                    </label>
                    <input type="text" name="judul" value="<?php echo e($news['judul']); ?>" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Berita *
                        </label>
                        <select name="kategori" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                            <option value="Kegiatan" <?php echo ($news['kategori'] === 'Kegiatan') ? 'selected' : ''; ?>>Kegiatan Pesantren / Reuni</option>
                            <option value="Opini" <?php echo ($news['kategori'] === 'Opini') ? 'selected' : ''; ?>>Opini & Kajian Santri</option>
                            <option value="Sosial" <?php echo ($news['kategori'] === 'Sosial') ? 'selected' : ''; ?>>Sosial & Wakaf</option>
                            <option value="Alumni" <?php echo ($news['kategori'] === 'Alumni') ? 'selected' : ''; ?>>Kabar Alumni</option>
                            <option value="Pesantren" <?php echo ($news['kategori'] === 'Pesantren') ? 'selected' : ''; ?>>Informasi Almamater</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Status Publikasi
                        </label>
                        <select name="status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100">
                            <option value="published" <?php echo ($news['status'] === 'published') ? 'selected' : ''; ?>>Langsung Tayang (Published)</option>
                            <option value="draft" <?php echo ($news['status'] === 'draft') ? 'selected' : ''; ?>>Simpan sebagai Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ringkasan Singkat (Lead Paragraph)
                    </label>
                    <textarea name="ringkasan" rows="2" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100"><?php echo e($news['ringkasan']); ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Isi Konten Berita Lengkap *
                    </label>
                    <textarea name="konten" rows="8" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100"><?php echo e($news['konten']); ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ganti Gambar Sampul (Opsional, JPG/PNG Maks 2MB)
                    </label>
                    <?php if (!empty($news['gambar'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo BASE_URL . '/' . e($news['gambar']); ?>" class="w-32 h-20 object-cover rounded-xl border">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="gambar" accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_pinned" name="is_pinned" value="1" <?php echo $news['is_pinned'] ? 'checked' : ''; ?> class="w-4 h-4 text-brand-600 rounded">
                    <label for="is_pinned" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        Sematkan (Pin) berita ini di posisi teratas
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-2xl text-xs transition shadow-md shadow-brand-600/30">
                        Simpan Perubahan Berita
                    </button>
                </div>
            </form>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
