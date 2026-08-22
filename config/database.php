<?php
/**
 * FORSAKDA 27 - Konfigurasi Database (PDO Handler)
 * Aman terhadap SQL Injection dengan Prepared Statements
 */

require_once __DIR__ . '/app.php';

// Database Credentials (Standar XAMPP/LAMPP)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'forsakda27_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

class Database {
    private static ?PDO $instance = null;

    /**
     * Dapatkan koneksi PDO Database ke MySQL (Singleton Pattern)
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dbName = DB_NAME;
            $dbUser = DB_USER;
            $dbPass = DB_PASS;
            
            // Daftar strategi DSN koneksi MySQL (TCP & Unix Socket)
            $dsnCandidates = [
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . $dbName . ";charset=utf8mb4",
                "mysql:host=localhost;port=" . DB_PORT . ";dbname=" . $dbName . ";charset=utf8mb4",
                "mysql:host=127.0.0.1;port=" . DB_PORT . ";dbname=" . $dbName . ";charset=utf8mb4",
                "mysql:unix_socket=/opt/lampp/var/mysql/mysql.sock;dbname=" . $dbName . ";charset=utf8mb4",
                "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=" . $dbName . ";charset=utf8mb4",
                "mysql:unix_socket=/tmp/mysql.sock;dbname=" . $dbName . ";charset=utf8mb4"
            ];

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $connected = false;
            $lastError = '';

            // 1. Coba koneksi langsung ke database yang sudah ada
            foreach ($dsnCandidates as $dsn) {
                try {
                    self::$instance = new PDO($dsn, $dbUser, $dbPass, $options);
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    $lastError = $e->getMessage();
                }
            }

            // 2. Jika gagal karena database belum ada, coba buat database otomatis
            if (!$connected) {
                $noDbCandidates = [
                    "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4",
                    "mysql:host=localhost;port=" . DB_PORT . ";charset=utf8mb4",
                    "mysql:host=127.0.0.1;port=" . DB_PORT . ";charset=utf8mb4",
                    "mysql:unix_socket=/opt/lampp/var/mysql/mysql.sock;charset=utf8mb4",
                    "mysql:unix_socket=/var/run/mysqld/mysqld.sock;charset=utf8mb4",
                    "mysql:unix_socket=/tmp/mysql.sock;charset=utf8mb4"
                ];

                foreach ($noDbCandidates as $index => $dsnNoDb) {
                    try {
                        $tempPdo = new PDO($dsnNoDb, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

                        $targetDsn = $dsnCandidates[$index] ?? "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . $dbName . ";charset=utf8mb4";
                        self::$instance = new PDO($targetDsn, $dbUser, $dbPass, $options);
                        
                        // Otomatis migrate schema dan seed data awal
                        self::initSchema(self::$instance);
                        $connected = true;
                        break;
                    } catch (Exception $e2) {
                        $lastError = $e2->getMessage();
                    }
                }
            }

            // 3. Jika masih belum berhasil terhubung ke MySQL
            if (!$connected) {
                self::renderDbErrorPage($lastError);
                exit;
            }
        }
        return self::$instance;
    }

    /**
     * Inisialisasi Schema Database jika tabel belum terbentuk
     */
    public static function initSchema(PDO $pdo): void {
        $schemaFile = ROOT_PATH . '/database/schema.sql';
        $seedFile = ROOT_PATH . '/database/seed.sql';

        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            $pdo->exec($sql);
        }
        if (file_exists($seedFile)) {
            $seedSql = file_get_contents($seedFile);
            $pdo->exec($seedSql);
        }
    }

    /**
     * Tampilan ramah pengguna jika MySQL server belum menyala
     */
    private static function renderDbErrorPage(string $err1): void {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Koneksi Database - FORSAKDA 27</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
            <div class="max-w-xl w-full bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl">
                <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center text-3xl mb-6 mx-auto">
                    <i class="fa-solid fa-database"></i>
                </div>
                <h1 class="text-2xl font-bold text-center text-white mb-2">Setup Database FORSAKDA 27</h1>
                <p class="text-slate-400 text-center text-sm mb-6">
                    Sistem belum dapat terhubung ke server MySQL. Pastikan service MySQL/MariaDB (seperti di XAMPP/LAMPP) sudah diaktifkan.
                </p>

                <div class="bg-slate-900/80 rounded-xl p-4 border border-slate-700/60 mb-6 text-xs text-slate-300 font-mono space-y-1">
                    <p><span class="text-emerald-400">Host:</span> <?php echo htmlspecialchars(DB_HOST); ?>:<?php echo htmlspecialchars(DB_PORT); ?></p>
                    <p><span class="text-emerald-400">Database:</span> <?php echo htmlspecialchars(DB_NAME); ?></p>
                    <p><span class="text-emerald-400">User:</span> <?php echo htmlspecialchars(DB_USER); ?></p>
                    <p class="text-rose-400 pt-2"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($err1); ?></p>
                </div>

                <div class="bg-emerald-950/40 border border-emerald-500/30 rounded-xl p-4 mb-6 text-sm text-emerald-200">
                    <h3 class="font-semibold mb-2 flex items-center gap-2 text-emerald-300">
                        <i class="fa-solid fa-lightbulb"></i> Cara Cepat Mengaktifkan:
                    </h3>
                    <ol class="list-decimal list-inside space-y-1 text-xs text-emerald-100/80">
                        <li>Buka <strong>XAMPP / LAMPP Control Panel</strong>.</li>
                        <li>Klik <strong>Start</strong> pada modul <strong>MySQL</strong>.</li>
                        <li>Setelah aktif, muat ulang halaman ini atau buka <a href="<?php echo BASE_URL; ?>/database/setup.php" class="underline font-bold text-emerald-300">Installer Database</a>.</li>
                    </ol>
                </div>

                <div class="flex gap-3">
                    <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/'); ?>" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-center font-medium rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate"></i> Coba Sambungkan Ulang
                    </a>
                    <a href="<?php echo BASE_URL; ?>/database/setup.php" class="py-3 px-4 bg-slate-700 hover:bg-slate-600 text-slate-200 text-center font-medium rounded-xl transition">
                        Buka Setup Wizard
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
