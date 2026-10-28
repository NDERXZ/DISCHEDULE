<div style="max-width:800px;margin:0 auto;">
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px;font-weight:800;letter-spacing:-0.5px;color:var(--text-main);margin-bottom:4px;">Security & Password</h1>
        <p style="font-size:13.5px;color:var(--text-muted);">Update your security credentials and manage authentication methods.</p>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex;gap:8px;border-bottom:1px solid var(--border-color);margin-bottom:24px;padding-bottom:2px;">
        <a href="<?= url('profile') ?>" class="btn btn-secondary">
            <i class="fa-regular fa-user"></i> Profile
        </a>
        <a href="<?= url('profile/security') ?>" class="btn btn-secondary active" style="background:var(--color-primary-light);color:var(--color-primary);border-color:transparent;font-weight:600;">
            <i class="fa-solid fa-lock"></i> Security & Password
        </a>
        <a href="<?= url('profile/api-tokens') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-key"></i> API Tokens
        </a>
    </div>

    <form action="<?= url('profile/security') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-key" style="margin-right:8px;color:var(--color-primary);"></i> Change Password</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Current Password <span style="color:var(--color-danger);">*</span></label>
                    <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label">New Password <span style="color:var(--color-danger);">*</span></label>
                    <input type="password" name="new_password" class="form-control" required placeholder="••••••••">
                    <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
                        Min 8 characters containing uppercase, lowercase, digit, and special symbol.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('confirm_password') ?> <span style="color:var(--color-danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                <i class="fa-solid fa-check"></i> Update Password
            </button>
        </div>
    </form>
</div>
