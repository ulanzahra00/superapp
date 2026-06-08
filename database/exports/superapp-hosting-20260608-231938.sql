-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: superapp
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `status` enum('hadir','izin','sakit','alfa') NOT NULL DEFAULT 'hadir',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_student_id_foreign` (`student_id`),
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `courses_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,2,'Matematika','XI IPA 1','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 09:20:45','2026-06-08 09:20:45'),(2,2,'Matematika','XI IPA 1','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 13:06:06','2026-06-08 13:06:06'),(3,2,'Matematika','XI IPA 1','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 13:12:03','2026-06-08 13:12:03'),(4,2,'Matematika','Lintas Kelas','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 13:14:46','2026-06-08 13:14:46'),(5,2,'Bahasa Indonesia','Lintas Kelas','Literasi, teks argumentasi, dan presentasi.','2026-06-08 13:14:46','2026-06-08 13:14:46'),(6,2,'Bahasa Inggris','Lintas Kelas','Reading, speaking, dan writing project.','2026-06-08 13:14:46','2026-06-08 13:14:46'),(7,2,'Fisika','Lintas Kelas','Gerak, energi, dan praktikum sederhana.','2026-06-08 13:14:46','2026-06-08 13:14:46'),(8,2,'Sejarah','Lintas Kelas','Kajian sejarah Indonesia dan diskusi kelas.','2026-06-08 13:14:46','2026-06-08 13:14:46'),(9,2,'Matematika','Lintas Kelas','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 13:25:46','2026-06-08 13:25:46'),(10,2,'Bahasa Indonesia','Lintas Kelas','Literasi, teks argumentasi, dan presentasi.','2026-06-08 13:25:46','2026-06-08 13:25:46'),(11,2,'Bahasa Inggris','Lintas Kelas','Reading, speaking, dan writing project.','2026-06-08 13:25:46','2026-06-08 13:25:46'),(12,2,'Fisika','Lintas Kelas','Gerak, energi, dan praktikum sederhana.','2026-06-08 13:25:46','2026-06-08 13:25:46'),(13,2,'Sejarah','Lintas Kelas','Kajian sejarah Indonesia dan diskusi kelas.','2026-06-08 13:25:46','2026-06-08 13:25:46'),(14,2,'Matematika','Lintas Kelas','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 13:53:48','2026-06-08 13:53:48'),(15,2,'Bahasa Indonesia','Lintas Kelas','Literasi, teks argumentasi, dan presentasi.','2026-06-08 13:53:48','2026-06-08 13:53:48'),(16,2,'Bahasa Inggris','Lintas Kelas','Reading, speaking, dan writing project.','2026-06-08 13:53:48','2026-06-08 13:53:48'),(17,2,'Fisika','Lintas Kelas','Gerak, energi, dan praktikum sederhana.','2026-06-08 13:53:48','2026-06-08 13:53:48'),(18,2,'Sejarah','Lintas Kelas','Kajian sejarah Indonesia dan diskusi kelas.','2026-06-08 13:53:48','2026-06-08 13:53:48'),(19,2,'Matematika','Lintas Kelas','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 14:07:46','2026-06-08 14:07:46'),(20,2,'Bahasa Indonesia','Lintas Kelas','Literasi, teks argumentasi, dan presentasi.','2026-06-08 14:07:46','2026-06-08 14:07:46'),(21,2,'Bahasa Inggris','Lintas Kelas','Reading, speaking, dan writing project.','2026-06-08 14:07:46','2026-06-08 14:07:46'),(22,2,'Fisika','Lintas Kelas','Gerak, energi, dan praktikum sederhana.','2026-06-08 14:07:46','2026-06-08 14:07:46'),(23,2,'Sejarah','Lintas Kelas','Kajian sejarah Indonesia dan diskusi kelas.','2026-06-08 14:07:46','2026-06-08 14:07:46'),(24,2,'Matematika','Lintas Kelas','Materi fungsi, turunan, dan latihan adaptif.','2026-06-08 14:30:37','2026-06-08 14:30:37'),(25,2,'Bahasa Indonesia','Lintas Kelas','Literasi, teks argumentasi, dan presentasi.','2026-06-08 14:30:37','2026-06-08 14:30:37'),(26,2,'Bahasa Inggris','Lintas Kelas','Reading, speaking, dan writing project.','2026-06-08 14:30:37','2026-06-08 14:30:37'),(27,2,'Fisika','Lintas Kelas','Gerak, energi, dan praktikum sederhana.','2026-06-08 14:30:37','2026-06-08 14:30:37'),(28,2,'Sejarah','Lintas Kelas','Kajian sejarah Indonesia dan diskusi kelas.','2026-06-08 14:30:37','2026-06-08 14:30:37');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `semester` varchar(255) NOT NULL DEFAULT 'Ganjil',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grades_student_id_foreign` (`student_id`),
  KEY `grades_course_id_foreign` (`course_id`),
  CONSTRAINT `grades_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (26,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 13:12:04','2026-06-08 13:12:04'),(52,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 13:14:46','2026-06-08 13:14:46'),(78,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 13:25:47','2026-06-08 13:25:47'),(104,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 13:53:49','2026-06-08 13:53:49'),(130,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 14:07:46','2026-06-08 14:07:46'),(156,1,2,'Mohon rekap komunikasi siswa minggu ini diperiksa sebelum rapat wali kelas.',NULL,'2026-06-08 14:30:37','2026-06-08 14:30:37');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_06_08_000001_create_school_core_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Sekolah',
  `cover_color` varchar(255) NOT NULL DEFAULT 'emerald',
  `excerpt` text NOT NULL,
  `content` longtext NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_author_id_foreign` (`author_id`),
  CONSTRAINT `news_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,1,'Transformasi Digital Sekolah','transformasi-digital-sekolah-0','Pengumuman','emerald','Informasi terbaru untuk warga sekolah dan orang tua.','Sekolah menghadirkan layanan digital terpadu untuk absensi, LMS, nilai, komunikasi, berita, serta monitoring karakter siswa.','2026-06-08 14:30:37','2026-06-08 09:20:45','2026-06-08 14:30:37'),(2,1,'Agenda Projek P5','agenda-projek-p5-1','Kegiatan','amber','Informasi terbaru untuk warga sekolah dan orang tua.','Sekolah menghadirkan layanan digital terpadu untuk absensi, LMS, nilai, komunikasi, berita, serta monitoring karakter siswa.','2026-06-07 14:30:37','2026-06-08 09:20:45','2026-06-08 14:30:37'),(3,1,'Prestasi Siswa Pekan Ini','prestasi-siswa-pekan-ini-2','Kegiatan','rose','Informasi terbaru untuk warga sekolah dan orang tua.','Sekolah menghadirkan layanan digital terpadu untuk absensi, LMS, nilai, komunikasi, berita, serta monitoring karakter siswa.','2026-06-06 14:30:37','2026-06-08 09:20:45','2026-06-08 14:30:37');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sanctions`
--

DROP TABLE IF EXISTS `sanctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sanctions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `total_points` int(11) NOT NULL,
  `sanction_type` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sanctions_student_id_foreign` (`student_id`),
  CONSTRAINT `sanctions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sanctions`
--

LOCK TABLES `sanctions` WRITE;
/*!40000 ALTER TABLE `sanctions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sanctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_notifications`
--

DROP TABLE IF EXISTS `school_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'info',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `school_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_notifications`
--

LOCK TABLES `school_notifications` WRITE;
/*!40000 ALTER TABLE `school_notifications` DISABLE KEYS */;
INSERT INTO `school_notifications` VALUES (1,3,'Pelanggaran siswa','Andi Pratama mendapat -20 poin: Bolos.','warning',NULL,'2026-06-08 09:20:45','2026-06-08 09:20:45'),(2,3,'Pelanggaran siswa','Andi Pratama mendapat -20 poin: Bolos.','warning',NULL,'2026-06-08 13:06:06','2026-06-08 13:06:06'),(3,3,'Pelanggaran siswa','Andi Pratama mendapat -5 poin: Terlambat','warning',NULL,'2026-06-08 13:09:06','2026-06-08 13:09:06'),(4,3,'Pelanggaran siswa','Andi Pratama mendapat -20 poin: Bolos.','warning',NULL,'2026-06-08 13:12:03','2026-06-08 13:12:03'),(5,3,'Pelanggaran siswa','Andi Pratama mendapat -20 poin: Bolos.','warning',NULL,'2026-06-08 13:14:46','2026-06-08 13:14:46'),(6,3,'Pelanggaran siswa','Andi Pratama mendapat -20 poin: Bolos.','warning',NULL,'2026-06-08 13:25:46','2026-06-08 13:25:46'),(7,24,'Pelanggaran siswa','sahyudi amparodo mendapat -2 poin: Tidak memakai atribut saat upacara','warning',NULL,'2026-06-08 15:06:31','2026-06-08 15:06:31');
/*!40000 ALTER TABLE `school_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_points`
--

DROP TABLE IF EXISTS `student_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('prestasi','pelanggaran') NOT NULL,
  `category` enum('Disiplin','Tanggung Jawab','Kejujuran','Kerjasama') NOT NULL,
  `point` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `occurred_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_points_student_id_foreign` (`student_id`),
  KEY `student_points_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `student_points_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_points_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_points`
--

LOCK TABLES `student_points` WRITE;
/*!40000 ALTER TABLE `student_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa','orang_tua') NOT NULL DEFAULT 'siswa',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `nis` varchar(255) DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nis_unique` (`nis`),
  KEY `users_parent_id_foreign` (`parent_id`),
  CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Kepala Sekolah','admin@sekolah.test','admin',NULL,NULL,NULL,'0811000001',NULL,'$2y$10$2g44nWdUrH0hJQT0npuk5u8pNmm1BBHmPuo7VysomK8AOHrT6hS3u',NULL,'2026-06-08 09:20:45','2026-06-08 14:30:35'),(2,'Ibu Guru Maya','guru@sekolah.test','guru',NULL,NULL,NULL,'0811000002',NULL,'$2y$10$RrS/qGMSAxlIAFp/qM.r.u.v7DuVw2/QY2.XtvJbi0yxmbQNje41.',NULL,'2026-06-08 09:20:45','2026-06-08 14:30:35'),(3,'Bapak Andi','ortu@sekolah.test','orang_tua',NULL,NULL,NULL,'0811000003',NULL,'$2y$10$BXgPd1cdsuVLHrRGh/Ar4.zivh.U4icGkplR9ngIEp30jAPVxpMGa',NULL,'2026-06-08 09:20:45','2026-06-08 14:30:35'),(24,'suparman','ortu.contoh@sekolah.test','orang_tua',NULL,NULL,NULL,NULL,NULL,'$2y$10$YA3W2Z88Bzt.Mmdgd9WjCuG2zI2XtFOgH13Bzdfi4.2aZusMo8sve',NULL,'2026-06-08 15:05:07','2026-06-08 15:05:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'superapp'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-08 23:19:38
