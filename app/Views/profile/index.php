<div style="max-width:800px;margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Account Settings</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Manage your personal profile, local timezone, language, and theme preferences.</p>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border-color);margin-bottom:24px;padding-bottom:2px;overflow-x:auto;">
        <a href="<?= url('profile') ?>" class="btn btn-secondary active" style="background:var(--color-primary-light);color:var(--color-primary);border-color:transparent;font-weight:600;">
            <i class="fa-regular fa-user"></i> Profile
        </a>
        <a href="<?= url('profile/security') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-lock"></i> Security & Password
        </a>
        <a href="<?= url('profile/api-tokens') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-key"></i> API Tokens
        </a>
    </div>

    <form action="<?= url('profile') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">Profile Information</span>
            </div>
            <div class="card-body">
                <!-- Avatar Preview & Upload -->
                <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= e($user['avatar']) ?>" alt="Avatar" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--color-primary);">
                    <?php elseif (!empty($user['discord_avatar'])): ?>
                        <img src="<?= e($user['discord_avatar']) ?>" alt="Discord Avatar" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid var(--color-discord);">
                    <?php else: ?>
                        <div style="width:72px;height:72px;border-radius:50%;background:var(--color-primary);color:#FFF;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;">
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="form-label" style="margin-bottom:4px;">Profile Photo</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*" style="font-size:12px;">
                        <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">JPG, PNG, GIF or WebP (Max 2MB)</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled style="background:var(--bg-subtle);">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:var(--color-danger);">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Bio & About Me</label>
                    <textarea name="bio" rows="3" class="form-control" placeholder="A short bio..."><?= e($user['bio'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">Localization & Appearance</span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Timezone <span style="color:var(--color-danger);">*</span></label>
                        <select name="timezone" class="form-control" required>
                            <?php foreach ($timezones as $tz): ?>
                                <option value="<?= $tz ?>" <?= ($user['timezone'] ?? 'UTC') === $tz ? 'selected' : '' ?>><?= $tz ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preferred Language</label>
                        <select name="language" class="form-control" required>
                            <option value="en" <?= ($user['language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="id" <?= ($user['language'] ?? '') === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                            <option value="es" <?= ($user['language'] ?? '') === 'es' ? 'selected' : '' ?>>Español</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Theme Preference</label>
                        <select name="theme" class="form-control" required>
                            <option value="system" <?= ($user['theme'] ?? 'system') === 'system' ? 'selected' : '' ?>>System Default</option>
                            <option value="light" <?= ($user['theme'] ?? '') === 'light' ? 'selected' : '' ?>>Light Theme</option>
                            <option value="dark" <?= ($user['theme'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark Theme</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:40px;">
            <button type="button" onclick="Dischedule.openModal('modal-delete-account')" class="btn btn-secondary" style="color:var(--color-danger);">
                <i class="fa-solid fa-user-xmark"></i> Deactivate Account
            </button>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px;">
                <i class="fa-solid fa-check"></i> <?= __('save') ?>
            </button>
        </div>
    </form>
</div>

<!-- Delete Account Modal -->
<div class="modal-backdrop" id="modal-delete-account">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 style="font-size:16px;font-weight:700;color:var(--color-danger);"><i class="fa-solid fa-triangle-exclamation"></i> Deactivate Account</h3>
            <button onclick="Dischedule.closeModal('modal-delete-account')" class="btn btn-icon"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="<?= url('profile/delete-account') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:16px;">
                    Are you sure you want to deactivate your account? Your personal schedules, Discord links, and API tokens will be permanently scrubbed.
                </p>
                <div class="form-group">
                    <label class="form-label">Confirm with your password:</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Enter password...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="Dischedule.closeModal('modal-delete-account')" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Permanently Deactivate</button>
            </div>
        </form>
    </div>
</div>
