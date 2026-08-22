<?php
/**
 * FORSAKDA 27 - Tujuan Pembuatan Website & Sejarah Organisasi
 */

$pageTitle = 'Tujuan & Sejarah - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT content FROM site_content WHERE key_name = 'tujuan_web' LIMIT 1");
$stmt->execute();
$data = $stmt->fetch();
$content = $data ? json_decode($data['content'], true) : [];

$tujuan = $content['tujuan'] ?? [
    'Pusat Informasi Terpadu: Menyajikan warta resmi almamater, kegiatan santri, dan pengumuman keluarga besar FORSAKDA 27.',
    'Jejaring Komunikasi Interaktif: Menyediakan ruang chatting dan forum diskusi aman sesama anggota santri kelas dua.',
    'Pusat Karir & Info Loker Santri: Memfasilitasi anggota dalam membagikan lowongan pekerjaan dan peluang usaha halal.',
    'Direktori Santri & Kontak: Mendata kontak, profesi, dan domisili santri kelas dua untuk kemudahan koordinasi.'
];
$sejarah = $content['sejarah'] ?? 'FORSAKDA 27 (Forum Santri Kelas Dua Angkatan 27) dibentuk sebagai wadah kebersamaan dan persaudaraan santri kelas dua angkatan 27 dalam menimba ilmu, mengasah potensi, dan merajut masa depan yang gemilang. Website ini dirancang sebagai jembatan digital agar komunikasi dan silaturahmi antar santri selalu terjalin erat.';
$sambutan = $content['sambutan_ketua'] ?? 'Alhamdulillah, puji syukur kehadirat Allah SWT. Platform digital FORSAKDA 27 (Forum Santri Kelas Dua 27) hadir sebagai sarana silaturahmi, belajar bersama, bertukar informasi warta, serta saling mendukung dalam karir dan karya. Mari kita jaga ukhuwah ini dengan sebaik-baiknya.';
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Header Banner -->
        <div class="text-center space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                Sejarah & Tujuan Digitalisasi
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white">
                Tujuan Pembuatan Website & Sejarah 27
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
                Mengenal latar belakang berdirinya FORSAKDA 27 (Forum Santri Kelas Dua) serta tujuan strategis platform digital ini dibangun.
            </p>
        </div>

        <!-- Sejarah Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-6">
                <div class="w-14 h-14 bg-brand-50 dark:bg-brand-950 text-brand-600 rounded-2xl flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-landmark"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kilas Balik</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Sejarah FORSAKDA Angkatan 27</h2>
                </div>
            </div>

            <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                <p><?php echo nl2br(e($sejarah)); ?></p>
            </div>
        </div>

        <!-- Tujuan Pembuatan Web -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-teal-100 text-teal-700 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-600">Transformasi Digital</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tujuan Pembuatan Website</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($tujuan as $idx => $t): ?>
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-start gap-4">
                        <span class="w-9 h-9 rounded-2xl bg-teal-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                            <?php echo e($t); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sambutan Ketua -->
        <div class="bg-gradient-to-br from-slate-900 to-brand-950 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-emerald-500/30">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-6">
                <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0">
                    <i class="fa-solid fa-quote-left"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Sambutan Ketua FORSAKDA 27</h3>
                    <p class="text-xs text-brand-300">Pesan Ukhuwah untuk Seluruh Sahabat Santri Kelas Dua 27</p>
                </div>
            </div>
            <p class="text-slate-300 italic text-sm sm:text-base leading-relaxed">
                "<?php echo e($sambutan); ?>"
            </p>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
