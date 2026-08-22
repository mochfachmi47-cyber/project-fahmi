<?php
/**
 * FORSAKDA 27 - Daftar Berita & Kabar Santri (Publik)
 */

$pageTitle = 'Berita & Kabar Santri - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

$pdo = Database::getConnection();

// Search & Kategori filter
$q = sanitize_input($_GET['q'] ?? '');
$kategori = sanitize_input($_GET['kategori'] ?? '');

$sql = "SELECT * FROM news WHERE status = 'published'";
$params = [];

if (!empty($q)) {
    $sql .= " AND (judul LIKE :q OR ringkasan LIKE :q OR konten LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($kategori)) {
    $sql .= " AND kategori = :kat";
    $params[':kat'] = $kategori;
}

$sql .= " ORDER BY is_pinned DESC, created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$newsList = $stmt->fetchAll();

// Daftar kategori
$kategoriList = ['Semua', 'Kegiatan', 'Opini', 'Sosial', 'Alumni', 'Pesantren'];
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Warta & Artikel
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white">
                Berita & Kabar Santri FORSAKDA 27
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xl mx-auto">
                Informasi resmi seputar almamater pesantren, kegiatan reuni, tulisan inspiratif santri, dan kabar alumni.
            </p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Category Badges -->
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <?php foreach ($kategoriList as $kat): ?>
                    <?php 
                    $isActive = ($kategori === $kat) || (empty($kategori) && $kat === 'Semua');
                    $linkUrl = ($kat === 'Semua') ? BASE_URL . '/views/public/berita.php' : BASE_URL . '/views/public/berita.php?kategori=' . urlencode($kat);
                    ?>
                    <a href="<?php echo $linkUrl; ?>" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition <?php echo $isActive ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-50'; ?>">
                        <?php echo e($kat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search Input -->
            <form method="GET" action="" class="w-full md:w-80 flex items-center gap-2">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari judul berita..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                </div>
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Cari
                </button>
            </form>

        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (empty($newsList)): ?>
                <div class="col-span-3 text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    <i class="fa-solid fa-newspaper text-4xl mb-3 text-slate-300"></i>
                    <p>Tidak ditemukan berita yang sesuai dengan pencarian Anda.</p>
                </div>
            <?php else: ?>
                <?php foreach ($newsList as $news): ?>
                    <article class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:shadow-xl transition group">
                        <div>
                            <div class="h-52 bg-gradient-to-tr from-brand-900 to-teal-800 relative overflow-hidden flex items-center justify-center">
                                <?php if (!empty($news['gambar'])): ?>
                                    <img src="<?php echo BASE_URL . '/' . e($news['gambar']); ?>" alt="<?php echo e($news['judul']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <?php else: ?>
                                    <i class="fa-solid fa-newspaper text-white/30 text-5xl"></i>
                                <?php endif; ?>

                                <?php if (!empty($news['is_pinned'])): ?>
                                    <span class="absolute top-3 right-3 bg-amber-500 text-slate-900 text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                                        <i class="fa-solid fa-thumbtack"></i> Disematkan
                                    </span>
                                <?php endif; ?>

                                <span class="absolute top-3 left-3 bg-brand-600/90 backdrop-blur-sm text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full shadow">
                                    <?php echo e($news['kategori']); ?>
                                </span>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center gap-3 text-[11px] text-slate-400 font-medium mb-2">
                                    <span><i class="fa-solid fa-calendar-days mr-1 text-slate-400"></i> <?php echo format_date_id($news['created_at'], false); ?></span>
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-eye mr-1 text-slate-400"></i> <?php echo e($news['views']); ?> dilihat</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-snug group-hover:text-brand-600 transition line-clamp-2">
                                    <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($news['slug']); ?>">
                                        <?php echo e($news['judul']); ?>
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                                    <?php echo e($news['ringkasan'] ?: strip_tags($news['konten'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($news['slug']); ?>" class="w-full py-2.5 px-4 bg-slate-50 dark:bg-slate-800 hover:bg-brand-50 hover:text-brand-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs text-center transition block">
                                Baca Artikel Lengkap
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
