<h2 style="font-size:22px;font-weight:800;color:var(--text-main);margin-bottom:6px;"><?= __('forgot_password') ?></h2>
<p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px;">Enter your registered email address to receive a secure password reset link.</p>

<form action="<?= url('forgot-password') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required autofocus placeholder="you@example.com">
        <?php if (has_error('email')): ?>
            <div class="form-error"><?= e(error_msg('email')) ?></div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:14px;margin-top:8px;">
        <i class="fa-solid fa-paper-plane"></i> <?= __('reset_password_btn') ?>
    </button>
</form>

<div style="margin-top:24px;text-align:center;font-size:13px;color:var(--text-muted);">
    Remember your password? <a href="<?= url('login') ?>" style="font-weight:600;"><?= __('login') ?></a>
</div>
