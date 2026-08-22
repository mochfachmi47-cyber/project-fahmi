<?php
/**
 * FORSAKDA 27 - Manajemen Anggota & Pengguna (Admin CRUD Lengkap)
 */

$pageTitle = 'Kelola Anggota & Pengguna - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['admin']);
$pdo = Database::getConnection();

$q = sanitize_input($_GET['q'] ?? '');
$statusFilter = sanitize_input($_GET['status'] ?? '');
$roleFilter = sanitize_input($_GET['role'] ?? '');

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (nama LIKE :q OR email LIKE :q OR domisili LIKE :q OR profesi LIKE :q OR no_hp LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($statusFilter) && $statusFilter !== 'all') {
    $sql .= " AND status = :st";
    $params[':st'] = $statusFilter;
}

if (!empty($roleFilter) && $roleFilter !== 'all') {
    $sql .= " AND role = :rl";
    $params[':rl'] = $roleFilter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Total stats
$totalAll = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalActive = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$totalPending = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                    Manajemen Pengguna
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Kelola Anggota & Pengguna (CRUD)
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Tambah anggota baru, edit profil santri, verifikasi antrean pendaftaran, reset kata sandi, dan atur role.
                </p>
            </div>

            <button type="button" onclick="openAddUserModal()" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-extrabold rounded-2xl shadow-md text-xs transition flex items-center gap-2 self-start sm:self-auto">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-semibold">Total Terdaftar</p>
                    <h4 class="text-xl font-extrabold text-slate-900 dark:text-white"><?php echo $totalAll; ?></h4>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-semibold">Akun Aktif</p>
                    <h4 class="text-xl font-extrabold text-emerald-600"><?php echo $totalActive; ?></h4>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-400 font-semibold">Menunggu Verifikasi</p>
                    <h4 class="text-xl font-extrabold <?php echo ($totalPending > 0) ? 'text-amber-600' : 'text-slate-900 dark:text-white'; ?>"><?php echo $totalPending; ?></h4>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            
            <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-[200px] sm:max-w-xs">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari nama, email, no hp, kota..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-200">
                </div>
                
                <select name="status" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Status</option>
                    <option value="active" <?php echo ($statusFilter === 'active') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="pending" <?php echo ($statusFilter === 'pending') ? 'selected' : ''; ?>>Pending (Menunggu)</option>
                    <option value="suspended" <?php echo ($statusFilter === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                </select>

                <select name="role" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                    <option value="all">Semua Role</option>
                    <option value="admin" <?php echo ($roleFilter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="anggota" <?php echo ($roleFilter === 'anggota') ? 'selected' : ''; ?>>Anggota</option>
                    <option value="publik" <?php echo ($roleFilter === 'publik') ? 'selected' : ''; ?>>Publik</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-500 transition">
                    Filter
                </button>
                <a href="<?php echo BASE_URL; ?>/views/admin/users.php" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
            </form>

        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Pengguna</th>
                            <th class="p-4">Role</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Kontak & Domisili</th>
                            <th class="p-4">Tanggal Daftar</th>
                            <th class="p-4 pr-6 text-right">Kelola (Aksi)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 text-xs">Tidak ada data pengguna ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo !empty($u['foto']) ? BASE_URL . '/' . e($u['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($u['nama']) . '&background=059669&color=fff'; ?>" 
                                                 class="w-10 h-10 rounded-xl object-cover border border-emerald-500/30">
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white"><?php echo e($u['nama']); ?></p>
                                                <p class="text-[11px] text-slate-400 font-mono"><?php echo e($u['email'] ?: 'Tanpa Email'); ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <!-- Change Role Form -->
                                        <form method="POST" action="<?php echo BASE_URL; ?>/actions/user_process.php" class="inline-block">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="change_role">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <select name="role" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase border <?php echo ($u['role'] === 'admin') ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-brand-50 text-brand-800 border-brand-300'; ?> cursor-pointer">
                                                <option value="admin" <?php echo ($u['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="anggota" <?php echo ($u['role'] === 'anggota') ? 'selected' : ''; ?>>Anggota</option>
                                                <option value="publik" <?php echo ($u['role'] === 'publik') ? 'selected' : ''; ?>>Publik</option>
                                            </select>
                                        </form>
                                    </td>

                                    <td class="p-4">
                                        <?php if ($u['status'] === 'active'): ?>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800">Aktif</span>
                                        <?php elseif ($u['status'] === 'pending'): ?>
                                            <a href="<?php echo BASE_URL; ?>/actions/user_process.php?action=approve&id=<?php echo $u['id']; ?>" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 hover:bg-emerald-600 hover:text-white transition" title="Klik untuk menyetujui akun ini">
                                                Pending (Klik Setujui)
                                            </a>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800">Suspended</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="p-4 text-xs text-slate-600 dark:text-slate-300">
                                        <p class="font-medium"><?php echo e($u['domisili'] ?: '-'); ?></p>
                                        <span class="text-[10px] text-slate-400"><?php echo e($u['no_hp'] ?: 'No HP: -'); ?> &bull; <?php echo e($u['profesi'] ?: 'Profesi: -'); ?></span>
                                    </td>

                                    <td class="p-4 text-xs text-slate-400">
                                        <?php echo format_date_id($u['created_at'], false); ?>
                                    </td>

                                    <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" onclick='openEditUserModal(<?php echo json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold" title="Edit Biodata Lengkap">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Toggle status button -->
                                        <a href="<?php echo BASE_URL; ?>/actions/user_process.php?action=toggle_status&id=<?php echo $u['id']; ?>" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold" title="Toggle Aktif/Suspend">
                                            <i class="fa-solid fa-power-off"></i>
                                        </a>

                                        <!-- Reset Password Modal Trigger -->
                                        <button type="button" onclick="openResetPasswordModal(<?php echo $u['id']; ?>, '<?php echo addslashes($u['nama']); ?>')" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold" title="Reset Password">
                                            <i class="fa-solid fa-key"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <a href="<?php echo BASE_URL; ?>/actions/user_process.php?action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus akun \'<?php echo addslashes($u['nama']); ?>\' secara permanen? Seluruh data terkait akan dibersihkan.')" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold" title="Hapus Pengguna">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
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

<!-- ======================================================== -->
<!-- MODAL: TAMBAH PENGGUNA BARU                              -->
<!-- ======================================================== -->
<div id="addUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-brand-600"></i> Tambah Pengguna Baru
            </h3>
            <button onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/actions/user_process.php" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="create_user">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" required placeholder="Nama santri / pengurus" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                    <input type="text" name="password" required value="santriforsakda27" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Role *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="anggota" selected>Anggota (Santri)</option>
                        <option value="admin">Admin</option>
                        <option value="publik">Publik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="active" selected>Aktif</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Angkatan</label>
                    <input type="text" name="angkatan" value="27" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Domisili / Kota</label>
                    <input type="text" name="domisili" placeholder="Kota tempat tinggal" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Profesi / Aktivitas</label>
                <input type="text" name="profesi" placeholder="Pekerjaan / aktivitas saat ini" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bio / Catatan Singkat</label>
                <textarea name="bio" rows="2" placeholder="Catatan informasi santri..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold rounded-xl transition shadow-md shadow-brand-600/30">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: EDIT DATA PENGGUNA                                -->
<!-- ======================================================== -->
<div id="editUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600"></i> Edit Data Pengguna
            </h3>
            <button onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/actions/user_process.php" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" id="editUserId" name="id" value="">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" id="editUserNama" name="nama" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" id="editUserEmail" name="email" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="new_password" placeholder="(Biarkan kosong jika tetap)" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Role *</label>
                    <select id="editUserRole" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="anggota">Anggota</option>
                        <option value="admin">Admin</option>
                        <option value="publik">Publik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status *</label>
                    <select id="editUserStatus" name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                        <option value="active">Aktif</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Angkatan</label>
                    <input type="text" id="editUserAngkatan" name="angkatan" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp / HP</label>
                    <input type="text" id="editUserNoHp" name="no_hp" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Domisili / Kota</label>
                    <input type="text" id="editUserDomisili" name="domisili" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Profesi / Aktivitas</label>
                <input type="text" id="editUserProfesi" name="profesi" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bio</label>
                <textarea id="editUserBio" name="bio" rows="2" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition shadow-md shadow-blue-600/30">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: RESET PASSWORD                                    -->
<!-- ======================================================== -->
<div id="resetPassModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-key text-amber-600"></i> Reset Password Pengguna
            </h3>
            <button onclick="closeResetPassModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500 dark:text-slate-400">
            Mereset kata sandi untuk: <strong id="resetPassUserName" class="text-slate-800 dark:text-slate-200"></strong>
        </p>

        <form method="POST" action="<?php echo BASE_URL; ?>/actions/user_process.php" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" id="resetModalUserId" name="id" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Baru *</label>
                <input type="text" name="new_password" required value="santriforsakda27" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 font-mono">
                <p class="text-[10px] text-slate-400 mt-1">Default: <code class="text-amber-600 font-bold">santriforsakda27</code></p>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="closeResetPassModal()" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition">
                    Terapkan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}
function closeAddUserModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserNama').value = user.nama || '';
    document.getElementById('editUserEmail').value = user.email || '';
    document.getElementById('editUserRole').value = user.role || 'anggota';
    document.getElementById('editUserStatus').value = user.status || 'active';
    document.getElementById('editUserAngkatan').value = user.angkatan || '27';
    document.getElementById('editUserNoHp').value = user.no_hp || '';
    document.getElementById('editUserDomisili').value = user.domisili || '';
    document.getElementById('editUserProfesi').value = user.profesi || '';
    document.getElementById('editUserBio').value = user.bio || '';
    document.getElementById('editUserModal').classList.remove('hidden');
}
function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

function openResetPasswordModal(userId, userName) {
    document.getElementById('resetModalUserId').value = userId;
    document.getElementById('resetPassUserName').textContent = userName;
    document.getElementById('resetPassModal').classList.remove('hidden');
}
function closeResetPassModal() {
    document.getElementById('resetPassModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
