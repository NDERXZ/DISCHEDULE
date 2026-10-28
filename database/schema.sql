-- =============================================================================
-- Dischedule - Complete Database Schema
-- MySQL 8.0+ / MariaDB 10.4+ (InnoDB, utf8mb4)
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `timezone` VARCHAR(50) NOT NULL DEFAULT 'UTC',
  `language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `theme` VARCHAR(20) NOT NULL DEFAULT 'system',
  `accent_color` VARCHAR(20) NOT NULL DEFAULT '#4F46E5',
  `discord_id` VARCHAR(50) NULL UNIQUE,
  `discord_username` VARCHAR(100) NULL,
  `discord_avatar` VARCHAR(255) NULL,
  `email_verified_at` TIMESTAMP NULL,
  `verification_token` VARCHAR(100) NULL,
  `reset_token` VARCHAR(100) NULL,
  `reset_expires_at` TIMESTAMP NULL,
  `remember_token` VARCHAR(255) NULL,
  `role` ENUM('admin', 'moderator', 'user') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active',
  `notify_email` TINYINT(1) NOT NULL DEFAULT 1,
  `notify_discord` TINYINT(1) NOT NULL DEFAULT 1,
  `notify_in_app` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_status` (`status`),
  INDEX `idx_users_discord_id` (`discord_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Teams Table
DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `owner_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_teams_owner_id` (`owner_id`),
  CONSTRAINT `fk_teams_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Team Members Table
DROP TABLE IF EXISTS `team_members`;
CREATE TABLE `team_members` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `team_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role` ENUM('owner', 'admin', 'member') NOT NULL DEFAULT 'member',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_team_user` (`team_id`, `user_id`),
  INDEX `idx_team_members_user_id` (`user_id`),
  CONSTRAINT `fk_team_members_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Categories Table
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `team_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(100) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#4F46E5',
  `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-solid fa-calendar',
  `is_global` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_categories_user_id` (`user_id`),
  INDEX `idx_categories_team_id` (`team_id`),
  INDEX `idx_categories_global` (`is_global`),
  CONSTRAINT `fk_categories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_categories_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tags Table
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(60) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_tags_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Events Table
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `timezone` VARCHAR(50) NOT NULL DEFAULT 'UTC',
  `location` VARCHAR(255) NULL,
  `location_url` VARCHAR(500) NULL,
  `color` VARCHAR(20) NULL,
  `priority` ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
  `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'published',
  `visibility` ENUM('private', 'public', 'shared') NOT NULL DEFAULT 'private',
  `share_token` VARCHAR(64) NULL UNIQUE,
  `share_password_hash` VARCHAR(255) NULL,
  `share_expires_at` DATETIME NULL,
  `recurrence_rule` VARCHAR(255) NULL,
  `recurrence_end` DATETIME NULL,
  `recurrence_count` INT UNSIGNED NULL,
  `parent_event_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL,
  INDEX `idx_events_user_id` (`user_id`),
  INDEX `idx_events_category_id` (`category_id`),
  INDEX `idx_events_start_datetime` (`start_datetime`),
  INDEX `idx_events_end_datetime` (`end_datetime`),
  INDEX `idx_events_status` (`status`),
  INDEX `idx_events_visibility` (`visibility`),
  INDEX `idx_events_share_token` (`share_token`),
  CONSTRAINT `fk_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_events_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_events_parent` FOREIGN KEY (`parent_event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Event Tags Pivot Table
DROP TABLE IF EXISTS `event_tag`;
CREATE TABLE `event_tag` (
  `event_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`, `tag_id`),
  INDEX `idx_event_tag_tag_id` (`tag_id`),
  CONSTRAINT `fk_event_tag_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Event Instances (Recurring expanded occurrences)
DROP TABLE IF EXISTS `event_instances`;
CREATE TABLE `event_instances` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `status` ENUM('scheduled', 'cancelled', 'completed') NOT NULL DEFAULT 'scheduled',
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_instances_event_id` (`event_id`),
  INDEX `idx_instances_range` (`start_datetime`, `end_datetime`),
  CONSTRAINT `fk_instances_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Reminders Table
DROP TABLE IF EXISTS `reminders`;
CREATE TABLE `reminders` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `remind_at` DATETIME NOT NULL,
  `minutes_before` INT NOT NULL DEFAULT 15,
  `type` ENUM('email', 'discord', 'in_app') NOT NULL DEFAULT 'discord',
  `sent_at` DATETIME NULL,
  `status` ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
  `error_message` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_reminders_event_id` (`event_id`),
  INDEX `idx_reminders_status_remind_at` (`status`, `remind_at`),
  CONSTRAINT `fk_reminders_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Webhooks Table
DROP TABLE IF EXISTS `webhooks`;
CREATE TABLE `webhooks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `url` TEXT NOT NULL,
  `channel_id` VARCHAR(50) NULL,
  `guild_id` VARCHAR(50) NULL,
  `mention_role` VARCHAR(50) NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_webhooks_user_id` (`user_id`),
  CONSTRAINT `fk_webhooks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Event Webhooks Pivot Table
DROP TABLE IF EXISTS `event_webhook`;
CREATE TABLE `event_webhook` (
  `event_id` BIGINT UNSIGNED NOT NULL,
  `webhook_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`, `webhook_id`),
  CONSTRAINT `fk_event_webhook_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_webhook_webhook` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Webhook Logs Table
DROP TABLE IF EXISTS `webhook_logs`;
CREATE TABLE `webhook_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `webhook_id` BIGINT UNSIGNED NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `event_id` BIGINT UNSIGNED NULL,
  `trigger_type` VARCHAR(50) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `response_code` INT NULL,
  `response_body` TEXT NULL,
  `status` ENUM('success', 'failed') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_webhook_logs_webhook_id` (`webhook_id`),
  INDEX `idx_webhook_logs_user_id` (`user_id`),
  INDEX `idx_webhook_logs_event_id` (`event_id`),
  INDEX `idx_webhook_logs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Notifications Table
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'info',
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `link` VARCHAR(255) NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_notifications_user_unread` (`user_id`, `is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Team Events Pivot Table
DROP TABLE IF EXISTS `team_events`;
CREATE TABLE `team_events` (
  `team_id` BIGINT UNSIGNED NOT NULL,
  `event_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`team_id`, `event_id`),
  CONSTRAINT `fk_team_events_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_team_events_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Attachments Table
DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `size` BIGINT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_attachments_event_id` (`event_id`),
  INDEX `idx_attachments_user_id` (`user_id`),
  CONSTRAINT `fk_attachments_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attachments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Comments Table
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_comments_event_id` (`event_id`),
  INDEX `idx_comments_user_id` (`user_id`),
  CONSTRAINT `fk_comments_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Activity Logs Table
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `event_id` BIGINT UNSIGNED NULL,
  `team_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_activity_user_id` (`user_id`),
  INDEX `idx_activity_event_id` (`event_id`),
  INDEX `idx_activity_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. API Tokens Table
DROP TABLE IF EXISTS `api_tokens`;
CREATE TABLE `api_tokens` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `permissions` VARCHAR(255) NOT NULL DEFAULT 'read,write',
  `last_used_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_api_tokens_user_id` (`user_id`),
  INDEX `idx_api_tokens_hash` (`token_hash`),
  CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Sessions Table
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(128) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT UNSIGNED NOT NULL,
  INDEX `idx_sessions_user_id` (`user_id`),
  INDEX `idx_sessions_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Rate Limits Table
DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE `rate_limits` (
  `key_hash` VARCHAR(64) PRIMARY KEY,
  `hits` INT UNSIGNED NOT NULL DEFAULT 1,
  `reset_at` INT UNSIGNED NOT NULL,
  INDEX `idx_rate_limits_reset_at` (`reset_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. Settings Table
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` LONGTEXT NULL,
  `description` VARCHAR(255) NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
