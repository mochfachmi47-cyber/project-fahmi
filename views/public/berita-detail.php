<?php
/**
 * FORSAKDA 27 - Detail Berita / Artikel Santri
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$pdo = Database::getConnection();
$slug = sanitize_input($_GET['slug'] ?? '');

$stmt = $pdo->prepare("
    SELECT n.*, u.nama as penulis_nama, u.foto as penulis_foto, u.role as penulis_role
    FROM news n
    JOIN users u ON n.user_id = u.id
    WHERE n.slug = :slug AND n.status = 'published'
    LIMIT 1
");
$stmt->execute([':slug' => $slug]);
$news = $stmt->fetch();

if (!$news) {
    header('Location: ' . BASE_URL . '/views/public/berita.php');
    exit;
}

// Tambah view counter
$updateViews = $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = :id");
$updateViews->execute([':id' => $news['id']]);

$pageTitle = $news['judul'] . ' - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

// Berita Terkait / Lainnya
$relatedStmt = $pdo->prepare("SELECT * FROM news WHERE id != :id AND status = 'published' ORDER BY created_at DESC LIMIT 3");
$relatedStmt->execute([':id' => $news['id']]);
$relatedNews = $relatedStmt->fetchAll();
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb & Back -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-brand-600">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="hover:text-brand-600">Berita Santri</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 dark:text-slate-300 truncate max-w-xs"><?php echo e($news['judul']); ?></span>
        </div>

        <!-- Article Card -->
        <article class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-sm space-y-8">
            
            <!-- Metadata & Title -->
            <div class="space-y-4 border-b border-slate-100 dark:border-slate-800 pb-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-brand-50 text-brand-700 border border-brand-200">
                    <?php echo e($news['kategori']); ?>
                </span>

                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                    <?php echo e($news['judul']); ?>
                </h1>

                <div class="flex flex-wrap items-center justify-between gap-4 text-xs text-slate-400">
                    <div class="flex items-center gap-3">
                        <img src="<?php echo !empty($news['penulis_foto']) ? BASE_URL . '/' . e($news['penulis_foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($news['penulis_nama']) . '&background=059669&color=fff'; ?>" 
                             alt="<?php echo e($news['penulis_nama']); ?>" 
                             class="w-10 h-10 rounded-full object-cover border border-emerald-500/30">
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200"><?php echo e($news['penulis_nama']); ?></p>
                            <p class="text-[11px] text-slate-400">Pengurus FORSAKDA 27</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span><i class="fa-solid fa-calendar-days mr-1.5 text-brand-600"></i> <?php echo format_date_id($news['created_at'], true); ?></span>
                        <span><i class="fa-solid fa-eye mr-1.5 text-amber-500"></i> <?php echo e($news['views'] + 1); ?> kali dibaca</span>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <?php if (!empty($news['gambar'])): ?>
                <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-100 dark:border-slate-800">
                    <img src="<?php echo BASE_URL . '/' . e($news['gambar']); ?>" alt="<?php echo e($news['judul']); ?>" class="w-full max-h-[450px] object-cover">
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="prose prose-slate dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 leading-relaxed text-sm sm:text-base space-y-4">
                <?php echo $news['konten']; ?>
            </div>

            <!-- Social Share / Return -->
            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Berita
                </a>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400">Bagikan Berita:</span>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($news['judul'] . ' - ' . BASE_URL . '/views/public/berita-detail.php?slug=' . $news['slug']); ?>" target="_blank" class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs hover:opacity-90 transition">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>

        </article>

        <!-- Related News -->
        <?php if (!empty($relatedNews)): ?>
            <div class="space-y-4 pt-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Berita Santri Lainnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($relatedNews as $rel): ?>
                        <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($rel['slug']); ?>" class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-brand-500 transition block">
                            <span class="text-[10px] font-bold text-brand-600 uppercase"><?php echo e($rel['kategori']); ?></span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mt-1 line-clamp-2"><?php echo e($rel['judul']); ?></h4>
                            <p class="text-[11px] text-slate-400 mt-2"><?php echo format_date_id($rel['created_at'], false); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
