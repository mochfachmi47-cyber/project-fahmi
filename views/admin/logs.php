<?php
/**
 * FORSAKDA 27 - Log Aktivitas & Keamanan Sistem (Admin)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/auth.php';

require_role(['admin']);
$pdo = Database::getConnection();
$user = current_user();

// Handle Purge/Clear Logs Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_logs') {
    if (verify_csrf()) {
        try {
            $pdo->exec("TRUNCATE TABLE activity_logs");
            log_activity($user['id'], 'CLEAR_LOGS', 'Admin membersihkan seluruh catatan log aktivitas.');
            set_flash('success', 'Seluruh riwayat log aktivitas berhasil dibersihkan.');
        } catch (Exception $e) {
            set_flash('error', 'Gagal membersihkan log: ' . $e->getMessage());
        }
    } else {
        set_flash('error', 'Token CSRF tidak valid.');
    }
    header('Location: ' . BASE_URL . '/views/admin/logs.php');
    exit;
}

$q = sanitize_input($_GET['q'] ?? '');
$actFilter = sanitize_input($_GET['act'] ?? '');

$sql = "
    SELECT l.*, u.nama, u.email, u.role
    FROM activity_logs l
    LEFT JOIN users u ON l.user_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($q)) {
    $sql .= " AND (l.action LIKE :q OR l.details LIKE :q OR l.ip_address LIKE :q OR u.nama LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($actFilter) && $actFilter !== 'all') {
    $sql .= " AND l.action = :act";
    $params[':act'] = $actFilter;
}

$sql .= " ORDER BY l.created_at DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Distinct actions for filter
$actionsList = $pdo->query("SELECT DISTINCT action FROM activity_logs ORDER BY action ASC")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Log Aktivitas & Keamanan - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    Audit Trail & Security
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Riwayat Log Aktivitas & Keamanan
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Catatan aktivitas login, pendaftaran, modifikasi data, dan event sistem untuk audit transparansi.
                </p>
            </div>

            <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/mengosongkan seluruh riwayat log aktivitas?')" class="self-start sm:self-auto">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="clear_logs">
                <button type="submit" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold rounded-2xl transition flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Bersihkan Semua Log</span>
                </button>
            </form>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari aktivitas, nama, IP..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                </div>

                <select name="act" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Jenis Aksi</option>
                    <?php foreach ($actionsList as $a): ?>
                        <option value="<?php echo e($a); ?>" <?php echo ($actFilter === $a) ? 'selected' : ''; ?>><?php echo e($a); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Filter
                </button>
                <a href="<?php echo BASE_URL; ?>/views/admin/logs.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
            </form>

            <span class="text-xs font-bold text-slate-400">Menampilkan <?php echo count($logs); ?> entri terbaru</span>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Waktu</th>
                            <th class="p-4">Pengguna</th>
                            <th class="p-4">Aksi / Event</th>
                            <th class="p-4">Rincian Aktivitas</th>
                            <th class="p-4 pr-6 text-right">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400 font-sans">Belum ada riwayat aktivitas yang tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-4 pl-6 text-slate-400 whitespace-nowrap font-mono text-[11px]">
                                        <?php echo format_date_id($log['created_at'], true); ?>
                                    </td>
                                    <td class="p-4 font-sans">
                                        <?php if (!empty($log['nama'])): ?>
                                            <p class="font-bold text-slate-800 dark:text-slate-200"><?php echo e($log['nama']); ?></p>
                                            <span class="text-[10px] text-brand-600 font-bold uppercase"><?php echo e($log['role']); ?></span>
                                        <?php else: ?>
                                            <span class="text-slate-400 italic font-sans">Tamu / Sistem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            <?php echo e($log['action']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-700 dark:text-slate-300 font-sans">
                                        <?php echo e($log['details']); ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right text-slate-400 font-mono text-[11px]">
                                        <?php echo e($log['ip_address'] ?: '127.0.0.1'); ?>
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
