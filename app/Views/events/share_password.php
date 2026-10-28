<div style="text-align:center;">
    <div style="width:56px;height:56px;border-radius:50%;background:var(--color-warning-bg);color:var(--color-warning);display:inline-flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:16px;">
        <i class="fa-solid fa-lock"></i>
    </div>
    <h2 style="font-size:22px;font-weight:800;color:var(--text-main);margin-bottom:6px;">Protected Event</h2>
    <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:24px;">
        This event schedule is password protected by the host. Please enter the password to proceed.
    </p>

    <form action="<?= url('share/' . $token) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group" style="text-align:left;">
            <label class="form-label">Event Password</label>
            <input type="password" name="password" class="form-control" required autofocus placeholder="Enter password...">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-size:14px;margin-top:8px;">
            <i class="fa-solid fa-unlock"></i> Unlock Schedule
        </button>
    </form>
</div>
