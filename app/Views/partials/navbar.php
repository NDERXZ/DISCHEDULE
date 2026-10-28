<header class="app-navbar">
    <div style="display:flex;align-items:center;gap:16px;">
        <button class="btn btn-icon" onclick="Dischedule.toggleSidebar()" style="display:none;" id="sidebar-toggle-btn">
            <i class="fa-solid fa-bars"></i>
        </button>
        
        <div class="navbar-search">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <form action="<?= url('events') ?>" method="GET" style="margin:0;">
                <input type="text" id="global-search" name="search" class="search-input" placeholder="<?= __('search_placeholder') ?>" autocomplete="off">
            </form>
        </div>
    </div>

    <div class="navbar-actions">
        <?php if (!empty($currentUser)): ?>
            <a href="<?= url('events/create') ?>" class="btn btn-primary" style="text-decoration:none;">
                <i class="fa-solid fa-plus"></i>
                <span class="btn-text"><?= __('new_event') ?></span>
            </a>
            
            <!-- Notification Bell -->
            <div class="dropdown" style="position:relative;">
                <button class="btn btn-icon" id="notif-bell-btn" data-toggle="dropdown" title="<?= __('notifications') ?>">
                    <i class="fa-regular fa-bell" style="font-size:16px;"></i>
                    <span id="notif-badge" class="nav-badge" style="position:absolute;top:2px;right:2px;display:none;font-size:10px;padding:1px 5px;">0</span>
                </button>
                
                <div id="notif-dropdown-menu" class="dropdown-menu card" style="right:0;top:calc(100% + 8px);width:340px;">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;justify-content:space-between;background:var(--bg-subtle);">
                        <span style="font-weight:700;font-size:13px;"><?= __('notifications') ?></span>
                        <button onclick="DischeduleNotifications.markAllRead()" style="background:none;border:none;color:var(--color-primary);font-size:11.5px;font-weight:600;cursor:pointer;">
                            Mark all read
                        </button>
                    </div>
                    <div id="notif-dropdown-list" style="max-height:360px;overflow-y:auto;">
                        <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:12px;">Loading notifications...</div>
                    </div>
                    <div style="padding:10px;text-align:center;border-top:1px solid var(--border-color);background:var(--bg-subtle);">
                        <a href="<?= url('notifications') ?>" style="font-size:12px;font-weight:600;">View All Notifications →</a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <button class="btn btn-icon theme-toggle-btn" onclick="Dischedule.toggleTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                <i class="fa-solid fa-moon theme-icon-moon"></i>
                <i class="fa-solid fa-sun theme-icon-sun" style="display:none;"></i>
            </button>

            <!-- User Menu Dropdown -->
            <div class="dropdown" style="position:relative;">
                <button class="btn btn-icon" id="user-menu-btn" data-toggle="dropdown" style="display:flex;align-items:center;gap:8px;width:auto;padding:4px 10px;border-radius:var(--radius-full);background:var(--bg-subtle);border:1px solid var(--border-color);cursor:pointer;">
                    <?php if (!empty($currentUser['avatar'])): ?>
                        <img src="<?= e($currentUser['avatar']) ?>" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                    <?php elseif (!empty($currentUser['discord_avatar'])): ?>
                        <img src="<?= e($currentUser['discord_avatar']) ?>" alt="Discord Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:28px;height:28px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;">
                            <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span style="font-size:13px;font-weight:600;color:var(--text-main);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($currentUser['username']) ?></span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--text-subtle);"></i>
                </button>

                <div class="dropdown-menu card" style="right:0;top:calc(100% + 8px);width:230px;">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border-color);background:var(--bg-subtle);">
                        <div style="font-weight:700;font-size:13.5px;color:var(--text-main);"><?= e($currentUser['username']) ?></div>
                        <div style="font-size:11.5px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($currentUser['email']) ?></div>
                    </div>
                    <div style="padding:6px 0;">
                        <a href="<?= url('profile') ?>" class="dropdown-item">
                            <i class="fa-regular fa-user" style="width:16px;"></i> <?= __('profile') ?>
                        </a>
                        <a href="<?= url('profile/security') ?>" class="dropdown-item">
                            <i class="fa-solid fa-lock" style="width:16px;"></i> Security & Password
                        </a>
                        <a href="<?= url('webhooks') ?>" class="dropdown-item">
                            <i class="fa-brands fa-discord" style="width:16px;color:var(--color-discord);"></i> Discord Webhooks
                        </a>
                        <a href="<?= url('profile/api-tokens') ?>" class="dropdown-item">
                            <i class="fa-solid fa-key" style="width:16px;"></i> API Tokens
                        </a>
                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= url('admin') ?>" class="dropdown-item" style="color:var(--color-danger);font-weight:600;">
                                <i class="fa-solid fa-shield-halved" style="width:16px;"></i> <?= __('admin_panel') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div style="padding:4px 0;">
                        <a href="<?= url('logout') ?>" class="dropdown-item" style="color:var(--color-danger);">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="width:16px;"></i> <?= __('logout') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <button class="btn btn-icon theme-toggle-btn" onclick="Dischedule.toggleTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle theme">
                <i class="fa-solid fa-moon theme-icon-moon"></i>
                <i class="fa-solid fa-sun theme-icon-sun" style="display:none;"></i>
            </button>
            <a href="<?= url('login') ?>" class="btn btn-secondary"><?= __('login') ?></a>
            <a href="<?= url('register') ?>" class="btn btn-primary"><?= __('register') ?></a>
        <?php endif; ?>
    </div>
</header>
