<?php
/**
 * FORSAKDA 27 - Beranda Utama (Landing Page Publik)
 */

$pageTitle = 'FORSAKDA 27 - Forum Santri Kelas Dua 27';
require_once __DIR__ . '/includes/header.php';

$pdo = Database::getConnection();

// Ambil Berita Terbaru (Max 3)
$newsStmt = $pdo->prepare("SELECT * FROM news WHERE status = 'published' ORDER BY is_pinned DESC, created_at DESC LIMIT 3");
$newsStmt->execute();
$latestNews = $newsStmt->fetchAll();

// Ambil Loker Terbaru (Max 3)
$jobStmt = $pdo->prepare("SELECT * FROM job_vacancies WHERE status = 'open' ORDER BY created_at DESC LIMIT 3");
$jobStmt->execute();
$latestJobs = $jobStmt->fetchAll();

// Ambil Galeri Terbaru (Max 4)
$galStmt = $pdo->prepare("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 4");
$galStmt->execute();
$latestGallery = $galStmt->fetchAll();

// Ambil Konten Visi Misi & Tujuan
$contentStmt = $pdo->prepare("SELECT * FROM site_content WHERE key_name IN ('visi_misi', 'tujuan_web')");
$contentStmt->execute();
$contents = [];
while ($row = $contentStmt->fetch()) {
    $contents[$row['key_name']] = json_decode($row['content'], true);
}

$visiMisi = $contents['visi_misi'] ?? [
    'visi' => 'Menjadi wadah silaturahmi Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27) yang solid, mandiri, dan berakhlakul karimah.',
    'misi' => ['Mempererat ukhuwah islamiyah santri kelas dua.', 'Membangun sinergi belajar dan bursa lowongan kerja.', 'Mewadahi ruang diskusi chatting santri.'],
    'nilai_utama' => ['Ukhuwah', 'Amanah', 'Kemandirian', 'Khidmah']
];

$tujuanData = $contents['tujuan_web'] ?? [
    'tujuan' => ['Pusat Informasi Terpadu', 'Jejaring Komunikasi Interaktif', 'Pusat Karir & Loker Santri'],
    'sejarah' => 'FORSAKDA 27 adalah wadah Forum Santri Kelas Dua Angkatan 27 dalam menimba ilmu dan mempererat tali persaudaraan.'
];

// Stats
$totalMembers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$totalJobs = $pdo->query("SELECT COUNT(*) FROM job_vacancies WHERE status = 'open'")->fetchColumn();
$totalNews = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn();
?>

<!-- ================= HERO SECTION ================= -->
<section class="relative bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 text-white overflow-hidden py-20 lg:py-28">
    <!-- Geometric Background Texture -->
    <div class="absolute inset-0 bg-islamic-pattern opacity-20 pointer-events-none"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-teal-400/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-500/20 border border-brand-400/30 text-brand-300 text-xs font-bold tracking-wide uppercase">
                    <i class="fa-solid fa-star-and-crescent text-amber-400"></i>
                    <span>Portal Resmi Forum Santri Kelas Dua 27</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    Membangun <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">Ukhuwah</span>, Mengabdi untuk Ummah.
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                    Selamat datang di website resmi <strong>FORSAKDA 27</strong> (Forum Santri Kelas Dua Angkatan 27). Wadah terintegrasi untuk menyambung silaturahmi, ruang chatting santri, warta kabar pondok, serta info lowongan kerja bagi seluruh anggota.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <?php if (!is_logged_in()): ?>
                        <a href="<?php echo BASE_URL; ?>/views/public/register.php" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white font-extrabold rounded-2xl shadow-xl shadow-brand-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-3">
                            <i class="fa-solid fa-user-plus text-lg"></i>
                            <span>Gabung Menjadi Anggota</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/views/public/login.php" class="w-full sm:w-auto px-8 py-4 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-white font-bold rounded-2xl transition flex items-center justify-center gap-3 backdrop-blur-sm">
                            <i class="fa-solid fa-right-to-bracket text-brand-400"></i>
                            <span>Masuk ke Akun</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo has_role('admin') ? BASE_URL . '/views/admin/dashboard.php' : BASE_URL . '/views/member/dashboard.php'; ?>" class="w-full sm:w-auto px-8 py-4 bg-brand-600 hover:bg-brand-500 text-white font-extrabold rounded-2xl shadow-xl transition flex items-center justify-center gap-3">
                            <i class="fa-solid fa-gauge text-lg"></i>
                            <span>Buka Dashboard Anda</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="w-full sm:w-auto px-8 py-4 bg-slate-800 border border-brand-500/40 text-brand-300 hover:text-white font-bold rounded-2xl transition flex items-center justify-center gap-3">
                            <i class="fa-solid fa-comments text-emerald-400"></i>
                            <span>Ruang Chat Santri (Live)</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Stats counters -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800/80 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-brand-400"><?php echo e($totalMembers); ?>+</p>
                        <p class="text-xs text-slate-400 font-medium">Santri & Alumni</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-amber-400"><?php echo e($totalJobs); ?></p>
                        <p class="text-xs text-slate-400 font-medium">Loker Tersedia</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-extrabold text-teal-400"><?php echo e($totalNews); ?></p>
                        <p class="text-xs text-slate-400 font-medium">Kabar Berita</p>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Cards -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-gradient-to-br from-slate-800/90 to-brand-950/90 border border-emerald-500/30 rounded-3xl p-6 shadow-2xl backdrop-blur-xl relative overflow-hidden">
                    <div class="w-12 h-12 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl mb-4">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1">Ruang Chatting Sesama Santri</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-3">
                        Kanal komunikasi real-time khusus anggota FORSAKDA 27 untuk saling menyapa, berbagi kabar, dan merawat persaudaraan santri.
                    </p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Fitur Khusus Role Anggota
                    </span>
                </div>

                <div class="bg-gradient-to-br from-slate-800/90 to-amber-950/80 border border-amber-500/30 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
                    <div class="w-12 h-12 bg-amber-500/20 text-amber-400 rounded-2xl flex items-center justify-center text-2xl mb-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1">Bursa Lowongan Kerja Santri</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-3">
                        Setiap anggota dapat mengunggah peluang karir dan mencari info lowongan pekerjaan terpercaya dari sesama jaringan alumni.
                    </p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-400">
                        <i class="fa-solid fa-check-circle"></i> Sinergi Ekonomi Umat
                    </span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= VISI & MISI SECTION ================= -->
<section class="py-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Landasan Perjuangan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mt-3">
                Visi & Misi FORSAKDA 27
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-3">
                Arah gerak dan komitmen bersama dalam menjaga marwah kepesantrenan dan memberikan kemanfaatan luas.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            
            <!-- Visi Card -->
            <div class="lg:col-span-5 bg-gradient-to-br from-brand-900 to-brand-950 text-white rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10 text-8xl">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-brand-300 text-2xl mb-6">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-300">Visi Utama</span>
                    <h3 class="text-xl sm:text-2xl font-bold mt-2 leading-relaxed">
                        "<?php echo e($visiMisi['visi'] ?? ''); ?>"
                    </h3>
                </div>

                <div class="pt-8 border-t border-brand-800/80 mt-6">
                    <a href="<?php echo BASE_URL; ?>/views/public/visi-misi.php" class="inline-flex items-center gap-2 text-xs font-bold text-amber-300 hover:text-amber-200 transition">
                        <span>Lihat Rincian Visi & Misi Lengkap</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Misi Card -->
            <div class="lg:col-span-7 bg-slate-50 dark:bg-slate-800/60 rounded-3xl p-8 border border-slate-200 dark:border-slate-700/60 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Misi Organisasi</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Langkah Nyata FORSAKDA 27</h3>
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        <?php 
                        $misiList = $visiMisi['misi'] ?? [];
                        foreach ($misiList as $idx => $misi): 
                        ?>
                            <div class="flex items-start gap-3 p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                                <span class="w-7 h-7 rounded-xl bg-brand-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <?php echo $idx + 1; ?>
                                </span>
                                <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                    <?php echo e($misi); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================= TUJUAN PEMBUATAN WEB & SEJARAH ================= -->
<section class="py-20 bg-slate-50 dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Latar Belakang & Tujuan
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                    Mengapa Website FORSAKDA 27 Diciptakan?
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <?php echo e($tujuanData['sejarah'] ?? 'FORSAKDA 27 dibentuk sebagai jembatan persaudaraan Forum Santri Kelas Dua Angkatan 27.'); ?>
                </p>

                <div class="space-y-3 pt-2">
                    <?php 
                    $tujuanList = $tujuanData['tujuan'] ?? [];
                    foreach ($tujuanList as $tujuan): 
                    ?>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xs mt-0.5 flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                                <?php echo e($tujuan); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pt-4">
                    <a href="<?php echo BASE_URL; ?>/views/public/tujuan.php" class="px-6 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl text-xs transition inline-flex items-center gap-2 shadow-md">
                        <span>Baca Sejarah Lengkap & Sambutan Ketua</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Interactive Illustration / Card Grid -->
            <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                    <div class="w-10 h-10 bg-brand-100 text-brand-700 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Keamanan Terstandar</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Dilengkapi proteksi CSRF, Anti-XSS, Enkripsi Password Bcrypt, dan PDO anti SQL Injection.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3 mt-6">
                    <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">3 Role Pengguna</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Hak akses terkelola rapi untuk Admin, Anggota Santri/Alumni, serta Pengunjung Publik.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                    <div class="w-10 h-10 bg-teal-100 text-teal-700 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Chatting Santri</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Ruang obrolan interaktif real-time sesama anggota angkatan dengan notifikasi suara.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3 mt-6">
                    <div class="w-10 h-10 bg-purple-100 text-purple-700 rounded-xl flex items-center justify-center text-lg">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Bursa Kerja Loker</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Kemudahan membagikan info lowongan pekerjaan dan menjangkau talenta sesama santri.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================= BERITA TERKINI ================= -->
<section class="py-20 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Informasi & Warta
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-3">
                    Berita Santri Terbaru
                </h2>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="mt-4 md:mt-0 text-sm font-bold text-brand-600 hover:text-brand-700 flex items-center gap-2">
                <span>Lihat Semua Berita</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (empty($latestNews)): ?>
                <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
                    Belum ada berita yang dipublikasikan.
                </div>
            <?php else: ?>
                <?php foreach ($latestNews as $news): ?>
                    <article class="bg-slate-50 dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 flex flex-col justify-between hover:shadow-lg transition">
                        <div>
                            <div class="h-48 bg-gradient-to-tr from-brand-900 to-teal-800 relative overflow-hidden flex items-center justify-center">
                                <?php if (!empty($news['gambar'])): ?>
                                    <img src="<?php echo BASE_URL . '/' . e($news['gambar']); ?>" alt="<?php echo e($news['judul']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fa-solid fa-newspaper text-white/30 text-5xl"></i>
                                <?php endif; ?>
                                <span class="absolute top-4 left-4 bg-brand-600 text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full shadow">
                                    <?php echo e($news['kategori']); ?>
                                </span>
                            </div>

                            <div class="p-6">
                                <span class="text-[11px] text-slate-400 font-medium">
                                    <i class="fa-solid fa-calendar-days mr-1"></i> <?php echo format_date_id($news['created_at'], false); ?>
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-2 line-clamp-2 hover:text-brand-600 transition">
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
                            <a href="<?php echo BASE_URL; ?>/views/public/berita-detail.php?slug=<?php echo urlencode($news['slug']); ?>" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                                <span>Baca Selengkapnya</span>
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ================= BURSA LOWONGAN KERJA (LOKER) ================= -->
<section class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Bursa Kerja & Karir
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-3">
                    Lowongan Kerja Santri Terkini
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1">
                    Peluang karir halal dari jaringan keluarga besar FORSAKDA 27.
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/public/loker.php" class="mt-4 md:mt-0 text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center gap-2">
                <span>Lihat Semua Loker</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if (empty($latestJobs)): ?>
                <div class="col-span-3 text-center py-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    Belum ada info lowongan kerja terbaru.
                </div>
            <?php else: ?>
                <?php foreach ($latestJobs as $job): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-xl transition group">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between">
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
                                    <?php echo e($job['tipe_pekerjaan']); ?>
                                </span>
                                <span class="text-xs font-black text-emerald-600"><?php echo format_rupiah($job['gaji_min']); ?></span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug group-hover:text-amber-600 transition">
                                <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $job['id']; ?>">
                                    <?php echo e($job['posisi']); ?>
                                </a>
                            </h3>
                            <p class="text-xs font-semibold text-brand-600"><?php echo e($job['perusahaan']); ?></p>
                            <p class="text-xs text-slate-400"><i class="fa-solid fa-location-dot mr-1"></i> <?php echo e($job['lokasi']); ?></p>
                        </div>

                        <div class="pt-5 mt-4 border-t border-slate-100 dark:border-slate-800">
                            <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $job['id']; ?>" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold rounded-xl text-xs text-center transition block">
                                Rincian &amp; Lamar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ================= CALL TO ACTION (CTA) ================= -->
<section class="py-20 bg-gradient-to-r from-brand-900 via-brand-800 to-teal-900 text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-3xl mx-auto text-amber-300 shadow-inner">
            <i class="fa-solid fa-hands-holding-child"></i>
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
            Apakah Anda Bagian dari Forum Santri Kelas Dua Angkatan 27?
        </h2>
        <p class="text-sm sm:text-base text-brand-100 max-w-2xl mx-auto leading-relaxed">
            Mari bersama-sama mempererat tali silaturahmi, berkolaborasi dalam karya, dan berkontribusi nyata untuk masa depan umat. Daftarkan diri Anda sekarang juga!
        </p>
        <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?php echo BASE_URL; ?>/views/public/register.php" class="px-8 py-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold rounded-2xl shadow-xl transition transform hover:scale-105">
                Daftar Sebagai Anggota Sekarang
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/login.php" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition border border-white/20">
                Masuk ke Ruang Santri
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
