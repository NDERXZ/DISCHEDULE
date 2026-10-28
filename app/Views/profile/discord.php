<div style="max-width:800px;margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Discord Integration</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Connect your Discord account to authorize personalized notifications and single sign-on.</p>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border-color);margin-bottom:24px;padding-bottom:2px;">
        <a href="<?= url('profile') ?>" class="btn btn-secondary">
            <i class="fa-regular fa-user"></i> Profile
        </a>
        <a href="<?= url('profile/security') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-lock"></i> Security & Password
        </a>
        <a href="<?= url('profile/discord') ?>" class="btn btn-secondary active" style="background:var(--color-primary-light);color:var(--color-primary);border-color:transparent;font-weight:600;">
            <i class="fa-brands fa-discord" style="color:var(--color-discord);"></i> Discord Sync
        </a>
        <a href="<?= url('profile/api-tokens') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-key"></i> API Tokens
        </a>
    </div>

    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <span class="card-title"><i class="fa-brands fa-discord" style="margin-right:8px;color:var(--color-discord);"></i> Discord Account Connection</span>
        </div>
        <div class="card-body">
            <?php if (!empty($user['discord_id'])): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:var(--color-discord-light);border:1px solid rgba(88, 101, 242, 0.2);border-radius:var(--radius-md);flex-wrap:wrap;gap:16px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <?php if (!empty($user['discord_avatar'])): ?>
                            <img src="<?= e($user['discord_avatar']) ?>" alt="Discord Avatar" style="width:48px;height:48px;border-radius:50%;">
                        <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:50%;background:var(--color-discord);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:22px;">
                                <i class="fa-brands fa-discord"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:var(--text-main);">
                                <?= e($user['discord_username']) ?>
                            </div>
                            <div style="font-size:12px;color:var(--text-muted);">
                                Discord ID: <?= e($user['discord_id']) ?> &bull; <span style="color:var(--color-success);font-weight:600;">Linked & Active</span>
                            </div>
                        </div>
                    </div>

                    <form action="<?= url('auth/discord/unlink') ?>" method="POST" style="margin:0;" onsubmit="return confirm('Disconnect Discord account?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-secondary" style="color:var(--color-danger);font-size:13px;">
                            <i class="fa-solid fa-link-slash"></i> Disconnect Discord
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <?php if (empty($isOAuthConfigured)): ?>
                    <div style="background:var(--color-warning-bg);border:1px solid rgba(245, 158, 11, 0.3);border-radius:var(--radius-md);padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;text-align:left;">
                        <i class="fa-solid fa-triangle-exclamation" style="color:var(--color-warning);font-size:18px;margin-top:2px;"></i>
                        <div>
                            <div style="font-weight:700;color:var(--text-main);font-size:13.5px;margin-bottom:2px;">Discord OAuth2 Not Configured Yet</div>
                            <div style="font-size:12.5px;color:var(--text-muted);line-height:1.4;">
                                Discord Single Sign-On requires a <strong>Client ID</strong> and <strong>Client Secret</strong> from the Discord Developer Portal.
                                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <a href="<?= url('admin/settings') ?>" style="font-weight:600;text-decoration:underline;">Configure in Admin Settings →</a>
                                <?php else: ?>
                                    Please contact the system administrator to configure Discord SSO.
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="text-align:center;padding:24px 16px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--color-discord-light);color:var(--color-discord);display:inline-flex;align-items:center;justify-content:center;font-size:32px;margin-bottom:16px;">
                        <i class="fa-brands fa-discord"></i>
                    </div>
                    <h3 style="font-size:16px;font-weight:700;margin-bottom:6px;">No Discord Account Connected</h3>
                    <p style="font-size:13.5px;color:var(--text-muted);max-width:440px;margin:0 auto 20px auto;line-height:1.5;">
                        Connect your Discord account to enable 1-click login, synchronize channel permissions, and receive personalized reminder DMs.
                    </p>
                    <a href="<?= url('auth/discord') ?>" class="btn btn-discord" style="padding:10px 24px;font-size:14px;">
                        <i class="fa-brands fa-discord"></i> Connect Discord Account
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
