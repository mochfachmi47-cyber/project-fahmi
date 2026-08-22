<?php
/**
 * FORSAKDA 27 - Main Navigation Bar (Dynamic for Public, Anggota, Admin)
 */

$currentUser = current_user();
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?>

<header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 transition-colors shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo & Brand (Left) -->
            <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center gap-3 group">
                <div class="w-12 h-12 bg-gradient-to-tr from-brand-700 via-brand-600 to-teal-400 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-brand-600/30 group-hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-mosque"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white"><?php echo APP_NAME; ?></span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Ukhuwah &amp; Pengabdian</p>
                </div>
            </a>

            <!-- Desktop Navigation (Center) -->
            <nav class="hidden lg:flex items-center gap-1">
                <?php if (!is_logged_in()): ?>
                    <!-- Menu Pengunjung / Publik -->
                    <a href="<?php echo BASE_URL; ?>/index.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Beranda</a>
                    <a href="<?php echo BASE_URL; ?>/views/public/visi-misi.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Visi &amp; Misi</a>
                    <a href="<?php echo BASE_URL; ?>/views/public/tujuan.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Tujuan &amp; Sejarah</a>
                    <a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Berita</a>
                    <a href="<?php echo BASE_URL; ?>/views/public/loker.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Bursa Loker</a>
                    <a href="<?php echo BASE_URL; ?>/views/public/galeri.php" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition">Galeri</a>

                <?php elseif (has_role('anggota')): ?>
                    <!-- Menu Anggota (Santri / Alumni) -->
                    <a href="<?php echo BASE_URL; ?>/views/member/dashboard.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge text-slate-400"></i> Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-brand-700 dark:text-brand-400 bg-brand-50/80 dark:bg-slate-800 hover:bg-brand-100/80 transition flex items-center gap-1.5 relative border border-brand-200/60 dark:border-brand-700/60">
                        <i class="fa-solid fa-comments text-brand-600"></i> Ruang Chat
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/berita.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-newspaper text-slate-400"></i> Berita
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-slate-400"></i> Lowongan Kerja
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/galeri.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-images text-slate-400"></i> Galeri
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 transition flex items-center gap-1.5 border border-amber-200/60 dark:border-amber-800/60">
                        <i class="fa-solid fa-plus-circle text-amber-600"></i> Pasang Loker
                    </a>

                <?php elseif (has_role('admin')): ?>
                    <!-- Menu Admin (Pengurus Pusat) -->
                    <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-pie text-slate-400"></i> Admin Panel
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/users.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield text-slate-400"></i> Anggota (CRUD)
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-newspaper text-slate-400"></i> Berita (CRUD)
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/loker.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-slate-400"></i> Loker (CRUD)
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/konten.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-sliders text-slate-400"></i> Konten Web
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="px-3 py-2 rounded-xl text-sm font-semibold text-brand-700 dark:text-brand-400 bg-brand-50 dark:bg-slate-800 hover:bg-brand-100 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-comments text-brand-600"></i> Ruang Chat
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Action Buttons / User Menu (Desktop Right) -->
            <div class="hidden lg:flex items-center gap-3">
                <?php if (!is_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>/views/public/login.php" class="px-5 py-2.5 rounded-xl text-sm font-bold text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-slate-800 transition">
                        <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Masuk
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/public/register.php" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 shadow-md shadow-brand-600/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Gabung Santri
                    </a>
                <?php else: ?>
                    <!-- User Dropdown (Desktop) -->
                    <div class="relative">
                        <button id="userMenuBtn" type="button" class="flex items-center gap-3 p-1.5 pr-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-brand-500/40 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <img src="<?php echo !empty($currentUser['foto']) ? BASE_URL . '/' . e($currentUser['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['nama']) . '&background=059669&color=fff'; ?>" 
                                 alt="<?php echo e($currentUser['nama']); ?>" 
                                 class="w-9 h-9 rounded-xl object-cover border border-emerald-500/30 shadow-sm">
                            <div class="text-left">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight"><?php echo e($currentUser['nama']); ?></p>
                                <p class="text-[10px] text-brand-600 dark:text-brand-400 font-semibold capitalize"><?php echo e($currentUser['role']); ?> &bull; Angkatan 27</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-1"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs text-slate-400 font-medium">Masuk sebagai</p>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate"><?php echo e($currentUser['nama']); ?></p>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-700 transition">
                                <i class="fa-solid fa-user text-slate-400 w-4"></i> Profil &amp; Akun Saya
                            </a>
                            <?php if (has_role('anggota')): ?>
                                <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-700 transition">
                                    <i class="fa-solid fa-briefcase text-slate-400 w-4"></i> Loker Saya
                                </a>
                            <?php endif; ?>
                            <?php if (has_role('admin')): ?>
                                <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-shield-halved text-amber-500 w-4"></i> Panel Administrator
                                </a>
                            <?php endif; ?>
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <a href="<?php echo BASE_URL; ?>/actions/auth_process.php?action=logout" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-right-from-bracket text-rose-500 w-4"></i> Keluar (Logout)
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button & Action (Mobile Right - Perfectly Aligned) -->
            <div class="flex lg:hidden items-center gap-2">
                <?php if (!is_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>/views/public/login.php" class="text-xs font-bold text-brand-700 dark:text-brand-400 px-3 py-1.5 rounded-xl border border-brand-400/40 hover:bg-brand-50 dark:hover:bg-slate-800 transition flex items-center gap-1">
                        <i class="fa-solid fa-right-to-bracket text-[11px]"></i>
                        <span>Masuk</span>
                    </a>
                <?php endif; ?>
                <button id="mobileMenuBtn" aria-label="Toggle menu" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-brand-600 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none transition shadow-sm">
                    <i id="mobileMenuIcon" class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

        </div>
    </div>
</header>

<!-- =================== MOBILE DRAWER OVERLAY =================== -->
<!-- Backdrop -->
<div id="mobileDrawerOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

<!-- Drawer Panel (slide from right) -->
<div id="mobileDrawer" class="fixed top-0 right-0 h-full w-[300px] max-w-[85vw] bg-white dark:bg-slate-900 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out lg:hidden flex flex-col">
    
    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-brand-700 to-teal-600">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-white text-lg">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <div>
                <p class="text-sm font-extrabold text-white"><?php echo APP_NAME; ?></p>
                <p class="text-[10px] text-brand-100">Forum Santri Kelas Dua 27</p>
            </div>
        </div>
        <button id="mobileDrawerClose" type="button" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Drawer Content (scrollable) -->
    <div class="flex-1 overflow-y-auto py-4 px-4 space-y-1">

        <?php if (!is_logged_in()): ?>
            <!-- PUBLIK MENU -->
            <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Navigasi</p>
            <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-house w-5 text-slate-400"></i> Beranda
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/visi-misi.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-bullseye w-5 text-slate-400"></i> Visi &amp; Misi
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/tujuan.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-flag w-5 text-slate-400"></i> Tujuan &amp; Sejarah
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-newspaper w-5 text-slate-400"></i> Berita Santri
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/loker.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-briefcase w-5 text-slate-400"></i> Bursa Loker
            </a>
            <a href="<?php echo BASE_URL; ?>/views/public/galeri.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                <i class="fa-solid fa-images w-5 text-slate-400"></i> Galeri Kegiatan
            </a>
            <div class="pt-3 px-1 space-y-2">
                <a href="<?php echo BASE_URL; ?>/views/public/register.php" class="flex items-center justify-center gap-2 w-full py-3 font-bold text-white bg-gradient-to-r from-brand-600 to-teal-500 rounded-xl shadow-md text-sm">
                    <i class="fa-solid fa-user-plus"></i> Gabung Santri
                </a>
            </div>

        <?php else: ?>
            <!-- USER INFO CARD -->
            <div class="flex items-center gap-3 p-3 mb-3 bg-gradient-to-br <?php echo has_role('admin') ? 'from-amber-50 to-amber-100/50 dark:from-amber-950/40 dark:to-amber-900/20 border border-amber-200 dark:border-amber-800' : 'from-brand-50 to-brand-100/50 dark:from-emerald-950/40 dark:to-emerald-900/20 border border-brand-200 dark:border-brand-800'; ?> rounded-2xl">
                <img src="<?php echo !empty($currentUser['foto']) ? BASE_URL . '/' . e($currentUser['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['nama']) . '&background=059669&color=fff'; ?>" 
                     class="w-11 h-11 rounded-xl object-cover border-2 border-white dark:border-slate-800 shadow-sm">
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate"><?php echo e($currentUser['nama']); ?></p>
                    <span class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider <?php echo has_role('admin') ? 'text-amber-700 dark:text-amber-400' : 'text-brand-700 dark:text-brand-400'; ?>">
                        <i class="fa-solid <?php echo has_role('admin') ? 'fa-shield-halved' : 'fa-user-check'; ?> mr-1"></i>
                        <?php echo e($currentUser['role']); ?> &bull; Angkatan 27
                    </span>
                </div>
            </div>

            <?php if (has_role('anggota')): ?>
                <!-- ANGGOTA MENU -->
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 mt-2">Menu Utama</p>
                <a href="<?php echo BASE_URL; ?>/views/member/dashboard.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-gauge w-5 text-slate-400"></i> Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold text-brand-700 dark:text-brand-400 bg-brand-50 dark:bg-slate-800 transition">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-comments w-5 text-brand-500"></i> Ruang Chatting</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/berita.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-newspaper w-5 text-slate-400"></i> Berita Santri
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-briefcase w-5 text-slate-400"></i> Bursa Loker
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 transition">
                    <i class="fa-solid fa-circle-plus w-5 text-amber-500"></i> Pasang Loker Baru
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/loker-saya.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-folder-open w-5 text-slate-400"></i> Loker Saya
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/galeri.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-images w-5 text-slate-400"></i> Galeri Kenangan
                </a>
                
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 mt-4">Akun &amp; Pengaturan</p>
                <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-user-pen w-5 text-slate-400"></i> Profil Saya
                </a>

            <?php elseif (has_role('admin')): ?>
                <!-- ADMIN MENU -->
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 mt-2">Panel Admin</p>
                <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-chart-pie w-5 text-slate-400"></i> Dashboard Admin
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/users.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-users-gear w-5 text-slate-400"></i> Kelola Anggota
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/berita.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-newspaper w-5 text-slate-400"></i> Kelola Berita
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/loker.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-briefcase w-5 text-slate-400"></i> Kelola Loker
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/konten.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-sliders w-5 text-slate-400"></i> Visi, Misi &amp; Tujuan
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/galeri.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-images w-5 text-slate-400"></i> Galeri Foto
                </a>
                <a href="<?php echo BASE_URL; ?>/views/admin/logs.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-slate-400"></i> Log Aktivitas
                </a>
                <a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-brand-700 dark:text-brand-400 bg-brand-50 dark:bg-slate-800 hover:bg-brand-100 transition">
                    <i class="fa-solid fa-comments w-5 text-brand-500"></i> Ruang Chat
                </a>
                <p class="px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 mt-4">Akun &amp; Pengaturan</p>
                <a href="<?php echo BASE_URL; ?>/views/member/profil.php" class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-brand-50 dark:hover:bg-slate-800 hover:text-brand-600 transition">
                    <i class="fa-solid fa-user-pen w-5 text-slate-400"></i> Profil Saya
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Drawer Footer: Logout -->
    <?php if (is_logged_in()): ?>
    <div class="px-4 py-4 border-t border-slate-200 dark:border-slate-800">
        <a href="<?php echo BASE_URL; ?>/actions/auth_process.php?action=logout" 
           class="flex items-center justify-center gap-2 w-full py-3 font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 rounded-xl text-sm transition">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar (Logout)
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
(function() {
    const btn      = document.getElementById('mobileMenuBtn');
    const icon     = document.getElementById('mobileMenuIcon');
    const overlay  = document.getElementById('mobileDrawerOverlay');
    const drawer   = document.getElementById('mobileDrawer');
    const closeBtn = document.getElementById('mobileDrawerClose');

    // Desktop user dropdown
    const userMenuBtn  = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    function openDrawer() {
        if (!overlay || !drawer) return;
        overlay.classList.remove('hidden');
        requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
            drawer.classList.remove('translate-x-full');
        });
        if (icon) icon.classList.replace('fa-bars', 'fa-xmark');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        if (!overlay || !drawer) return;
        overlay.classList.add('opacity-0');
        drawer.classList.add('translate-x-full');
        if (icon) icon.classList.replace('fa-xmark', 'fa-bars');
        document.body.style.overflow = '';
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    if (btn)      btn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay)  overlay.addEventListener('click', closeDrawer);

    // Close drawer on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDrawer();
    });

    // Desktop dropdown toggle
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
})();
</script>
