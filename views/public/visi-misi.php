<?php
/**
 * FORSAKDA 27 - Halaman Visi & Misi Organisasi
 */

$pageTitle = 'Visi & Misi - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT content FROM site_content WHERE key_name = 'visi_misi' LIMIT 1");
$stmt->execute();
$data = $stmt->fetch();
$content = $data ? json_decode($data['content'], true) : [];

$visi = $content['visi'] ?? 'Menjadi wadah silaturahmi Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27) yang solid, mandiri, berakhlakul karimah, serta berdaya saing tinggi untuk kemajuan umat dan bangsa.';
$misi = $content['misi'] ?? [
    'Mempererat tali ukhuwah islamiyah dan kekeluargaan antar Forum Santri Kelas Dua Angkatan 27.',
    'Membangun sinergi belajar, wirausaha, karir, dan dakwah melalui jejaring santri dan bursa lowongan kerja.',
    'Mewadahi pertukaran ilmu dan pengalaman santri melalui ruang diskusi chatting dan portal digital modern.',
    'Melestarikan nilai-nilai kepesantrenan dan memberikan sumbangsih nyata bagi pondok serta masyarakat.'
];
$nilaiUtama = $content['nilai_utama'] ?? [
    'Ukhuwah Islamiyah (Persaudaraan yang tulus)',
    'Integritas & Adab Santri (Menjaga kejujuran dan akhlak)',
    'Kemandirian Santri (Berdikari dalam karya dan ikhtiar)',
    'Khidmah Lil Ummah (Pengabdian untuk masyarakat)'
];
?>

<main class="py-12 bg-slate-50 dark:bg-slate-950 flex-1">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Header Banner -->
        <div class="text-center space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                Landasan Idealisme & Gerak
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white">
                Visi & Misi FORSAKDA 27
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
                Panduan kompas organisasi Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27) dalam berkarya dan berkhidmah.
            </p>
        </div>

        <!-- Visi Card -->
        <div class="bg-gradient-to-br from-brand-900 via-brand-800 to-teal-900 text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3 mb-6">
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-3xl text-amber-300 shadow-inner">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-200">Kompas Utama</span>
                    <h2 class="text-2xl font-extrabold text-white">VISI KAMI</h2>
                </div>
            </div>

            <blockquote class="text-xl sm:text-3xl font-semibold leading-snug text-emerald-50 italic">
                “<?php echo e($visi); ?>”
            </blockquote>
        </div>

        <!-- Misi Grid -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-600">Eksekusi Program</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">MISI STRATEGIS</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($misi as $idx => $m): ?>
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-start gap-4">
                        <span class="w-10 h-10 rounded-2xl bg-brand-600 text-white font-bold text-base flex items-center justify-center flex-shrink-0 shadow-md shadow-brand-600/30">
                            <?php echo $idx + 1; ?>
                        </span>
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                            <?php echo e($m); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nilai-nilai Dasar -->
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-8 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-500/20 text-amber-600 rounded-2xl flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Karakter Santri</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Nilai-Nilai Utama (Core Values)</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($nilaiUtama as $nilai): ?>
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-amber-200 dark:border-amber-900/40 shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-sm mb-3">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                            <?php echo e($nilai); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
