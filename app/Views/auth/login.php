<h2 style="font-size:22px;font-weight:800;color:var(--text-main);margin-bottom:6px;"><?= __('welcome_back') ?></h2>
<p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px;"><?= __('sign_in_desc') ?></p>



<form action="<?= url('login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="form-label"><?= __('email_or_username') ?></label>
        <input type="text" name="login" class="form-control" value="<?= e(old('login')) ?>" required autofocus placeholder="username or you@example.com">
        <?php if (has_error('login')): ?>
            <div class="form-error"><?= e(error_msg('login')) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <label class="form-label" style="margin-bottom:0;"><?= __('password') ?></label>
            <a href="<?= url('forgot-password') ?>" style="font-size:12px;font-weight:600;"><?= __('forgot_password') ?></a>
        </div>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
        <?php if (has_error('password')): ?>
            <div class="form-error"><?= e(error_msg('password')) ?></div>
        <?php endif; ?>
    </div>

    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
        <input type="checkbox" name="remember" id="remember" value="1" checked style="cursor:pointer;">
        <label for="remember" style="font-size:13px;color:var(--text-muted);cursor:pointer;"><?= __('remember_me') ?></label>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:14px;">
        <i class="fa-solid fa-right-to-bracket"></i> <?= __('login') ?>
    </button>
</form>

<div style="margin-top:24px;text-align:center;font-size:13px;color:var(--text-muted);">
    <?= __('dont_have_account') ?> <a href="<?= url('register') ?>" style="font-weight:600;"><?= __('register') ?></a>
</div>
