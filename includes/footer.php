<?php
/**
 * FORSAKDA 27 - Footer Template
 */
?>

<footer class="bg-slate-900 text-slate-300 mt-auto border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            
            <!-- Column 1: Brand & Tagline -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-tr from-brand-600 to-teal-400 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-brand-600/30">
                        <i class="fa-solid fa-mosque"></i>
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight"><?php echo APP_NAME; ?></span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Wadah Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27). Membangun ukhuwah islamiyah, saling mendukung dalam belajar, karir, dan berkhidmah untuk pondok serta umat.
                </p>
                <div class="pt-2 text-xs text-emerald-400 font-serif italic">
                    "مَنْ أَحَبَّ أَنْ يُبْسَطَ لَهُ فِي رِزْقِهِ وَيُنْسَأَ لَهُ فِي أَثَرِهِ فَلْيَصِلْ رَحِمَهُ"
                    <span class="block text-[11px] text-slate-400 not-italic mt-1 font-sans">
                        "Barangsiapa yang ingin dilapangkan rezekinya dan dipanjangkan umurnya, hendaklah ia menyambung tali silaturahmi." (HR. Bukhari)
                    </span>
                </div>
            </div>

            <!-- Column 2: Navigasi Cepat -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Navigasi Utama</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-brand-400 transition">Beranda Utama</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/public/visi-misi.php" class="hover:text-brand-400 transition">Visi & Misi Organisasi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/public/tujuan.php" class="hover:text-brand-400 transition">Tujuan Web & Sejarah 27</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/public/berita.php" class="hover:text-brand-400 transition">Berita & Kabar Santri</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/public/galeri.php" class="hover:text-brand-400 transition">Galeri Kegiatan Santri</a></li>
                </ul>
            </div>

            <!-- Column 3: Fitur Komunitas -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Fitur Anggota</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="<?php echo BASE_URL; ?>/views/member/chat.php" class="hover:text-brand-400 transition flex items-center gap-1.5"><i class="fa-solid fa-comments text-brand-500"></i> Ruang Chat Santri</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/member/loker-tambah.php" class="hover:text-brand-400 transition flex items-center gap-1.5"><i class="fa-solid fa-plus-circle text-amber-500"></i> Pasang Info Loker</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/member/direktori.php" class="hover:text-brand-400 transition flex items-center gap-1.5"><i class="fa-solid fa-users text-teal-400"></i> Direktori Santri 27</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/views/public/galeri.php" class="hover:text-brand-400 transition flex items-center gap-1.5"><i class="fa-solid fa-images text-purple-400"></i> Galeri Kenangan</a></li>
                </ul>
            </div>

            <!-- Column 4: Keamanan & Kontak -->
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider">Sekretariat & Bantuan</h4>
                <p class="text-xs text-slate-400 flex items-start gap-2">
                    <i class="fa-solid fa-location-dot text-brand-500 mt-1"></i>
                    <span>Sekretariat Pusat Forum Santri Kelas Dua Angkatan 27</span>
                </p>
                <p class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-brand-500"></i>
                    <span>sekretariat@forsakda27.com</span>
                </p>
                <div class="pt-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-800 border border-emerald-500/30 rounded-xl text-[11px] text-emerald-400">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Dilindungi Keamanan Standar Web</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; <?php echo date('Y'); ?> <strong><?php echo APP_NAME; ?></strong>. Hak Cipta Dilindungi Undang-Undang.</p>
            <div class="flex items-center gap-6">
                <span>Dibuat dengan Ta'dzim untuk Almamater & Ummah</span>
            </div>
        </div>
    </div>
</footer>

<!-- Global Scripts -->
<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>

</body>
</html>
