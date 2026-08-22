<?php
/**
 * FORSAKDA 27 - Bursa Loker Santri (Member View)
 */

$pageTitle = 'Bursa Loker Santri - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$tipe = sanitize_input($_GET['tipe'] ?? '');

$sql = "SELECT j.*, u.nama as pemosting_nama, u.angkatan as pemosting_angkatan 
        FROM job_vacancies j
        JOIN users u ON j.user_id = u.id
        WHERE j.status = 'open'";
$params = [];

if (!empty($q)) {
    $sql .= " AND (j.posisi LIKE :q OR j.perusahaan LIKE :q OR j.lokasi LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($tipe) && $tipe !== 'Semua') {
    $sql .= " AND j.tipe_pekerjaan = :tipe";
    $params[':tipe'] = $tipe;
}

$sql .= " ORDER BY j.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$tipeList = ['Semua', 'Full-time', 'Part-time', 'Freelance', 'Internship/Magang', 'Remote'];
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Bursa Kerja & Karir
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Bursa Lowongan Kerja Santri
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Peluang karir terpercaya yang dibagikan oleh rekan-rekan santri dan alumni KDA 27.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 transition">
                    Loker Saya
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-extrabold rounded-2xl shadow-md text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Pasang Loker Baru</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <?php foreach ($tipeList as $t): ?>
                    <?php 
                    $isActive = ($tipe === $t) || (empty($tipe) && $t === 'Semua');
                    $linkUrl = ($t === 'Semua') ? BASE_URL . '/views/member/loker.php' : BASE_URL . '/views/member/loker.php?tipe=' . urlencode($t);
                    ?>
                    <a href="<?php echo $linkUrl; ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition <?php echo $isActive ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-50'; ?>">
                        <?php echo e($t); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="GET" action="" class="w-full md:w-72 flex items-center gap-2">
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari posisi, kota..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-amber-500 text-slate-800 dark:text-slate-200">
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold hover:bg-amber-500 transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Job Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($jobs)): ?>
                <div class="col-span-3 text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    <p>Tidak ada info lowongan kerja ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-xl transition">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between">
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
                                    <?php echo e($job['tipe_pekerjaan']); ?>
                                </span>
                                <span class="text-xs font-black text-emerald-600"><?php echo format_rupiah($job['gaji_min']); ?></span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                                <?php echo e($job['posisi']); ?>
                            </h3>
                            <p class="text-xs font-semibold text-brand-600"><?php echo e($job['perusahaan']); ?></p>
                            <p class="text-xs text-slate-400"><i class="fa-solid fa-location-dot mr-1"></i> <?php echo e($job['lokasi']); ?></p>
                        </div>

                        <div class="pt-5 mt-4 border-t border-slate-100 dark:border-slate-800">
                            <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $job['id']; ?>" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold rounded-xl text-xs text-center transition block">
                                Rincian & Lamar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
