CREATE TABLE IF NOT EXISTS `reminders` (
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
  INDEX `idx_reminders_status_remind_at` (`status`, `remind_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
