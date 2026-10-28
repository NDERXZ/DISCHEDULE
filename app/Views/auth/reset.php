<h2 style="font-size:22px;font-weight:800;color:var(--text-main);margin-bottom:6px;">Create New Password</h2>
<p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px;">Please enter your new strong password below.</p>

<form action="<?= url('reset-password') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token) ?>">

    <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" name="password" class="form-control" required autofocus placeholder="••••••••">
        <div style="font-size:11.5px;color:var(--text-subtle);margin-top:4px;">
            Min 8 chars with uppercase, lowercase, number, and special character.
        </div>
        <?php if (has_error('password')): ?>
            <div class="form-error"><?= e(error_msg('password')) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label"><?= __('confirm_password') ?></label>
        <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
        <?php if (has_error('password_confirmation')): ?>
            <div class="form-error"><?= e(error_msg('password_confirmation')) ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:14px;margin-top:8px;">
        <i class="fa-solid fa-key"></i> Update Password
    </button>
</form>
