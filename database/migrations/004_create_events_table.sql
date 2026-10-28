CREATE TABLE IF NOT EXISTS `events` (
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
  INDEX `idx_events_share_token` (`share_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `event_tag` (
  `event_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`, `tag_id`),
  INDEX `idx_event_tag_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `event_instances` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `status` ENUM('scheduled', 'cancelled', 'completed') NOT NULL DEFAULT 'scheduled',
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_instances_event_id` (`event_id`),
  INDEX `idx_instances_range` (`start_datetime`, `end_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
