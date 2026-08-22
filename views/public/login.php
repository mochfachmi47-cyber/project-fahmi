<?php
/**
 * FORSAKDA 27 - Halaman Masuk / Login
 */

$pageTitle = 'Masuk ke Akun - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

// Jika sudah login, redirect
if (is_logged_in()) {
    if (has_role('admin')) {
        header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/views/member/dashboard.php');
    }
    exit;
}
?>

<main class="py-12 bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 min-h-[calc(100vh-80px)] flex items-center justify-center p-4 relative overflow-hidden flex-1">
    
    <!-- Background Accents -->
    <div class="absolute inset-0 bg-islamic-pattern opacity-15 pointer-events-none"></div>
    <div class="absolute -top-24 -left-24 w-80 h-80 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-teal-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-slate-800/90 backdrop-blur-xl border border-emerald-500/30 rounded-3xl p-8 sm:p-10 shadow-2xl relative z-10 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-teal-400 rounded-2xl flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-brand-600/40">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <h1 class="text-2xl font-black text-white">Masuk Sebagai Anggota</h1>
            <p class="text-xs text-slate-400">Silakan login untuk mengakses Ruang Chat, Berita &amp; Loker</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="<?php echo BASE_URL; ?>/actions/auth_process.php" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="login">

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Nama</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                    <input type="text" id="loginNama" name="nama" required placeholder="Masukkan nama Anda" class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-700 rounded-2xl text-sm focus:outline-none focus:border-brand-500 text-white transition">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-300">Kata Sandi</label>
                </div>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                    <input type="password" id="loginPassword" name="password" required placeholder="••••••••" class="w-full pl-11 pr-11 py-3 bg-slate-900/90 border border-slate-700 rounded-2xl text-sm focus:outline-none focus:border-brand-500 text-white transition">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-3.5 text-slate-400 hover:text-slate-200">
                        <i id="eyeIcon" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white font-extrabold rounded-2xl shadow-lg shadow-brand-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk Sekarang</span>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-700/60">
            <p class="text-xs text-slate-400">
                Belum terdaftar sebagai anggota?
                <a href="<?php echo BASE_URL; ?>/views/public/register.php" class="font-bold text-brand-400 hover:underline">
                    Daftar di sini
                </a>
            </p>
        </div>

    </div>
</main>

<script>
function togglePasswordVisibility() {
    const input = document.getElementById('loginPassword');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
