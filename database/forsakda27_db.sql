-- ========================================================
-- FORSAKDA 27 - Database Schema & Initial Data
-- Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27)
-- Siap diimpor langsung ke phpMyAdmin / MySQL CLI
-- ========================================================

CREATE DATABASE IF NOT EXISTS `forsakda27_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `forsakda27_db`;

-- --------------------------------------------------------
-- 1. TABEL: users (Pengguna / Santri / Admin)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'anggota', 'publik') DEFAULT 'anggota',
  `status` ENUM('active', 'pending', 'suspended') DEFAULT 'active',
  `angkatan` VARCHAR(20) DEFAULT '27',
  `no_hp` VARCHAR(30) NULL,
  `domisili` VARCHAR(100) NULL,
  `profesi` VARCHAR(100) NULL,
  `bio` TEXT NULL,
  `foto` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `email_unique` (`email`),
  INDEX (`nama`),
  INDEX (`role`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. TABEL: news (Berita & Artikel Santri)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `kategori` VARCHAR(50) DEFAULT 'Berita',
  `ringkasan` TEXT NULL,
  `konten` LONGTEXT NOT NULL,
  `gambar` VARCHAR(255) NULL,
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `is_pinned` TINYINT(1) DEFAULT 0,
  `views` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`user_id`),
  INDEX (`slug`),
  INDEX (`status`),
  INDEX (`is_pinned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. TABEL: job_vacancies (Bursa Lowongan Kerja)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_vacancies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `posisi` VARCHAR(150) NOT NULL,
  `perusahaan` VARCHAR(150) NOT NULL,
  `lokasi` VARCHAR(100) NOT NULL,
  `tipe_pekerjaan` ENUM('Full-time', 'Part-time', 'Freelance', 'Internship/Magang', 'Remote') DEFAULT 'Full-time',
  `gaji_min` BIGINT NULL DEFAULT 0,
  `gaji_max` BIGINT NULL DEFAULT 0,
  `deskripsi` LONGTEXT NOT NULL,
  `kualifikasi` LONGTEXT NULL,
  `kontak_lamaran` VARCHAR(255) NOT NULL,
  `logo_perusahaan` VARCHAR(255) NULL,
  `status` ENUM('open', 'closed', 'pending') DEFAULT 'open',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`user_id`),
  INDEX (`status`),
  INDEX (`tipe_pekerjaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. TABEL: messages (Ruang Chatting Santri)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `room` VARCHAR(50) DEFAULT 'general',
  `pesan` TEXT NOT NULL,
  `lampiran` VARCHAR(255) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`user_id`),
  INDEX (`room`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. TABEL: site_content (Konten Publik: Visi Misi & Tujuan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_content` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. TABEL: gallery (Galeri Dokumentasi & Kenangan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL DEFAULT 1,
  `judul` VARCHAR(150) NOT NULL,
  `kategori` VARCHAR(50) DEFAULT 'Kegiatan',
  `gambar` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. TABEL: activity_logs (Audit Trail & Keamanan)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(50) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`user_id`),
  INDEX (`action`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DATA AWAL (SEED DATA)
-- ========================================================

-- Kredensial Login Default:
-- 1. Admin: admin@forsakda27.com (atau username: Admin Pusat FORSAKDA) | Password: adminforsakda27
-- 2. Santri: santri1@forsakda27.com | Password: santriforsakda27
-- 3. Santri: santri2@forsakda27.com | Password: santriforsakda27

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `status`, `angkatan`, `no_hp`, `domisili`, `profesi`, `bio`, `foto`, `created_at`) VALUES
(1, 'Admin Pusat FORSAKDA', 'admin@forsakda27.com', '$2y$10$3ulmUzI0Q6SGw2Az5.fnne68WjFaKDy8GbEuhW.Vzx7Von4bFIJii', 'admin', 'active', '27', '081234567890', 'Jawa Timur', 'Pengurus FORSAKDA 27', 'Khadimul Ummah & Administrator Pusat Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27).', NULL, NOW()),
(2, 'Ust. Ahmad Fauzi, S.Kom.', 'santri1@forsakda27.com', '$2y$10$.8R7Oz9gVpFlpfBoDXoODe3cLivmb26Uxz4T1edfKA6B9ncQ/LFd.', 'anggota', 'active', '27', '081298765432', 'Jakarta Selatan', 'Software Engineer & Pegiat Dakwah', 'Santri Kelas Dua Angkatan 27 yang berkecimpung di bidang teknologi informasi dan rekayasa perangkat lunak.', NULL, NOW()),
(3, 'Muhammad Ridwan, S.E.', 'santri2@forsakda27.com', '$2y$10$.8R7Oz9gVpFlpfBoDXoODe3cLivmb26Uxz4T1edfKA6B9ncQ/LFd.', 'anggota', 'active', '27', '081345678901', 'Surabaya', 'Wirausahawan Kuliner Halal', 'Founder Berkah Santri Food. Anggota FORSAKDA 27 yang siap berkolaborasi dan berbagi peluang bisnis.', NULL, NOW()),
(4, 'Zulfa Hidayatullah, M.Pd.', 'santri3@forsakda27.com', '$2y$10$.8R7Oz9gVpFlpfBoDXoODe3cLivmb26Uxz4T1edfKA6B9ncQ/LFd.', 'anggota', 'active', '27', '081567890123', 'Yogyakarta', 'Dosen & Pembina Pesantren', 'Mengajar di perguruan tinggi Islam dan membina halaqah tahfidz santri.', NULL, NOW()),
(5, 'Hilman Syarifudin', 'santri4@forsakda27.com', '$2y$10$.8R7Oz9gVpFlpfBoDXoODe3cLivmb26Uxz4T1edfKA6B9ncQ/LFd.', 'anggota', 'pending', '27', '081789012345', 'Bandung', 'Graphic Designer', 'Santri kelas dua yang menunggu verifikasi keanggotaan oleh admin.', NULL, NOW())
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- DATA KONTEN SITUS (VISI, MISI, TUJUAN WEB, SEJARAH)
INSERT INTO `site_content` (`key_name`, `title`, `content`, `updated_at`) VALUES
('visi_misi', 'Visi & Misi FORSAKDA 27', '{"visi": "Menjadi wadah silaturahmi Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27) yang solid, mandiri, berakhlakul karimah, serta berdaya saing tinggi untuk kemajuan almamater, umat, dan bangsa.", "misi": ["Mempererat tali ukhuwah islamiyah dan kekeluargaan antar Forum Santri Kelas Dua Angkatan 27.", "Membangun sinergi belajar, wirausaha, karir, dan dakwah melalui jejaring santri dan bursa lowongan kerja.", "Mewadahi pertukaran ilmu dan pengalaman santri melalui ruang diskusi chatting dan portal digital modern.", "Melestarikan nilai-nilai kepesantrenan dan memberikan sumbangsih nyata bagi pondok serta masyarakat."], "nilai_utama": ["Ukhuwah Islamiyah (Persaudaraan yang tulus)", "Integritas & Adab Santri (Menjaga kejujuran dan akhlak)", "Kemandirian Santri (Berdikari dalam karya dan ikhtiar)", "Khidmah Lil Ummah (Pengabdian untuk masyarakat)"]}', NOW()),
('tujuan_web', 'Tujuan Pembuatan Website FORSAKDA 27', '{"tujuan": ["Pusat Informasi Terpadu: Menyajikan warta resmi almamater, kegiatan santri, dan pengumuman keluarga besar FORSAKDA 27.", "Jejaring Komunikasi Interaktif: Menyediakan ruang chatting dan forum diskusi aman sesama anggota santri kelas dua.", "Pusat Karir & Info Loker Santri: Memfasilitasi anggota dalam membagikan lowongan pekerjaan dan peluang usaha halal.", "Direktori Santri & Kontak: Mendata kontak, profesi, dan domisili santri kelas dua untuk kemudahan koordinasi."], "sejarah": "FORSAKDA 27 (Forum Santri Kelas Dua Angkatan 27) dibentuk sebagai wadah kebersamaan dan persaudaraan santri kelas dua angkatan 27 dalam menimba ilmu, mengasah potensi, dan merajut masa depan yang gemilang. Website ini dirancang sebagai jembatan digital agar komunikasi dan silaturahmi antar santri selalu terjalin erat.", "sambutan_ketua": "Alhamdulillah, puji syukur kehadirat Allah SWT. Platform digital FORSAKDA 27 (Forum Santri Kelas Dua 27) hadir sebagai sarana silaturahmi, belajar bersama, bertukar informasi warta, serta saling mendukung dalam karir dan karya. Mari kita jaga ukhuwah ini dengan sebaik-baiknya."}', NOW())
ON DUPLICATE KEY UPDATE `key_name` = VALUES(`key_name`);

-- DATA BERITA & ARTIKEL SANTRI
INSERT INTO `news` (`id`, `user_id`, `judul`, `slug`, `kategori`, `ringkasan`, `konten`, `gambar`, `status`, `is_pinned`, `views`, `created_at`) VALUES
(1, 1, 'Silaturahmi Akbar & Agenda Kegiatan FORSAKDA 27 Siap Digelar', 'silaturahmi-akbar-agenda-kegiatan-forsakda-27', 'Kegiatan', 'Pengurus FORSAKDA 27 mengumumkan persiapan agenda silaturahmi santri kelas dua angkatan 27.', '<p>Assalamu’alaikum Warahmatullahi Wabarakatuh.</p><p>Kabar gembira untuk seluruh keluarga besar Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27). Pengurus telah menjadwalkan kegiatan silaturahmi akbar dan kajian bersama.</p><p>Acara ini akan diisi dengan khataman Al-Qur’an bersama, diskusi keilmuan, bakti sosial, serta sarasehan pengembangan potensi santri. Mari persiapkan diri untuk hadir dan mempererat ukhuwah!</p>', NULL, 'published', 1, 350, NOW()),
(2, 2, 'Santri Kelas Dua Menatap Masa Depan: Memadukan Ilmu Agama dan Teknologi', 'santri-kelas-dua-menatap-masa-depan', 'Opini', 'Ulasan inspiratif mengenai bekal karakter pesantren yang menjadi fondasi utama bagi santri di era teknologi.', '<p>Santri tidak hanya tekun muthala’ah kitab kuning, namun juga mampu beradaptasi dengan teknologi modern. Disiplin, adab, dan ketekunan yang diajarkan para kiai adalah bekal tak ternilai.</p><p>Melalui wadah FORSAKDA 27, kita saling menguatkan langkah untuk meraih cita-cita mulia.</p>', NULL, 'published', 0, 225, NOW()),
(3, 1, 'Program Wakaf Buku Kitab & Solidaritas Santri FORSAKDA 27', 'program-wakaf-buku-kitab-solidaritas-santri-forsakda-27', 'Sosial', 'FORSAKDA 27 meluncurkan program donasi wakaf kitab dan buku referensi untuk perpustakaan santri.', '<p>Sebagai wujud kepedulian bersama, FORSAKDA 27 menginisiasi penggalangan dana wakaf kitab dan sarana belajar santri. Setiap kontribusi akan disalurkan secara amanah.</p><p>Semoga menjadi amal jariyah yang membawa keberkahan bagi kita semua.</p>', NULL, 'published', 0, 160, NOW())
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`);

-- DATA LOWONGAN PEKERJAAN (LOKER)
INSERT INTO `job_vacancies` (`id`, `user_id`, `posisi`, `perusahaan`, `lokasi`, `tipe_pekerjaan`, `gaji_min`, `gaji_max`, `deskripsi`, `kualifikasi`, `kontak_lamaran`, `logo_perusahaan`, `status`, `created_at`) VALUES
(1, 2, 'Junior Web Developer (PHP & Tailwind CSS)', 'PT Berkah Solusi Digital', 'Jakarta Selatan (Hybrid)', 'Full-time', 5000000, 8000000, 'Mencari rekan santri/alumni yang memiliki keahlian dalam pemrograman web PHP, database MySQL, dan styling Tailwind CSS untuk bergabung dengan tim pengembangan aplikasi kami.', '- Menguasai PHP Native / MVC dan MySQL (PDO)\n- Terbiasa dengan Tailwind CSS & JavaScript modern\n- Memiliki adab kerja yang baik, disiplin, dan jujur\n- Diutamakan santri FORSAKDA 27', 'hrd@berkahsolusidigital.id / WA: 081298765432', NULL, 'open', NOW()),
(2, 3, 'Supervisor Outlet Kuliner & Operasional', 'Berkah Santri Food Group', 'Surabaya', 'Full-time', 4000000, 6500000, 'Dibutuhkan supervisor outlet kuliner yang amanah untuk mengelola operasional harian, tim kasir, serta kontrol kualitas produk makanan halal.', '- Pendidikan minimal SMA/MA/Pondok Pesantren\n- Memiliki jiwa kepemimpinan & teliti\n- Memahami standar kebersihan dan kehalalan produk pangan', 'rekrutmen@berkahsantrifood.com', NULL, 'open', NOW()),
(3, 4, 'Tenaga Pendidik / Guru Tahfidz & Bahasa Arab', 'Pesantren Modern Al-Hikmah', 'Yogyakarta', 'Full-time', 3500000, 5000000, 'Kesempatan berkhidmah menjadi pengajar Al-Qur’an dan bahasa Arab untuk santri.', '- Memiliki hafalan minimal 5 Juz / Mutqin\n- Fasih berbahasa Arab aktif dan pasif\n- Bersedia tinggal di asrama pesantren', 'admin@pesantrenalhikmah.sch.id', NULL, 'open', NOW())
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`);

-- DATA PESAN CHAT RUANG SANTRI
INSERT INTO `messages` (`id`, `user_id`, `room`, `pesan`, `lampiran`, `created_at`) VALUES
(1, 1, 'general', 'Assalamu’alaikum Warahmatullahi Wabarakatuh sahabat FORSAKDA 27! Selamat datang di Ruang Chat Resmi Forum Santri Kelas Dua.', NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 2, 'general', 'Wa’alaikumussalam Warahmatullah! MasyaAllah, alhamdulillah web FORSAKDA 27 sudah aktif. Salam takzim untuk antum semua dari Jakarta.', NULL, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(3, 3, 'general', 'Alhamdulillah, salam hangat kabeh konco-konco santri kelas dua 27 dari Surabaya! Jangan lupa cek info loker dan warta terbaru ya.', NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(4, 4, 'general', 'Semoga wadah silaturahmi FORSAKDA 27 ini membawa keberkahan dan mempererat persaudaraan kita. Aamiin ya Rabbal \'Alamin.', NULL, DATE_SUB(NOW(), INTERVAL 30 MINUTE))
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`);
