<div style="max-width:800px;margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Platform Settings</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Manage global site configuration, Discord bot defaults, and maintenance status.</p>
    </div>

    <form action="<?= url('admin/settings') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">General Platform Config</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Application Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? 'Dischedule') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Meta Description</label>
                    <input type="text" name="site_description" class="form-control" value="<?= e($settings['site_description'] ?? 'Discord-integrated schedule & event management system') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Default System Timezone</label>
                    <input type="text" name="default_timezone" class="form-control" value="<?= e($settings['default_timezone'] ?? 'UTC') ?>">
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-brands fa-discord" style="margin-right:8px;color:var(--color-discord);"></i> Discord Webhook Integration Defaults</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Discord Bot Display Name</label>
                        <input type="text" name="discord_bot_name" class="form-control" placeholder="Dischedule Bot" value="<?= e($settings['discord_bot_name'] ?? 'Dischedule Bot') ?>">
                        <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
                            The sender username displayed in Discord webhook embeds.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Discord Bot Avatar URL</label>
                        <input type="url" name="discord_avatar_url" class="form-control" placeholder="https://..." value="<?= e($settings['discord_avatar_url'] ?? 'https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png') ?>">
                        <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
                            Public HTTPS image URL used for the bot avatar and embed footers.
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Global Admin Discord Webhook URL (Optional)</label>
                    <input type="url" name="discord_global_webhook_url" class="form-control" placeholder="https://discord.com/api/webhooks/..." value="<?= e($settings['discord_global_webhook_url'] ?? '') ?>">
                    <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
                        When set, a copy of all new public events across the system will be sent to this central Discord channel.
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Allow New User Registrations</label>
                        <select name="allow_registration" class="form-control">
                            <option value="1" <?= ($settings['allow_registration'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled</option>
                            <option value="0" <?= ($settings['allow_registration'] ?? '') === '0' ? 'selected' : '' ?>>Disabled (Invite Only)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Maintenance Mode</label>
                        <select name="maintenance_mode" class="form-control">
                            <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled (Live for all users)</option>
                            <option value="1" <?= ($settings['maintenance_mode'] ?? '') === '1' ? 'selected' : '' ?>>Enabled (Admins only)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                <i class="fa-solid fa-check"></i> Save Platform Settings
            </button>
        </div>
    </form>
</div>
