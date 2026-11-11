-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: buku_tamu
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bidangs`
--

DROP TABLE IF EXISTS `bidangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bidangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bidangs`
--

LOCK TABLES `bidangs` WRITE;
/*!40000 ALTER TABLE `bidangs` DISABLE KEYS */;
INSERT INTO `bidangs` VALUES (1,'Pimpinan','2026-10-20 20:38:26','2026-10-20 20:38:26'),(2,'Sekretariat','2026-10-20 20:38:26','2026-10-20 20:38:26'),(3,'Bidang Umum','2026-10-20 20:38:26','2026-10-20 20:38:26'),(4,'Bidang Perencanaan','2026-10-20 20:38:26','2026-10-20 20:38:26'),(5,'Layanan Umum','2026-10-20 20:38:26','2026-10-20 20:38:26');
/*!40000 ALTER TABLE `bidangs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1792647483),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1792647483;',1792647483);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kunjungans`
--

DROP TABLE IF EXISTS `kunjungans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kunjungans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_kunjungan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_identitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'KTP',
  `nomor_identitas` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_identitas_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang_id` bigint unsigned NOT NULL,
  `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_pendamping` tinyint unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `waktu_masuk` timestamp NOT NULL,
  `waktu_keluar` timestamp NULL DEFAULT NULL,
  `foto_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanda_tangan_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setuju_data` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunjungans_nomor_kunjungan_unique` (`nomor_kunjungan`),
  KEY `kunjungans_bidang_id_foreign` (`bidang_id`),
  KEY `kunjungans_nomor_identitas_hash_index` (`nomor_identitas_hash`),
  CONSTRAINT `kunjungans_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `bidangs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kunjungans`
--

LOCK TABLES `kunjungans` WRITE;
/*!40000 ALTER TABLE `kunjungans` DISABLE KEYS */;
INSERT INTO `kunjungans` VALUES (1,'KJ-20261021-0001','doni','KTP','eyJpdiI6InU3QWx2NGNCSTFlckVrczg3OEdOR2c9PSIsInZhbHVlIjoiZTYxSEFhRWR1T1hkcTdrYllHczlrcE9HRlJuYlYzK1JEQUIxNU5pZVplTT0iLCJtYWMiOiJlNGQ4MjkzMGVhMDJkZjdkZWJlMDM4MWE3MjUyYjNkODZhZmM2NTZkZDc4ZTE5M2ZiNmUzNWZlMGYxMzY4OGU4IiwidGFnIjoiIn0=','7b68f9185cb3d41ef6684bceb77a09b15135d401a015e459f678e6dd1ea2d6ef','pemda','pemda','08951612723','ssaaawawawa@gmail.com',4,'awkakwawaw',1,'menunggu','2026-10-20 21:56:51',NULL,NULL,NULL,1,'2026-10-20 21:56:51','2026-10-20 21:56:51',NULL),(2,'KJ-20261021-0002','dini','KTP','eyJpdiI6IlhJVU5EeUZ0NjB5V1NKOGRFZVcvdHc9PSIsInZhbHVlIjoicEpObUVsT3QzSHBnNFJoaTBLVzRyUnRKVHY2TGdEbFIxV0xxVy8vb08vVT0iLCJtYWMiOiJlOTA3OTVhNTljYjBiMjc4YmUyZWU5MjIwYzQ5MWQzNDBkZjA5NzgxZDdhM2Q1ZTE5YWY0Y2U0ODgyZjQ2N2VkIiwidGFnIjoiIn0=','a321459c9dfe32cbe7e3f6ddfc635275813afa13588b1da684e2da9c37c4cf92','pemda','pemda','08951122231','ssasawas@gmail.com',1,'awaw',1,'menunggu','2026-10-20 22:14:39',NULL,'kunjungan/foto/b319c276-578b-4b36-aaa8-ba0bb453636f.jpg','kunjungan/tanda-tangan/70b48462-3c41-480d-a50b-702e82250587.png',1,'2026-10-20 22:14:39','2026-10-20 22:14:39',NULL),(3,'KJ-20261021-0003','kopak','KTP','eyJpdiI6InJNVTN4YkJ5RFE2ZEZGcDg4OEtoa2c9PSIsInZhbHVlIjoiYzlycGdMWDVKS3RyNHRQenhwRFFHU09mcVBmN3NHSTBYcDd1Qnd1RTljQT0iLCJtYWMiOiI1N2EwOGYxZDY2N2E0Yzg3MGZiMDE2OWY0MzVmYTkzM2Y0MmNhMWM2NjRiOTJiZmJhOGE5MjYxZjVlZWE2NjdhIiwidGFnIjoiIn0=','a321459c9dfe32cbe7e3f6ddfc635275813afa13588b1da684e2da9c37c4cf92','pemda','pemda','08951122231','sayutisyaputra@gmail.com',4,'aaa',1,'selesai','2026-10-21 07:55:48','2026-10-21 07:57:23','kunjungan/foto/fff5ad48-006c-43ed-a2b5-22bdfac7107d.jpg','kunjungan/tanda-tangan/0e0674ae-799e-42f3-ab92-646e23470a53.png',1,'2026-10-21 07:55:48','2026-10-21 07:59:19','2026-10-21 07:59:19'),(4,'KJ-20261021-0004','kopak','KTP','eyJpdiI6Ik1mUkpDTGFwNFBMaDZreWw5U2tIWVE9PSIsInZhbHVlIjoiZVdKcDY5WEpvUEVZOXlJRGJ3UEZjbGU1UGk4QTdTbDlNbS9pcitIaTU4OD0iLCJtYWMiOiJhMDA5Y2ExNDRlMGI5MzA1YjdmOTMyMDUzM2M2MDJkODIwNzU1YjllMTY3MGNlMGM3YWM5OGIyZDBiMTZhMjg2IiwidGFnIjoiIn0=','a321459c9dfe32cbe7e3f6ddfc635275813afa13588b1da684e2da9c37c4cf92','pemda','pemda','08951122231','sayutisyaputra@gmail.com',4,'aaa',1,'selesai','2026-10-21 07:55:48','2026-10-21 09:50:35','kunjungan/foto/763374c3-995f-4803-9c21-49ce9f8ab9c4.jpg','kunjungan/tanda-tangan/47bf0543-15fe-4a6e-86dd-d00beef62e07.png',1,'2026-10-21 07:55:48','2026-10-21 09:50:35',NULL);
/*!40000 ALTER TABLE `kunjungans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_21_032641_create_bidangs_table',2),(5,'2026_10_21_032648_create_kunjungans_table',2),(6,'2026_10_21_035103_add_role_to_users_table',3),(7,'2026_10_21_044705_ubah_foto_tanda_tangan_nullable',4),(8,'2026_10_21_152142_create_tamus_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('1LN8WFT4gRKnFWyrUD1InnlJ9tIvh9oLgrhSjwgO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJvZVdTZjlRbDZHOXZvWWx1MDQ1ZE92b0txUFJmcjlhZGRpZU1HT1RLIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1792646480),('1nDS3ttoaY4lVJN0maLaZKIc1XN4RK7UseYK36DL',3,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJSSVpWc29QOHJFWlBCQ1ozRm5JeExCUHZWUWxKUWFyOWo3TDNndENCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BldHVnYXNcL2t1bmp1bmdhblwvNCIsInJvdXRlIjoicGV0dWdhcy5rdW5qdW5nYW4uc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MywicGFzc3dvcmRfaGFzaF93ZWIiOiIxNTQ5YjEzNWQxODVjY2E0YzFlOGNiMmZhMDI5NTA3NjIwZGYwNGQxNDBmYWJkN2U2Nzk1M2M3ZWY1NmQ0MDNlIn0=',1792576286),('1ZnlmFrH1svgwpWrbwbL5XC29BR87OkOS81mDykB',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJCRW42NVd6VUtMWEg5aDBIVVpCbUc5ZmlsNk1FbjBKSVV5SWM2MmhZIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGFzc3dvcmRfaGFzaF93ZWIiOiI4ZDFiNTdlYjUwYTRmNmEzN2E5Nzg4YzI4ZjdhZmFlYTg4NjA5M2YzODgwODE3OWM3ZWM1MmVhOWU4NmM1NzAyIn0=',1792576222),('40diyiYMAJ22fax5W9ap7Qw9QwaYTYruYFqZKcNO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','eyJfdG9rZW4iOiJQZlp1c2ZEZWlybmJCc1ZCaERRS0FYUXd2Z0tyblo4dmpLM3huVW83IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2J1a3UtdGFtdS50ZXN0XC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1792597960),('pCoIzLcxTva9fEiqaqR5nCR9vvnS94bL8CrsCjsf',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','eyJfdG9rZW4iOiJUekd6M1pDTFo2OFdhMVVPVG1LWnU0aW1rTFV1em9laUdnT2hvYWR6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOnsiaW50ZW5kZWQiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvZGFzaGJvYXJkIn19',1792576136),('SkppwOtDg6MYVdB9piMgGdYkqWI9ybaI1Q9I0CFz',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJzdUhGTzBDSklXcHF3cGtLQmtoTG95OGM3ZjdLbU1QYXM4UlJiMEhFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2J1a3UtdGFtdS50ZXN0Iiwicm91dGUiOiJrdW5qdW5nYW4uY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsInBhc3N3b3JkX2hhc2hfd2ViIjoiOGQxYjU3ZWI1MGE0ZjZhMzdhOTc4OGMyOGY3YWZhZWE4ODYwOTNmMzg4MDgxNzljN2VjNTJlYTllODZjNTcwMiJ9',1792598339),('UtFN6R1qAOO6lI1YXcdeJr8Ft9gza8G0V5iSaNQd',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJEMmsyQUpKNllHV3gxSXdocEFpemVyZkc5R0NXQlB5UXhvR3NzdVlGIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvYnVrdS10YW11LnRlc3RcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1792647437);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tamus`
--

DROP TABLE IF EXISTS `tamus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tamus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_identitas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_pendamping` int unsigned NOT NULL DEFAULT '0',
  `keperluan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tamus`
--

LOCK TABLES `tamus` WRITE;
/*!40000 ALTER TABLE `tamus` DISABLE KEYS */;
/*!40000 ALTER TABLE `tamus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'petugas',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@bukutamu.test','admin',1,NULL,'$2y$12$.JYwEX7ulpXFU9lQn8yGau157mBInVuNnfyNZNyvVI/ld3MaqFSki',NULL,'2026-10-20 20:52:52','2026-10-20 20:52:52'),(2,'Petugas Resepsionis','petugas@bukutamu.test','petugas',1,NULL,'$2y$12$AyJ4FZq0aTEB7b2gNV12e..0NfmWUAhSOQwTZ2nOe/S/Nb.VgKXPG',NULL,'2026-10-20 20:52:52','2026-10-20 20:52:52'),(3,'Pimpinan','pimpinan@bukutamu.test','pimpinan',1,NULL,'$2y$12$vIypQrscXv4pjcdcEwrP5elMruwzTrRUVTz8iJm/D4tPBgjjZigVe',NULL,'2026-10-20 20:52:52','2026-10-20 20:52:52');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 12:39:00
