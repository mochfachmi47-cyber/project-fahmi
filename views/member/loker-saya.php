<?php
/**
 * FORSAKDA 27 - Kelola Lowongan Kerja Saya (Role Anggota)
 */

$pageTitle = 'Loker Saya - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$user = current_user();
$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT * FROM job_vacancies WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $user['id']]);
$myJobs = $stmt->fetchAll();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    Manajemen Karir
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Daftar Lowongan yang Anda Posting
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Kelola status penerimaan atau perbarui rincian lowongan kerja yang telah Anda bagikan.
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-extrabold rounded-2xl shadow-md text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Loker Baru</span>
            </a>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <?php if (empty($myJobs)): ?>
                <div class="text-center py-16 text-slate-400 text-sm space-y-3">
                    <i class="fa-solid fa-folder-open text-4xl text-slate-300"></i>
                    <p>Anda belum memposting lowongan pekerjaan.</p>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="inline-block px-5 py-2 bg-amber-600 text-white rounded-xl text-xs font-bold hover:bg-amber-500">
                        Pasang Loker Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th class="p-4 pl-6">Posisi & Perusahaan</th>
                                <th class="p-4">Lokasi & Tipe</th>
                                <th class="p-4">Estimasi Gaji</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Tanggal Buat</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php foreach ($myJobs as $j): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-4 pl-6">
                                        <p class="font-bold text-slate-900 dark:text-white"><?php echo e($j['posisi']); ?></p>
                                        <p class="text-xs text-brand-600 font-semibold"><?php echo e($j['perusahaan']); ?></p>
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
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">Dibuka</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-700">Ditutup</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">
                                        <?php echo format_date_id($j['created_at'], false); ?>
                                    </td>
                                    <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                        <a href="<?php echo BASE_URL; ?>/views/public/loker-detail.php?id=<?php echo $j['id']; ?>" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-block" title="Lihat Tampilan Loker">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/views/member/loker-edit.php?id=<?php echo $j['id']; ?>" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold inline-block" title="Edit Loker">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=toggle_status&id=<?php echo $j['id']; ?>" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold inline-block" title="Ubah Status Open/Closed">
                                            <i class="fa-solid fa-power-off"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/actions/job_process.php?action=delete&id=<?php echo $j['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?')" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold inline-block" title="Hapus Loker">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </main>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
