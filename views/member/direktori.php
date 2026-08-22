<?php
/**
 * FORSAKDA 27 - Direktori Santri & Kontak Alumni
 */

$pageTitle = 'Direktori Santri & Alumni - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$domisiliFilter = sanitize_input($_GET['domisili'] ?? '');

$sql = "SELECT id, nama, email, angkatan, no_hp, domisili, profesi, bio, foto, created_at 
        FROM users 
        WHERE status = 'active'";
$params = [];

if (!empty($q)) {
    $sql .= " AND (nama LIKE :q OR profesi LIKE :q OR bio LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($domisiliFilter) && $domisiliFilter !== 'all') {
    $sql .= " AND domisili LIKE :dom";
    $params[':dom'] = "%$domisiliFilter%";
}

$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

// Distinct domisili list
$domisiliList = $pdo->query("SELECT DISTINCT domisili FROM users WHERE status = 'active' AND domisili IS NOT NULL AND domisili != '' ORDER BY domisili ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                    Jejaring Ukhuwah
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Direktori Santri & Kontak Alumni
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Temukan dan sambung silaturahmi dengan sesama sahabat santri kelas dua angkatan 27 di berbagai penjuru kota.
                </p>
            </div>
            
            <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-2xl transition">
                <i class="fa-solid fa-user-pen mr-1"></i> Lengkapi Profil Saya
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari nama santri, profesi..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                </div>

                <select name="domisili" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Domisili</option>
                    <?php foreach ($domisiliList as $d): ?>
                        <option value="<?php echo e($d); ?>" <?php echo ($domisiliFilter === $d) ? 'selected' : ''; ?>><?php echo e($d); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Cari
                </button>
                <a href="<?php echo BASE_URL; ?>/views/member/direktori.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
            </form>

            <span class="text-xs font-bold text-slate-400"><?php echo count($members); ?> Anggota Santri Terdata</span>
        </div>

        <!-- Members Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($members)): ?>
                <div class="col-span-3 text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-400 text-sm">
                    <i class="fa-solid fa-users-slash text-4xl mb-3 text-slate-300"></i>
                    <p>Tidak ada data santri yang cocok dengan kriteria pencarian.</p>
                </div>
            <?php else: ?>
                <?php foreach ($members as $m): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-xl transition group">
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <img src="<?php echo !empty($m['foto']) ? BASE_URL . '/' . e($m['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['nama']) . '&background=059669&color=fff'; ?>" 
                                     alt="<?php echo e($m['nama']); ?>" 
                                     class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-500/30 group-hover:scale-105 transition shadow-sm">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white"><?php echo e($m['nama']); ?></h3>
                                    <span class="text-[11px] font-semibold text-brand-600">Angkatan <?php echo e($m['angkatan']); ?></span>
                                </div>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                                <?php if (!empty($m['profesi'])): ?>
                                    <p class="flex items-center gap-2">
                                        <i class="fa-solid fa-briefcase text-slate-400 w-4"></i>
                                        <span><?php echo e($m['profesi']); ?></span>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($m['domisili'])): ?>
                                    <p class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-amber-500 w-4"></i>
                                        <span><?php echo e($m['domisili']); ?></span>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($m['bio'])): ?>
                                    <p class="text-[11px] text-slate-400 italic pt-1 line-clamp-2">
                                        "<?php echo e($m['bio']); ?>"
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            <?php if (!empty($m['no_hp'])): ?>
                                <?php 
                                $cleanPhone = preg_replace('/[^0-9]/', '', $m['no_hp']);
                                if (strpos($cleanPhone, '08') === 0) $cleanPhone = '62' . substr($cleanPhone, 1);
                                ?>
                                <a href="https://api.whatsapp.com/send?phone=<?php echo $cleanPhone; ?>&text=<?php echo urlencode('Assalamu’alaikum, salam silaturahmi sahabat FORSAKDA 27!'); ?>" target="_blank" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                                </a>
                            <?php else: ?>
                                <span class="text-[11px] text-slate-400 italic">Kontak belum diisi</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
