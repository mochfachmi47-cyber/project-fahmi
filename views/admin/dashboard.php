<?php
/**
 * FORSAKDA 27 - Admin Dashboard Panel
 * Role Admin: Mengatur semuanya seperti CRUD pengguna, berita, loker, konten, dan log
 */

$pageTitle = 'Admin Dashboard - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$user = current_user();
$pdo = Database::getConnection();

// Metrics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalJobs = $pdo->query("SELECT COUNT(*) FROM job_vacancies")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Recent logs
$logsStmt = $pdo->prepare("SELECT l.*, u.nama FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 5");
$logsStmt->execute();
$recentLogs = $logsStmt->fetchAll();

// Pending Users List
$pendingStmt = $pdo->prepare("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
$pendingStmt->execute();
$pendingList = $pendingStmt->fetchAll();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Administrator Pusat
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Pusat Kendali FORSAKDA 27
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Kelola seluruh data sistem, anggota, berita, bursa kerja, dan konten publik.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?php echo BASE_URL; ?>/views/admin/berita-tambah.php" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Tulis Berita
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Buat Loker
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Total Pengguna</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($totalUsers); ?></h3>
                    <a href="<?php echo BASE_URL; ?>/views/admin/users.php" class="text-[11px] font-bold text-brand-600 hover:underline">Kelola &rarr;</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Menunggu Verifikasi</p>
                    <h3 class="text-2xl font-black <?php echo ($pendingUsers > 0) ? 'text-amber-600' : 'text-slate-900 dark:text-white'; ?>">
                        <?php echo e($pendingUsers); ?>
                    </h3>
                    <a href="<?php echo BASE_URL; ?>/views/admin/users.php?status=pending" class="text-[11px] font-bold text-amber-600 hover:underline">Tinjau antrean &rarr;</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Berita & Artikel</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($totalNews); ?></h3>
                    <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="text-[11px] font-bold text-teal-600 hover:underline">Kelola warta &rarr;</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Lowongan Kerja</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($totalJobs); ?></h3>
                    <a href="<?php echo BASE_URL; ?>/views/admin/loker.php" class="text-[11px] font-bold text-purple-600 hover:underline">Moderasi loker &rarr;</a>
                </div>
            </div>

        </div>

        <!-- 2 Column Section: Pending Users & Recent Activity Logs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Pending Users Review -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-clock text-amber-600"></i> Pendaftaran Anggota Baru
                    </h3>
                    <span class="text-xs font-bold text-slate-400"><?php echo count($pendingList); ?> Menunggu</span>
                </div>

                <?php if (empty($pendingList)): ?>
                    <p class="text-xs text-slate-400 py-6 text-center">Tidak ada pendaftaran yang sedang menunggu persetujuan.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pendingList as $p): ?>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($p['nama']); ?></h4>
                                    <p class="text-[10px] text-slate-400 font-mono"><?php echo e($p['email']); ?> &bull; <?php echo e($p['domisili'] ?: 'Domisili tidak diisi'); ?></p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a href="<?php echo BASE_URL; ?>/actions/user_process.php?action=approve&id=<?php echo $p['id']; ?>" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-[11px] transition">
                                        Setujui
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activity Logs -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-brand-600"></i> Log Aktivitas Terakhir
                    </h3>
                    <a href="<?php echo BASE_URL; ?>/views/admin/logs.php" class="text-xs font-bold text-brand-600 hover:underline">Semua Log</a>
                </div>

                <div class="space-y-3">
                    <?php foreach ($recentLogs as $log): ?>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl flex items-start justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-brand-700 dark:text-brand-400 uppercase text-[10px] bg-brand-100 px-1.5 py-0.5 rounded">
                                    <?php echo e($log['action']); ?>
                                </span>
                                <p class="text-xs text-slate-700 dark:text-slate-300 mt-1"><?php echo e($log['details']); ?></p>
                                <span class="text-[10px] text-slate-400">Oleh: <?php echo e($log['nama'] ?: 'Sistem/Tamu'); ?></span>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap"><?php echo time_ago($log['created_at']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
