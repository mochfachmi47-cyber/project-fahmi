<?php
/**
 * FORSAKDA 27 - Dashboard Anggota (Santri / Alumni)
 */

$pageTitle = 'Dashboard Anggota - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$user = current_user();
$pdo = Database::getConnection();

// Recent news
$newsStmt = $pdo->prepare("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$newsStmt->execute();
$recentNews = $newsStmt->fetchAll();

// Recent jobs
$jobStmt = $pdo->prepare("SELECT * FROM job_vacancies WHERE status = 'open' ORDER BY created_at DESC LIMIT 3");
$jobStmt->execute();
$recentJobs = $jobStmt->fetchAll();

// Total my posted jobs
$myJobsCount = $pdo->prepare("SELECT COUNT(*) FROM job_vacancies WHERE user_id = :uid");
$myJobsCount->execute([':uid' => $user['id']]);
$totalMyJobs = $myJobsCount->fetchColumn();

// Active chat messages count
$chatCount = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <!-- Sidebar -->
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-teal-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-white/10 text-brand-200 border border-white/10 inline-block">
                    Keluarga Besar Santri & Alumni KDA 27
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    Ahlan wa Sahlan, <?php echo e($user['nama']); ?>!
                </h1>
                <p class="text-xs sm:text-sm text-brand-100 max-w-2xl leading-relaxed">
                    Selamat datang di portal komunitas FORSAKDA 27. Manfaatkan fitur ruang chat untuk menyapa sahabat, pantau warta terbaru pesantren, dan bagikan peluang lowongan pekerjaan.
                </p>
                <div class="pt-3 flex flex-wrap gap-3">
                    <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-comments"></i>
                        <span>Buka Ruang Chatting (Live)</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-circle-plus"></i>
                        <span>Pasang Info Loker Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Pesan di Ruang Chat</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($chatCount); ?></h3>
                    <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="text-[11px] font-bold text-brand-600 hover:underline">Gabung obrolan &rarr;</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950 text-amber-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Loker yang Anda Posting</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($totalMyJobs); ?></h3>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="text-[11px] font-bold text-amber-600 hover:underline">Kelola loker Anda &rarr;</a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-teal-50 dark:bg-teal-950 text-teal-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Status Keanggotaan</p>
                    <h3 class="text-base font-bold text-emerald-600 capitalize">Aktif &bull; Angkatan 27</h3>
                    <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="text-[11px] font-bold text-teal-600 hover:underline">Edit profil &rarr;</a>
                </div>
            </div>
        </div>

        <!-- 2 Column Section: News & Jobs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Left: Latest News -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-newspaper text-brand-600"></i> Berita & Warta Santri Terbaru
                    </h3>
                    <a href="<?php echo BASE_URL; ?>/views/member/berita.php" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    <?php foreach ($recentNews as $n): ?>
                        <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($n['slug']); ?>" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl hover:bg-brand-50 transition block">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-brand-100 text-brand-700"><?php echo e($n['kategori']); ?></span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 mt-1 line-clamp-1"><?php echo e($n['judul']); ?></h4>
                            <span class="text-[10px] text-slate-400 block mt-1"><?php echo format_date_id($n['created_at'], false); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Latest Jobs -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-amber-600"></i> Lowongan Kerja Santri Terbaru
                    </h3>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker.php" class="text-xs font-bold text-amber-600 hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    <?php foreach ($recentJobs as $j): ?>
                        <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $j['id']; ?>" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl hover:bg-amber-50 transition block">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200"><?php echo e($j['posisi']); ?></h4>
                                <span class="text-[10px] text-slate-500 font-semibold"><?php echo e($j['tipe_pekerjaan']); ?></span>
                            </div>
                            <p class="text-xs text-brand-600 font-medium mt-0.5"><?php echo e($j['perusahaan']); ?> &bull; <?php echo e($j['lokasi']); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
