-- Categories Seed Data
INSERT INTO `categories` (`id`, `user_id`, `team_id`, `name`, `color`, `icon`, `is_global`, `created_at`) VALUES
(1, NULL, NULL, 'Work & Engineering', '#4F46E5', 'fa-solid fa-briefcase', 1, NOW()),
(2, NULL, NULL, 'Meetings & Syncs', '#0EA5E9', 'fa-solid fa-users', 1, NOW()),
(3, NULL, NULL, 'Deadlines & Launches', '#EF4444', 'fa-solid fa-fire', 1, NOW()),
(4, NULL, NULL, 'Personal & Life', '#10B981', 'fa-solid fa-user', 1, NOW()),
(5, NULL, NULL, 'Health & Fitness', '#EC4899', 'fa-solid fa-heart-pulse', 1, NOW()),
(6, NULL, NULL, 'Community & Discord', '#5865F2', 'fa-brands fa-discord', 1, NOW()),
(7, 3, NULL, 'Side Projects', '#8B5CF6', 'fa-solid fa-rocket', 0, NOW());
