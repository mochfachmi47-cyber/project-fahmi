<?php
/**
 * FORSAKDA 27 - Manajemen Berita & Artikel (Admin CRUD)
 */

$pageTitle = 'Kelola Berita & Artikel - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$kategoriFilter = sanitize_input($_GET['kategori'] ?? '');
$statusFilter = sanitize_input($_GET['status'] ?? '');

$sql = "SELECT n.*, u.nama as penulis_nama FROM news n JOIN users u ON n.user_id = u.id WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (n.judul LIKE :q OR n.kategori LIKE :q OR n.ringkasan LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($kategoriFilter) && $kategoriFilter !== 'all') {
    $sql .= " AND n.kategori = :kat";
    $params[':kat'] = $kategoriFilter;
}

if (!empty($statusFilter) && $statusFilter !== 'all') {
    $sql .= " AND n.status = :st";
    $params[':st'] = $statusFilter;
}

$sql .= " ORDER BY n.is_pinned DESC, n.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$newsList = $stmt->fetchAll();

$totalPublished = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn();
$totalDraft = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'draft'")->fetchColumn();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Manajemen Konten
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Kelola Berita & Artikel Santri (CRUD)
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Terbitkan informasi almamater, warta kegiatan reuni, dan opini keislaman Forum Santri Kelas Dua Angkatan 27.
                </p>
            </div>

            <a href="<?php echo BASE_URL; ?>/views/admin/berita-tambah.php" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-extrabold rounded-2xl shadow-md text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tulis Berita Baru</span>
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari judul berita..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                </div>

                <select name="kategori" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Kategori</option>
                    <option value="Kegiatan" <?php echo ($kategoriFilter === 'Kegiatan') ? 'selected' : ''; ?>>Kegiatan</option>
                    <option value="Opini" <?php echo ($kategoriFilter === 'Opini') ? 'selected' : ''; ?>>Opini</option>
                    <option value="Sosial" <?php echo ($kategoriFilter === 'Sosial') ? 'selected' : ''; ?>>Sosial</option>
                    <option value="Alumni" <?php echo ($kategoriFilter === 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                    <option value="Pesantren" <?php echo ($kategoriFilter === 'Pesantren') ? 'selected' : ''; ?>>Pesantren</option>
                </select>

                <select name="status" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Status</option>
                    <option value="published" <?php echo ($statusFilter === 'published') ? 'selected' : ''; ?>>Tayang (Published)</option>
                    <option value="draft" <?php echo ($statusFilter === 'draft') ? 'selected' : ''; ?>>Draft</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Filter
                </button>
                <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
            </form>

            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl"><?php echo $totalPublished; ?> Tayang</span>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl"><?php echo $totalDraft; ?> Draft</span>
            </div>
        </div>

        <!-- News Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Judul & Kategori</th>
                            <th class="p-4">Penulis</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Disematkan (Pin)</th>
                            <th class="p-4">Dibaca</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($newsList)): ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada data berita yang ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($newsList as $n): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <?php if (!empty($n['gambar'])): ?>
                                                <img src="<?php echo BASE_URL . '/' . e($n['gambar']); ?>" class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg border flex-shrink-0">
                                                    <i class="fa-solid fa-newspaper"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white line-clamp-1"><?php echo e($n['judul']); ?></p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[10px] font-bold text-brand-600 uppercase bg-brand-50 px-2 py-0.5 rounded"><?php echo e($n['kategori']); ?></span>
                                                    <?php if ($n['is_pinned']): ?>
                                                        <span class="text-[10px] text-amber-600 font-bold"><i class="fa-solid fa-thumbtack"></i> Pinned</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        <?php echo e($n['penulis_nama']); ?>
                                    </td>
                                    <td class="p-4">
                                        <?php if ($n['status'] === 'published'): ?>
                                            <a href="<?php echo BASE_URL; ?>/actions/news_process.php?action=toggle_status&id=<?php echo $n['id']; ?>" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition inline-block" title="Klik untuk ubah jadi Draft">
                                                Tayang
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>/actions/news_process.php?action=toggle_status&id=<?php echo $n['id']; ?>" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700 hover:bg-slate-200 transition inline-block" title="Klik untuk ubah jadi Tayang">
                                                Draft
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4">
                                        <a href="<?php echo BASE_URL; ?>/actions/news_process.php?action=toggle_pin&id=<?php echo $n['id']; ?>" class="p-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1 <?php echo $n['is_pinned'] ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-slate-400 hover:text-slate-600 bg-slate-100'; ?>" title="Klik untuk toggle pin">
                                            <i class="fa-solid fa-thumbtack"></i>
                                            <span class="text-[10px]"><?php echo $n['is_pinned'] ? 'Pinned' : 'Normal'; ?></span>
                                        </a>
                                    </td>
                                    <td class="p-4 text-xs text-slate-500">
                                        <?php echo e($n['views']); ?>x
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">
                                        <?php echo format_date_id($n['created_at'], false); ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                        <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($n['slug']); ?>" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-block" title="Lihat Berita">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/views/admin/berita-edit.php?id=<?php echo $n['id']; ?>" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold inline-block" title="Edit Berita">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/actions/news_process.php?action=delete&id=<?php echo $n['id']; ?>" onclick="return confirm('Hapus berita \'<?php echo addslashes($n['judul']); ?>\'? Berkas gambar juga akan dihapus.')" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold inline-block" title="Hapus Berita">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
