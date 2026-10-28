<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$activeRoute = function(string $path) use ($currentPath): string {
    return str_starts_with($currentPath, $path) ? 'active' : '';
};
?>
<aside class="app-sidebar" id="app-sidebar">
    <div class="sidebar-header">
        <a href="<?= url() ?>" class="brand-logo" style="display:flex;align-items:center;gap:10px;">
            <img src="https://i.imgur.com/Yo8WHdg.png" alt="Dischedule Logo" style="height:32px;width:auto;object-fit:contain;border-radius:6px;">
            <span style="font-weight:800;font-size:16px;color:var(--text-main);letter-spacing:-0.3px;">Dischedule</span>
        </a>
        <button class="btn btn-icon" onclick="Dischedule.toggleSidebar()" style="display:none;" id="sidebar-close-btn">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="sidebar-content">
        <!-- Main Navigation -->
        <div>
            <div class="nav-section-title">Workspace</div>
            <ul class="sidebar-nav">
                <li class="nav-item <?= $activeRoute('/calendar') ?>">
                    <a href="<?= url('calendar') ?>">
                        <i class="fa-regular fa-calendar nav-icon"></i>
                        <span><?= __('calendar') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/events') ?>">
                    <a href="<?= url('events') ?>">
                        <i class="fa-solid fa-list-check nav-icon"></i>
                        <span><?= __('events') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/categories') ?>">
                    <a href="<?= url('categories') ?>">
                        <i class="fa-solid fa-tags nav-icon"></i>
                        <span><?= __('categories') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/webhooks') ?>">
                    <a href="<?= url('webhooks') ?>">
                        <i class="fa-brands fa-discord nav-icon" style="color:var(--color-discord);"></i>
                        <span><?= __('webhooks') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/teams') ?>">
                    <a href="<?= url('teams') ?>">
                        <i class="fa-solid fa-users nav-icon"></i>
                        <span><?= __('teams') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/notifications') ?>">
                    <a href="<?= url('notifications') ?>">
                        <i class="fa-regular fa-bell nav-icon"></i>
                        <span><?= __('notifications') ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Discord Webhook Integration Box -->
        <div style="background:var(--bg-subtle);border:1px solid var(--border-color);border-radius:var(--radius-md);padding:14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span style="font-weight:700;font-size:12px;display:flex;align-items:center;gap:6px;">
                    <i class="fa-brands fa-discord" style="color:var(--color-discord);font-size:15px;"></i> Discord Webhooks
                </span>
                <span class="badge badge-primary" style="font-size:9.5px;">Live</span>
            </div>
            <p style="font-size:11.5px;color:var(--text-muted);margin-bottom:10px;line-height:1.4;">
                Send automated real-time event reminders and schedule updates to your Discord channels.
            </p>
            <a href="<?= url('webhooks') ?>" class="btn btn-secondary" style="width:100%;font-size:11.5px;padding:6px 10px;display:flex;align-items:center;justify-content:center;gap:6px;">
                <i class="fa-solid fa-satellite-dish" style="color:var(--color-primary);"></i> Manage Webhooks
            </a>
        </div>

        <!-- Account & Profile Section -->
        <div>
            <div class="nav-section-title">Account & Settings</div>
            <ul class="sidebar-nav">
                <li class="nav-item <?= $currentPath === '/profile' ? 'active' : '' ?>">
                    <a href="<?= url('profile') ?>">
                        <i class="fa-regular fa-user nav-icon"></i>
                        <span><?= __('profile') ?></span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/profile/security') ?>">
                    <a href="<?= url('profile/security') ?>">
                        <i class="fa-solid fa-lock nav-icon"></i>
                        <span>Security & Password</span>
                    </a>
                </li>
                <li class="nav-item <?= $activeRoute('/profile/api-tokens') ?>">
                    <a href="<?= url('profile/api-tokens') ?>">
                        <i class="fa-solid fa-key nav-icon"></i>
                        <span>API Tokens</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Administration Section -->
        <?php if (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin'): ?>
            <div>
                <div class="nav-section-title">Administration</div>
                <ul class="sidebar-nav">
                    <li class="nav-item <?= $activeRoute('/admin') ?>">
                        <a href="<?= url('admin') ?>">
                            <i class="fa-solid fa-gauge-high nav-icon"></i>
                            <span>Admin Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $activeRoute('/admin/users') ?>">
                        <a href="<?= url('admin/users') ?>">
                            <i class="fa-solid fa-users-gear nav-icon"></i>
                            <span>Manage Users</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $activeRoute('/admin/webhooks') ?>">
                        <a href="<?= url('admin/webhooks') ?>">
                            <i class="fa-solid fa-network-wired nav-icon"></i>
                            <span>Webhook Logs</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $activeRoute('/admin/settings') ?>">
                        <a href="<?= url('admin/settings') ?>">
                            <i class="fa-solid fa-sliders nav-icon"></i>
                            <span>Platform Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</aside>
