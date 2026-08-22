-- ========================================================
-- FORSAKDA 27 - Schema DDL
-- Forum Santri Kelas Dua Angkatan 27 (FORSAKDA 27)
-- ========================================================

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

CREATE TABLE IF NOT EXISTS `site_content` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
