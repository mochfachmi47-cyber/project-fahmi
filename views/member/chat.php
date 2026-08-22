<?php
/**
 * FORSAKDA 27 - Ruang Chatting Santri & Alumni (Live Chat)
 * Fitur Role Anggota: Chatingan sesama role anggota secara interaktif
 */

$pageTitle = 'Ruang Chat Santri - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$user = current_user();
$pdo = Database::getConnection();

// Ambil data anggota aktif untuk daftar kontak di sidebar chat
$membersStmt = $pdo->prepare("SELECT id, nama, role, foto, domisili, profesi FROM users WHERE status = 'active' ORDER BY (id = :uid) DESC, nama ASC");
$membersStmt->execute([':uid' => $user['id']]);
$membersList = $membersStmt->fetchAll();
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <!-- Sidebar Utama -->
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <!-- Chat App Container -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 flex flex-col h-[calc(100vh-80px)]">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl flex-1 flex flex-col md:flex-row overflow-hidden">
            
            <!-- Chat Room Area (Left / Main) -->
            <div class="flex-1 flex flex-col justify-between h-full bg-slate-50/50 dark:bg-slate-900/50">
                
                <!-- Chat Header -->
                <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-gradient-to-tr from-brand-600 to-teal-400 rounded-2xl flex items-center justify-center text-white text-xl shadow-md shadow-brand-600/20">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Majelis Chat Santri 27</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Realtime
                                </span>
                            </div>
                            <p class="text-xs text-slate-400">Ruang obrolan dan silaturahmi seluruh anggota FORSAKDA 27</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="chatApp.playNotificationSound()" title="Tes Nada Dering" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-brand-600 text-xs flex items-center gap-1.5 transition">
                            <i class="fa-solid fa-bell"></i>
                            <span class="hidden sm:inline">Suara Notifikasi</span>
                        </button>
                    </div>
                </div>

                <!-- Messages Stream Container -->
                <div id="chatMessagesContainer" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4">
                    <!-- Loading initial messages placeholder -->
                    <div class="flex items-center justify-center h-full text-slate-400 text-xs">
                        <i class="fa-solid fa-spinner fa-spin mr-2 text-brand-600 text-base"></i>
                        <span>Memuat percakapan santri...</span>
                    </div>
                </div>

                <!-- Emoji Bar & Input Form -->
                <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 space-y-2">
                    
                    <!-- Quick Emoji Picker -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-base select-none">
                        <span class="text-[11px] font-semibold text-slate-400 mr-1 flex items-center gap-1">
                            <i class="fa-regular fa-face-smile"></i> Emoji:
                        </span>
                        <?php 
                        $emojis = ['🤲', '🕋', '🕌', '🤝', '👍', '❤️', '👏', '🌟', '💡', '😊', '🙏'];
                        foreach ($emojis as $em): 
                        ?>
                            <button type="button" onclick="chatApp.insertEmoji('<?php echo $em; ?>')" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition text-base leading-none">
                                <?php echo $em; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Chat Form -->
                    <form id="chatForm" class="flex items-center gap-2">
                        <input type="text" 
                               id="chatMessageInput" 
                               placeholder="Tulis pesan silaturahmi... (Tekan Enter untuk kirim)" 
                               autocomplete="off"
                               maxlength="1000"
                               class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 text-slate-800 dark:text-slate-100 transition">
                        
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white font-extrabold rounded-2xl shadow-md shadow-brand-600/20 text-xs sm:text-sm transition flex items-center justify-center gap-2 flex-shrink-0">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span class="hidden sm:inline">Kirim</span>
                        </button>
                    </form>
                    <p class="text-[10px] text-slate-400 text-right">Gunakan bahasa yang santun sesuai adab santri</p>
                </div>

            </div>

            <!-- Online / Contact List Sidebar (Right) -->
            <div class="hidden lg:flex flex-col w-72 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Anggota Santri (<?php echo count($membersList); ?>)</h3>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>

                <div class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                    <?php foreach ($membersList as $m): ?>
                        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition <?php echo ($m['id'] == $user['id']) ? 'bg-brand-50/50 dark:bg-brand-950/20 border border-brand-200/50' : ''; ?>">
                            <div class="relative flex-shrink-0">
                                <img src="<?php echo !empty($m['foto']) ? BASE_URL . '/' . e($m['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($m['nama']) . '&background=059669&color=fff'; ?>" 
                                     alt="<?php echo e($m['nama']); ?>" 
                                     class="w-9 h-9 rounded-xl object-cover border border-emerald-500/30">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 absolute -bottom-0.5 -right-0.5"></span>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                    <?php echo e($m['nama']); ?>
                                    <?php if ($m['id'] == $user['id']): ?>
                                        <span class="text-[10px] text-brand-600 font-bold">(Anda)</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-[10px] text-slate-400 truncate"><?php echo e($m['profesi'] ?: 'Santri KDA 27'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </main>

</div>

<!-- Chat JS Integration -->
<script src="<?php echo BASE_URL; ?>/assets/js/chat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Clear initial loading
    document.getElementById('chatMessagesContainer').innerHTML = '';
    
    window.chatApp = new ForsakdaChat({
        containerId: 'chatMessagesContainer',
        formId: 'chatForm',
        inputId: 'chatMessageInput',
        apiUrl: '<?php echo BASE_URL; ?>/actions/chat_api.php',
        currentUserId: <?php echo (int)$user['id']; ?>,
        csrfToken: '<?php echo csrf_token(); ?>',
        room: 'general'
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
