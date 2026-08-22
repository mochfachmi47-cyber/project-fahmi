<?php
/**
 * FORSAKDA 27 - Database Installer & Migration Wizard
 * Forum Santri Kelas Dua Angkatan 27
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/security.php';

$message = '';
$status = '';
$step = $_GET['step'] ?? 'intro';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    if (!verify_csrf()) {
        $status = 'error';
        $message = 'Token keamanan CSRF tidak valid. Silakan coba lagi.';
    } else {
        $dbHost = sanitize_input($_POST['db_host'] ?? '127.0.0.1');
        $dbPort = sanitize_input($_POST['db_port'] ?? '3306');
        $dbName = sanitize_input($_POST['db_name'] ?? 'forsakda27_db');
        $dbUser = sanitize_input($_POST['db_user'] ?? 'root');
        $dbPass = $_POST['db_pass'] ?? '';

        try {
            // 1. Connect without DB first
            $dsnNoDb = "mysql:host=$dbHost;port=$dbPort;charset=utf8mb4";
            $pdo = new PDO($dsnNoDb, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // 2. Create Database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // 3. Connect to created Database
            $dsnWithDb = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
            $pdoDb = new PDO($dsnWithDb, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // 4. Run Schema
            $schemaFile = __DIR__ . '/schema.sql';
            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $pdoDb->exec($schemaSql);
            }

            // 5. Run Seed
            $seedFile = __DIR__ . '/seed.sql';
            if (file_exists($seedFile)) {
                $seedSql = file_get_contents($seedFile);
                $pdoDb->exec($seedSql);
            }

            $status = 'success';
            $message = 'Selamat! Database FORSAKDA 27 berhasil diinstal dan data awal telah siap digunakan.';
            $step = 'finish';
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Gagal menginstal database: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Wizard - FORSAKDA 27</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .arabic-font { font-family: 'Amiri', serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 min-h-screen text-slate-100 flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-slate-800/90 backdrop-blur-xl border border-emerald-500/30 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Glow Effect -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-emerald-600 to-teal-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30 text-white text-3xl">
                <i class="fa-solid fa-mosque"></i>
            </div>
            <span class="arabic-font text-emerald-400 text-lg">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</span>
            <h1 class="text-2xl font-extrabold text-white mt-1">Setup Database FORSAKDA 27</h1>
            <p class="text-slate-400 text-sm">Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27)</p>
        </div>

        <?php if ($status === 'error'): ?>
            <div class="bg-rose-950/60 border border-rose-500/50 text-rose-200 rounded-2xl p-4 mb-6 flex items-start gap-3 text-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg mt-0.5"></i>
                <div>
                    <strong class="font-semibold block mb-0.5">Terjadi Kesalahan:</strong>
                    <span><?php echo e($message); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($step === 'finish'): ?>
            <div class="bg-emerald-950/60 border border-emerald-500/50 text-emerald-200 rounded-2xl p-5 mb-6 text-center">
                <div class="w-12 h-12 bg-emerald-500/30 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 class="font-bold text-white text-lg mb-1">Inisialisasi Berhasil!</h3>
                <p class="text-xs text-emerald-200/90 mb-4"><?php echo e($message); ?></p>

                <div class="bg-slate-900/80 rounded-xl p-4 text-left text-xs space-y-2 border border-slate-700 font-mono">
                    <p class="font-bold text-emerald-400 border-b border-slate-700 pb-1">Kredensial Default:</p>
                    <p><span class="text-slate-400">1. Admin:</span> <span class="text-amber-300">admin@forsakda27.com</span> | Pass: <span class="text-amber-300">adminforsakda27</span></p>
                    <p><span class="text-slate-400">2. Santri 1:</span> <span class="text-emerald-300">santri1@forsakda27.com</span> | Pass: <span class="text-emerald-300">santriforsakda27</span></p>
                    <p><span class="text-slate-400">3. Santri 2:</span> <span class="text-emerald-300">santri2@forsakda27.com</span> | Pass: <span class="text-emerald-300">santriforsakda27</span></p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="<?php echo BASE_URL; ?>/index.php" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-house"></i> Buka Halaman Utama
                </a>
                <a href="<?php echo BASE_URL; ?>/views/public/login.php" class="flex-1 py-3 px-4 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk / Login
                </a>
            </div>

        <?php else: ?>
            <form method="POST" action="" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="install">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Host Database</label>
                        <input type="text" name="db_host" value="<?php echo e(DB_HOST); ?>" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-emerald-500 text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Port</label>
                        <input type="text" name="db_port" value="<?php echo e(DB_PORT); ?>" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-emerald-500 text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Database</label>
                    <input type="text" name="db_name" value="<?php echo e(DB_NAME); ?>" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-emerald-500 text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">User DB</label>
                        <input type="text" name="db_user" value="<?php echo e(DB_USER); ?>" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-emerald-500 text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Password DB</label>
                        <input type="password" name="db_pass" placeholder="(Kosongkan jika default XAMPP)" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm focus:outline-none focus:border-emerald-500 text-white">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-900/40 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-play"></i> Mulai Instalasi Database
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <div class="mt-6 pt-4 border-t border-slate-700/60 text-center text-xs text-slate-500">
            &copy; <?php echo date('Y'); ?> FORSAKDA 27 &bull; Forum Santri Kelas Dua 27
        </div>
    </div>

</body>
</html>
