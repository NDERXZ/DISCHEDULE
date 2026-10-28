-- =============================================================================
-- Dischedule - Consolidated Seed Data
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users
TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `bio`, `timezone`, `language`, `theme`, `role`, `status`, `email_verified_at`, `created_at`) VALUES
(1, 'admin', 'admin@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator and Dischedule Lead', 'UTC', 'en', 'dark', 'admin', 'active', NOW(), NOW()),
(2, 'moderator', 'mod@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Community Moderator and Event Coordinator', 'UTC', 'en', 'system', 'moderator', 'active', NOW(), NOW()),
(3, 'demouser', 'demo@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Product Engineer & Discord Enthusiast', 'UTC', 'en', 'system', 'user', 'active', NOW(), NOW());

-- 2. Teams
TRUNCATE TABLE `teams`;
INSERT INTO `teams` (`id`, `owner_id`, `name`, `description`, `created_at`) VALUES
(1, 1, 'Core Platform Team', 'Responsible for backend architecture, integrations, and releases.', NOW()),
(2, 3, 'Discord Community Squad', 'Managing community events, AMAs, and bot features.', NOW());

-- 3. Team Members
TRUNCATE TABLE `team_members`;
INSERT INTO `team_members` (`id`, `team_id`, `user_id`, `role`, `created_at`) VALUES
(1, 1, 1, 'owner', NOW()),
(2, 1, 2, 'admin', NOW()),
(3, 1, 3, 'member', NOW()),
(4, 2, 3, 'owner', NOW()),
(5, 2, 1, 'member', NOW());

-- 4. Categories
TRUNCATE TABLE `categories`;
INSERT INTO `categories` (`id`, `user_id`, `team_id`, `name`, `color`, `icon`, `is_global`, `created_at`) VALUES
(1, NULL, NULL, 'Work & Engineering', '#4F46E5', 'fa-solid fa-briefcase', 1, NOW()),
(2, NULL, NULL, 'Meetings & Syncs', '#0EA5E9', 'fa-solid fa-users', 1, NOW()),
(3, NULL, NULL, 'Deadlines & Launches', '#EF4444', 'fa-solid fa-fire', 1, NOW()),
(4, NULL, NULL, 'Personal & Life', '#10B981', 'fa-solid fa-user', 1, NOW()),
(5, NULL, NULL, 'Health & Fitness', '#EC4899', 'fa-solid fa-heart-pulse', 1, NOW()),
(6, NULL, NULL, 'Community & Discord', '#5865F2', 'fa-brands fa-discord', 1, NOW()),
(7, 3, NULL, 'Side Projects', '#8B5CF6', 'fa-solid fa-rocket', 0, NOW());

-- 5. Tags
TRUNCATE TABLE `tags`;
INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Sprint', 'sprint', NOW()),
(2, 'Discord', 'discord', NOW()),
(3, 'Release', 'release', NOW()),
(4, 'Planning', 'planning', NOW()),
(5, 'Review', 'review', NOW());

-- 6. Webhooks
TRUNCATE TABLE `webhooks`;
INSERT INTO `webhooks` (`id`, `user_id`, `name`, `url`, `mention_role`, `is_default`, `created_at`) VALUES
(1, 1, 'Admin Announcements Channel', 'https://discord.com/api/webhooks/example/admin-token', '@everyone', 1, NOW()),
(2, 3, 'Dev Team Feed', 'https://discord.com/api/webhooks/example/dev-token', '@here', 1, NOW());

-- 7. Events
TRUNCATE TABLE `events`;
INSERT INTO `events` (`id`, `user_id`, `category_id`, `title`, `slug`, `description`, `start_datetime`, `end_datetime`, `timezone`, `location`, `location_url`, `color`, `priority`, `status`, `visibility`, `share_token`, `recurrence_rule`, `created_at`) VALUES
(1, 1, 2, 'Daily Engineering Standup', 'daily-engineering-standup', 'Review blockers, yesterday achievements, and today goals for the engineering team.', DATE_ADD(CURDATE(), INTERVAL 9 HOUR), DATE_ADD(CURDATE(), INTERVAL 9 HOUR) + INTERVAL 30 MINUTE, 'UTC', 'Discord Voice Channel #standup', 'https://discord.gg/example', '#0EA5E9', 'medium', 'published', 'public', 'token_standup_123', 'FREQ=DAILY', NOW()),
(2, 1, 1, 'Sprint 42 Planning & Backlog Grooming', 'sprint-42-planning', 'Plan next two weeks deliverables, story point estimation and Discord bot integrations.', DATE_ADD(CURDATE(), INTERVAL 14 HOUR), DATE_ADD(CURDATE(), INTERVAL 15 HOUR) + INTERVAL 30 MINUTE, 'UTC', 'Conference Room A / Google Meet', 'https://meet.google.com/abc-defg-hij', '#4F46E5', 'high', 'published', 'public', 'token_sprint_456', NULL, NOW()),
(3, 3, 3, 'Dischedule v2.0 Production Release', 'dischedule-v2-production-release', 'Deploy new high-performance native PHP backend and Discord OAuth2 sync.', DATE_ADD(CURDATE(), INTERVAL 18 HOUR), DATE_ADD(CURDATE(), INTERVAL 19 HOUR), 'UTC', 'Production Server Pipeline', NULL, '#EF4444', 'high', 'published', 'public', 'token_release_789', NULL, NOW()),
(4, 3, 6, 'Discord Community Townhall & AMA', 'discord-community-townhall-ama', 'Live Q&A with server members discussing upcoming Discord webhook features.', DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 16 HOUR), DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 17 HOUR) + INTERVAL 30 MINUTE, 'UTC', 'Discord Stage Channel #stage', 'https://discord.gg/example-stage', '#5865F2', 'high', 'published', 'public', 'token_townhall_321', NULL, NOW()),
(5, 1, 5, 'Evening Gym Workout', 'evening-gym-workout', 'Cardio and strength training session.', DATE_ADD(CURDATE(), INTERVAL 19 HOUR) + INTERVAL 30 MINUTE, DATE_ADD(CURDATE(), INTERVAL 20 HOUR) + INTERVAL 45 MINUTE, 'UTC', 'Fitness Center', NULL, '#EC4899', 'low', 'published', 'private', NULL, 'FREQ=WEEKLY;BYDAY=MO,WE,FR', NOW());

-- 8. Event Tags Pivot
TRUNCATE TABLE `event_tag`;
INSERT INTO `event_tag` (`event_id`, `tag_id`) VALUES
(1, 1),
(2, 1),
(2, 4),
(3, 3),
(4, 2);

-- 9. Event Webhooks Pivot
TRUNCATE TABLE `event_webhook`;
INSERT INTO `event_webhook` (`event_id`, `webhook_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 2);

-- 10. Reminders
TRUNCATE TABLE `reminders`;
INSERT INTO `reminders` (`id`, `event_id`, `remind_at`, `minutes_before`, `type`, `status`, `created_at`) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL 8 HOUR) + INTERVAL 45 MINUTE, 15, 'discord', 'pending', NOW()),
(2, 2, DATE_ADD(CURDATE(), INTERVAL 13 HOUR) + INTERVAL 30 MINUTE, 30, 'discord', 'pending', NOW()),
(3, 3, DATE_ADD(CURDATE(), INTERVAL 17 HOUR), 60, 'email', 'pending', NOW()),
(4, 4, DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 2 DAY), INTERVAL 15 HOUR), 60, 'discord', 'pending', NOW());

-- 11. Notifications
TRUNCATE TABLE `notifications`;
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'info', 'Welcome to Dischedule!', 'Your workspace is ready. Link your Discord account in Settings to enable real-time alerts.', '/profile/discord', 0, NOW()),
(2, 1, 'success', 'Webhook Connected', 'Default admin webhook has been configured successfully.', '/webhooks', 0, NOW()),
(3, 3, 'warning', 'Upcoming Release Event', 'Dischedule v2.0 Production Release is scheduled for today.', '/events/3', 0, NOW());

-- 12. Settings
TRUNCATE TABLE `settings`;
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('site_name', 'Dischedule', 'Website brand name'),
('site_description', 'Discord-integrated schedule & event management system', 'Meta description'),
('default_timezone', 'UTC', 'Default system timezone'),
('allow_registration', '1', 'Enable public registration'),
('discord_enabled', '1', 'Enable Discord OAuth2 and Webhooks'),
('maintenance_mode', '0', 'Put site in maintenance mode');

SET FOREIGN_KEY_CHECKS = 1;
