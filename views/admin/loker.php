<?php
/**
 * FORSAKDA 27 - Manajemen Lowongan Kerja (Admin CRUD)
 */

$pageTitle = 'Kelola Lowongan Kerja - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$statusFilter = sanitize_input($_GET['status'] ?? '');
$sql = "SELECT j.*, u.nama as pemosting_nama, u.email as pemosting_email 
        FROM job_vacancies j
        JOIN users u ON j.user_id = u.id
        WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (j.posisi LIKE :q OR j.perusahaan LIKE :q OR j.lokasi LIKE :q OR u.nama LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($statusFilter) && $statusFilter !== 'all') {
    $sql .= " AND j.status = :st";
    $params[':st'] = $statusFilter;
}

$sql .= " ORDER BY j.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$totalOpen = $pdo->query("SELECT COUNT(*) FROM job_vacancies WHERE status = 'open'")->fetchColumn();
$totalClosed = $pdo->query("SELECT COUNT(*) FROM job_vacancies WHERE status = 'closed'")->fetchColumn();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Bursa Kerja
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Kelola Seluruh Lowongan Kerja (CRUD)
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Moderasi, edit, tinjau, atau hapus info lowongan kerja yang diposting oleh seluruh anggota santri.
                </p>
            </div>

            <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-extrabold rounded-2xl shadow-md text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Loker Baru</span>
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari posisi, perusahaan, pemosting..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-200">
                </div>

                <select name="status" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Status</option>
                    <option value="open" <?php echo ($statusFilter === 'open') ? 'selected' : ''; ?>>Dibuka (Open)</option>
                    <option value="closed" <?php echo ($statusFilter === 'closed') ? 'selected' : ''; ?>>Ditutup (Closed)</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold hover:bg-amber-500 transition">
                    Filter
                </button>
                <a href="<?php echo BASE_URL; ?>/views/admin/loker.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
            </form>

            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl"><?php echo $totalOpen; ?> Dibuka</span>
                <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl"><?php echo $totalClosed; ?> Ditutup</span>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Posisi & Perusahaan</th>
                            <th class="p-4">Pemosting</th>
                            <th class="p-4">Lokasi & Tipe</th>
                            <th class="p-4">Estimasi Gaji</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tanggal Buat</th>
                            <th class="p-4 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($jobs)): ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 text-xs">Belum ada info lowongan kerja.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jobs as $j): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <?php if (!empty($j['logo_perusahaan'])): ?>
                                                <img src="<?php echo BASE_URL . '/' . e($j['logo_perusahaan']); ?>" class="w-10 h-10 rounded-xl object-cover border flex-shrink-0">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base border flex-shrink-0">
                                                    <i class="fa-solid fa-briefcase"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white"><?php echo e($j['posisi']); ?></p>
                                                <p class="text-xs text-brand-600 font-semibold"><?php echo e($j['perusahaan']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs">
                                        <p class="font-bold text-slate-800 dark:text-slate-200"><?php echo e($j['pemosting_nama']); ?></p>
                                        <p class="text-[10px] text-slate-400 font-mono"><?php echo e($j['pemosting_email'] ?: '-'); ?></p>
                                    </td>
                                    <td class="p-4 text-slate-600 dark:text-slate-300">
                                        <p><?php echo e($j['lokasi']); ?></p>
                                        <span class="text-[10px] text-slate-400"><?php echo e($j['tipe_pekerjaan']); ?></span>
                                    </td>
                                    <td class="p-4 font-bold text-emerald-600">
                                        <?php echo format_rupiah($j['gaji_min']); ?>
                                    </td>
                                    <td class="p-4">
                                        <?php if ($j['status'] === 'open'): ?>
                                            <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=toggle_status&id=<?php echo $j['id']; ?>" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition inline-block" title="Klik untuk mengubah status jadi Ditutup">
                                                Dibuka (Open)
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=toggle_status&id=<?php echo $j['id']; ?>" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-700 hover:bg-rose-200 transition inline-block" title="Klik untuk mengubah status jadi Dibuka">
                                                Ditutup (Closed)
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">
                                        <?php echo format_date_id($j['created_at'], false); ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                        <!-- View Detail Button -->
                                        <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $j['id']; ?>" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-block" title="Lihat Tampilan Loker">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="<?php echo BASE_URL; ?>/views/member/loker-edit.php?id=<?php echo $j['id']; ?>" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold inline-block" title="Edit Lowongan">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Toggle Status Icon -->
                                        <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=toggle_status&id=<?php echo $j['id']; ?>" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-block" title="Ubah Status Open/Closed">
                                            <i class="fa-solid fa-power-off"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=delete&id=<?php echo $j['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus lowongan \'<?php echo addslashes($j['posisi']); ?>\'?')" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold inline-block" title="Hapus Loker">
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
