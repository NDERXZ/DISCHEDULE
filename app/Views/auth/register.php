<h2 style="font-size:22px;font-weight:800;color:var(--text-main);margin-bottom:6px;"><?= __('register') ?></h2>
<p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px;">Join Dischedule and connect your schedule to Discord.</p>



<form action="<?= url('register') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= e(old('username')) ?>" required autofocus placeholder="alex_dev">
        <?php if (has_error('username')): ?>
            <div class="form-error"><?= e(error_msg('username')) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required placeholder="alex@example.com">
        <?php if (has_error('email')): ?>
            <div class="form-error"><?= e(error_msg('email')) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label class="form-label"><?= __('password') ?></label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
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
        <i class="fa-solid fa-user-plus"></i> <?= __('register') ?>
    </button>
</form>

<div style="margin-top:24px;text-align:center;font-size:13px;color:var(--text-muted);">
    <?= __('already_registered') ?> <a href="<?= url('login') ?>" style="font-weight:600;"><?= __('login') ?></a>
</div>
