<?php
/**
 * FORSAKDA 27 - Galeri Kenangan & Kegiatan Santri (Publik)
 */

$pageTitle = 'Galeri Kenangan & Kegiatan - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
$stmt->execute();
$galleries = $stmt->fetchAll();
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Dokumentasi & Kenangan
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white">
                Galeri Kegiatan Santri FORSAKDA 27
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xl mx-auto">
                Koleksi momen kebersamaan, reuni akbar, dan napak tilas perjuangan masa mondok angkatan 27.
            </p>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($galleries)): ?>
                <!-- Default aesthetic placeholders if empty -->
                <div class="col-span-3 text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    <i class="fa-solid fa-images text-4xl mb-3 text-slate-300"></i>
                    <p>Foto kenangan dan kegiatan akan diunggah oleh pengurus FORSAKDA 27.</p>
                </div>
            <?php else: ?>
                <?php foreach ($galleries as $item): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition group">
                        <div class="h-64 bg-slate-800 overflow-hidden relative">
                            <img src="<?php echo BASE_URL . '/' . e($item['gambar']); ?>" alt="<?php echo e($item['judul']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full border border-white/10">
                                <?php echo e($item['kategori']); ?>
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white"><?php echo e($item['judul']); ?></h3>
                            <?php if (!empty($item['deskripsi'])): ?>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"><?php echo e($item['deskripsi']); ?></p>
                            <?php endif; ?>
                            <span class="text-[10px] text-slate-400 block mt-3 font-medium">
                                <i class="fa-solid fa-clock mr-1"></i> <?php echo format_date_id($item['created_at'], false); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
