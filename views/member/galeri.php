<?php
/**
 * FORSAKDA 27 - Galeri Foto Santri (Anggota & Alumni)
 */

$pageTitle = 'Galeri Kenangan & Kegiatan Santri - FORSAKDA 27';
require_once __DIR__ . '/../../includes/header.php';

require_role(['anggota', 'admin']);
$user = current_user();
$pdo = Database::getConnection();

$kategoriFilter = sanitize_input($_GET['kategori'] ?? '');

$sql = "
    SELECT g.*, u.nama as uploader_nama, u.angkatan as uploader_angkatan
    FROM gallery g
    LEFT JOIN users u ON g.user_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($kategoriFilter) && $kategoriFilter !== 'all') {
    $sql .= " AND g.kategori = :kat";
    $params[':kat'] = $kategoriFilter;
}

$sql .= " ORDER BY g.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$galleries = $stmt->fetchAll();

$kategoriList = ['all' => 'Semua Kategori', 'Kegiatan' => 'Kegiatan Reuni', 'Kenangan' => 'Kenangan Mondok', 'Sosial' => 'Bakti Sosial', 'Pesantren' => 'Almamater'];
?>

<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950">
    
    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 sm:p-10 space-y-8 overflow-y-auto">
        
        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-purple-600 bg-purple-50 dark:bg-purple-950/60 px-3 py-1 rounded-full border border-purple-200 dark:border-purple-800">
                    Dokumentasi & Kenangan
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                    Galeri Kenangan Santri FORSAKDA 27
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                    Koleksi foto kegiatan, nostalgia masa pondok, dan momen kebersamaan sahabat santri angkatan 27.
                </p>
            </div>

            <button type="button" onclick="openUploadModal()" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold rounded-2xl shadow-lg shadow-purple-600/20 text-xs transition flex items-center gap-2 self-start sm:self-auto">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Unggah Foto Kenangan</span>
            </button>
        </div>

        <!-- Filter Category Tabs -->
        <div class="bg-white dark:bg-slate-900 p-3.5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto w-full pb-1 sm:pb-0">
                <?php foreach ($kategoriList as $key => $label): ?>
                    <?php 
                    $isActive = ($kategoriFilter === $key) || (empty($kategoriFilter) && $key === 'all');
                    $link = ($key === 'all') ? BASE_URL . '/views/member/galeri.php' : BASE_URL . '/views/member/galeri.php?kategori=' . urlencode($key);
                    ?>
                    <a href="<?php echo $link; ?>" class="px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition <?php echo $isActive ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-slate-700'; ?>">
                        <?php echo e($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <span class="text-xs font-bold text-slate-400 whitespace-nowrap hidden sm:inline"><?php echo count($galleries); ?> Foto</span>
        </div>

        <!-- Gallery Grid -->
        <?php if (empty($galleries)): ?>
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800 space-y-3">
                <div class="w-16 h-16 bg-purple-50 dark:bg-purple-950 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mx-auto">
                    <i class="fa-solid fa-images"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum ada foto dalam kategori ini</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Jadilah yang pertama mengabadikan kenangan silaturahmi atau kegiatan santri dengan mengklik tombol unggah di atas.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($galleries as $g): ?>
                    <?php 
                    $canDelete = has_role('admin') || ((int)($g['user_id'] ?? 0) === (int)$user['id']);
                    ?>
                    <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between group hover:shadow-xl transition">
                        <div>
                            <!-- Image container -->
                            <div class="relative h-56 bg-slate-900 overflow-hidden cursor-pointer" onclick="viewPhoto('<?php echo addslashes(BASE_URL . '/' . $g['gambar']); ?>', '<?php echo addslashes($g['judul']); ?>', '<?php echo addslashes($g['deskripsi'] ?? ''); ?>')">
                                <img src="<?php echo BASE_URL . '/' . e($g['gambar']); ?>" 
                                     alt="<?php echo e($g['judul']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                
                                <span class="absolute top-3 left-3 bg-slate-950/75 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-xl">
                                    <?php echo e($g['kategori']); ?>
                                </span>

                                <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                    <span class="p-3 rounded-2xl bg-white/20 backdrop-blur-md text-lg"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-5 space-y-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-purple-600 transition">
                                    <?php echo e($g['judul']); ?>
                                </h3>

                                <?php if (!empty($g['deskripsi'])): ?>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                        <?php echo e($g['deskripsi']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-5 py-3.5 bg-slate-50/80 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
                            <div class="text-[11px] text-slate-400 truncate">
                                <span>Oleh: <strong class="text-slate-700 dark:text-slate-300"><?php echo e($g['uploader_nama'] ?: 'Admin'); ?></strong></span>
                            </div>

                            <?php if ($canDelete): ?>
                                <a href="<?php echo BASE_URL; ?>/actions/content_process.php?action=delete_gallery&id=<?php echo $g['id']; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus foto \'<?php echo addslashes($g['judul']); ?>\'?')" 
                                   class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-xs font-semibold transition" 
                                   title="Hapus Foto">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

</div>

<!-- ======================================================== -->
<!-- MODAL: UNGGAH FOTO KENANGAN (ANGGOTA)                    -->
<!-- ======================================================== -->
<div id="uploadPhotoModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up text-purple-600"></i> Unggah Foto Kenangan
            </h3>
            <button onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/actions/content_process.php" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="add_gallery">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Judul / Caption Foto *</label>
                <input type="text" name="judul" required placeholder="Contoh: Momen Silaturahmi Santri 2026" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori *</label>
                <select name="kategori" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100">
                    <option value="Kegiatan">Kegiatan Reuni</option>
                    <option value="Kenangan" selected>Kenangan Masa Mondok</option>
                    <option value="Sosial">Bakti Sosial & Santunan</option>
                    <option value="Pesantren">Kunjungan Almamater</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="2" placeholder="Catatan cerita singkat momen foto ini..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Berkas Foto (JPG/PNG/WebP, Maks 2MB) *</label>
                <input type="file" name="gambar" required accept="image/png, image/jpeg, image/webp" class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-500">
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold rounded-xl transition shadow-md shadow-purple-600/30">
                    Simpan Foto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: LIGHTBOX PREVIEW FOTO                             -->
<!-- ======================================================== -->
<div id="lightboxModal" class="fixed inset-0 bg-slate-950/90 backdrop-blur-md z-50 hidden flex flex-col items-center justify-center p-4" onclick="closeLightbox()">
    <div class="max-w-4xl w-full bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 shadow-2xl relative" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-950/80 text-white hover:bg-slate-800 flex items-center justify-center text-sm">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="max-h-[70vh] flex items-center justify-center bg-black">
            <img id="lightboxImg" src="" class="max-h-[70vh] w-auto object-contain">
        </div>

        <div class="p-5 bg-slate-900 border-t border-slate-800 space-y-1">
            <h3 id="lightboxTitle" class="text-sm font-bold text-white"></h3>
            <p id="lightboxDesc" class="text-xs text-slate-400 leading-relaxed"></p>
        </div>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadPhotoModal').classList.remove('hidden');
}
function closeUploadModal() {
    document.getElementById('uploadPhotoModal').classList.add('hidden');
}

function viewPhoto(src, title, desc) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxTitle').textContent = title;
    document.getElementById('lightboxDesc').textContent = desc;
    document.getElementById('lightboxModal').classList.remove('hidden');
}
function closeLightbox() {
    document.getElementById('lightboxModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
