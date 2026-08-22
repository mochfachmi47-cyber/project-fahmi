<?php
/**
 * FORSAKDA 27 - Dashboard Sidebar Component
 */

$user = current_user();
$currentScript = basename($_SERVER['PHP_SELF'] ?? '');
$isAdmin = has_role('admin');
?>

<aside class="hidden md:flex w-64 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800 p-5 flex-col justify-between shrink-0 shadow-sm">
    <div class="space-y-6">
        
        <!-- User Info Card -->
        <div class="p-4 rounded-2xl bg-gradient-to-br <?php echo $isAdmin ? 'from-amber-900/10 to-amber-500/5 border border-amber-500/20' : 'from-brand-900/10 to-brand-500/5 border border-brand-500/20'; ?>">
            <div class="flex items-center gap-3">
                <img src="<?php echo !empty($user['foto']) ? BASE_URL . '/' . e($user['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=059669&color=fff'; ?>" 
                     alt="<?php echo e($user['nama']); ?>" 
                     class="w-12 h-12 rounded-2xl object-cover border-2 <?php echo $isAdmin ? 'border-amber-500/40' : 'border-brand-500/40'; ?> shadow-sm">
                <div class="overflow-hidden">
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate"><?php echo e($user['nama']); ?></h4>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $isAdmin ? 'bg-amber-100 text-amber-800' : 'bg-brand-100 text-brand-800'; ?>">
                        <i class="fa-solid <?php echo $isAdmin ? 'fa-shield-halved mr-1' : 'fa-user-check mr-1'; ?>"></i>
                        <?php echo e($user['role']); ?>

                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="space-y-1">
            <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>

            <?php if ($isAdmin): ?>
                <!-- ADMIN MENU -->
                <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'dashboard.php') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-chart-pie w-5"></i> Dashboard Admin
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/users.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'users.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-users-gear w-5"></i> Kelola Anggota
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'berita.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-newspaper w-5"></i> Kelola Berita
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/loker.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'loker.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-briefcase w-5"></i> Kelola Loker
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/konten.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'konten.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-sliders w-5"></i> Visi, Misi & Tujuan
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/galeri.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'galeri.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-images w-5"></i> Galeri Foto
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/logs.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'logs.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-clock-rotate-left w-5"></i> Log Aktivitas
                </a>

            <?php else: ?>
                <!-- ANGGOTA MENU -->
                <a href="<?php echo BASE_URL; ?>/views/member/dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'dashboard.php') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-gauge w-5"></i> Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'chat.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-comments w-5"></i> Ruang Chatting
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/berita.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'berita.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-newspaper w-5"></i> Berita Santri
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'loker.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-briefcase w-5"></i> Bursa Loker
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'loker-tambah.php') ? 'bg-amber-600 text-white shadow-md' : 'text-amber-700 bg-amber-50 hover:bg-amber-100'; ?> transition">
                    <i class="fa-solid fa-circle-plus w-5 text-amber-500"></i> Pasang Loker Baru
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'loker-saya.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-folder-open w-5"></i> Loker Saya
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/galeri.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'galeri.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                    <i class="fa-solid fa-images w-5"></i> Galeri Kenangan
                </a>
            <?php endif; ?>

            <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider pt-4 mb-2">Akun & Pengaturan</p>
            <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold <?php echo ($currentScript === 'profil.php') ? 'bg-brand-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'; ?> transition">
                <i class="fa-solid fa-user-pen w-5"></i> Profil Saya
            </a>
            
        </div>
    </div>

    <!-- Bottom Logout -->
    <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
        <a href="<?php echo BASE_URL; ?>/actions/auth_process.php?action=logout" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-50 transition">
            <i class="fa-solid fa-arrow-right-from-bracket w-5"></i> Keluar
        </a>
    </div>
</aside>
