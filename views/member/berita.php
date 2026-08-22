<?php
/**
 * FORSAKDA 27 - Berita Santri & Kabar Almamater (Member View)
 */

$pageTitle = 'Berita Santri - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$kategori = sanitize_input($_GET['kategori'] ?? '');

$sql = "SELECT * FROM news WHERE status = 'published'";
$params = [];

if (!empty($q)) {
    $sql .= " AND (judul LIKE :q OR ringkasan LIKE :q OR konten LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($kategori) && $kategori !== 'Semua') {
    $sql .= " AND kategori = :kat";
    $params[':kat'] = $kategori;
}

$sql .= " ORDER BY is_pinned DESC, created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$newsList = $stmt->fetchAll();

$kategoriList = ['Semua', 'Kegiatan', 'Opini', 'Sosial', 'Alumni', 'Pesantren'];
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Informasi & Opini
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Warta & Berita Santri Terkini
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Kumpulan berita resmi, opini keislaman, dan kabar keluarga besar FORSAKDA 27.
                </p>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <?php foreach ($kategoriList as $kat): ?>
                    <?php 
                    $isActive = ($kategori === $kat) || (empty($kategori) && $kat === 'Semua');
                    $linkUrl = ($kat === 'Semua') ? BASE_URL . '/views/member/berita.php' : BASE_URL . '/views/member/berita.php?kategori=' . urlencode($kat);
                    ?>
                    <a href="<?php echo $linkUrl; ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition <?php echo $isActive ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-50'; ?>">
                        <?php echo e($kat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="GET" action="" class="w-full md:w-72 flex items-center gap-2">
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari warta santri..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- News Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($newsList)): ?>
                <div class="col-span-3 text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    <p>Tidak ada berita ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($newsList as $news): ?>
                    <article class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-lg transition">
                        <div>
                            <div class="h-44 bg-slate-800 relative overflow-hidden flex items-center justify-center">
                                <?php if (!empty($news['gambar'])): ?>
                                    <img src="<?php echo BASE_URL . '/' . e($news['gambar']); ?>" alt="<?php echo e($news['judul']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fa-solid fa-newspaper text-white/30 text-4xl"></i>
                                <?php endif; ?>
                                <span class="absolute top-3 left-3 bg-brand-600 text-white text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full shadow">
                                    <?php echo e($news['kategori']); ?>
                                </span>
                            </div>

                            <div class="p-5">
                                <span class="text-[11px] text-slate-400 font-medium">
                                    <i class="fa-solid fa-calendar mr-1"></i> <?php echo format_date_id($news['created_at'], false); ?>
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1.5 line-clamp-2 hover:text-brand-600">
                                    <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($news['slug']); ?>">
                                        <?php echo e($news['judul']); ?>
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 line-clamp-3 leading-relaxed">
                                    <?php echo e($news['ringkasan'] ?: strip_tags($news['konten'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="p-5 pt-0">
                            <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($news['slug']); ?>" class="w-full py-2 bg-slate-50 dark:bg-slate-800 hover:bg-brand-50 hover:text-brand-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs text-center transition block">
                                Baca Berita
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
