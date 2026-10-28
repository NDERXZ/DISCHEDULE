CREATE TABLE IF NOT EXISTS `webhooks` (
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
  INDEX `idx_webhooks_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `event_webhook` (
  `event_id` BIGINT UNSIGNED NOT NULL,
  `webhook_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`, `webhook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_logs` (
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
