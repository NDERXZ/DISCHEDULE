/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dischedule
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-0+deb13u1 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `team_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_user_id` (`user_id`),
  KEY `idx_activity_event_id` (`event_id`),
  KEY `idx_activity_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES
(1,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-15 12:10:00'),
(2,4,1,NULL,'comment_added','Added comment to event.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-15 12:11:11'),
(3,1,NULL,NULL,'login','User logged in successfully.','127.0.0.1','CLI','2026-08-15 13:00:57'),
(4,4,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-15 13:02:57'),
(5,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-15 13:12:13'),
(6,4,NULL,3,'team_created','Created team \'AHAY SMP\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-16 03:30:29'),
(7,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-16 03:31:21'),
(8,4,NULL,NULL,'webhook_created','Added Discord webhook \'# general\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:40:06'),
(9,4,6,NULL,'event_created','Created event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:45:16'),
(10,4,7,NULL,'event_created','Created event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:45:16'),
(11,4,6,NULL,'event_deleted','Deleted event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:45:31'),
(12,4,7,NULL,'event_updated','Updated event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:45:56'),
(13,4,7,NULL,'event_updated','Updated event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:46:27'),
(14,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 01:47:29'),
(15,4,NULL,NULL,'login','User logged in successfully.','127.0.0.1','curl/8.14.1','2026-08-18 02:16:37'),
(16,4,NULL,NULL,'api_token_created','Created API token \'nasi padang\'s token\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:28:31'),
(17,4,NULL,NULL,'api_token_revoked','Revoked API token #1.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:28:40'),
(18,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:29:26'),
(19,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:29:34'),
(20,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:29:41'),
(21,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:29:58'),
(22,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:30:08'),
(23,4,7,NULL,'event_updated','Updated event \'NASI PADANGGG\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:49:23'),
(24,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:56:31'),
(25,4,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 02:59:21'),
(26,4,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:11:19'),
(27,5,NULL,NULL,'register','New user account created.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:12:23'),
(28,5,NULL,NULL,'profile_updated','Updated profile information.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:13:13'),
(29,5,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:14:37'),
(30,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:14:40'),
(31,4,NULL,NULL,'admin_user_ban_toggle','Set user #5 status to banned.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:15:07'),
(32,4,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:15:17'),
(33,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:15:38'),
(34,4,NULL,NULL,'admin_user_ban_toggle','Set user #5 status to active.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:16:16'),
(35,4,8,NULL,'event_created','Created event \'AAAAA\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:18:28'),
(36,4,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:19:18'),
(37,5,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:19:30'),
(38,5,NULL,NULL,'logout','User signed out.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:19:47'),
(39,4,NULL,NULL,'login','User logged in successfully.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:19:52'),
(40,4,NULL,NULL,'admin_settings_updated','Updated system settings.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:24:10'),
(41,4,8,NULL,'event_updated','Updated event \'AAAAA\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:28:50'),
(42,4,8,NULL,'event_updated','Updated event \'AAAAA\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:37:02'),
(43,4,8,NULL,'event_updated','Updated event \'AAAAA\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:38:31'),
(44,4,8,NULL,'event_updated','Updated event \'AAAAA\'.','10.67.67.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-18 03:38:44');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `name` varchar(100) NOT NULL,
  `permissions` varchar(255) NOT NULL DEFAULT 'read,write',
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `idx_api_tokens_user_id` (`user_id`),
  KEY `idx_api_tokens_hash` (`token_hash`),
  CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_attachments_event_id` (`event_id`),
  KEY `idx_attachments_user_id` (`user_id`),
  CONSTRAINT `fk_attachments_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attachments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES
(1,8,4,'71d81ed523d0bd50fecd3041f270d29b.png','ascii-art-text.png','image/png',149504,'/var/www/html/storage/uploads/71d81ed523d0bd50fecd3041f270d29b.png','2026-08-18 03:18:28');
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `team_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#4F46E5',
  `icon` varchar(50) NOT NULL DEFAULT 'fa-solid fa-calendar',
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_categories_user_id` (`user_id`),
  KEY `idx_categories_team_id` (`team_id`),
  KEY `idx_categories_global` (`is_global`),
  CONSTRAINT `fk_categories_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_categories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,NULL,NULL,'Work & Engineering','#4F46E5','fa-solid fa-briefcase',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(2,NULL,NULL,'Meetings & Syncs','#0EA5E9','fa-solid fa-users',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(3,NULL,NULL,'Deadlines & Launches','#EF4444','fa-solid fa-fire',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(4,NULL,NULL,'Personal & Life','#10B981','fa-solid fa-user',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(5,NULL,NULL,'Health & Fitness','#EC4899','fa-solid fa-heart-pulse',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(6,NULL,NULL,'Community & Discord','#5865F2','fa-brands fa-discord',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(7,3,NULL,'Side Projects','#8B5CF6','fa-solid fa-rocket',0,'2026-08-15 12:01:06','2026-08-15 12:01:06');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comments_event_id` (`event_id`),
  KEY `idx_comments_user_id` (`user_id`),
  CONSTRAINT `fk_comments_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
(1,1,4,'bagaimana jika kita entot?','2026-08-15 19:11:11','2026-08-15 12:11:11');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `event_instances`
--

DROP TABLE IF EXISTS `event_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_instances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `status` enum('scheduled','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_instances_event_id` (`event_id`),
  KEY `idx_instances_range` (`start_datetime`,`end_datetime`),
  CONSTRAINT `fk_instances_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_instances`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `event_instances` WRITE;
/*!40000 ALTER TABLE `event_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_instances` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `event_tag`
--

DROP TABLE IF EXISTS `event_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_tag` (
  `event_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`tag_id`),
  KEY `idx_event_tag_tag_id` (`tag_id`),
  CONSTRAINT `fk_event_tag_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_tag`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `event_tag` WRITE;
/*!40000 ALTER TABLE `event_tag` DISABLE KEYS */;
INSERT INTO `event_tag` VALUES
(1,1),
(2,1),
(4,2),
(3,3),
(2,4),
(8,6),
(8,7),
(8,8),
(8,9);
/*!40000 ALTER TABLE `event_tag` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `event_webhook`
--

DROP TABLE IF EXISTS `event_webhook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_webhook` (
  `event_id` bigint(20) unsigned NOT NULL,
  `webhook_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`webhook_id`),
  KEY `fk_event_webhook_webhook` (`webhook_id`),
  CONSTRAINT `fk_event_webhook_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_webhook_webhook` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_webhook`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `event_webhook` WRITE;
/*!40000 ALTER TABLE `event_webhook` DISABLE KEYS */;
INSERT INTO `event_webhook` VALUES
(1,1),
(2,1),
(3,2),
(4,2),
(7,3),
(8,3);
/*!40000 ALTER TABLE `event_webhook` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `location` varchar(255) DEFAULT NULL,
  `location_url` varchar(500) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'published',
  `visibility` enum('private','public','shared') NOT NULL DEFAULT 'private',
  `share_token` varchar(64) DEFAULT NULL,
  `share_password_hash` varchar(255) DEFAULT NULL,
  `share_expires_at` datetime DEFAULT NULL,
  `recurrence_rule` varchar(255) DEFAULT NULL,
  `recurrence_end` datetime DEFAULT NULL,
  `recurrence_count` int(10) unsigned DEFAULT NULL,
  `parent_event_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `share_token` (`share_token`),
  KEY `idx_events_user_id` (`user_id`),
  KEY `idx_events_category_id` (`category_id`),
  KEY `idx_events_start_datetime` (`start_datetime`),
  KEY `idx_events_end_datetime` (`end_datetime`),
  KEY `idx_events_status` (`status`),
  KEY `idx_events_visibility` (`visibility`),
  KEY `idx_events_share_token` (`share_token`),
  KEY `fk_events_parent` (`parent_event_id`),
  CONSTRAINT `fk_events_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_events_parent` FOREIGN KEY (`parent_event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES
(1,1,2,'Daily Engineering Standup','daily-engineering-standup','Review blockers, yesterday achievements, and today goals for the engineering team.','2026-08-15 09:00:00','2026-08-15 09:30:00','UTC','Discord Voice Channel #standup','https://discord.gg/example','#0EA5E9','medium','published','public','token_standup_123',NULL,NULL,'FREQ=DAILY',NULL,NULL,NULL,'2026-08-15 12:01:06','2026-08-18 03:14:52','2026-08-18 03:14:52'),
(2,1,1,'Sprint 42 Planning & Backlog Grooming','sprint-42-planning','Plan next two weeks deliverables, story point estimation and Discord bot integrations.','2026-08-15 14:00:00','2026-08-15 15:30:00','UTC','Conference Room A / Google Meet','https://meet.google.com/abc-defg-hij','#4F46E5','high','published','public','token_sprint_456',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-15 12:01:06','2026-08-18 03:14:52','2026-08-18 03:14:52'),
(3,3,3,'Dischedule v2.0 Production Release','dischedule-v2-production-release','Deploy new high-performance native PHP backend and Discord OAuth2 sync.','2026-08-15 18:00:00','2026-08-15 19:00:00','UTC','Production Server Pipeline',NULL,'#EF4444','high','published','public','token_release_789',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-15 12:01:06','2026-08-18 03:14:52','2026-08-18 03:14:52'),
(4,3,6,'Discord Community Townhall & AMA','discord-community-townhall-ama','Live Q&A with server members discussing upcoming Discord webhook features.','2026-08-17 16:00:00','2026-08-17 17:30:00','UTC','Discord Stage Channel #stage','https://discord.gg/example-stage','#5865F2','high','published','public','token_townhall_321',NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-15 12:01:06','2026-08-18 03:14:52','2026-08-18 03:14:52'),
(5,1,5,'Evening Gym Workout','evening-gym-workout','Cardio and strength training session.','2026-08-15 19:30:00','2026-08-15 20:45:00','UTC','Fitness Center',NULL,'#EC4899','low','published','private',NULL,NULL,NULL,'FREQ=WEEKLY;BYDAY=MO,WE,FR',NULL,NULL,NULL,'2026-08-15 12:01:06','2026-08-15 12:01:06',NULL),
(6,4,4,'NASI PADANGGG','nasi-padanggg-e50021','','2026-08-18 02:00:00','2026-08-18 03:00:00','Asia/Jakarta','jawa',NULL,NULL,'high','published','public',NULL,NULL,NULL,'FREQ=DAILY',NULL,NULL,NULL,'2026-08-18 08:45:15','2026-08-18 01:45:30','2026-08-18 08:45:30'),
(7,4,4,'NASI PADANGGG','nasi-padanggg-1fd8d0','','2026-08-17 03:00:00','2026-08-17 16:00:00','Asia/Jakarta','jawa','','#4f46e5','high','published','public','c13958e8dc6b9e577442b81bf82ec69806ac1c8f',NULL,NULL,'FREQ=DAILY',NULL,NULL,NULL,'2026-08-18 08:45:16','2026-08-18 03:14:52','2026-08-18 03:14:52'),
(8,4,6,'AAAAA','aaaaa-10c5a6','ngumpulin tekad','2026-08-18 03:39:00','2026-08-18 04:00:00','Asia/Jakarta','jawa',NULL,'#4f46e5','high','published','private',NULL,NULL,NULL,'FREQ=DAILY','2026-08-26 17:00:00',NULL,NULL,'2026-08-18 03:18:28','2026-08-18 03:38:44',NULL);
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_unread` (`user_id`,`is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
(1,1,'info','Welcome to Dischedule!','Your workspace is ready. Link your Discord account in Settings to enable real-time alerts.','/profile/discord',0,NULL,'2026-08-15 12:01:06'),
(2,1,'success','Webhook Connected','Default admin webhook has been configured successfully.','/webhooks',0,NULL,'2026-08-15 12:01:06'),
(3,3,'warning','Upcoming Release Event','Dischedule v2.0 Production Release is scheduled for today.','/events/3',0,NULL,'2026-08-15 12:01:06'),
(4,4,'success','Event Scheduled: NASI PADANGGG','Your event \'NASI PADANGGG\' was successfully created.','/events/6',1,'2026-08-18 09:27:36','2026-08-18 01:45:15'),
(5,4,'success','Event Scheduled: NASI PADANGGG','Your event \'NASI PADANGGG\' was successfully created.','/events/7',1,'2026-08-18 09:27:36','2026-08-18 01:45:16'),
(6,4,'warning','Event Cancelled: NASI PADANGGG','Event \'NASI PADANGGG\' has been removed from your calendar.','/calendar',1,'2026-08-18 09:27:36','2026-08-18 01:45:30'),
(7,4,'info','Event Updated: NASI PADANGGG','Event details for \'NASI PADANGGG\' have been modified.','/events/7',1,'2026-08-18 09:27:36','2026-08-18 01:45:56'),
(8,4,'info','Event Updated: NASI PADANGGG','Event details for \'NASI PADANGGG\' have been modified.','/events/7',1,'2026-08-18 09:27:36','2026-08-18 01:46:27'),
(9,4,'info','Event Updated: NASI PADANGGG','Event details for \'NASI PADANGGG\' have been modified.','/events/7',1,'2026-08-18 10:16:06','2026-08-18 02:49:23'),
(10,4,'success','Event Scheduled: AAAAA','Your event \'AAAAA\' was successfully created.','/events/8',0,NULL,'2026-08-18 03:18:28'),
(11,4,'info','Event Updated: AAAAA','Event details for \'AAAAA\' have been modified.','/events/8',0,NULL,'2026-08-18 03:28:49'),
(12,4,'info','Event Updated: AAAAA','Event details for \'AAAAA\' have been modified.','/events/8',0,NULL,'2026-08-18 03:37:02'),
(13,4,'info','Event Updated: AAAAA','Event details for \'AAAAA\' have been modified.','/events/8',0,NULL,'2026-08-18 03:38:31'),
(14,4,'info','Event Updated: AAAAA','Event details for \'AAAAA\' have been modified.','/events/8',0,NULL,'2026-08-18 03:38:44');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_limits` (
  `key_hash` varchar(64) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT 1,
  `reset_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`key_hash`),
  KEY `idx_rate_limits_reset_at` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES
('8c00b613a5bc0e5cadd403c0f31347e4',5,1787023779),
('99fd86c7c17b22c789ccde8f9a3c42c0',3,1787023603),
('c20fd7d0efeb94c751c1c4254e506a5f',1,1787020297);
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `reminders`
--

DROP TABLE IF EXISTS `reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reminders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `remind_at` datetime NOT NULL,
  `minutes_before` int(11) NOT NULL DEFAULT 15,
  `type` enum('email','discord','in_app') NOT NULL DEFAULT 'discord',
  `sent_at` datetime DEFAULT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reminders_event_id` (`event_id`),
  KEY `idx_reminders_status_remind_at` (`status`,`remind_at`),
  CONSTRAINT `fk_reminders_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reminders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `reminders` WRITE;
/*!40000 ALTER TABLE `reminders` DISABLE KEYS */;
INSERT INTO `reminders` VALUES
(1,1,'2026-08-15 08:45:00',15,'discord','2026-08-18 02:13:10','sent',NULL,'2026-08-15 12:01:06'),
(2,2,'2026-08-15 13:30:00',30,'discord','2026-08-18 02:13:11','sent',NULL,'2026-08-15 12:01:06'),
(3,3,'2026-08-15 17:00:00',60,'email','2026-08-18 02:13:11','sent',NULL,'2026-08-15 12:01:06'),
(4,4,'2026-08-17 15:00:00',60,'discord','2026-08-18 02:13:12','sent',NULL,'2026-08-15 12:01:06'),
(5,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:19:01','sent',NULL,'2026-08-18 03:18:28'),
(6,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:20:07','sent',NULL,'2026-08-18 03:19:01'),
(7,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:21:01','sent',NULL,'2026-08-18 03:20:07'),
(8,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:22:01','sent',NULL,'2026-08-18 03:21:01'),
(9,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:23:02','sent',NULL,'2026-08-18 03:22:01'),
(10,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:24:01','sent',NULL,'2026-08-18 03:23:02'),
(11,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:25:02','sent',NULL,'2026-08-18 03:24:01'),
(12,8,'2026-08-18 03:05:00',15,'discord','2026-08-18 03:26:01','sent',NULL,'2026-08-18 03:25:02'),
(13,8,'2026-08-18 03:05:00',15,'discord',NULL,'sent',NULL,'2026-08-18 03:26:01'),
(14,8,'2026-08-18 03:23:00',15,'discord','2026-08-18 03:38:02','failed','Call to protected method Core\\Model::db() from global scope','2026-08-18 03:37:02'),
(16,8,'2026-08-18 03:39:00',0,'discord','2026-08-18 03:39:02','failed','Call to protected method Core\\Model::db() from global scope','2026-08-18 03:38:44');
/*!40000 ALTER TABLE `reminders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user_id` (`user_id`),
  KEY `idx_sessions_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` longtext DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
('allow_registration','1','Enable public registration','2026-08-15 12:01:06'),
('default_timezone','UTC','Default system timezone','2026-08-15 12:01:06'),
('discord_avatar_url','https://i.imgur.com/DGosmDV.png','','2026-08-18 03:24:10'),
('discord_bot_name','Dischedule Bot','','2026-08-18 03:24:10'),
('discord_enabled','1','Enable Discord OAuth2 and Webhooks','2026-08-15 12:01:06'),
('discord_global_webhook_url','','','2026-08-18 03:24:10'),
('maintenance_mode','0','Put site in maintenance mode','2026-08-15 12:01:06'),
('site_description','Discord-integrated schedule & event management system','Meta description','2026-08-15 12:01:06'),
('site_name','Dischedule','Website brand name','2026-08-15 12:01:06');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_tags_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES
(1,'Sprint','sprint','2026-08-15 12:01:06'),
(2,'Discord','discord','2026-08-15 12:01:06'),
(3,'Release','release','2026-08-15 12:01:06'),
(4,'Planning','planning','2026-08-15 12:01:06'),
(5,'Review','review','2026-08-15 12:01:06'),
(6,'naspad padang','naspad-padang','2026-08-18 03:18:28'),
(7,'nyopag','nyopag','2026-08-18 03:18:28'),
(8,'madang','madang','2026-08-18 03:18:28'),
(9,'embege','embege','2026-08-18 03:18:28');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `team_events`
--

DROP TABLE IF EXISTS `team_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_events` (
  `team_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`team_id`,`event_id`),
  KEY `fk_team_events_event` (`event_id`),
  CONSTRAINT `fk_team_events_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_events_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_events`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `team_events` WRITE;
/*!40000 ALTER TABLE `team_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_events` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `team_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` enum('owner','admin','member') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_team_user` (`team_id`,`user_id`),
  KEY `idx_team_members_user_id` (`user_id`),
  CONSTRAINT `fk_team_members_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES
(1,1,1,'owner','2026-08-15 12:01:06'),
(2,1,2,'admin','2026-08-15 12:01:06'),
(3,1,3,'member','2026-08-15 12:01:06'),
(4,2,3,'owner','2026-08-15 12:01:06'),
(5,2,1,'member','2026-08-15 12:01:06'),
(6,3,4,'owner','2026-08-16 03:30:29');
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_teams_owner_id` (`owner_id`),
  CONSTRAINT `fk_teams_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES
(1,1,'Core Platform Team','Responsible for backend architecture, integrations, and releases.','2026-08-15 12:01:06','2026-08-15 12:01:06'),
(2,3,'Discord Community Squad','Managing community events, AMAs, and bot features.','2026-08-15 12:01:06','2026-08-15 12:01:06'),
(3,4,'AHAY SMP','','2026-08-16 10:30:29','2026-08-16 03:30:29');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `theme` varchar(20) NOT NULL DEFAULT 'system',
  `accent_color` varchar(20) NOT NULL DEFAULT '#4F46E5',
  `discord_id` varchar(50) DEFAULT NULL,
  `discord_username` varchar(100) DEFAULT NULL,
  `discord_avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `role` enum('admin','moderator','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `notify_discord` tinyint(1) NOT NULL DEFAULT 1,
  `notify_in_app` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `discord_id` (`discord_id`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_discord_id` (`discord_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@dischedule.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'System Administrator and Dischedule Lead','UTC','en','dark','#4F46E5',NULL,NULL,NULL,'2026-08-15 12:01:06',NULL,NULL,NULL,NULL,'admin','active',1,1,1,'2026-08-15 12:01:06','2026-08-15 12:01:06',NULL),
(2,'moderator','mod@dischedule.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'Community Moderator and Event Coordinator','UTC','en','system','#4F46E5',NULL,NULL,NULL,'2026-08-15 12:01:06',NULL,NULL,NULL,NULL,'moderator','active',1,1,1,'2026-08-15 12:01:06','2026-08-15 12:01:06',NULL),
(3,'demouser','demo@dischedule.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,'Product Engineer & Discord Enthusiast','UTC','en','system','#4F46E5',NULL,NULL,NULL,'2026-08-15 12:01:06',NULL,NULL,NULL,NULL,'user','active',1,1,1,'2026-08-15 12:01:06','2026-08-15 12:01:06',NULL),
(4,'ender','ender@ender.dev','$2y$12$3yhqghg2xt5jY3f8gGdq4OjIoJayxJ1M3NCQHL/f0yAQCARNx7d5q','/storage/uploads/avatar_4_1786851081.png','Known as Chaidar','Asia/Jakarta','en','system','#4F46E5',NULL,NULL,NULL,'2026-08-15 12:03:15',NULL,NULL,NULL,'5ff3161701df576710dc65458231bbd45aef3018257620c1d77b67ba3be099d2','admin','active',1,1,1,'2026-08-15 12:03:15','2026-08-18 03:19:52',NULL),
(5,'chaidar','chaidar@ender.dev','$2y$12$8oVhCh7h8ll21w..jvCQAeEpyxDk2aFrYGboWotI7xcrT1AbMixDu',NULL,NULL,'Asia/Jakarta','en','system','#4F46E5',NULL,NULL,NULL,NULL,'bb9697d2d9ed97d1a25606a46e3165932bc45205594dbcffa577a570e954e415',NULL,NULL,NULL,'user','active',1,1,1,'2026-08-18 03:12:22','2026-08-18 03:19:47',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `webhook_logs`
--

DROP TABLE IF EXISTS `webhook_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `trigger_type` varchar(50) NOT NULL,
  `payload` longtext NOT NULL,
  `response_code` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('success','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_webhook_logs_webhook_id` (`webhook_id`),
  KEY `idx_webhook_logs_user_id` (`user_id`),
  KEY `idx_webhook_logs_event_id` (`event_id`),
  KEY `idx_webhook_logs_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhook_logs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `webhook_logs` WRITE;
/*!40000 ALTER TABLE `webhook_logs` DISABLE KEYS */;
INSERT INTO `webhook_logs` VALUES
(1,NULL,NULL,NULL,'webhook_test','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"content\":\"Test mention: @everyone\",\"embeds\":[{\"title\":\"✅ Dischedule Webhook Connection Test\",\"description\":\"Your Discord webhook has been successfully linked to **Dischedule**! You will receive live event reminders and schedule updates in this channel.\",\"color\":5793266,\"fields\":[{\"name\":\"System Status\",\"value\":\"🟢 Operational\",\"inline\":true},{\"name\":\"Server Time\",\"value\":\"2026-08-18 08:40:09 WIB\",\"inline\":true}],\"footer\":{\"text\":\"Dischedule Discord Integration\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"timestamp\":\"2026-08-18T08:40:09+07:00\"}]}',204,'','success','2026-08-18 08:40:09'),
(2,NULL,4,6,'created','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"🎉 New Event Created: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T08:45:15+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/6\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 9:00 AM - Aug 18, 2026 10:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Personal & Life\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',400,'{\"embeds\": [\"0\"]}','failed','2026-08-18 08:45:15'),
(3,NULL,4,7,'created','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"🎉 New Event Created: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T08:45:16+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 9:00 AM - Aug 18, 2026 10:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Personal & Life\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',400,'{\"embeds\": [\"0\"]}','failed','2026-08-18 08:45:16'),
(4,NULL,4,6,'deleted','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"🗑️ Event Cancelled/Deleted: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T08:45:30+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/6\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 9:00 AM - Aug 18, 2026 10:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',400,'{\"embeds\": [\"0\"]}','failed','2026-08-18 08:45:31'),
(5,NULL,4,7,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T08:45:56+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 2:00 AM - Aug 18, 2026 3:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Personal & Life\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',400,'{\"embeds\": [\"0\"]}','failed','2026-08-18 08:45:56'),
(6,NULL,4,7,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T08:46:27+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 17, 2026 9:00 AM - Aug 17, 2026 10:00 PM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Personal & Life\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',400,'{\"embeds\": [\"0\"]}','failed','2026-08-18 08:46:27'),
(7,NULL,1,1,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: Daily Engineering Standup\",\"description\":\"Review blockers, yesterday achievements, and today goals for the engineering team.\",\"color\":959977,\"timestamp\":\"2026-08-18T02:12:35+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/1\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 15, 2026 9:00 AM - Aug 15, 2026 9:30 AM (UTC)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"Discord Voice Channel #standup\",\"inline\":true}]}]}',400,'{\"webhook_id\": [\"Value \\\"example\\\" is not snowflake.\"]}','failed','2026-08-18 02:12:35'),
(8,NULL,1,1,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: Daily Engineering Standup\",\"description\":\"Review blockers, yesterday achievements, and today goals for the engineering team.\",\"color\":959977,\"timestamp\":\"2026-08-18T02:13:10+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/1\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 15, 2026 9:00 AM - Aug 15, 2026 9:30 AM (UTC)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"Discord Voice Channel #standup\",\"inline\":true}]}]}',400,'{\"webhook_id\": [\"Value \\\"example\\\" is not snowflake.\"]}','failed','2026-08-18 02:13:10'),
(9,NULL,1,2,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: Sprint 42 Planning & Backlog Grooming\",\"description\":\"Plan next two weeks deliverables, story point estimation and Discord bot integrations.\",\"color\":5195493,\"timestamp\":\"2026-08-18T02:13:10+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/2\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 15, 2026 2:00 PM - Aug 15, 2026 3:30 PM (UTC)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"Conference Room A / Google Meet\",\"inline\":true}]}]}',400,'{\"webhook_id\": [\"Value \\\"example\\\" is not snowflake.\"]}','failed','2026-08-18 02:13:11'),
(10,NULL,3,4,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: Discord Community Townhall & AMA\",\"description\":\"Live Q&A with server members discussing upcoming Discord webhook features.\",\"color\":5793266,\"timestamp\":\"2026-08-18T02:13:11+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"/events/4\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 17, 2026 4:00 PM - Aug 17, 2026 5:30 PM (UTC)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"Discord Stage Channel #stage\",\"inline\":true}]}]}',400,'{\"webhook_id\": [\"Value \\\"example\\\" is not snowflake.\"]}','failed','2026-08-18 02:13:12'),
(11,NULL,4,7,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: Test Event - Webhook Fix Verification\",\"description\":\"Verifying Discord webhook rich embed with valid absolute URL.\",\"color\":5195493,\"timestamp\":\"2026-08-18T02:13:56+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 5:00 PM - Aug 18, 2026 6:00 PM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"Jakarta, Indonesia\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Testing\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true}]}]}',204,'','success','2026-08-18 02:13:57'),
(12,NULL,4,7,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: NASI PADANGGG\",\"description\":\"No description provided.\",\"color\":5195493,\"timestamp\":\"2026-08-18T09:49:23+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 17, 2026 10:00 AM - Aug 17, 2026 11:00 PM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Personal & Life\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 09:49:23'),
(13,NULL,4,8,'created','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"🎉 New Event Created: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T10:18:28+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Community & Discord\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 10:18:28'),
(14,NULL,4,5,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:19:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/5\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:19:01'),
(15,NULL,4,6,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:20:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/6\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:20:07'),
(16,NULL,4,7,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:21:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/7\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:21:01'),
(17,NULL,4,8,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:22:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:22:01'),
(18,NULL,4,9,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:23:02+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/9\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:23:02'),
(19,NULL,4,10,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:24:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png\"},\"url\":\"http://192.168.3.101/events/10\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:24:01'),
(20,NULL,4,11,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:25:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/11\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:25:02'),
(21,NULL,4,12,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:26:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/12\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:20 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:26:01'),
(22,NULL,4,8,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T10:28:49+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:30 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Community & Discord\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 10:28:50'),
(23,NULL,4,8,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T10:37:02+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:38 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Community & Discord\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 10:37:02'),
(24,NULL,4,14,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"⏰ Event Reminder (15m before): AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:38:02+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/14\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:38 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:38:02'),
(25,NULL,4,8,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T10:38:31+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:38 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Community & Discord\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 10:38:31'),
(26,NULL,4,8,'updated','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"✏️ Event Updated: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T10:38:44+07:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/8\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:39 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🏷️ Category\",\"value\":\"Community & Discord\",\"inline\":true},{\"name\":\"⚡ Priority\",\"value\":\"High\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 10:38:44'),
(27,NULL,4,16,'reminder','{\"username\":\"Dischedule Bot\",\"avatar_url\":\"https://i.imgur.com/DGosmDV.png\",\"embeds\":[{\"title\":\"⏰ Event Starting Now: AAAAA\",\"description\":\"ngumpulin tekad\",\"color\":5195493,\"timestamp\":\"2026-08-18T03:39:01+00:00\",\"footer\":{\"text\":\"Dischedule • Event Management\",\"icon_url\":\"https://i.imgur.com/DGosmDV.png\"},\"url\":\"http://192.168.3.101/events/16\",\"fields\":[{\"name\":\"📅 Schedule Time\",\"value\":\"Aug 18, 2026 10:39 AM - Aug 18, 2026 11:00 AM (Asia/Jakarta)\",\"inline\":false},{\"name\":\"📍 Location\",\"value\":\"jawa\",\"inline\":true},{\"name\":\"🔄 Recurrence\",\"value\":\"FREQ=DAILY\",\"inline\":true}]}]}',204,'','success','2026-08-18 03:39:02');
/*!40000 ALTER TABLE `webhook_logs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `webhooks`
--

DROP TABLE IF EXISTS `webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhooks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `channel_id` varchar(50) DEFAULT NULL,
  `guild_id` varchar(50) DEFAULT NULL,
  `mention_role` varchar(50) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_webhooks_user_id` (`user_id`),
  CONSTRAINT `fk_webhooks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhooks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `webhooks` WRITE;
/*!40000 ALTER TABLE `webhooks` DISABLE KEYS */;
INSERT INTO `webhooks` VALUES
(1,1,'Admin Announcements Channel','https://discord.com/api/webhooks/example/admin-token',NULL,NULL,'@everyone',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(2,3,'Dev Team Feed','https://discord.com/api/webhooks/example/dev-token',NULL,NULL,'@here',1,'2026-08-15 12:01:06','2026-08-15 12:01:06'),
(3,4,'# general','https://discord.com/api/webhooks/1529661897052258515/cdq6Ijq5QV49F8AeIOa9J9wa4EvKw_OATnSYzde81XzxmxPTf2Uav-7BBuuecBqgJdcU',NULL,NULL,'@everyone',1,'2026-08-18 08:40:06','2026-08-18 01:40:06');
/*!40000 ALTER TABLE `webhooks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-08-18  3:42:21
