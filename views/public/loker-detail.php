<?php
/**
 * FORSAKDA 27 - Detail Lowongan Kerja & Rincian Karir
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../config/auth.php';

$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT j.*, u.nama as pemosting_nama, u.foto as pemosting_foto, u.email as pemosting_email, u.angkatan as pemosting_angkatan
    FROM job_vacancies j
    JOIN users u ON j.user_id = u.id
    WHERE j.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$job = $stmt->fetch();

if (!$job) {
    set_flash('error', 'Info lowongan kerja tidak ditemukan.');
    header('Location: ' . (is_logged_in() ? BASE_URL . '/views/member/loker.php' : BASE_URL . '/index.php'));
    exit;
}

$pageTitle = $job['posisi'] . ' di ' . $job['perusahaan'] . ' - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

// Other recent jobs
$otherJobsStmt = $pdo->prepare("SELECT * FROM job_vacancies WHERE id != :id AND status = 'open' ORDER BY created_at DESC LIMIT 3");
$otherJobsStmt->execute([':id' => $id]);
$otherJobs = $otherJobsStmt->fetchAll();

// Clean phone/wa link if present in contact
$rawContact = $job['kontak_lamaran'];
$waLink = '';
if (preg_match('/(08\d{8,12}|628\d{8,12})/', $rawContact, $matches)) {
    $waNum = $matches[0];
    if (strpos($waNum, '08') === 0) {
        $waNum = '62' . substr($waNum, 1);
    }
    $waMsg = urlencode("Assalamu’alaikum, saya tertarik melamar posisi " . $job['posisi'] . " di " . $job['perusahaan'] . " yang diinfokan melalui website FORSAKDA 27.");
    $waLink = "https://api.whatsapp.com/send?phone=" . $waNum . "&text=" . $waMsg;
}
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-amber-600">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?php echo is_logged_in() ? BASE_URL . '/views/member/loker.php' : BASE_URL . '/views/public/loker.php'; ?>" class="hover:text-amber-600">Bursa Loker</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 dark:text-slate-300 truncate max-w-xs"><?php echo e($job['posisi']); ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Main Detail (Col 8) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Main Header Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-6">
                        <div class="flex items-center gap-4">
                            <?php if (!empty($job['logo_perusahaan'])): ?>
                                <img src="<?php echo BASE_URL . '/' . e($job['logo_perusahaan']); ?>" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-sm">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/80 text-amber-600 flex items-center justify-center text-3xl border border-amber-200 dark:border-amber-900/50 shadow-sm">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white"><?php echo e($job['posisi']); ?></h1>
                                <p class="text-sm font-bold text-brand-600"><?php echo e($job['perusahaan']); ?></p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                <?php echo e($job['tipe_pekerjaan']); ?>
                            </span>
                            <?php if ($job['status'] === 'open'): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Dibuka
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i> Ditutup
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Meta details badge row -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Lokasi Kerja</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-location-dot text-amber-500"></i> <?php echo e($job['lokasi']); ?>
                            </span>
                        </div>
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Estimasi Gaji</span>
                            <span class="text-xs font-black text-emerald-600 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-money-bill-wave"></i> <?php echo format_rupiah($job['gaji_min']); ?>
                                <?php if ($job['gaji_max'] > $job['gaji_min']): ?>
                                    - <?php echo format_rupiah($job['gaji_max']); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block">Tanggal Rilis</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-calendar-check text-brand-500"></i> <?php echo format_date_id($job['created_at'], false); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider text-amber-600 flex items-center gap-2">
                            <i class="fa-solid fa-file-lines"></i> Deskripsi Pekerjaan
                        </h3>
                        <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-slate-50/70 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <?php echo e($job['deskripsi']); ?>
                        </div>
                    </div>

                    <!-- Qualifications -->
                    <?php if (!empty($job['kualifikasi'])): ?>
                        <div class="space-y-3">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider text-teal-600 flex items-center gap-2">
                                <i class="fa-solid fa-list-check"></i> Kualifikasi & Persyaratan
                            </h3>
                            <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-slate-50/70 dark:bg-slate-800/40 p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <?php echo e($job['kualifikasi']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Apply Buttons -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500">
                            Bagikan info loker ini:
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($job['posisi'] . ' di ' . $job['perusahaan'] . ' - ' . BASE_URL . '/views/public/loker-detail.php?id=' . $job['id']); ?>" target="_blank" class="inline-flex items-center gap-1 font-bold text-emerald-600 hover:underline ml-2">
                                <i class="fa-brands fa-whatsapp text-sm"></i> Kirim ke Sahabat Santri
                            </a>
                        </div>

                        <?php if (has_role('admin') || (is_logged_in() && (int)$user['id'] === (int)$job['user_id'])): ?>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo BASE_URL; ?>/views/member/loker-edit.php?id=<?php echo $job['id']; ?>" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Loker
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <!-- Right: Contact & Apply Sidebar (Col 4) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Contact Lamaran Card -->
                <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-teal-500/10 border border-amber-500/30 rounded-3xl p-6 sm:p-7 shadow-sm space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-2xl font-bold shadow-md shadow-amber-500/30">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Cara Melamar</h3>
                            <p class="text-xs text-slate-400">Kontak resmi rekrutmen</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white/80 dark:bg-slate-900/80 rounded-2xl border border-amber-200/60 dark:border-amber-900/40 text-xs text-slate-800 dark:text-slate-200 font-medium space-y-2">
                        <p class="text-[11px] text-slate-400 font-bold uppercase">Kontak / Instruksi Lamaran:</p>
                        <p class="font-mono text-xs select-all break-words font-semibold text-slate-900 dark:text-white"><?php echo e($job['kontak_lamaran']); ?></p>
                    </div>

                    <?php if (!empty($waLink)): ?>
                        <a href="<?php echo $waLink; ?>" target="_blank" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl text-xs text-center transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Hubungi Rekrutmen via WhatsApp</span>
                        </a>
                    <?php endif; ?>

                    <!-- Poster Info -->
                    <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800 flex items-center gap-3">
                        <img src="<?php echo !empty($job['pemosting_foto']) ? BASE_URL . '/' . e($job['pemosting_foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($job['pemosting_nama']) . '&background=059669&color=fff'; ?>" 
                             class="w-10 h-10 rounded-xl object-cover border border-emerald-500/30">
                        <div>
                            <span class="text-[10px] text-slate-400">Diverifikasi & Diposting Oleh:</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200"><?php echo e($job['pemosting_nama']); ?></p>
                            <span class="text-[10px] text-brand-600 font-semibold">Alumni Angkatan <?php echo e($job['pemosting_angkatan']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Other Open Jobs -->
                <?php if (!empty($otherJobs)): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Lowongan Terbuka Lainnya</h4>
                        <div class="space-y-3">
                            <?php foreach ($otherJobs as $oj): ?>
                                <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $oj['id']; ?>" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl hover:bg-amber-50 transition block">
                                    <h5 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1"><?php echo e($oj['posisi']); ?></h5>
                                    <p class="text-[11px] text-brand-600 font-semibold mt-0.5"><?php echo e($oj['perusahaan']); ?></p>
                                    <span class="text-[10px] text-slate-400 mt-1 block"><?php echo e($oj['lokasi']); ?> &bull; <?php echo format_rupiah($oj['gaji_min']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
