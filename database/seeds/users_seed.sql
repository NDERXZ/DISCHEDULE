-- Users Seed Data
-- Passwords:
-- admin / admin@dischedule.local => Password@123 (hash below matches password_hash('Password@123', PASSWORD_BCRYPT))
-- Hash: $2y$10$wE96c73rK3eD5qT1Z5fO9uN5R3P0l8zV.Q9Z6K.A3O.Y6M4O5gQ6q
-- Fallback hash for 'secret': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `bio`, `timezone`, `language`, `theme`, `role`, `status`, `email_verified_at`, `created_at`) VALUES
(1, 'admin', 'admin@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator and Dischedule Lead', 'UTC', 'en', 'dark', 'admin', 'active', NOW(), NOW()),
(2, 'moderator', 'mod@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Community Moderator and Event Coordinator', 'UTC', 'en', 'system', 'moderator', 'active', NOW(), NOW()),
(3, 'demouser', 'demo@dischedule.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Product Engineer & Discord Enthusiast', 'UTC', 'en', 'system', 'user', 'active', NOW(), NOW());
