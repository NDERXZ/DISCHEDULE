<div style="max-width:650px;margin:60px auto;text-align:center;">
    <div style="width:70px;height:70px;border-radius:50%;background:var(--color-danger-bg);color:var(--color-danger);display:inline-flex;align-items:center;justify-content:center;font-size:32px;margin-bottom:20px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <h1 style="font-size:28px;font-weight:800;color:var(--text-main);margin-bottom:8px;">500 - Internal Server Error</h1>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:24px;">An unexpected error occurred during processing. Our automated logs have recorded this event.</p>

    <?php if (!empty($exception)): ?>
        <div class="card" style="text-align:left;margin-bottom:24px;background:#0F172A;color:#E2E8F0;font-family:monospace;font-size:12px;overflow-x:auto;">
            <div class="card-body" style="padding:16px;">
                <div style="color:#F87171;font-weight:700;margin-bottom:8px;"><?= e($exception->getMessage()) ?></div>
                <div style="color:#94A3B8;"><?= e($exception->getFile()) ?>:<?= $exception->getLine() ?></div>
                <pre style="margin-top:12px;white-space:pre-wrap;color:#64748B;"><?= e($exception->getTraceAsString()) ?></pre>
            </div>
        </div>
    <?php endif; ?>

    <a href="<?= url('calendar') ?>" class="btn btn-primary">
        <i class="fa-solid fa-house"></i> Return to Homepage
    </a>
</div>
